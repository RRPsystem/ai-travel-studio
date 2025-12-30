import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "npm:@supabase/supabase-js@2";
import { deductCredits } from '../_shared/credits.ts';

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

async function extractTextFromPDF(pdfBuffer: ArrayBuffer): Promise<string> {
  const uint8Array = new Uint8Array(pdfBuffer);
  const textDecoder = new TextDecoder('utf-8');

  let text = '';
  let i = 0;

  while (i < uint8Array.length) {
    if (uint8Array[i] === 0x42 && uint8Array[i + 1] === 0x54) {
      i += 2;
      let content = '';
      while (i < uint8Array.length && !(uint8Array[i] === 0x45 && uint8Array[i + 1] === 0x54)) {
        if (uint8Array[i] >= 0x20 && uint8Array[i] <= 0x7E) {
          content += String.fromCharCode(uint8Array[i]);
        } else if (uint8Array[i] === 0x0A || uint8Array[i] === 0x0D) {
          content += '\n';
        }
        i++;
      }
      text += content + ' ';
    }
    i++;
  }

  return text.trim();
}

async function parseWithGPT(pdfText: string, openaiApiKey: string) {
  const systemPrompt = `Je bent een expert reisdocument parser. Extraheer en structureer ALLE reis informatie uit de tekst.

VERPLICHTE VELDEN:
- trip_name: Naam van de reis
- reservation_id: Hoofdreserveringsnummer (eerste boekingnummer dat je vindt)
- departure_date: Vertrekdatum (ISO 8601: YYYY-MM-DD)
- arrival_date: Aankomstdatum (ISO 8601: YYYY-MM-DD)
- destination: { city, country, region }
- segments: Array van reissegmenten
- booking_refs: Alle boeknummers
- emergency_contacts: Noodnummers

Elk segment MOET:
- kind: "flight" | "hotel" | "transfer" | "activity"
- segment_ref: Boeknummer
- start_datetime: ISO 8601
- end_datetime: ISO 8601 (of null)
- location: { name, address, city, country }
- details: Extra info

BELANGRIJK:
- Alle datums in ISO 8601 format
- Als info ontbreekt: gebruik null
- Return ALLEEN valid JSON`;

  const response = await fetch('https://api.openai.com/v1/chat/completions', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${openaiApiKey}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      model: 'gpt-4o',
      messages: [
        { role: 'system', content: systemPrompt },
        { role: 'user', content: `Analyseer dit reisdocument en extraheer ALLE informatie:\n\n${pdfText.substring(0, 50000)}` }
      ],
      response_format: {
        type: "json_schema",
        json_schema: {
          name: "travel_document_schema",
          strict: false,
          schema: {
            type: "object",
            properties: {
              trip_name: { type: "string" },
              reservation_id: { type: "string" },
              departure_date: { type: "string" },
              arrival_date: { type: "string" },
              destination: {
                type: "object",
                properties: {
                  city: { type: "string" },
                  country: { type: "string" },
                  region: { type: ["string", "null"] }
                },
                required: ["city", "country"],
                additionalProperties: false
              },
              segments: {
                type: "array",
                items: {
                  type: "object",
                  properties: {
                    kind: { type: "string", enum: ["flight", "hotel", "transfer", "activity"] },
                    segment_ref: { type: "string" },
                    start_datetime: { type: "string" },
                    end_datetime: { type: ["string", "null"] },
                    location: {
                      type: "object",
                      properties: {
                        name: { type: "string" },
                        address: { type: "string" },
                        city: { type: ["string", "null"] },
                        country: { type: ["string", "null"] }
                      },
                      required: ["name", "address"],
                      additionalProperties: false
                    },
                    details: { type: "object", additionalProperties: true }
                  },
                  required: ["kind", "segment_ref", "start_datetime", "location"],
                  additionalProperties: false
                }
              },
              booking_refs: {
                type: "object",
                properties: {
                  flight: { type: ["string", "null"] },
                  hotel: { type: ["string", "null"] },
                  transfer: { type: ["string", "null"] },
                  other: { type: "array", items: { type: "string" } }
                },
                required: [],
                additionalProperties: false
              },
              emergency_contacts: {
                type: "array",
                items: {
                  type: "object",
                  properties: {
                    name: { type: "string" },
                    phone: { type: "string" },
                    type: { type: "string" }
                  },
                  required: ["name", "phone", "type"],
                  additionalProperties: false
                }
              },
              important_notes: { type: "array", items: { type: "string" } },
              included_services: { type: "array", items: { type: "string" } }
            },
            required: ["trip_name", "reservation_id", "departure_date", "arrival_date", "destination", "segments"],
            additionalProperties: false
          }
        }
      },
      temperature: 0.1,
    }),
  });

  if (!response.ok) {
    const error = await response.text();
    throw new Error(`GPT parsing failed: ${error}`);
  }

  const result = await response.json();
  const content = result.choices[0].message.content;
  return JSON.parse(content);
}

function convertToItinerary(parsedData: any, rawText: string, openaiApiKey: string): any[] {
  if (!parsedData?.segments) return [];

  const hotelSegments = parsedData.segments.filter((s: any) => s.kind === 'hotel');

  return hotelSegments.map((hotel: any, index: number) => {
    const startDate = new Date(hotel.start_datetime);
    const dayNumber = index + 1;

    return {
      day: dayNumber,
      date: startDate.toLocaleDateString('nl-NL', { day: 'numeric', month: 'long' }),
      location: hotel.location.city || hotel.location.name,
      hotel: {
        name: hotel.location.name,
        address: hotel.location.address,
        city: hotel.location.city,
        country: hotel.location.country,
        has_restaurant: extractRestaurantInfo(hotel, rawText),
        amenities: extractAmenities(hotel, rawText)
      },
      activities: extractActivities(hotel, parsedData.segments)
    };
  });
}

