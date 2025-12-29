import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2";
import { formatTripDataForAI } from "./format-trip-data.ts";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "Content-Type, Authorization, X-Client-Info, Apikey",
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

    let openaiApiKey = apiSettings?.find(s => s.provider === 'OpenAI')?.api_key;

    if (!openaiApiKey) {
      openaiApiKey = Deno.env.get('OPENAI_API_KEY');
      if (openaiApiKey) {
        console.log('⚠️ Using system-wide OPENAI_API_KEY fallback');
      }
    }

    const googleMapsApiKey = apiSettings?.find(s => s.provider === 'Google' && s.service_name === 'Google Maps API')?.api_key;

    const systemSettings = apiSettings?.find(s => s.provider === 'system' && s.service_name === 'Twilio WhatsApp');
    const googleApiKey = systemSettings?.google_search_api_key;
    const googleCseId = systemSettings?.google_search_engine_id;

    console.log('🔍 API Settings Check:', {
      hasOpenAI: !!openaiApiKey,
      hasGoogleMaps: !!googleMapsApiKey,
      hasGoogleSearchApiKey: !!googleApiKey,
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
      .limit(20);

    let searchResults = "";
    if (googleApiKey && googleCseId) {
      try {
        const searchQuery = `${message} ${trip.name}`;
        const searchUrl = `https://www.googleapis.com/customsearch/v1?key=${googleApiKey}&cx=${googleCseId}&q=${encodeURIComponent(searchQuery)}&num=3`;

        const searchResponse = await fetch(searchUrl);
        if (searchResponse.ok) {
          const searchData = await searchResponse.json();
          if (searchData.items && searchData.items.length > 0) {
            searchResults = "\n\nRelevante zoekresultaten:\n" + searchData.items
              .map((item: any) => `- ${item.title}: ${item.snippet}`)
              .join("\n");
          }
        }
      } catch (error) {
        console.error("Search error:", error);
      }
    }

    let locationData = "";
    let contextualLocation = "";
    
    // Detecteer de huidige locatie uit de conversatie context
    if (conversationHistory && conversationHistory.length > 0) {
      const recentMessages = conversationHistory.slice(-5).map((c: any) => c.message.toLowerCase()).join(" ");
      
      // Zoek naar stad namen in de recente conversatie
      const accommodationsList = trip.parsed_data?.accommodations || [];
      for (const acc of accommodationsList) {
        const location = (acc.location || acc.city || acc.name || '').toLowerCase();
        if (location && recentMessages.includes(location.split(',')[0].trim())) {
          contextualLocation = acc.location || acc.city || acc.name;
          console.log('🎯 Detected contextual location from conversation:', contextualLocation);
          break;
        }
      }
    }

    const locationKeywords = [
      'route', 'routes', 'hoe kom ik', 'afstand', 'reistijd', 'navigatie', 'rijden', 'waar is', 'waar ligt', 'adres', 'locatie',
      'hotel', 'accommodatie', 'overnachten', 'slapen',
      'restaurant', 'eten', 'drinken', 'cafe', 'bar', 'bakker', 'bakkerij', 'supermarkt', 'winkel', 'boodschappen', 'brood', 'ontbijt', 'lunch', 'diner', 'koffie',
      'activiteiten', 'te doen', 'bezienswaardigheden', 'attractie', 'museum', 'park', 'doen', 'zien', 'bezichtigen', 'uitje', 'dagje uit',
      'dokter', 'arts', 'huisarts', 'ziekenhuis', 'apotheek', 'pharmacy', 'medisch', 'ehbo', 'tandarts',
      'taxi', 'bus', 'trein', 'station', 'vliegveld', 'airport', 'vervoer', 'openbaar vervoer', 'parkeren', 'fiets', 'fietsen', 'huren', 'autoverhuur', 'scooter',
      'bank', 'atm', 'pinautomaat', 'geld', 'wisselkantoor', 'benzinestation', 'tankstation', 'postkantoor', 'post',
      'winkelen', 'shoppen', 'winkelcentrum', 'mall', 'markt', 'kopen',
      'politie', 'brandweer', 'hulpdiensten', 'noodgeval',
      'zwembad', 'gym', 'fitness', 'sport', 'sporten', 'wandelen', 'hiken',
      'dichtbij', 'nabij', 'omgeving', 'buurt', 'in de buurt', 'ver', 'dichtbij', 'loopafstand', 'hoelang', 'hoe ver'
    ];
    const isLocationQuery = locationKeywords.some(keyword => message.toLowerCase().includes(keyword));

    // Als de vraag gaat over afstand/locatie EN we hebben een contextuele locatie, bereken dan de afstand
    if (isLocationQuery && googleMapsApiKey) {
      try {
        const distanceKeywords = ['ver', 'afstand', 'dichtbij', 'loopafstand', 'hoe ver', 'hoelang'];
        const isDistanceQuery = distanceKeywords.some(kw => message.toLowerCase().includes(kw));
        
        if (isDistanceQuery && conversationHistory && conversationHistory.length > 0) {
          // Zoek naar een plaats/restaurant genoemd in recente berichten
          const lastAssistantMessage = conversationHistory
            .slice()
            .reverse()
            .find((c: any) => c.role === 'assistant');
          
          if (lastAssistantMessage && contextualLocation) {
            // Probeer een plaats/restaurant naam te extraheren uit het laatste AI bericht
            const placeMatches = lastAssistantMessage.message.match(/["']([^"']+)["']|\*\*([^*]+)\*\*/g);
            
            if (placeMatches && placeMatches.length > 0) {
              const placeName = placeMatches[0].replace(/["'*]/g, '');
              
              // Zoek het juiste hotel in deze locatie
              const currentHotel = trip.parsed_data?.accommodations?.find((acc: any) => {
                const accLocation = (acc.location || acc.city || '').toLowerCase();
                return accLocation.includes(contextualLocation.toLowerCase().split(',')[0]);
              });
              
              if (currentHotel) {
                const hotelName = currentHotel.name || currentHotel.accommodation_name;
                const hotelLocation = currentHotel.location || currentHotel.city;
                
                console.log('🔍 Calculating distance between:', hotelName, 'and', placeName);
                
                // Bereken afstand met Google Directions API
                const directionsUrl = `https://maps.googleapis.com/maps/api/directions/json?origin=${encodeURIComponent(hotelName + ', ' + hotelLocation)}&destination=${encodeURIComponent(placeName + ', ' + contextualLocation)}&mode=driving&key=${googleMapsApiKey}&language=nl`;
                
                const directionsResponse = await fetch(directionsUrl);
                if (directionsResponse.ok) {
                  const directionsData = await directionsResponse.json();
                  
                  if (directionsData.status === 'OK' && directionsData.routes && directionsData.routes.length > 0) {
                    const route = directionsData.routes[0];
                    const leg = route.legs[0];
                    
                    locationData = `\n\n📍 EXACTE AFSTANDSINFORMATIE:\n\n`;
                    locationData += `Van: **${hotelName}** (${hotelLocation})\n`;
                    locationData += `Naar: **${placeName}**\n\n`;
                    locationData += `🚗 Met de auto: ${leg.distance.text} (${leg.duration.text})\n`;
                    
                    // Bereken loopafstand
                    const walkingUrl = `https://maps.googleapis.com/maps/api/directions/json?origin=${encodeURIComponent(hotelName + ', ' + hotelLocation)}&destination=${encodeURIComponent(placeName + ', ' + contextualLocation)}&mode=walking&key=${googleMapsApiKey}&language=nl`;
                    const walkingResponse = await fetch(walkingUrl);
                    
                    if (walkingResponse.ok) {
                      const walkingData = await walkingResponse.json();
                      if (walkingData.status === 'OK' && walkingData.routes && walkingData.routes.length > 0) {
                        const walkLeg = walkingData.routes[0].legs[0];
                        locationData += `🚶 Lopen: ${walkLeg.distance.text} (${walkLeg.duration.text})\n`;
                      }
                    }
                    
                    locationData += `\n✅ Dit is dus goed te doen!\n`;
                    console.log('✅ Successfully calculated distance');
                  }
                }
              }
            }
          }
        }
        
        // Rest van de originele locatie logica...
        if (!locationData) {
          const serviceMappings: { [key: string]: { types: string[], emoji: string, label: string } } = {
            'restaurant|eten': { types: ['restaurant'], emoji: '🍽️', label: 'Restaurants' },
            'dokter|arts|huisarts|medisch|ehbo': { types: ['doctor', 'hospital'], emoji: '🏥', label: 'Medische voorzieningen' },
            'ziekenhuis': { types: ['hospital'], emoji: '🏥', label: 'Ziekenhuizen' },
            'apotheek|pharmacy': { types: ['pharmacy'], emoji: '💊', label: 'Apotheken' },
            'tandarts': { types: ['dentist'], emoji: '🦷', label: 'Tandartsen' },
            'supermarkt|boodschappen': { types: ['supermarket', 'grocery_or_supermarket'], emoji: '🛒', label: 'Supermarkten' },
            'bakker|bakkerij|brood': { types: ['bakery'], emoji: '🥖', label: 'Bakkerijen' },
            'cafe|koffie': { types: ['cafe'], emoji: '☕', label: 'Cafés' },
            'bar|drinken': { types: ['bar'], emoji: '🍺', label: 'Bars' },
            'bank|atm|pinautomaat|geld': { types: ['bank', 'atm'], emoji: '🏦', label: 'Banken & Geldautomaten' },
            'benzine|tankstation': { types: ['gas_station'], emoji: '⛽', label: 'Tankstations' },
            'fiets|fietsen huren': { types: ['bicycle_store'], emoji: '🚲', label: 'Fietsverhuur' },
            'taxi': { types: ['taxi_stand'], emoji: '🚕', label: 'Taxi standplaatsen' },
          };

          let selectedService: { types: string[], emoji: string, label: string } | null = null;
          const messageLower = message.toLowerCase();

          for (const [keywords, service] of Object.entries(serviceMappings)) {
            const keywordList = keywords.split('|');
            if (keywordList.some(kw => messageLower.includes(kw))) {
              selectedService = service;
              break;
            }
          }

          if (selectedService && contextualLocation) {
            let searchCoordinates = null;
            
            const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(contextualLocation)}&key=${googleMapsApiKey}&language=nl`;
            const geocodeResponse = await fetch(geocodeUrl);

            if (geocodeResponse.ok) {
              const geocodeData = await geocodeResponse.json();
              if (geocodeData.status === 'OK' && geocodeData.results && geocodeData.results.length > 0) {
                searchCoordinates = geocodeData.results[0].geometry.location;
              }
            }

            if (searchCoordinates) {
              const radius = 10000;
              const primaryType = selectedService.types[0];

              const nearbyUrl = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=${searchCoordinates.lat},${searchCoordinates.lng}&radius=${radius}&type=${primaryType}&key=${googleMapsApiKey}&language=nl`;

              const nearbyResponse = await fetch(nearbyUrl);
              if (nearbyResponse.ok) {
                const nearbyData = await nearbyResponse.json();

                if (nearbyData.status === 'OK' && nearbyData.results && nearbyData.results.length > 0) {
                  locationData = `\n\n${selectedService.emoji} ${selectedService.label} in de buurt van ${contextualLocation}:\n\n`;

                  const sortedResults = nearbyData.results
                    .sort((a: any, b: any) => {
                      if (a.opening_hours?.open_now !== b.opening_hours?.open_now) {
                        return (b.opening_hours?.open_now ? 1 : 0) - (a.opening_hours?.open_now ? 1 : 0);
                      }
                      return (b.rating || 0) - (a.rating || 0);
                    })
                    .slice(0, 6);

                  sortedResults.forEach((place: any, index: number) => {
                    locationData += `${index + 1}. **${place.name}**\n`;

                    if (place.rating) {
                      locationData += `   ⭐ ${place.rating}/5`;
                      if (place.user_ratings_total) {
                        locationData += ` (${place.user_ratings_total} reviews)`;
                      }
                      locationData += `\n`;
                    }

                    locationData += `   📍 ${place.vicinity}\n`;

                    if (place.opening_hours) {
                      locationData += `   ${place.opening_hours.open_now ? '✅ Nu open' : '❌ Gesloten'}\n`;
                    }

                    if (place.geometry?.location) {
                      const lat1 = searchCoordinates.lat;
                      const lon1 = searchCoordinates.lng;
                      const lat2 = place.geometry.location.lat;
                      const lon2 = place.geometry.location.lng;

                      const R = 6371;
                      const dLat = (lat2 - lat1) * Math.PI / 180;
                      const dLon = (lon2 - lon1) * Math.PI / 180;
                      const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                                Math.sin(dLon/2) * Math.sin(dLon/2);
                      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                      const distance = R * c;

                      if (distance < 1) {
                        locationData += `   🚶 ${Math.round(distance * 1000)}m lopen (ca. ${Math.round(distance * 12)} min)\n`;
                      } else {
                        locationData += `   🚗 ${distance.toFixed(1)}km rijden (ca. ${Math.round(distance * 2)} min)\n`;
                      }
                    }

                    locationData += `\n`;
                  });
                }
              }
            }
          }
        }
      } catch (error) {
        console.error("Location data error:", error);
      }
    }

    let hasValidTripData = trip.parsed_data && !trip.parsed_data.error && !trip.parsed_data.note;
    let tripDataText = "";

    if (!hasValidTripData) {
      tripDataText = "Geen gedetailleerde reis informatie beschikbaar.";
    } else {
      tripDataText = formatTripDataForAI(trip.parsed_data, trip.name);
    }

    const systemPrompt = `Je bent TravelBRO, een SLIMME en CONTEXT-BEWUSTE Nederlandse reisassistent.

🧠 KRITIEKE REGEL - CONVERSATIE GEHEUGEN:
Je moet ALTIJD de context van het gesprek onthouden en gebruiken:

❌ DOE DIT NOOIT:
- Vragen "over welke locatie wil je informatie?" als we net over een stad spraken
- Zeggen "kun je specifieker zijn?" als de context duidelijk is  
- Vragen welk hotel als de stad al genoemd is
- Algemene antwoorden geven als je specifieke data hebt

✅ DOE DIT WEL:
- Als iemand vraagt over "een restaurant in Swellendam" en daarna "is het ver van ons hotel?"
  → Snap dat ze bedoelen: het hotel IN SWELLENDAM waar ze verblijven
- Als de conversatie over een specifieke stad gaat, blijf op die stad gefocust
- Gebruik de accommodatielijst om te weten waar ze slapen in elke stad
- Als er locatie data beschikbaar is (zie hieronder), GEBRUIK DIE VOLLEDIG!

🗺️ REIS INFORMATIE:
${tripDataText}

${trip.source_urls && trip.source_urls.length > 0 ? `📚 Extra informatie bronnen:\n${trip.source_urls.join("\n")}\n` : ''}

👥 REIZIGER INFORMATIE:
${intake ? JSON.stringify(intake.intake_data, null, 2) : "Geen intake data beschikbaar"}

${locationData ? `\n📍 REAL-TIME LOCATIE DATA:\n${locationData}\n\n⚠️ BELANGRIJK: Deze locatie data is speciaal opgehaald voor deze vraag. Gebruik alle details hieruit!\n` : ''}

${searchResults}

🎯 ANTWOORD REGELS:

1. **WEES SPECIFIEK**: Geen algemene tips, gebruik exacte namen, adressen, afstanden
2. **GEBRUIK ALLE DATA**: Als er locatie informatie hierboven staat, gebruik die VOLLEDIG
3. **LOGISCH REDENEREN**: 
   - "Restaurant in Swellendam" + "ver van hotel?" → Zoek hotel in Swellendam uit de lijst
   - "Is er een supermarkt?" (na gesprek over Knysna) → Supermarkt in Knysna
4. **PERSOONLIJK**: Gebruik namen van reizigers, match aan hun voorkeuren
5. **EMOJI'S**: Maak je antwoorden levendig

💡 VOORBEELDEN:

❌ SLECHT: "Er zijn veel restaurants in de omgeving"
✅ GOED: "Drostdy Restaurant ligt op 2.3km van jullie Aan de Oever Guesthouse (5 minuten met de auto, 28 minuten lopen)"

❌ SLECHT: "Over welke locatie wil je informatie?"
✅ GOED: "In Swellendam waar jullie verblijven in het Aan de Oever Guesthouse..."

❌ SLECHT: "Je kunt skiën in de omgeving"
✅ GOED: "Je kunt skiën bij Skiarena Silvretta Montafon (⭐ 4.6/5), op 15km van je hotel"`;

    let conversationContext = "";
    if (conversationHistory && conversationHistory.length > 0) {
      const recent = conversationHistory.slice(-5);
      conversationContext = "\n\n💬 RECENTE CONVERSATIE (laatste 5 berichten):\n";
      conversationContext += "⚠️ GEBRUIK DEZE CONTEXT OM SLIMME ANTWOORDEN TE GEVEN!\n\n";

      recent.forEach((conv: any) => {
        const roleLabel = conv.role === 'user' ? '👤 Reiziger' : '🤖 Jij (TravelBRO)';
        conversationContext += `${roleLabel}: ${conv.message}\n\n`;
      });

      conversationContext += "\n🎯 Als de nieuwe vraag aansluit bij bovenstaand gesprek, gebruik dan die context!\n";
      conversationContext += "Bijvoorbeeld: als er net over Swellendam gesproken werd en nu wordt gevraagd \"is er een restaurant?\", dan bedoelen ze in Swellendam!\n";
    }

    const messages = [
      { role: "system", content: systemPrompt + conversationContext },
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
        model: trip.gpt_model || "gpt-4o-mini",
        messages,
        max_tokens: 2000,
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
        session_token: sessionToken,
        trip_id: tripId,
        role: "user",
        message: message,
      },
      {
        session_token: sessionToken,
        trip_id: tripId,
        role: "assistant",
        message: aiResponse,
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