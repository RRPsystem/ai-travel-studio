import 'jsr:@supabase/functions-js/edge-runtime.d.ts';
import { createClient } from 'jsr:@supabase/supabase-js@2';

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
  'Access-Control-Allow-Headers': 'Content-Type, Authorization, X-Client-Info, Apikey',
};

interface TestResult {
  success: boolean;
  apiKeyFound: boolean;
  test: string;
  result?: any;
  error?: string;
  details?: any;
}

Deno.serve(async (req: Request) => {
  if (req.method === 'OPTIONS') {
    return new Response(null, {
      status: 200,
      headers: corsHeaders,
    });
  }

  try {
    const { test } = await req.json();

    // Initialize Supabase client
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!;
    const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!;
    const supabase = createClient(supabaseUrl, supabaseServiceKey);

    // Get Google Maps API key from database
    const { data: apiSettings, error: dbError } = await supabase
      .from('api_settings')
      .select('api_key, service_name')
      .eq('service_name', 'Google Maps API')
      .eq('is_active', true)
      .maybeSingle();

    if (dbError || !apiSettings?.api_key) {
      return new Response(
        JSON.stringify({
          success: false,
          apiKeyFound: false,
          test,
          error: 'Google Maps API key not found in database',
          details: dbError
        } as TestResult),
        { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

    const apiKey = apiSettings.api_key;
    console.log('✅ Google Maps API key found, length:', apiKey.length);

    let testUrl = '';
    let testDescription = '';

    // Different test types
    switch (test) {
      case 'places-search':
        testUrl = `https://maps.googleapis.com/maps/api/place/textsearch/json?query=Eiffel+Tower+Paris&key=${apiKey}&language=nl`;
        testDescription = 'Text Search API - Searching for Eiffel Tower';
        break;
      
      case 'places-nearby':
        // Amsterdam coordinates
        testUrl = `https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=52.3676,4.9041&radius=5000&type=tourist_attraction&key=${apiKey}&language=nl`;
        testDescription = 'Nearby Search API - Tourist attractions in Amsterdam';
        break;
      
      case 'geocoding':
        testUrl = `https://maps.googleapis.com/maps/api/geocode/json?address=Amsterdam,Netherlands&key=${apiKey}`;
        testDescription = 'Geocoding API - Geocoding Amsterdam';
        break;
      
      case 'directions':
        testUrl = `https://maps.googleapis.com/maps/api/directions/json?origin=Amsterdam&destination=Paris&key=${apiKey}&language=nl`;
        testDescription = 'Directions API - Route from Amsterdam to Paris';
        break;
      
      default:
        // Default: simple places search
        testUrl = `https://maps.googleapis.com/maps/api/place/textsearch/json?query=Amsterdam&key=${apiKey}&language=nl`;
        testDescription = 'Text Search API - Searching for Amsterdam';
    }

    console.log(`🧪 Testing: ${testDescription}`);
    console.log(`📍 URL: ${testUrl.replace(apiKey, 'API_KEY_HIDDEN')}`);

    const response = await fetch(testUrl);
    const data = await response.json();

    console.log('📊 Response status:', data.status);
    console.log('📊 Response:', JSON.stringify(data).substring(0, 500));

    if (data.status === 'OK') {
      return new Response(
        JSON.stringify({
          success: true,
          apiKeyFound: true,
          test: testDescription,
          result: {
            status: data.status,
            resultsCount: data.results?.length || 0,
            firstResult: data.results?.[0] || data.routes?.[0] || null,
            summary: data.results ? `Found ${data.results.length} results` : 
                     data.routes ? `Found ${data.routes.length} routes` : 'Success'
          }
        } as TestResult),
        { status: 200, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    } else {
      return new Response(
        JSON.stringify({
          success: false,
          apiKeyFound: true,
          test: testDescription,
          error: `API returned status: ${data.status}`,
          details: {
            status: data.status,
            error_message: data.error_message,
            fullResponse: data
          }
        } as TestResult),
        { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      );
    }

  } catch (error) {
    console.error('❌ Test error:', error);
    return new Response(
      JSON.stringify({
        success: false,
        apiKeyFound: false,
        test: 'unknown',
        error: error instanceof Error ? error.message : 'Unknown error',
        details: error
      } as TestResult),
      { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
    );
  }
});