function extractRestaurantInfo(hotel: any, rawText: string): boolean | null {
  const hotelText = rawText.toLowerCase();
  const hotelName = hotel.location.name.toLowerCase();

  const restaurantKeywords = ['restaurant', 'dining', 'breakfast', 'dinner', 'lunch'];
  const hotelSection = hotelText.indexOf(hotelName);

  if (hotelSection === -1) return null;

  const sectionText = hotelText.substring(hotelSection, hotelSection + 500);

  return restaurantKeywords.some(keyword => sectionText.includes(keyword)) ? true : null;
}

function extractAmenities(hotel: any, rawText: string): string[] {
  const amenities: string[] = [];
  const hotelText = rawText.toLowerCase();
  const hotelName = hotel.location.name.toLowerCase();

  const hotelSection = hotelText.indexOf(hotelName);
  if (hotelSection === -1) return amenities;

  const sectionText = hotelText.substring(hotelSection, hotelSection + 1000);

  const amenityMap: { [key: string]: string[] } = {
    'zwembad': ['pool', 'swimming', 'zwembad'],
    'restaurant': ['restaurant', 'dining'],
    'bar': ['bar', 'lounge'],
    'spa': ['spa', 'wellness', 'massage'],
    'wifi': ['wifi', 'wi-fi', 'internet'],
    'parking': ['parking', 'parkeren'],
    'gym': ['gym', 'fitness'],
  };

  for (const [amenity, keywords] of Object.entries(amenityMap)) {
    if (keywords.some(keyword => sectionText.includes(keyword))) {
      amenities.push(amenity);
    }
  }

  return amenities;
}

function extractActivities(hotel: any, allSegments: any[]): string[] {
  const activities: string[] = [];

  const hotelStart = new Date(hotel.start_datetime);
  const hotelEnd = hotel.end_datetime ? new Date(hotel.end_datetime) : null;

  allSegments.forEach(segment => {
    if (segment.kind === 'activity') {
      const activityDate = new Date(segment.start_datetime);

      if (activityDate >= hotelStart && (!hotelEnd || activityDate < hotelEnd)) {
        activities.push(segment.location.name);
      }
    }
  });

  return activities;
}

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, {
      status: 200,
      headers: corsHeaders,
    });
  }

  try {
    const { pdfUrl } = await req.json();

    if (!pdfUrl) {
      return new Response(
        JSON.stringify({ error: "PDF URL is required" }),
        {
          status: 400,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const supabaseUrl = Deno.env.get("SUPABASE_URL");
    const supabaseServiceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY");
    const supabase = createClient(supabaseUrl!, supabaseServiceKey!);

    const authHeader = req.headers.get('Authorization');
    let userId: string | null = null;

    if (authHeader?.startsWith('Bearer ')) {
      const token = authHeader.substring(7);
      const { data: { user } } = await supabase.auth.getUser(token);
      userId = user?.id || null;
    }

    if (!userId) {
      return new Response(
        JSON.stringify({ error: "Authentication required" }),
        {
          status: 401,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const { data: apiSettings, error: apiError } = await supabase
      .from("api_settings")
      .select("api_key")
      .eq("provider", "OpenAI")
      .eq("service_name", "OpenAI API")
      .maybeSingle();

    if (apiError || !apiSettings?.api_key) {
      return new Response(
        JSON.stringify({ error: "OpenAI API key not configured" }),
        {
          status: 500,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    const openaiApiKey = apiSettings.api_key;

    const creditResult = await deductCredits(
      supabase,
      userId,
      'ai_travel_import',
      `AI PDF parsing: ${pdfUrl.substring(pdfUrl.lastIndexOf('/') + 1, pdfUrl.lastIndexOf('/') + 30)}`,
      { pdfUrl }
    );

    if (!creditResult.success) {
      return new Response(
        JSON.stringify({ error: creditResult.error || 'Failed to deduct credits' }),
        {
          status: 402,
          headers: { ...corsHeaders, "Content-Type": "application/json" },
        }
      );
    }

    console.log('Downloading PDF from:', pdfUrl);
    const pdfResponse = await fetch(pdfUrl);
    if (!pdfResponse.ok) {
      throw new Error("Failed to download PDF");
    }

    const pdfBuffer = await pdfResponse.arrayBuffer();

    console.log('Extracting text from PDF...');
    const pdfText = await extractTextFromPDF(pdfBuffer);

    if (!pdfText || pdfText.length < 100) {
      throw new Error("Could not extract enough text from PDF");
    }

    console.log('Parsing with GPT (text length:', pdfText.length, ')');
    const parsedData = await parseWithGPT(pdfText, openaiApiKey);

    const itinerary = convertToItinerary(parsedData, pdfText, openaiApiKey);

    console.log('Successfully parsed PDF');

    return new Response(
      JSON.stringify({
        ...parsedData,
        itinerary
      }),
      {
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      }
    );
  } catch (error) {
    console.error("Error parsing PDF:", error);

    return new Response(
      JSON.stringify({ error: error.message }),
      {
        status: 500,
        headers: { ...corsHeaders, "Content-Type": "application/json" },
      }
    );
  }
});