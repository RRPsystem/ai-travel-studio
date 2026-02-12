import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

async function sendWhatsAppMessage(
  to: string,
  message: string,
  twilioAccountSid: string,
  twilioAuthToken: string,
  twilioWhatsAppNumber: string,
  mediaUrl?: string
) {
  const url = `https://api.twilio.com/2010-04-01/Accounts/${twilioAccountSid}/Messages.json`;

  const params: Record<string, string> = {
    From: `whatsapp:${twilioWhatsAppNumber}`,
    To: `whatsapp:${to}`,
    Body: message,
  };

  if (mediaUrl) {
    params.MediaUrl = mediaUrl;
  }

  const body = new URLSearchParams(params);

  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Authorization': 'Basic ' + btoa(`${twilioAccountSid}:${twilioAuthToken}`),
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: body.toString(),
  });

  if (!response.ok) {
    const error = await response.text();
    console.error('Twilio API error:', error);
    throw new Error('Failed to send WhatsApp message');
  }

  return await response.json();
}

async function transcribeAudio(audioUrl: string, openaiApiKey: string, twilioAccountSid: string, twilioAuthToken: string): Promise<string> {
  try {
    const authHeader = 'Basic ' + btoa(`${twilioAccountSid}:${twilioAuthToken}`);
    const audioResponse = await fetch(audioUrl, {
      headers: { 'Authorization': authHeader }
    });

    if (!audioResponse.ok) {
      throw new Error(`Failed to fetch audio: ${audioResponse.statusText}`);
    }

    const audioBlob = await audioResponse.blob();
    const formData = new FormData();
    formData.append('file', audioBlob, 'audio.ogg');
    formData.append('model', 'whisper-1');
    formData.append('language', 'nl');

    const transcriptionResponse = await fetch('https://api.openai.com/v1/audio/transcriptions', {
      method: 'POST',
      headers: { 'Authorization': `Bearer ${openaiApiKey}` },
      body: formData,
    });

    if (!transcriptionResponse.ok) {
      const error = await transcriptionResponse.text();
      console.error('OpenAI API error:', error);
      throw new Error('Failed to transcribe audio');
    }

    const result = await transcriptionResponse.json();
    return result.text;
  } catch (error) {
    console.error('Error in transcribeAudio:', error);
    throw error;
  }
}

