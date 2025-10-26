import { createClient } from 'npm:@supabase/supabase-js@2';

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization, X-Client-Info, Apikey',
};

interface GenerateContentRequest {
  contentType: string;
  prompt: string;
  writingStyle?: string;
  additionalContext?: string;
  options?: {
    vacationType?: string;
    routeType?: string;
    days?: string;
    destination?: string;
    temperature?: number;
    maxTokens?: number;
    model?: string;
    systemPrompt?: string;
  };
}

Deno.serve(async (req: Request) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, {
      status: 200,
      headers: corsHeaders,
    });
  }

  try {
    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    // Get OpenAI API key from database
    const { data: settings, error: settingsError } = await supabaseClient
      .from('api_settings')
      .select('api_key')
      .eq('provider', 'OpenAI')
      .maybeSingle();

    if (settingsError) {
      console.error('Error fetching API settings:', settingsError);
      throw new Error('Failed to load API settings');
    }

    if (!settings?.api_key || !settings.api_key.startsWith('sk-')) {
      throw new Error('OpenAI API key not configured');
    }

    const openaiApiKey = settings.api_key;

    // Parse request body
    const body: GenerateContentRequest = await req.json();
    const { contentType, prompt, writingStyle = 'professional', additionalContext = '', options = {} } = body;

    // Build system prompt
    const getRouteInstruction = (routeType: string) => {
      switch (routeType) {
        case 'snelle-route': return 'Focus op de snelste route met minimale reistijd.';
        case 'toeristische-route': return 'Kies de mooiste route met bezienswaardigheden onderweg.';
        case 'binnendoor-weggetjes': return 'Gebruik kleinere wegen en ontdek verborgen parels.';
        case 'gemengd': return 'Combineer snelheid met mooie bezienswaardigheden.';
        default: return '';
      }
    };

    const getSystemPrompt = (contentType: string) => {
      const basePrompts: Record<string, string> = {
        destination: `Je bent een professionele reisschrijver die boeiende bestemmingsteksten schrijft over {DESTINATION}. Schrijf in {WRITING_STYLE} stijl voor {VACATION_TYPE} reizigers. Gebruik actuele informatie en maak de tekst aantrekkelijk.`,
        route: `Je bent een routeplanner die gedetailleerde routebeschrijvingen maakt. {ROUTE_TYPE_INSTRUCTION} Geef praktische informatie over de route, bezienswaardigheden onderweg, en reistips. Schrijf in {WRITING_STYLE} stijl voor {VACATION_TYPE} reizigers.`,
        planning: `Je bent een reisplanner die {DAYS} dagplanningen maakt voor {DESTINATION}. Geef een praktische planning met tijden, activiteiten, en tips. Schrijf in {WRITING_STYLE} stijl voor {VACATION_TYPE} reizigers.`,
        hotel: `Je bent een hotelexpert die hotelzoekresultaten presenteert voor {VACATION_TYPE} reizigers. Geef gedetailleerde informatie over hotels, voorzieningen, en boekingsadvies. Schrijf in {WRITING_STYLE} stijl.`,
        image: `Je bent een AI die afbeeldingsbeschrijvingen genereert voor DALL-E. Maak een gedetailleerde, visuele beschrijving voor een {VACATION_TYPE} reisafbeelding in {WRITING_STYLE} stijl.`
      };

      let systemPrompt = options.systemPrompt || basePrompts[contentType] || basePrompts.destination;

      // Replace variables
      systemPrompt = systemPrompt
        .replace(/{WRITING_STYLE}/g, writingStyle)
        .replace(/{VACATION_TYPE}/g, options.vacationType || 'algemene')
        .replace(/{ROUTE_TYPE}/g, options.routeType || '')
        .replace(/{ROUTE_TYPE_INSTRUCTION}/g, getRouteInstruction(options.routeType || ''))
        .replace(/{DAYS}/g, options.days || '')
        .replace(/{DESTINATION}/g, options.destination || '');

      return systemPrompt;
    };

    const systemPrompt = getSystemPrompt(contentType);
    const userPrompt = `${prompt}${additionalContext ? `\n\nExtra context: ${additionalContext}` : ''}`;

    // Call OpenAI API
    const openaiResponse = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${openaiApiKey}`,
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        model: options.model || 'gpt-3.5-turbo',
        messages: [
          { role: 'system', content: systemPrompt },
          { role: 'user', content: userPrompt }
        ],
        max_tokens: options.maxTokens || 1500,
        temperature: options.temperature || 0.7,
      }),
    });

    if (!openaiResponse.ok) {
      const errorText = await openaiResponse.text();
      console.error('OpenAI API Error:', errorText);
      throw new Error(`OpenAI API error: ${openaiResponse.status}`);
    }

    const data = await openaiResponse.json();
    const content = data.choices[0]?.message?.content || 'Geen response ontvangen van OpenAI';

    return new Response(
      JSON.stringify({ content }),
      {
        headers: {
          ...corsHeaders,
          'Content-Type': 'application/json',
        },
      }
    );

  } catch (error) {
    console.error('Error in generate-content function:', error);
    return new Response(
      JSON.stringify({ error: error instanceof Error ? error.message : 'Unknown error' }),
      {
        status: 500,
        headers: {
          ...corsHeaders,
          'Content-Type': 'application/json',
        },
      }
    );
  }
});
