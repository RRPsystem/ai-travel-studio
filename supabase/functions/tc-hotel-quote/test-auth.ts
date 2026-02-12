/**
 * Test script to debug Travel Compositor authentication
 * 
 * This helps identify which micrositeId format works for the booking API
 */

export async function testAuthentication(username: string, password: string, baseMicrositeId: string) {
  const TC_API_BASE = "https://online.travelcompositor.com/resources";
  
  // Try different micrositeId formats
  const variants = [
    baseMicrositeId,
    `${baseMicrositeId}.nl`,
    `www.${baseMicrositeId}.nl`,
    `${baseMicrositeId}.com`,
  ];

  const results = [];

  for (const msId of variants) {
    try {
      console.log(`\n[Test Auth] Testing micrositeId: "${msId}"`);
      
      // Step 1: Authenticate
      const authResp = await fetch(`${TC_API_BASE}/authentication/authenticate`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept-Encoding": "gzip" },
        body: JSON.stringify({ username, password, micrositeId: msId }),
      });

      const authText = await authResp.text();
      let authData: any = {};
      try {
        authData = JSON.parse(authText);
      } catch {
        authData = { rawResponse: authText.substring(0, 200) };
      }

      const result: any = {
        micrositeId: msId,
        authStatus: authResp.status,
        hasToken: !!authData.token,
        tokenExpiry: authData.expirationInSeconds,
      };

      // Step 2: If auth succeeded, try booking quote
      if (authResp.ok && authData.token) {
        console.log(`[Test Auth] ✓ Auth successful, testing booking quote...`);
        
        const quoteResp = await fetch(`${TC_API_BASE}/booking/accommodations/quote`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "auth-token": authData.token,
            "Accept-Encoding": "gzip"
          },
          body: JSON.stringify({
            checkIn: "2026-07-15",
            checkOut: "2026-07-18",
            distributions: [{ persons: [{ age: 30 }, { age: 30 }] }],
            language: "NL",
            sourceMarket: "NL",
            destinationId: "AMS",
            filter: { bestCombinations: true, maxCombinations: 3 }
          })
        });

        const quoteText = await quoteResp.text();
        result.quoteStatus = quoteResp.status;
        result.quoteSuccess = quoteResp.ok;
        
        if (!quoteResp.ok) {
          result.quoteError = quoteText.substring(0, 300);
        } else {
          try {
            const quoteData = JSON.parse(quoteText);
            result.quoteAccommodations = quoteData.accommodations?.length || 0;
          } catch {
            result.quoteError = "Invalid JSON response";
          }
        }
      } else {
        console.log(`[Test Auth] ✗ Auth failed: ${authResp.status}`);
        result.authError = authText.substring(0, 200);
      }

      results.push(result);
      
    } catch (error: any) {
      results.push({
        micrositeId: msId,
        error: error.message
      });
    }
  }

  return results;
}
