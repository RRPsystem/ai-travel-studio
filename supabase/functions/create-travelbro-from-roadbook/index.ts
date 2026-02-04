import { serve } from 'https://deno.land/std@0.168.0/http/server.ts'
import { createClient } from 'https://esm.sh/@supabase/supabase-js@2'

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Headers': 'authorization, x-client-info, apikey, content-type',
}

serve(async (req) => {
  // Handle CORS preflight
  if (req.method === 'OPTIONS') {
    return new Response('ok', { headers: corsHeaders })
  }

  try {
    const supabaseUrl = Deno.env.get('SUPABASE_URL')!
    const supabaseServiceKey = Deno.env.get('SUPABASE_SERVICE_ROLE_KEY')!
    const supabase = createClient(supabaseUrl, supabaseServiceKey)

    const { page_id, brand_id } = await req.json()

    if (!page_id) {
      return new Response(
        JSON.stringify({ success: false, error: 'page_id is required' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      )
    }

    if (!brand_id) {
      return new Response(
        JSON.stringify({ success: false, error: 'brand_id is required' }),
        { status: 400, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      )
    }

    console.log('[create-travelbro-from-roadbook] Creating TravelBro for page:', page_id)

    // 1. Get the page/roadbook data
    const { data: page, error: pageError } = await supabase
      .from('pages')
      .select('*')
      .eq('id', page_id)
      .single()

    if (pageError || !page) {
      console.error('[create-travelbro-from-roadbook] Page not found:', pageError)
      return new Response(
        JSON.stringify({ success: false, error: 'Page not found' }),
        { status: 404, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      )
    }

    // 2. Generate a random share token
    const share_token = crypto.randomUUID().replace(/-/g, '')

    // 3. Create the travel_trip record
    const { data: newTrip, error: tripError } = await supabase
      .from('travel_trips')
      .insert({
        name: page.title || 'Roadbook Reis',
        brand_id: brand_id,
        page_id: page_id,
        share_token: share_token,
        parsed_data: page.content || {},
        status: 'active',
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString()
      })
      .select()
      .single()

    if (tripError) {
      console.error('[create-travelbro-from-roadbook] Error creating trip:', tripError)
      return new Response(
        JSON.stringify({ success: false, error: tripError.message }),
        { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
      )
    }

    console.log('[create-travelbro-from-roadbook] Trip created:', newTrip.id)

    // 4. Update the page with the travelBroUrl
    const travelBroUrl = `https://www.travelbro.nl/${share_token}`
    
    const updatedContent = {
      ...(page.content || {}),
      travelBroUrl: travelBroUrl,
      tripId: newTrip.id,
      shareToken: share_token
    }

    const { error: updateError } = await supabase
      .from('pages')
      .update({ content: updatedContent })
      .eq('id', page_id)

    if (updateError) {
      console.error('[create-travelbro-from-roadbook] Error updating page:', updateError)
      // Don't fail - the trip was created successfully
    }

    console.log('[create-travelbro-from-roadbook] Success! TravelBro URL:', travelBroUrl)

    return new Response(
      JSON.stringify({
        success: true,
        share_token: share_token,
        url: travelBroUrl,
        trip_id: newTrip.id
      }),
      { headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
    )

  } catch (error) {
    console.error('[create-travelbro-from-roadbook] Error:', error)
    return new Response(
      JSON.stringify({ success: false, error: error.message }),
      { status: 500, headers: { ...corsHeaders, 'Content-Type': 'application/json' } }
    )
  }
})
