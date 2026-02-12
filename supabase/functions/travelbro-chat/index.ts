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
    console.log('🔍 Google Search - Initial check:', {
      hasGoogleSearchApiKey: !!googleSearchApiKey,
      hasCseId: !!googleCseId,
      message: message.substring(0, 50)
    });

    if (googleSearchApiKey && googleCseId) {
      try {
        // Detect weather-related questions and use current date + location
        const isWeatherQuery = /\b(weer|weather|temperature|temperatuur|rain|regen|zon|sun|cloud|bewolkt)\b/i.test(message);
        
        let searchQuery;
        if (isWeatherQuery) {
          // Extract location from trip data or use trip name
          const location = trip.parsed_data?.destination || trip.name || 'Disneyland Paris';
          const today = new Date();
          const tomorrow = new Date(today);
          tomorrow.setDate(tomorrow.getDate() + 1);
          
          // Format: "weather [location] [date]"
          const dateStr = tomorrow.toLocaleDateString('nl-NL', { day: 'numeric', month: 'long', year: 'numeric' });
          searchQuery = `weer ${location} ${dateStr}`;
          console.log('🌤️ Weather query detected, using current date');
        } else {
          searchQuery = `${message} ${trip.name}`;
        }
        
        console.log('🔍 Google Search - Config:', {
          query: searchQuery,
          isWeatherQuery,
          apiKeyPrefix: googleSearchApiKey.substring(0, 10) + '...',
          apiKeyLength: googleSearchApiKey.length,
          cseId: googleCseId,
          cseIdLength: googleCseId.length
        });

        const searchUrl = `https://www.googleapis.com/customsearch/v1?key=${googleSearchApiKey}&cx=${googleCseId}&q=${encodeURIComponent(searchQuery)}&num=3`;
        console.log('🔍 Google Search - Calling URL (key hidden)');

        const searchResponse = await fetch(searchUrl);
        console.log('🔍 Google Search - Response status:', searchResponse.status);

        if (searchResponse.ok) {
          const searchData = await searchResponse.json();
          console.log('🔍 Google Search - Response data:', {
            hasItems: !!searchData.items,
            itemCount: searchData.items?.length || 0,
            searchInfo: searchData.searchInformation
          });

          if (searchData.items && searchData.items.length > 0) {
            searchResults = "\n\nRelevante zoekresultaten:\n" + searchData.items
              .map((item: any) => `- ${item.title}: ${item.snippet}`)
              .join("\n");
            console.log('🔍 Google Search - Search results created, length:', searchResults.length);
            console.log('🔍 Google Search - First result:', searchData.items[0].title);
          } else {
            console.log('🔍 Google Search - No items in response');
          }
        } else {
          const errorText = await searchResponse.text();
          console.error('🔍 Google Search - Error response:', errorText);
          try {
            const errorData = JSON.parse(errorText);
            console.error('🔍 Google Search - Error details:', {
              code: errorData.error?.code,
              message: errorData.error?.message,
              status: errorData.error?.status
            });
          } catch (e) {
            console.error('🔍 Google Search - Could not parse error');
          }
        }
      } catch (error) {
        console.error("🔍 Google Search - Exception:", error);
      }
    } else {
      console.log('🔍 Google Search - SKIPPED (missing keys):', {
        hasSearchApiKey: !!googleSearchApiKey,
        hasCseId: !!googleCseId
      });
    }
    
    console.log('🔍 Google Search - Final searchResults length:', searchResults.length);

    let tripContext = `REISINFORMATIE:\n- Reis: ${trip.name}`;

    // Check custom_context (contains synced compositor data as readable text)
    if (trip.custom_context && typeof trip.custom_context === 'string' && trip.custom_context.length > 0) {
      tripContext += `\n\nGEDETAILLEERDE REISINFORMATIE:\n${trip.custom_context.substring(0, 8000)}`;
    }
    
    // Also add parsed_data if available (structured data)
    if (trip.parsed_data && typeof trip.parsed_data === 'object' && Object.keys(trip.parsed_data).length > 0) {
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

    const systemPrompt = `Je bent TravelBRO, een vriendelijke en behulpzame Nederlandse reisassistent met toegang tot actuele informatie via Google Search.

${tripContext}${intakeContext}

${searchResults ? `=== ACTUELE INFORMATIE VIA GOOGLE SEARCH ===
Hieronder staan VERSE, ACTUELE zoekresultaten die je VERPLICHT moet gebruiken om de vraag te beantwoorden:
${searchResults}

KRITISCH: Deze Google Search resultaten bevatten de meest actuele informatie. Gebruik deze ALTIJD als basis voor je antwoord. Negeer NIET deze informatie!
===` : ''}

INSTRUCTIES:
${searchResults ? '- GEBRUIK DE GOOGLE SEARCH RESULTATEN HIERBOVEN om de vraag te beantwoorden\n- Geef het weer, prijzen, openingstijden etc. zoals vermeld in de zoekresultaten\n- Wees specifiek en concreet op basis van de zoekresultaten\n- Als de zoekresultaten temperaturen, weersomstandigheden of andere details bevatten, NOEM deze expliciet' : '- Voor actuele informatie (weer, prijzen, openingstijden) heb ik helaas geen live data beschikbaar'}
- Gebruik de reisinformatie voor algemene vragen over de reis
- Wees vriendelijk en behulpzaam
- Als informatie ontbreekt, zeg dat eerlijk`;

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