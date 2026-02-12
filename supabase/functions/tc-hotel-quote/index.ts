import "jsr:@supabase/functions-js/edge-runtime.d.ts";
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

const TC_API_BASE = "https://online.travelcompositor.com/resources";

// Token cache per micrositeId
const tokenCache: Record<string, { token: string; expiresAt: number }> = {};

// Cache DB credentials in memory
let dbCredentialsCache: Array<{ microsite_id: string; username: string; password: string; name: string }> | null = null;
let dbCredentialsCacheTime = 0;
const DB_CACHE_TTL = 300_000; // 5 minutes

function getServiceClient() {
  return createClient(Deno.env.get("SUPABASE_URL")!, Deno.env.get("SUPABASE_SERVICE_ROLE_KEY")!);
}

async function loadDbCredentials(): Promise<Array<{ microsite_id: string; username: string; password: string; name: string }>> {
  if (dbCredentialsCache && Date.now() - dbCredentialsCacheTime < DB_CACHE_TTL) {
    return dbCredentialsCache;
  }
  try {
    const sb = getServiceClient();
    
    // Load from tc_microsites table (Operator Dashboard → API & Integraties → TC Credentials)
    const { data, error } = await sb
      .from("tc_microsites")
      .select("microsite_id, username, password, name")
      .eq("is_active", true);
    
    if (error) { 
      console.error("[TC Hotel Quote] Error loading tc_microsites:", error.message); 
      return dbCredentialsCache || []; 
    }
    
    dbCredentialsCache = data || [];
    dbCredentialsCacheTime = Date.now();
    console.log(`[TC Hotel Quote] Loaded ${dbCredentialsCache.length} microsites from tc_microsites table`);
    return dbCredentialsCache;
  } catch (err: any) {
    console.error("[TC Hotel Quote] Failed to load credentials:", err.message);
    return dbCredentialsCache || [];
  }
}

async function getCredentialsForMicrosite(micrositeId: string): Promise<{ username: string; password: string }> {
  // 1. Database first
  const dbCreds = await loadDbCredentials();
  const dbMatch = dbCreds.find(c => c.microsite_id === micrositeId);
  if (dbMatch) {
    console.log(`[TC Hotel Quote] Found credentials in database for: ${micrositeId} (${dbMatch.name})`);
    return { username: dbMatch.username, password: dbMatch.password };
  }
  
  // 2. Env vars fallback
  for (let i = 1; i <= 10; i++) {
    const msId = Deno.env.get(`TRAVEL_COMPOSITOR_MICROSITE_ID_${i}`);
    if (msId && msId === micrositeId) {
      const username = Deno.env.get(`TRAVEL_COMPOSITOR_USERNAME_${i}`);
      const password = Deno.env.get(`TRAVEL_COMPOSITOR_PASSWORD_${i}`);
      if (username && password) {
        console.log(`[TC Hotel Quote] Found credentials in env set ${i} for: ${micrositeId}`);
        return { username, password };
      }
    }
  }
  
  const fallbackUser = Deno.env.get("TRAVEL_COMPOSITOR_USERNAME") || Deno.env.get("TC_API_USERNAME");
  const fallbackPass = Deno.env.get("TRAVEL_COMPOSITOR_PASSWORD") || Deno.env.get("TC_API_PASSWORD");
  if (fallbackUser && fallbackPass) {
    console.log(`[TC Hotel Quote] Using fallback credentials for: ${micrositeId}`);
    return { username: fallbackUser, password: fallbackPass };
  }
  
  throw new Error(`Geen TC credentials voor microsite "${micrositeId}". Voeg toe in Brand Instellingen > Travel Compositor.`);
}