// ============================================
// ASYNC PROCESSING - runs after Twilio gets its response
// This is the key to stability: Twilio has a 15s timeout,
// but OpenAI can take 5-20s. By responding immediately and
// processing async, we never timeout.
// ============================================
async function processMessageAsync(
  from: string,
  userMessage: string,
  imageUrl: string | undefined,
  mediaContentType: string | undefined,
  sessionData: any,
  trip: any,
  twilioAccountSid: string,
  twilioAuthToken: string,
  twilioWhatsAppNumber: string,
) {
  const supabaseUrl = Deno.env.get('SUPABASE_URL')!;
  const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
  const supabase = createClient(supabaseUrl, supabaseServiceKey);

  try {
    // Get conversation history
    const { data: recentMessages } = await supabase
      .from('travel_conversations')
      .select('role, message')
      .eq('session_token', sessionData.session_token)
      .order('created_at', { ascending: false })
      .limit(10);

    const conversationHistory = (recentMessages || []).reverse().map((m: any) => ({ role: m.role, content: m.message }));
    conversationHistory.push({ role: 'user', content: userMessage });

    // Save user message
    await supabase.from('travel_conversations').insert({
      trip_id: trip.id,
      session_token: sessionData.session_token,
      message: userMessage,
      role: 'user'
    });

    // Get API settings (OpenAI + Google Search)
    const { data: apiSettings } = await supabase
      .from('api_settings')
      .select('provider, service_name, api_key, google_search_api_key, google_search_engine_id')
      .in('provider', ['OpenAI', 'system'])
      .eq('is_active', true);

    const openaiSettings = apiSettings?.find(s => s.provider === 'OpenAI');
    const systemSettings = apiSettings?.find(s => s.provider === 'system' && s.service_name === 'Twilio WhatsApp');
    const googleSearchApiKey = systemSettings?.google_search_api_key;
    const googleCseId = systemSettings?.google_search_engine_id;

    console.log('🔍 API Settings:', {
      hasOpenAI: !!openaiSettings?.api_key,
      hasGoogleSearch: !!googleSearchApiKey,
      hasCseId: !!googleCseId
    });

    if (!openaiSettings?.api_key) {
      console.error('❌ No OpenAI API key');
      await sendWhatsAppMessage(from, 'Sorry, ik kan momenteel geen berichten verwerken. Probeer het later opnieuw.', twilioAccountSid, twilioAuthToken, twilioWhatsAppNumber);
      return;
    }

    // Prepare trip info - REMOVE raw_compositor_data to reduce tokens
    let compactData = trip.parsed_data;
    if (compactData?.raw_compositor_data) {
      const { raw_compositor_data, ...rest } = compactData;
      compactData = rest;
    }

    const tripInfo = compactData ? JSON.stringify(compactData, null, 2) : '';
    const customContext = trip.custom_context || '';

    // Extract destination info for context
    const destinations = compactData?.destinations || compactData?.destination || [];
    const destinationNames = Array.isArray(destinations) 
      ? destinations.map((d: any) => typeof d === 'string' ? d : d.name || d.city).filter(Boolean).join(', ')
      : (typeof destinations === 'string' ? destinations : '');

    // Google Search for weather and current information
    let searchResults = "";
    if (googleSearchApiKey && googleCseId) {
      try {
        const isWeatherQuery = /\b(weer|weather|temperature|temperatuur|rain|regen|zon|sun|cloud|bewolkt)\b/i.test(userMessage);
        
        if (isWeatherQuery) {
          const location = destinationNames || trip.name || 'Disneyland Paris';
          const today = new Date();
          const tomorrow = new Date(today);
          tomorrow.setDate(tomorrow.getDate() + 1);
          
          const dateStr = tomorrow.toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
          const searchQuery = `weer ${location} ${dateStr}`;
          
          console.log('🌤️ Weather query detected:', searchQuery);

          const searchUrl = `https://www.googleapis.com/customsearch/v1?key=${googleSearchApiKey}&cx=${googleCseId}&q=${encodeURIComponent(searchQuery)}&num=3`;
          const searchResponse = await fetch(searchUrl);
          
          console.log('🔍 Google Search response:', searchResponse.status);

          if (searchResponse.ok) {
            const searchData = await searchResponse.json();
            if (searchData.items && searchData.items.length > 0) {
              searchResults = "\n\n=== ACTUELE WEERSINFORMATIE VIA GOOGLE ===\n" + 
                searchData.items.map((item: any) => `- ${item.title}: ${item.snippet}`).join("\n") +
                "\n\nGEBRUIK DEZE ACTUELE INFORMATIE in je antwoord!\n===";
              console.log('✅ Google Search results added, length:', searchResults.length);
            }
          } else {
            console.error('❌ Google Search error:', await searchResponse.text());
          }
        }
      } catch (error) {
        console.error('❌ Google Search exception:', error);
      }
    }

    const systemPrompt = `Je bent TravelBro, de persoonlijke reisassistent voor de reis "${trip.name}".

=== BELANGRIJKE CONTEXT ===
🎯 BESTEMMING: ${destinationNames || 'Zie reisdata hieronder'}
📅 REIS: "${trip.name}"

De reiziger heeft deze reis GEBOEKT en gaat naar: ${destinationNames || 'de bestemming in de reisdata'}.
ALLES wat je bespreekt moet relevant zijn voor DEZE specifieke reis.

=== REISDATA ===
${customContext}
${tripInfo}

${searchResults ? searchResults : ''}

=== GEDRAGSREGELS ===
1. **GESPREKSCONTEXT VOLGEN (ZEER BELANGRIJK!):**
   - Lees de HELE conversatie-historie voordat je antwoordt
   - Als de reiziger vraagt over "het hotel" of "daar", refereer dan naar de LAATST BESPROKEN locatie/hotel in het gesprek
   - Voorbeeld: Als je net over Belfast sprak en de reiziger vraagt "zit er eentje dicht bij het hotel?", dan bedoelt hij het hotel in BELFAST, niet een ander hotel
   - Spring NOOIT naar een andere locatie tenzij de reiziger dat expliciet vraagt

2. CONTEXT BEWAKEN: Relateer ALLES aan de geboekte reis. Als een locatie in een ANDER LAND ligt dan de reis, wijs daar vriendelijk op.

3. BIJ AFBEELDINGEN (ZEER BELANGRIJK!):
   - NOEM ALTIJD de naam van wat je ziet! Zeg "Dat is de Eiffeltoren!" niet "een beroemde toren"
   - Wees specifiek en enthousiast bij herkenning
   - Check of de locatie bereikbaar is vanuit de reisbestemming:
     * ZELFDE LAND/REGIO → geef tips hoe ze er kunnen komen (bijv. "Vanaf Disneyland Paris pak je de RER A, 40 min!")
     * ANDER LAND → "Dat is [naam] in [land]! Maar jouw reis gaat naar [bestemming]. Bedoel je misschien iets anders?"
   - Bij onbekende afbeeldingen: beschrijf wat je ziet en vraag context

4. BIJ FOUTEN: Als de reiziger zegt dat je iets verkeerd hebt, erken dit direct en corrigeer jezelf. Zeg bijvoorbeeld: "Je hebt gelijk, mijn excuses! Laat me je helpen met de juiste informatie voor jouw reis naar ${destinationNames}."

5. STIJL:
   - Antwoord in het Nederlands
   - Wees vriendelijk maar beknopt
   - Toon NOOIT prijzen (klant heeft al geboekt)
   - Wees proactief met suggesties

6. LOKALE TIPS: Bij vragen over fietsverhuur, restaurants, etc. geef concrete suggesties gebaseerd op de HUIDIGE locatie in het gesprek.

7. WANDELROUTES:
   **WANNEER VRAGEN STELLEN:**
   - Stel ALLEEN vragen als de gebruiker GEEN details geeft
   - Als gebruiker zegt "wandelroute Dublin centrum 1 uur koffie" → DIRECT route maken, GEEN vragen!
   - Als gebruiker zegt "5 km natuur geen koffie" → DIRECT route maken!
   - Alleen vragen als echt onduidelijk (bijv. alleen "wandelroute" zonder details)
   
   **LOCATIE (KRITIEK!):**
   - LEES de HELE conversatie! Als eerder "Dublin" genoemd is, gebruik DUBLIN!
   - "natuur" betekent NIET naar een andere stad gaan!
   - "natuur" = park/groen in de GENOEMDE stad (bijv. Phoenix Park in Dublin, St Stephen's Green)
   - NOOIT naar Letterkenny gaan als Dublin genoemd is!
   - Als geen stad genoemd: vraag "In welke stad ben je?"
   
   **NATUUR vs CENTRUM:**
   - "natuur" = stadsparken, groene gebieden IN de genoemde stad
   - Dublin natuur = Phoenix Park, St Stephen's Green, Merrion Square
   - NIET: naar het platteland of andere steden!
   
   **AFSTAND EN RONDJE:**
   - 1 uur = ± 5 km, kies 5-6 stops
   - Route is ALTIJD een RONDJE (eind = start), tenzij anders gevraagd
   - Alle stops binnen 1 km van elkaar
   
   - Google Maps link: https://www.google.com/maps/dir/[start]/[stop2]/[stop3]/[start]/data=!4m2!4m1!3e2
   - Output:
     🚶 **Wandelroute (± 5 km, 1 uur)**
     📍 Dublin - Phoenix Park
     🔄 Parkgate St → Papal Cross → Zoo → Ashtown Castle → Parkgate St
     👉 [link]`;

    // Use gpt-4o for vision, gpt-4o-mini for text only
    const gptModel = imageUrl ? 'gpt-4o' : 'gpt-4o-mini';
    
    // Build messages - add image if present
    let messages: any[] = [
      { role: 'system', content: systemPrompt },
      ...conversationHistory.slice(-10)
    ];

    // If image is present, modify the last user message to include the image
    if (imageUrl) {
      try {
        const imgResponse = await fetch(imageUrl, {
          headers: { 'Authorization': 'Basic ' + btoa(`${twilioAccountSid}:${twilioAuthToken}`) }
        });
        if (imgResponse.ok) {
          const imgBlob = await imgResponse.blob();
          const imgBuffer = await imgBlob.arrayBuffer();
          const base64Image = btoa(String.fromCharCode(...new Uint8Array(imgBuffer)));
          const mimeType = mediaContentType || 'image/jpeg';
          
          const lastMsg = messages[messages.length - 1];
          messages[messages.length - 1] = {
            role: 'user',
            content: [
              { type: 'text', text: lastMsg.content + '\n\nBeschrijf wat je ziet op deze afbeelding en help de gebruiker met hun vraag over deze locatie/plek.' },
              { type: 'image_url', image_url: { url: `data:${mimeType};base64,${base64Image}` } }
            ]
          };
          console.log('📷 Image added to vision request');
        }
      } catch (imgError) {
        console.error('Failed to fetch image:', imgError);
      }
    }

    console.log('🤖 Calling OpenAI:', gptModel, 'messages:', messages.length);

    // Call OpenAI with retry
    let aiMessage = '';
    for (let attempt = 1; attempt <= 2; attempt++) {
      try {
        const openaiResponse = await fetch('https://api.openai.com/v1/chat/completions', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${openaiSettings.api_key}`,
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            model: gptModel,
            messages: messages,
            temperature: 0.7,
            max_tokens: 1000,
          }),
        });

        if (!openaiResponse.ok) {
          const error = await openaiResponse.text();
          console.error(`❌ OpenAI error (attempt ${attempt}):`, error);
          if (attempt === 2) throw new Error('OpenAI failed after 2 attempts');
          continue;
        }

        const aiResult = await openaiResponse.json();
        aiMessage = aiResult.choices[0].message.content;
        break;
      } catch (e) {
        console.error(`❌ OpenAI exception (attempt ${attempt}):`, e);
        if (attempt === 2) throw e;
        // Wait 1s before retry
        await new Promise(r => setTimeout(r, 1000));
      }
    }

    if (!aiMessage) {
      throw new Error('No AI response generated');
    }

    // Save assistant response
    await supabase.from('travel_conversations').insert({
      trip_id: trip.id,
      session_token: sessionData.session_token,
      message: aiMessage,
      role: 'assistant'
    });

    // Update session timestamp
    await supabase
      .from('travel_whatsapp_sessions')
      .update({ last_message_at: new Date().toISOString() })
      .eq('id', sessionData.id);

    // Split long messages for WhatsApp (max ~1600 chars per message for readability)
    const MAX_WA_LENGTH = 1500;
    if (aiMessage.length > MAX_WA_LENGTH) {
      const parts = splitMessage(aiMessage, MAX_WA_LENGTH);
      for (const part of parts) {
        await sendWhatsAppMessage(from, part, twilioAccountSid, twilioAuthToken, twilioWhatsAppNumber);
        // Small delay between parts to maintain order
        await new Promise(r => setTimeout(r, 500));
      }
    } else {
      await sendWhatsAppMessage(from, aiMessage, twilioAccountSid, twilioAuthToken, twilioWhatsAppNumber);
    }

    console.log('✅ Response sent to', from);

  } catch (error) {
    console.error('❌ Async processing error:', error);
    // ALWAYS try to send a fallback message so the user isn't left hanging
    try {
      await sendWhatsAppMessage(
        from,
        'Sorry, er ging iets mis bij het verwerken van je bericht. Probeer het nog een keer! 🙏',
        twilioAccountSid,
        twilioAuthToken,
        twilioWhatsAppNumber
      );
    } catch (sendError) {
      console.error('❌ Failed to send fallback message:', sendError);
    }
  }
}

// Split a long message into parts at paragraph/sentence boundaries
function splitMessage(text: string, maxLength: number): string[] {
  const parts: string[] = [];
  let remaining = text;

  while (remaining.length > maxLength) {
    // Try to split at double newline (paragraph)
    let splitIdx = remaining.lastIndexOf('\n\n', maxLength);
    if (splitIdx < maxLength * 0.3) {
      // Try single newline
      splitIdx = remaining.lastIndexOf('\n', maxLength);
    }
    if (splitIdx < maxLength * 0.3) {
      // Try period + space
      splitIdx = remaining.lastIndexOf('. ', maxLength);
      if (splitIdx > 0) splitIdx += 1; // include the period
    }
    if (splitIdx < maxLength * 0.3) {
      // Hard split
      splitIdx = maxLength;
    }

    parts.push(remaining.substring(0, splitIdx).trim());
    remaining = remaining.substring(splitIdx).trim();
  }

  if (remaining) parts.push(remaining);
  return parts;
}

// ============================================
// MAIN WEBHOOK HANDLER
// Returns immediately to Twilio, processes async
// ============================================
Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 200, headers: corsHeaders });
  }

  // Immediately parse the incoming message and return 200 to Twilio
  // All heavy processing happens async after the response
  const emptyTwiml = '<?xml version="1.0" encoding="UTF-8"?><Response></Response>';

  try {
    console.log('=== WHATSAPP WEBHOOK CALLED ===');

    const formData = await req.formData();
    const from = formData.get('From')?.toString().replace('whatsapp:', '') || '';
    const body = formData.get('Body')?.toString() || '';
    const numMedia = parseInt(formData.get('NumMedia')?.toString() || '0');
    const mediaUrl = numMedia > 0 ? formData.get('MediaUrl0')?.toString() : undefined;
    const mediaContentType = numMedia > 0 ? formData.get('MediaContentType0')?.toString() : undefined;
    
    const latitude = formData.get('Latitude')?.toString();
    const longitude = formData.get('Longitude')?.toString();
    const hasLocation = latitude && longitude;

    console.log('📩 Received:', { from, body: body.substring(0, 50), numMedia, hasLocation });

    if (!from) {
      return new Response(emptyTwiml, { headers: { ...corsHeaders, 'Content-Type': 'text/xml' } });
    }

    const supabaseUrl = Deno.env.get('SUPABASE_URL')!;
    const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
    const supabase = createClient(supabaseUrl, supabaseServiceKey);

    // Quick lookups needed before we can go async
    const { data: sessionData } = await supabase
      .from('travel_whatsapp_sessions')
      .select(`*, travel_trips (id, name, parsed_data, source_urls, custom_context, gpt_model, gpt_temperature, brand_id)`)
      .eq('phone_number', from)
      .order('created_at', { ascending: false })
      .limit(1)
      .maybeSingle();

    if (!sessionData?.travel_trips) {
      console.error('❌ No session/trip for:', from);
      return new Response(emptyTwiml, { headers: { ...corsHeaders, 'Content-Type': 'text/xml' } });
    }

    const trip = sessionData.travel_trips;

    // Get Twilio settings
    const { data: twilioSettings } = await supabase
      .from('api_settings')
      .select('twilio_account_sid, twilio_auth_token, twilio_whatsapp_number')
      .or(`brand_id.eq.${trip.brand_id},provider.eq.system`)
      .order('brand_id', { ascending: false })
      .limit(1)
      .maybeSingle();

    if (!twilioSettings?.twilio_account_sid) {
      console.error('❌ Twilio credentials not found');
      return new Response(emptyTwiml, { headers: { ...corsHeaders, 'Content-Type': 'text/xml' } });
    }

    const twilioAccountSid = twilioSettings.twilio_account_sid;
    const twilioAuthToken = twilioSettings.twilio_auth_token;
    const twilioWhatsAppNumber = twilioSettings.twilio_whatsapp_number || '+14155238886';

    // Build user message from various input types
    let userMessage = body;
    let imageUrl: string | undefined = undefined;

    if (mediaContentType?.startsWith('image/') && mediaUrl) {
      imageUrl = mediaUrl;
      if (!userMessage) userMessage = '[Gebruiker stuurde een afbeelding]';
    }

    if (hasLocation) {
      const locationContext = `[Gebruiker deelde locatie: ${latitude}, ${longitude}]`;
      userMessage = userMessage ? locationContext + ' ' + userMessage : locationContext + ' Geef me een wandelroute vanaf deze locatie.';
    }

    // Handle audio - this needs to happen before async since we need the transcription
    if (mediaContentType?.startsWith('audio/') && mediaUrl) {
      const { data: audioSettings } = await supabase
        .from('api_settings')
        .select('api_key')
        .eq('provider', 'OpenAI')
        .eq('service_name', 'OpenAI API')
        .maybeSingle();

      if (audioSettings?.api_key) {
        try {
          userMessage = await transcribeAudio(mediaUrl, audioSettings.api_key, twilioAccountSid, twilioAuthToken);
        } catch (e) {
          console.error('Transcription failed:', e);
          userMessage = userMessage || '[Spraakbericht kon niet worden verwerkt]';
        }
      }
    }

    if (!userMessage) {
      return new Response(emptyTwiml, { headers: { ...corsHeaders, 'Content-Type': 'text/xml' } });
    }

    console.log('✅ Trip:', trip.name, '| Processing async...');

    // Fire-and-forget: process the message asynchronously
    // EdgeRuntime.waitUntil keeps the function alive after response is sent
    const asyncPromise = processMessageAsync(
      from, userMessage, imageUrl, mediaContentType,
      sessionData, trip,
      twilioAccountSid, twilioAuthToken, twilioWhatsAppNumber
    );

    // Use EdgeRuntime.waitUntil if available (Supabase Edge Functions support this)
    // This ensures the async work completes even after we return the response
    if (typeof (globalThis as any).EdgeRuntime !== 'undefined' && (globalThis as any).EdgeRuntime.waitUntil) {
      (globalThis as any).EdgeRuntime.waitUntil(asyncPromise);
    } else {
      // Fallback: just fire and don't await
      asyncPromise.catch(e => console.error('Async processing failed:', e));
    }

    // Return immediately to Twilio - no timeout!
    return new Response(emptyTwiml, {
      headers: { ...corsHeaders, 'Content-Type': 'text/xml' },
    });

  } catch (error) {
    console.error('❌ Webhook parse error:', error);
    return new Response(emptyTwiml, {
      status: 200, // Still return 200 to Twilio to prevent retries
      headers: { ...corsHeaders, 'Content-Type': 'text/xml' },
    });
  }
});
