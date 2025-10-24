import "jsr:@supabase/functions-js/edge-runtime.d.ts";

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
    const { accountSid, authToken, whatsappNumber } = await req.json();

    if (!accountSid || !authToken) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Account SID en Auth Token zijn verplicht'
        }),
        {
          status: 400,
          headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        }
      );
    }

    const twilioUrl = `https://api.twilio.com/2010-04-01/Accounts/${accountSid}/Messages.json`;

    const response = await fetch(twilioUrl, {
      method: 'GET',
      headers: {
        'Authorization': 'Basic ' + btoa(`${accountSid}:${authToken}`),
      },
    });

    if (response.ok) {
      return new Response(
        JSON.stringify({
          success: true,
          message: '\u2705 Verbinding succesvol! Twilio credentials werken correct.',
          details: `Account ${accountSid} is geldig${whatsappNumber ? ` met nummer ${whatsappNumber}` : ''}`
        }),
        {
          headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        }
      );
    } else {
      const errorText = await response.text();
      let errorMessage = 'Onbekende fout';

      try {
        const errorJson = JSON.parse(errorText);
        errorMessage = errorJson.message || errorText;
      } catch {
        errorMessage = errorText;
      }

      return new Response(
        JSON.stringify({
          success: false,
          message: `\u274c Verbinding mislukt: ${response.status}`,
          details: errorMessage
        }),
        {
          status: 200,
          headers: { ...corsHeaders, 'Content-Type': 'application/json' },
        }
      );
    }

  } catch (error) {
    console.error('Error testing Twilio:', error);
    return new Response(
      JSON.stringify({
        success: false,
        message: `\u274c Fout bij testen: ${error.message}`
      }),
      {
        status: 500,
        headers: { ...corsHeaders, 'Content-Type': 'application/json' },
      }
    );
  }
});