async function getTcToken(micrositeId: string): Promise<string> {
  const cached = tokenCache[micrositeId];
  if (cached && Date.now() / 1000 < cached.expiresAt - 60) {
    console.log(`[TC Hotel Quote] Using cached token for ${micrositeId}`);
    return cached.token;
  }

  const { username, password } = await getCredentialsForMicrosite(micrositeId);
  
  console.log(`[TC Hotel Quote] Authenticating for microsite: ${micrositeId}`);
  const authResponse = await fetch(`${TC_API_BASE}/authentication/authenticate`, {
    method: "POST",
    headers: { 
      "Content-Type": "application/json",
      "Accept-Encoding": "gzip"
    },
    body: JSON.stringify({ username, password, micrositeId }),
  });

  if (!authResponse.ok) {
    const errorText = await authResponse.text();
    console.error(`[TC Hotel Quote] Auth failed (${authResponse.status}):`, errorText.substring(0, 500));
    throw new Error(`TC authenticatie mislukt (${authResponse.status})`);
  }
  
  const authResult = await authResponse.json();
  if (!authResult.token) {
    console.error(`[TC Hotel Quote] Auth response has no token:`, authResult);
    throw new Error("TC auth returned no token");
  }

  console.log(`[TC Hotel Quote] Authentication successful for ${micrositeId}, token expires in ${authResult.expirationInSeconds}s`);
  
  tokenCache[micrositeId] = {
    token: authResult.token,
    expiresAt: Date.now() / 1000 + (authResult.expirationInSeconds || 7200),
  };
  
  return authResult.token;
}

