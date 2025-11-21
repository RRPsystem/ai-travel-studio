import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from 'jsr:@supabase/supabase-js@2';

Deno.serve(async (req: Request) => {
  try {
    const url = new URL(req.url);
    const websiteId = url.searchParams.get('website_id');
    const pageIndex = parseInt(url.searchParams.get('page') || '0');

    if (!websiteId) {
      throw new Error('website_id is required');
    }

    const supabaseClient = createClient(
      Deno.env.get('SUPABASE_URL') ?? '',
      Deno.env.get('SUPABASE_SERVICE_ROLE_KEY') ?? ''
    );

    const { data: website, error: websiteError } = await supabaseClient
      .from('websites')
      .select('*')
      .eq('id', websiteId)
      .single();

    if (websiteError) throw websiteError;

    const pages = website.pages || [];
    const page = pages[pageIndex];

    if (!page || !page.html) {
      throw new Error('Page not found');
    }

    // Return the HTML directly without any CSP restrictions
    return new Response(page.html, {
      status: 200,
      headers: {
        'Content-Type': 'text/html; charset=utf-8',
        'X-Frame-Options': 'SAMEORIGIN',
      },
    });
  } catch (error) {
    console.error('Error serving WordPress preview:', error);
    return new Response(
      `<html><body><h1>Error</h1><p>${error instanceof Error ? error.message : 'Unknown error'}</p></body></html>`,
      {
        status: 400,
        headers: {
          'Content-Type': 'text/html; charset=utf-8',
        },
      }
    );
  }
});