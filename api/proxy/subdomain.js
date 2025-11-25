module.exports = async (req, res) => {
  console.log('[PROXY] Test handler - Host:', req.headers.host, 'Path:', req.url);

  res.setHeader('Content-Type', 'text/html; charset=utf-8');
  res.setHeader('Cache-Control', 'no-cache');
  res.setHeader(
    'Content-Security-Policy',
    "default-src * 'unsafe-inline' 'unsafe-eval' data: blob:; script-src * 'unsafe-inline' 'unsafe-eval'; style-src * 'unsafe-inline'; img-src * data: blob:; font-src * data:; connect-src *;"
  );

  const subdomain = req.headers.host?.split('.')[0] || 'unknown';

  res.status(200).send(`
    <!DOCTYPE html>
    <html lang="nl">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Proxy Test</title>
      <style>
        body {
          margin: 0;
          padding: 0;
          font-family: system-ui, -apple-system, sans-serif;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          min-height: 100vh;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .container {
          background: white;
          padding: 3rem;
          border-radius: 1rem;
          box-shadow: 0 20px 60px rgba(0,0,0,0.3);
          max-width: 600px;
          text-align: center;
        }
        h1 { color: #667eea; margin: 0 0 1rem 0; font-size: 2.5rem; }
        .success { color: #10b981; font-size: 4rem; margin: 0; }
        p { color: #666; line-height: 1.8; margin: 1rem 0; }
        .info { background: #f3f4f6; padding: 1rem; border-radius: 0.5rem; font-family: monospace; font-size: 0.9rem; }
        .test { margin-top: 2rem; padding: 1rem; background: #fef3c7; border-radius: 0.5rem; }
      </style>
    </head>
    <body>
      <div class="container">
        <p class="success">✅</p>
        <h1>Proxy werkt!</h1>
        <p>Als je dit ziet, komt de HTML via de Vercel proxy en wordt de relaxed CSP toegepast.</p>

        <div class="info">
          <strong>Subdomain:</strong> ${subdomain}<br>
          <strong>Host:</strong> ${req.headers.host}<br>
          <strong>Path:</strong> ${req.url}
        </div>

        <div class="test">
          <strong>CSP Test:</strong> Open de Console (F12) en check of er <strong>geen CSP errors</strong> zijn.
          <script>
            console.log('✅ Inline script werkt - eval test volgt...');
            try {
              eval('console.log("✅ eval() werkt - CSP is relaxed!")');
            } catch(e) {
              console.error('❌ eval() geblokkeerd - CSP is NOG STEEDS strict!');
            }
          </script>
        </div>
      </div>
    </body>
    </html>
  `);
};
