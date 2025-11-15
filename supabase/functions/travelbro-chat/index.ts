import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, {
      status: 200,
      headers: corsHeaders,
    });
  }

  try {
    const { tripId, sessionToken, message } = await req.json();

    if (!tripId || !sessionToken || !message) {
      return new Response(
        JSON.stringify({ error: "Missing required fields" }),
        {
          status: 400,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const supabaseUrl = Deno.env.get("SUPABASE_URL")!;
    const supabaseKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;
    const supabase = createClient(supabaseUrl, supabaseKey);

    const { data: apiSettings } = await supabase
      .from('api_settings')
      .select('provider, service_name, api_key, metadata, google_search_api_key, google_search_engine_id, google_places_api_key')
      .in('provider', ['OpenAI', 'Google', 'system'])
      .eq('is_active', true);

    const openaiApiKey = apiSettings?.find(s => s.provider === 'OpenAI')?.api_key;
    const googleMapsApiKey = apiSettings?.find(s => s.provider === 'Google' && s.service_name === 'Google Maps API')?.api_key;

    const systemSettings = apiSettings?.find(s => s.provider === 'system' && s.service_name === 'Twilio WhatsApp');
    const googleSearchApiKey = systemSettings?.google_search_api_key;
    const googleCseId = systemSettings?.google_search_engine_id;

    console.log('🔍 API Settings Check:', {
      hasOpenAI: !!openaiApiKey,
      hasGoogleMaps: !!googleMapsApiKey,
      hasGoogleSearchApiKey: !!googleSearchApiKey,
      hasGoogleCseId: !!googleCseId,
      settingsCount: apiSettings?.length || 0
    });

    const { data: trip, error: tripError } = await supabase
      .from("travel_trips")
      .select("*")
      .eq("id", tripId)
      .single();

    if (tripError || !trip) {
      return new Response(
        JSON.stringify({ error: "Trip not found" }),
        {
          status: 404,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const { data: intake } = await supabase
      .from("travel_intakes")
      .select("*")
      .eq("session_token", sessionToken)
      .single();

    const { data: conversationHistory } = await supabase
      .from("travel_conversations")
      .select("*")
      .eq("session_token", sessionToken)
      .order("created_at", { ascending: true })
      .limit(10);

    let searchResults = "";
    const needsSearch = /\b(openingstijd|weer|actueel|prijs|kosten|ticket|website|adres|hoe laat|wanneer|seizoen|nu|vandaag|morgen|huidige|laatste|recent)\b/i.test(message);

    if (googleSearchApiKey && googleCseId && needsSearch) {
      try {
        const searchQuery = `${message} ${trip.name}`;
        console.log('🔍 Google Search - Config:', {
          query: searchQuery,
          needsSearch: true,
          apiKeyPrefix: googleSearchApiKey.substring(0, 10) + '...',
          cseId: googleCseId,
          cseIdLength: googleCseId.length
        });

        const searchUrl = `https://www.googleapis.com/customsearch/v1?key=${googleSearchApiKey}&cx=${googleCseId}&q=${encodeURIComponent(searchQuery)}&num=3`;
        console.log('🔍 Google Search - URL:', searchUrl.replace(googleSearchApiKey, 'API_KEY_HIDDEN'));

        const searchResponse = await fetch(searchUrl);
        console.log('🔍 Google Search - Response status:', searchResponse.status);

        if (searchResponse.ok) {
          const searchData = await searchResponse.json();
          console.log('🔍 Google Search - Found items:', searchData.items?.length || 0);

          if (searchData.items && searchData.items.length > 0) {
            searchResults = "\n\nRelevante zoekresultaten:\n" + searchData.items
              .map((item: any) => `- ${item.title}: ${item.snippet}`)
              .join("\n");
            console.log('🔍 Google Search - Added to context');
          }
        } else {
          const errorText = await searchResponse.text();
          console.error('🔍 Google Search - Error response:', errorText);
          const errorData = JSON.parse(errorText);
          console.error('🔍 Google Search - Error details:', {
            code: errorData.error?.code,
            message: errorData.error?.message,
            status: errorData.error?.status
          });
        }
      } catch (error) {
        console.error("🔍 Google Search - Exception:", error);
      }
    } else {
      console.log('🔍 Google Search - SKIPPED:', {
        hasSearchApiKey: !!googleSearchApiKey,
        hasCseId: !!googleCseId,
        needsSearch: needsSearch,
        message: message.substring(0, 50)
      });
    }

    let tripContext = `REISINFORMATIE:\n- Reis: ${trip.name}`;

    if (trip.raw_text) {
      tripContext += `\n\nGEDETAILLEERDE REISINFORMATIE (uit PDF/document):\n${trip.raw_text.substring(0, 8000)}`;
    } else if (trip.parsed_data && Object.keys(trip.parsed_data).length > 0) {
      tripContext += `\n\nGESTRUCTUREERDE REISINFORMATIE:\n${JSON.stringify(trip.parsed_data, null, 2)}`;
    }

    if (trip.gpt_instructions) {
      tripContext += `\n\nSPECIALE INSTRUCTIES VOOR DEZE REIS:\n${trip.gpt_instructions}`;
    }

    let intakeContext = '';
    if (intake?.intake_data) {
      intakeContext = `\n\nCLIËNT VOORKEUREN (uit intake):\n${JSON.stringify(intake.intake_data, null, 2)}`;
    }

    console.log('📚 Context gebouwd:', {
      tripName: trip.name,
      hasRawText: !!trip.raw_text,
      rawTextLength: trip.raw_text?.length || 0,
      hasParsedData: !!(trip.parsed_data && Object.keys(trip.parsed_data).length > 0),
      hasGptInstructions: !!trip.gpt_instructions,
      hasIntakeData: !!intake?.intake_data,
      hasSearchResults: !!searchResults,
      totalContextLength: (tripContext + intakeContext + searchResults).length
    });

    const hasSearchCapability = !!(googleSearchApiKey && googleCseId);

    const systemPrompt = `Je bent TravelBRO, een vriendelijke en behulpzame Nederlandse reisassistent.

${hasSearchCapability ? '🌐 JE HEBT TOEGANG TOT GOOGLE SEARCH en kunt actuele informatie opzoeken op internet!\nAls je vragen krijgt over actuele info (openingstijden, prijzen, weer, etc.), vertel dan dat je dat voor ze kunt opzoeken.' : '⚠️ Je hebt momenteel geen toegang tot Google Search. Gebruik alleen de beschikbare reisinformatie.'}

${tripContext}${intakeContext}${searchResults ? `\n\nACTUELE INFORMATIE VAN INTERNET (via Google Search):${searchResults}` : ''}

BELANGRIJKE INSTRUCTIES:
${hasSearchCapability ? '- Je HEBT toegang tot Google Search voor actuele informatie\n- Als iemand vraagt of je internet hebt, zeg JA\n- Voor actuele vragen (openingstijden, prijzen, weer) vertel je dat je dat kunt opzoeken' : '- Je hebt geen toegang tot internet\n- Gebruik alleen de beschikbare reisinformatie'}
${searchResults ? '- De zoekresultaten hierboven komen van internet en zijn actueel - gebruik ze!' : ''}
- Gebruik ALTIJD eerst de reisinformatie uit de documenten
- Als informatie ontbreekt in documenten maar je kunt zoeken, vertel dat je het kunt opzoeken
- Geef concrete, specifieke antwoorden op basis van beschikbare informatie
- Wees eerlijk als je iets niet weet`;


    const messages = [
      { role: "system", content: systemPrompt },
    ];

    if (conversationHistory && conversationHistory.length > 0) {
      conversationHistory.forEach((conv: any) => {
        messages.push({
          role: conv.role,
          content: conv.message,
        });
      });
    }

    messages.push({ role: "user", content: message });

    if (!openaiApiKey) {
      return new Response(
        JSON.stringify({ error: "OpenAI API key not configured" }),
        {
          status: 500,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const openaiResponse = await fetch("https://api.openai.com/v1/chat/completions", {
      method: "POST",
      headers: {
        "Authorization": `Bearer ${openaiApiKey}`,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        model: trip.gpt_model || "gpt-4o",
        messages,
        max_tokens: 1000,
        temperature: trip.gpt_temperature ?? 0.7,
      }),
    });

    if (!openaiResponse.ok) {
      const error = await openaiResponse.text();
      console.error("OpenAI API error:", error);
      return new Response(
        JSON.stringify({ error: "Failed to get AI response", details: error }),
        {
          status: 500,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const openaiData = await openaiResponse.json();
    const aiResponse = openaiData.choices[0].message.content;

    await supabase.from("travel_conversations").insert([
      {
        trip_id: tripId,
        session_token: sessionToken,
        message: message,
        role: "user",
      },
      {
        trip_id: tripId,
        session_token: sessionToken,
        message: aiResponse,
        role: "assistant",
      },
    ]);

    return new Response(
      JSON.stringify({ response: aiResponse }),
      {
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      }
    );
  } catch (error) {
    console.error("Error in travelbro-chat:", error);
    return new Response(
      JSON.stringify({ error: error.message }),
      {
        status: 500,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      }
    );
  }
});