async function getConfiguredMicrosites(): Promise<string[]> {
  const result: string[] = [];
  const dbCreds = await loadDbCredentials();
  dbCreds.forEach(c => { if (!result.includes(c.microsite_id)) result.push(c.microsite_id); });
  for (let i = 1; i <= 10; i++) {
    const msId = Deno.env.get(`TRAVEL_COMPOSITOR_MICROSITE_ID_${i}`);
    if (msId && !result.includes(msId)) result.push(msId);
  }
  const fallbackMs = Deno.env.get("TRAVEL_COMPOSITOR_MICROSITE_ID");
  if (fallbackMs && !result.includes(fallbackMs)) result.push(fallbackMs);
  return result;
}

Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 200, headers: corsHeaders });
  }

  try {
    const body = await req.json();
    const { 
      micrositeId, 
      checkIn, 
      checkOut, 
      adults = 2, 
      children = 0,
      childAges = [],
      destination,
      accommodationCodes,
      bestCombinations = true,
      maxCombinations = 5,
      includeOnRequestOptions = true
    } = body;

    // Validate required fields
    if (!checkIn || !checkOut) {
      return new Response(
        JSON.stringify({ error: "checkIn en checkOut zijn verplicht" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    if (!destination && !accommodationCodes) {
      return new Response(
        JSON.stringify({ error: "destination of accommodationCodes is verplicht" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Get microsite
    const microsite = micrositeId || (await getConfiguredMicrosites())[0];
    if (!microsite) {
      return new Response(
        JSON.stringify({ error: "Geen microsite geconfigureerd" }),
        { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    console.log(`[TC Hotel Quote] Request for microsite: ${microsite}, checkIn: ${checkIn}, checkOut: ${checkOut}, adults: ${adults}, children: ${children}`);

    // Get authentication token
    const token = await getTcToken(microsite);

    // Build distributions (rooms with persons)
    const distributions: any[] = [];
    const persons: any[] = [];
    
    // Add adults
    for (let i = 0; i < adults; i++) {
      persons.push({ age: 30 });
    }
    
    // Add children
    if (children > 0 && childAges.length > 0) {
      for (let i = 0; i < Math.min(children, childAges.length); i++) {
        persons.push({ age: childAges[i] });
      }
    }
    
    // Put all persons in one distribution (room)
    distributions.push({ persons });

    // Build quote request body according to TC API spec
    // IMPORTANT: Match EXACT format from Postman docs
    const quoteBody: any = {
      checkIn,
      checkOut,
      distributions,
      language: "EN", // Use EN as in Postman example
      sourceMarket: "ES", // Use ES as in Postman example
      filter: {
        bestCombinations,
        maxCombinations,
        includeOnRequestOptions
      },
      timeout: 5000 // Add timeout as in Postman example
    };

    // Add destination OR accommodation codes
    if (accommodationCodes && Array.isArray(accommodationCodes) && accommodationCodes.length > 0) {
      quoteBody.accommodations = accommodationCodes;
      console.log(`[TC Hotel Quote] Searching ${accommodationCodes.length} specific hotels`);
    } else if (destination) {
      quoteBody.destinationId = destination;
      console.log(`[TC Hotel Quote] Searching by destination: ${destination}`);
    }

    console.log(`[TC Hotel Quote] Request body:`, JSON.stringify(quoteBody, null, 2));
    console.log(`[TC Hotel Quote] Using microsite: "${microsite}"`);
    console.log(`[TC Hotel Quote] Token length: ${token.length}`);

    // Call TC API - EXACT format from Postman
    const quoteResponse = await fetch(`${TC_API_BASE}/booking/accommodations/quote`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "auth-token": token,
        "Accept-Encoding": "gzip"
      },
      body: JSON.stringify(quoteBody)
    });

    const responseText = await quoteResponse.text();
    
    if (!quoteResponse.ok) {
      console.error(`[TC Hotel Quote] Quote failed (${quoteResponse.status}):`, responseText.substring(0, 1000));
      
      // Parse error message if possible
      let errorMessage = `Hotel zoeken mislukt (${quoteResponse.status})`;
      try {
        const errorJson = JSON.parse(responseText);
        if (errorJson.message) errorMessage = errorJson.message;
        if (errorJson.error) errorMessage = errorJson.error;
      } catch {
        // Not JSON, use status text
        if (quoteResponse.status === 401) {
          errorMessage = "Geen toegang tot hotel booking API. Controleer je Travel Compositor credentials.";
        } else if (quoteResponse.status === 400) {
          errorMessage = "Ongeldige zoekopdracht. Controleer de parameters.";
        }
      }
      
      return new Response(
        JSON.stringify({ 
          error: errorMessage,
          status: quoteResponse.status,
          details: responseText.substring(0, 500)
        }),
        { status: quoteResponse.status, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    // Parse successful response
    let quoteResult;
    try {
      quoteResult = JSON.parse(responseText);
    } catch (e) {
      console.error("[TC Hotel Quote] Failed to parse response:", e);
      return new Response(
        JSON.stringify({ error: "Invalid JSON response from TC API" }),
        { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
      );
    }

    console.log(`[TC Hotel Quote] Success! Got ${quoteResult.accommodations?.length || 0} accommodations`);

    // Map results to our format
    const accommodations = (quoteResult.accommodations || []).map((acc: any) => {
      const firstCombination = acc.combinations?.[0];
      
      return {
        type: "hotel",
        id: acc.code,
        name: acc.name || "Onbekend hotel",
        stars: acc.category?.code ? parseInt(acc.category.code.replace(/\D/g, '')) : 0,
        location: acc.destination?.name || "",
        country: acc.destination?.country || "",
        geolocation: acc.geolocation,
        combinations: (acc.combinations || []).map((combo: any) => ({
          combinationKey: combo.combinationKey,
          rooms: combo.rooms || [],
          mealPlan: combo.mealPlan,
          onRequest: combo.onRequest || false,
          price: combo.price?.amount || 0,
          currency: combo.price?.currency || "EUR",
          provider: combo.provider || "",
          cancellationPolicies: combo.cancellationPolicies || [],
          remarks: combo.remarks || [],
          currentCancellationType: combo.currentCancellationType
        })),
        // Quick access to first combination
        price: firstCombination?.price?.amount || 0,
        currency: firstCombination?.price?.currency || "EUR",
        mealPlan: firstCombination?.mealPlan?.description || "",
        roomType: firstCombination?.rooms?.[0]?.description || "",
        provider: firstCombination?.provider || "",
        onRequest: firstCombination?.onRequest || false
      };
    });

    return new Response(
      JSON.stringify({
        success: true,
        microsite,
        checkIn,
        checkOut,
        count: accommodations.length,
        accommodations,
        auditData: quoteResult.auditData
      }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );

  } catch (error: any) {
    console.error("[TC Hotel Quote] Error:", error);
    return new Response(
      JSON.stringify({ 
        error: error.message || "Hotel quote failed",
        stack: error.stack
      }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
