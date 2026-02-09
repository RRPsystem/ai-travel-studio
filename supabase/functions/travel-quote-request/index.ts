import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "jsr:@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 200, headers: corsHeaders });
  }

  try {
    const body = await req.json();
    const {
      brand_id,
      travel_id,
      travel_title,
      travel_url,
      customer_name,
      customer_email,
      customer_phone,
      departure_date,
      number_of_persons,
      request_type,
      message,
      source_url,
    } = body;

    // Validate required fields
    if (!brand_id) {
      return new Response(
        JSON.stringify({ error: "brand_id is verplicht" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }
    if (!customer_name || !customer_email) {
      return new Response(
        JSON.stringify({ error: "Naam en e-mailadres zijn verplicht" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Basic email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(customer_email)) {
      return new Response(
        JSON.stringify({ error: "Ongeldig e-mailadres" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Get source IP from headers
    const sourceIp = req.headers.get("x-forwarded-for") || req.headers.get("x-real-ip") || "";

    // Use service role to insert (public endpoint, no auth required)
    const supabaseUrl = Deno.env.get("SUPABASE_URL")!;
    const supabaseServiceKey = Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!;
    const supabase = createClient(supabaseUrl, supabaseServiceKey);

    const { data, error } = await supabase
      .from("travel_quote_requests")
      .insert({
        brand_id,
        travel_id: travel_id || null,
        travel_title: travel_title || null,
        travel_url: travel_url || null,
        customer_name,
        customer_email,
        customer_phone: customer_phone || null,
        departure_date: departure_date || null,
        number_of_persons: number_of_persons ? parseInt(number_of_persons) : 2,
        request_type: request_type || "quote",
        message: message || null,
        source_url: source_url || null,
        source_ip: sourceIp,
        status: "new",
      })
      .select("id")
      .single();

    if (error) {
      console.error("[Quote Request] Insert error:", error);
      return new Response(
        JSON.stringify({ error: "Kon aanvraag niet opslaan. Probeer het later opnieuw." }),
        { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    console.log(`[Quote Request] Saved: ${data.id} for brand ${brand_id}, travel: ${travel_title || travel_id}`);

    return new Response(
      JSON.stringify({
        success: true,
        message: "Bedankt voor je aanvraag! We nemen zo snel mogelijk contact met je op.",
        id: data.id,
      }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );

  } catch (error: any) {
    console.error("[Quote Request] Error:", error);
    return new Response(
      JSON.stringify({ error: error.message || "Er is een fout opgetreden" }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
