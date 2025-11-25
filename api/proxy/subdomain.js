export default async function handler(req, res) {
  try {
    const host = req.headers.host || '';
    const pathname = req.url || '/';

    console.log('[PROXY] Request:', { host, pathname });

    // Extract subdomain
    const subdomain = host.split('.')[0];

    // Build Supabase Edge Function URL
    const supabaseUrl = `https://huaaogdxxdcakxryecnw.supabase.co/functions/v1/website-viewer${pathname}`;
    const targetUrl = `${supabaseUrl}?subdomain=${encodeURIComponent(subdomain)}`;

    console.log('[PROXY] Fetching:', targetUrl);

    // Fetch HTML from Supabase Edge Function
    const response = await fetch(targetUrl, {
      method: req.method,
      headers: {
        'user-agent': req.headers['user-agent'] || 'Mozilla/5.0',
      },
    });

    const html = await response.text();

    console.log('[PROXY] Response status:', response.status);

    // Send response WITHOUT any CSP header (completely disable CSP)
    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    res.setHeader('Cache-Control', 'public, max-age=60, s-maxage=300');

    // Explicitly remove any CSP headers
    res.removeHeader('Content-Security-Policy');
    res.removeHeader('X-Content-Security-Policy');
    res.removeHeader('X-WebKit-CSP');

    res.status(response.status).send(html);
  } catch (error) {
    console.error('[PROXY] Error:', error);

    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    res.removeHeader('Content-Security-Policy');

    res.status(500).send(`
      <!DOCTYPE html>
      <html>
        <head><title>Proxy Error</title></head>
        <body style="font-family: system-ui; padding: 2rem; text-align: center;">
          <h1>Error loading website</h1>
          <p>${error.message || 'Unknown error'}</p>
          <p style="color: #666; font-size: 0.9rem;">Host: ${req.headers.host}</p>
        </body>
      </html>
    `);
  }
}
