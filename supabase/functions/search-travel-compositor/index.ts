import "jsr:@supabase/functions-js/edge-runtime.d.ts";

const corsHeaders = {
  "Access-Control-Allow-Origin": "*",
  "Access-Control-Allow-Methods": "POST, OPTIONS",
  "Access-Control-Allow-Headers": "authorization, x-client-info, apikey, content-type",
};

const TC_API_BASE = "https://online.travelcompositor.com/resources";

// Token cache per micrositeId
const tokenCache: Record<string, { token: string; expiresAt: number }> = {};

function getCredentialsForMicrosite(micrositeId: string): { username: string; password: string } {
  for (let i = 1; i <= 10; i++) {
    const msId = Deno.env.get(`TRAVEL_COMPOSITOR_MICROSITE_ID_${i}`);
    if (msId && msId === micrositeId) {
      const username = Deno.env.get(`TRAVEL_COMPOSITOR_USERNAME_${i}`);
      const password = Deno.env.get(`TRAVEL_COMPOSITOR_PASSWORD_${i}`);
      if (username && password) return { username, password };
    }
  }
  // Fallback: try TRAVEL_COMPOSITOR_USERNAME / PASSWORD (without number)
  const fallbackUser = Deno.env.get("TRAVEL_COMPOSITOR_USERNAME") || Deno.env.get("TC_API_USERNAME");
  const fallbackPass = Deno.env.get("TRAVEL_COMPOSITOR_PASSWORD") || Deno.env.get("TC_API_PASSWORD");
  if (fallbackUser && fallbackPass) {
    console.log(`[TC Auth] Using fallback credentials for microsite: ${micrositeId}`);
    return { username: fallbackUser, password: fallbackPass };
  }
  throw new Error(`Geen TC credentials voor microsite "${micrositeId}"`);
}

function getConfiguredMicrosites(): string[] {
  const result: string[] = [];
  for (let i = 1; i <= 10; i++) {
    const msId = Deno.env.get(`TRAVEL_COMPOSITOR_MICROSITE_ID_${i}`);
    if (msId) result.push(msId);
  }
  // Also check fallback (without number)
  const fallbackMs = Deno.env.get("TRAVEL_COMPOSITOR_MICROSITE_ID");
  if (fallbackMs && !result.includes(fallbackMs)) result.push(fallbackMs);
  return result;
}

async function getTcToken(micrositeId: string): Promise<string> {
  const cached = tokenCache[micrositeId];
  if (cached && Date.now() / 1000 < cached.expiresAt - 60) return cached.token;

  const { username, password } = getCredentialsForMicrosite(micrositeId);
  const authResponse = await fetch(`${TC_API_BASE}/authentication/authenticate`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, password, micrositeId }),
  });

  if (!authResponse.ok) throw new Error(`TC auth failed (${authResponse.status})`);
  const authResult = await authResponse.json();
  if (!authResult.token) throw new Error("TC auth returned no token");

  tokenCache[micrositeId] = {
    token: authResult.token,
    expiresAt: Date.now() / 1000 + (authResult.expirationInSeconds || 3600),
  };
  return authResult.token;
}

// ============================================================
// ACCOMMODATION SEARCH
// ============================================================
// Resolve a free-text destination query to a TC destination ID
async function resolveDestinationId(token: string, micrositeId: string, query: string): Promise<string | null> {
  try {
    const response = await fetch(`${TC_API_BASE}/destination/${micrositeId}?lang=NL`, {
      headers: { "auth-token": token },
    });
    if (!response.ok) return null;
    const result = await response.json();
    const destinations = result.destinations || result.destination || [];
    
    const q = query.toLowerCase();
    // Try exact match first, then partial match
    const exact = destinations.find((d: any) => d.name?.toLowerCase() === q);
    if (exact) return exact.id || exact.code;
    
    const partial = destinations.find((d: any) => 
      d.name?.toLowerCase().includes(q) || q.includes(d.name?.toLowerCase())
    );
    if (partial) return partial.id || partial.code;
    
    // Try country match
    const countryMatch = destinations.find((d: any) => 
      d.country?.toLowerCase().includes(q) || d.countryName?.toLowerCase().includes(q)
    );
    if (countryMatch) return countryMatch.id || countryMatch.code;
    
    console.log(`[TC Search] No destination match for "${query}" among ${destinations.length} destinations`);
    return null;
  } catch (e) {
    console.warn(`[TC Search] Destination resolve failed:`, e);
    return null;
  }
}

async function searchAccommodations(micrositeId: string, params: any) {
  const { destination, checkIn, checkOut, adults, children, childAges, rooms } = params;

  if (!destination || !checkIn || !checkOut) {
    console.log(`[TC Search] Missing required params: dest=${destination}, checkIn=${checkIn}, checkOut=${checkOut}`);
    return [];
  }

  // Build distributions array (rooms with persons) — exact Postman format
  const numRooms = rooms || 1;
  const adultsPerRoom = Math.max(1, Math.floor((adults || 2) / numRooms));
  const distributions: any[] = [];
  for (let r = 0; r < numRooms; r++) {
    const persons: any[] = [];
    for (let a = 0; a < adultsPerRoom; a++) persons.push({ age: 30 });
    if (children && childAges && r === 0) {
      for (const age of childAges) persons.push({ age });
    }
    distributions.push({ persons });
  }

  // Exact format from Postman collection "Quote by destination"
  const quoteBody = {
    checkIn,
    checkOut,
    distributions,
    language: "NL",
    sourceMarket: "NL",
    filter: {
      bestCombinations: true,
    },
    timeout: 20000,
    destinationId: destination,
  };

  // Try all microsites
  const microsites = micrositeId ? [micrositeId] : getConfiguredMicrosites();
  console.log(`[TC Search] Quote by destination "${destination}" | ${checkIn} → ${checkOut} | ${adults || 2} adults | ${numRooms} room(s)`);
  console.log(`[TC Search] Request body: ${JSON.stringify(quoteBody)}`);
  console.log(`[TC Search] Trying microsites: ${microsites.join(", ")}`);

  for (const ms of microsites) {
    try {
      const token = await getTcToken(ms);

      const response = await fetch(`${TC_API_BASE}/booking/accommodations/quote`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "auth-token": token,
          "Accept-Encoding": "gzip",
        },
        body: JSON.stringify(quoteBody),
      });

      const responseText = await response.text();
      console.log(`[TC Search] ${ms} response (${response.status}): ${responseText.substring(0, 800)}`);

      if (!response.ok) {
        console.warn(`[TC Search] ${ms} quote failed: ${response.status}`);
        continue;
      }

      let result;
      try { result = JSON.parse(responseText); } catch { continue; }

      const accommodations = result.accommodations || [];
      console.log(`[TC Search] ${ms}: ${accommodations.length} hotels found`);

      if (accommodations.length > 0) {
        // Fetch datasheets for photos/descriptions
        const accIds = accommodations.slice(0, 20).map((a: any) => String(a.code || a.accommodationId)).filter(Boolean);
        let datasheets: Record<string, any> = {};

        if (accIds.length > 0) {
          try {
            const dsParams = accIds.map((id: string) => `accommodationId=${id}`).join("&");
            const dsResponse = await fetch(`${TC_API_BASE}/accommodations/datasheet?${dsParams}&lang=NL`, {
              headers: { "auth-token": token, "Accept-Encoding": "gzip" },
            });
            if (dsResponse.ok) {
              const dsResult = await dsResponse.json();
              const sheets = Array.isArray(dsResult) ? dsResult : (dsResult.accommodations || dsResult.dataSheets || []);
              for (const ds of sheets) {
                const key = String(ds.code || ds.accommodationId || ds.id);
                if (key) datasheets[key] = ds;
              }
              console.log(`[TC Search] Got ${Object.keys(datasheets).length} datasheets`);
            }
          } catch (e) {
            console.warn("[TC Search] Datasheet fetch failed:", e);
          }
        }

        return accommodations.slice(0, 15).map((acc: any) => {
          const accId = String(acc.code || acc.accommodationId);
          const ds = datasheets[accId] || {};
          const dsLang = ds.datasheets?.NL || ds.datasheets?.EN || Object.values(ds.datasheets || {})[0] || {};
          const images = ds.images || [];
          const imageUrls = images.map((img: any) => typeof img === 'string' ? img : img.url || img.path).filter(Boolean);
          const bestCombo = acc.combinations?.[0];
          const price = bestCombo?.price?.amount || acc.fromPrice?.amount || 0;

          return {
            type: "hotel",
            id: accId,
            name: acc.name || ds.name || "Onbekend hotel",
            stars: acc.category ? parseInt(acc.category) : 0,
            location: acc.destinationName || ds.city || "",
            country: ds.country || "",
            description: dsLang.description || dsLang.shortDescription || "",
            images: imageUrls.length > 0 ? imageUrls : (ds.imageUrls || []),
            price,
            currency: bestCombo?.price?.currency || "EUR",
            roomType: bestCombo?.rooms?.[0]?.roomName || "",
            mealPlan: bestCombo?.rooms?.[0]?.boardName || "",
            geolocation: ds.geolocation || null,
            provider: acc.provider || "",
          };
        });
      }
    } catch (e) {
      console.warn(`[TC Search] Quote failed on ${ms}:`, e);
    }
  }

  console.log(`[TC Search] No hotels found for ${destination} in any microsite`);
  return [];
}


// ============================================================
// ACCOMMODATION LIST (static content, no dates needed)
// ============================================================
async function listAccommodations(micrositeId: string, params: any) {
  const token = await getTcToken(micrositeId);
  const { first, limit, destination, country } = params;

  // Try global /accommodations endpoint first (all hotels in the system)
  try {
    const globalUrl = `${TC_API_BASE}/accommodations?first=${first || 0}&limit=${limit || 50}`;
    console.log(`[TC Search] Trying global accommodations: ${globalUrl}`);
    const globalResponse = await fetch(globalUrl, {
      headers: { "auth-token": token },
    });
    if (globalResponse.ok) {
      const globalResult = await globalResponse.json();
      const allAccs = globalResult.accommodations || [];
      console.log(`[TC Search] Global accommodations: ${allAccs.length}`);
      if (allAccs.length > 0) {
        // Filter by destination/country if provided
        let filtered = allAccs;
        if (destination) {
          const q = destination.toLowerCase();
          filtered = allAccs.filter((a: any) => 
            a.destinationId?.toLowerCase() === q ||
            a.destinationName?.toLowerCase().includes(q) ||
            a.city?.toLowerCase().includes(q)
          );
        }
        if (country) {
          filtered = filtered.filter((a: any) => a.country?.toLowerCase().includes(country.toLowerCase()));
        }
        return filtered.map(mapAccommodationListItem);
      }
    }
  } catch (e) {
    console.warn(`[TC Search] Global accommodations failed:`, e);
  }

  // Fallback: preferred hotels
  try {
    let prefUrl = `${TC_API_BASE}/accommodations/preferred/${micrositeId}?first=${first || 0}&limit=${limit || 20}&lang=NL`;
    if (destination) prefUrl += `&destinationId=${destination}`;
    if (country) prefUrl += `&countryCode=${country}`;
    const prefResponse = await fetch(prefUrl, { headers: { "auth-token": token } });
    if (prefResponse.ok) {
      const prefResult = await prefResponse.json();
      return (prefResult.hotels || prefResult.accommodations || []).map(mapAccommodationListItem);
    }
  } catch (e) {
    console.warn(`[TC Search] Preferred hotels failed:`, e);
  }

  return [];
}

function mapAccommodationListItem(acc: any) {
  return {
    type: "hotel",
    id: acc.code || acc.accommodationId || acc.id,
    name: acc.name || "Onbekend hotel",
    stars: acc.category ? parseInt(acc.category) : 0,
    location: acc.destinationName || acc.city || "",
    country: acc.country || "",
    images: acc.imageUrls || acc.images || [],
    geolocation: acc.geolocation || null,
  };
}

// ============================================================
// TRANSPORT SEARCH (flights)
// ============================================================
async function searchTransports(micrositeId: string, params: any) {
  const token = await getTcToken(micrositeId);
  const { departure, departureType, arrival, arrivalType, departureDate, returnDate, adults, children, childAges, tripType } = params;

  const persons: any[] = [];
  for (let i = 0; i < (adults || 2); i++) persons.push({ age: 30 });
  if (children && childAges) {
    for (const age of childAges) persons.push({ age });
  }

  const journeys: any[] = [{
    departureDate,
    departure: departure,
    departureType: departureType || "TRANSPORT_BASE",
    arrival: arrival,
    arrivalType: arrivalType || "TRANSPORT_BASE",
  }];

  if (returnDate) {
    journeys.push({
      departureDate: returnDate,
      departure: arrival,
      departureType: arrivalType || "TRANSPORT_BASE",
      arrival: departure,
      arrivalType: departureType || "TRANSPORT_BASE",
    });
  }

  const quoteBody = {
    journeys,
    persons,
    language: "NL",
    tripType: tripType || (returnDate ? "RT" : "OW"),
    filter: {},
  };

  console.log(`[TC Search] Transport quote:`, JSON.stringify(quoteBody));

  const response = await fetch(`${TC_API_BASE}/booking/transports/quote`, {
    method: "POST",
    headers: { "Content-Type": "application/json", "auth-token": token },
    body: JSON.stringify(quoteBody),
  });

  if (!response.ok) {
    const errText = await response.text();
    console.error(`[TC Search] Transport quote failed (${response.status}):`, errText);
    throw new Error(`Vlucht zoeken mislukt (${response.status})`);
  }

  const result = await response.json();
  const services = result.services || [];
  const recommendations = result.recommendations || [];

  return recommendations.map((rec: any) => {
    const outbound = services.find((s: any) => s.ref === rec.outboundRef);
    const inbound = services.find((s: any) => s.ref === rec.inboundRef);

    return {
      type: "flight",
      id: rec.recommendationKey,
      name: `${outbound?.departure || departure} → ${outbound?.arrival || arrival}`,
      subtitle: inbound ? `Retour: ${inbound.departure} → ${inbound.arrival}` : "Enkele reis",
      price: rec.priceBreakdown?.totalPrice?.amount || 0,
      currency: rec.priceBreakdown?.totalPrice?.currency || "EUR",
      provider: rec.provider || "",
      fare: rec.fare || "",
      lowcost: rec.lowcost || false,
      outbound: outbound ? {
        departure: outbound.departure,
        arrival: outbound.arrival,
        departureTime: outbound.departureDateTime,
        arrivalTime: outbound.arrivalDateTime,
        duration: outbound.duration,
        segments: outbound.segments || [],
      } : null,
      inbound: inbound ? {
        departure: inbound.departure,
        arrival: inbound.arrival,
        departureTime: inbound.departureDateTime,
        arrivalTime: inbound.arrivalDateTime,
        duration: inbound.duration,
        segments: inbound.segments || [],
      } : null,
      recommendationKey: rec.recommendationKey,
    };
  });
}

// ============================================================
// TRANSFER SEARCH
// ============================================================
async function searchTransfers(micrositeId: string, params: any) {
  const token = await getTcToken(micrositeId);
  const { from, to, pickupDateTime, adults, children, childAges } = params;

  const persons: any[] = [];
  for (let i = 0; i < (adults || 2); i++) persons.push({ age: 30 });
  if (children && childAges) {
    for (const age of childAges) persons.push({ age });
  }

  const quoteBody: any = {
    persons,
    language: "NL",
    pickupDateTime,
    filter: {},
  };

  if (from) quoteBody.from = from;
  if (to) quoteBody.to = to;

  const response = await fetch(`${TC_API_BASE}/booking/transfers/quote`, {
    method: "POST",
    headers: { "Content-Type": "application/json", "auth-token": token },
    body: JSON.stringify(quoteBody),
  });

  if (!response.ok) {
    const errText = await response.text();
    console.error(`[TC Search] Transfer quote failed (${response.status}):`, errText);
    throw new Error(`Transfer zoeken mislukt (${response.status})`);
  }

  const result = await response.json();
  return (result.transfers || []).map((t: any) => ({
    type: "transfer",
    id: t.transferKey,
    name: `${result.from?.name || ""} → ${result.to?.name || ""}`,
    subtitle: t.vehicleDescriptionText || "",
    price: t.price?.amount || 0,
    currency: t.price?.currency || "EUR",
    provider: t.provider || "",
    vehicleType: t.transferType || "",
    productType: t.productType || "",
    serviceType: t.serviceType || "",
    pickupInfo: t.pickupInformation || "",
    characteristics: t.characteristics || [],
    image: t.image || "",
    transferKey: t.transferKey,
  }));
}

// ============================================================
// TICKET/ACTIVITY SEARCH
// ============================================================
async function searchTickets(micrositeId: string, params: any) {
  const token = await getTcToken(micrositeId);
  const { destination, checkIn, checkOut, adults, children, childAges } = params;

  const persons: any[] = [];
  for (let i = 0; i < (adults || 2); i++) persons.push({ age: 30 });
  if (children && childAges) {
    for (const age of childAges) persons.push({ age });
  }

  const quoteBody: any = {
    checkIn,
    checkOut,
    persons,
    language: "NL",
    filter: {},
  };

  if (destination) quoteBody.destinationId = destination;

  const response = await fetch(`${TC_API_BASE}/booking/tickets/quote`, {
    method: "POST",
    headers: { "Content-Type": "application/json", "auth-token": token },
    body: JSON.stringify(quoteBody),
  });

  if (!response.ok) {
    const errText = await response.text();
    console.error(`[TC Search] Ticket quote failed (${response.status}):`, errText);
    throw new Error(`Activiteit zoeken mislukt (${response.status})`);
  }

  const result = await response.json();
  const tickets = result.tickets || [];

  // Fetch datasheets for ticket details (photos, descriptions)
  const ticketIds = tickets.slice(0, 20).map((t: any) => t.ticketId).filter(Boolean);
  let ticketData: Record<string, any> = {};

  if (ticketIds.length > 0) {
    try {
      // Fetch individual ticket datasheets
      const dsPromises = ticketIds.slice(0, 10).map(async (id: string) => {
        try {
          const dsResp = await fetch(`${TC_API_BASE}/tickets/${id}/datasheet?lang=NL`, {
            headers: { "auth-token": token },
          });
          if (dsResp.ok) {
            const ds = await dsResp.json();
            ticketData[id] = ds;
          }
        } catch (e) {
          // ignore individual failures
        }
      });
      await Promise.all(dsPromises);
    } catch (e) {
      console.warn("[TC Search] Ticket datasheet fetch failed:", e);
    }
  }

  return tickets.map((t: any) => {
    const ds = ticketData[t.ticketId] || {};
    const dsLang = ds.datasheets?.NL || ds.datasheets?.EN || Object.values(ds.datasheets || {})[0] || {};

    return {
      type: "activity",
      id: t.ticketId,
      name: t.name || ds.name || "Onbekende activiteit",
      description: dsLang.description || "",
      images: ds.imageUrls || [],
      price: t.fromPrice?.amount || 0,
      currency: t.fromPrice?.currency || "EUR",
      provider: t.provider || "",
      location: ds.city || "",
      duration: ds.duration || null,
      durationType: ds.durationType || null,
      includes: dsLang.includes || [],
      excludes: dsLang.excludes || [],
      geolocation: ds.geolocation || null,
    };
  });
}

// ============================================================
// DESTINATIONS LIST (for search autocomplete)
// ============================================================
async function listDestinations(micrositeId: string) {
  const token = await getTcToken(micrositeId);

  const response = await fetch(`${TC_API_BASE}/destination/${micrositeId}?lang=NL`, {
    headers: { "auth-token": token },
  });

  if (!response.ok) {
    console.warn(`[TC Search] Destinations list failed (${response.status})`);
    return [];
  }

  const result = await response.json();
  return (result.destinations || result.destination || []).map((d: any) => ({
    id: d.id || d.code,
    name: d.name || "",
    country: d.country || d.countryName || "",
    countryCode: d.countryCode || "",
  }));
}

// ============================================================
// TRANSPORT BASES (airports) LIST
// ============================================================
async function listTransportBases(micrositeId: string, params: any) {
  const token = await getTcToken(micrositeId);
  const { first, limit } = params;

  const response = await fetch(`${TC_API_BASE}/transportbase?first=${first || 0}&limit=${limit || 100}`, {
    headers: { "auth-token": token },
  });

  if (!response.ok) return [];

  const result = await response.json();
  return (result.transportbase || []).map((tb: any) => ({
    code: tb.code,
    name: tb.name,
    city: tb.cityName || "",
    country: tb.country || "",
    type: tb.type || "",
  }));
}

// ============================================================
// MAIN HANDLER
// ============================================================
Deno.serve(async (req: Request) => {
  if (req.method === "OPTIONS") {
    return new Response(null, { status: 200, headers: corsHeaders });
  }

  try {
    const body = await req.json();
    const { action, micrositeId } = body;
    const microsite = micrositeId || getConfiguredMicrosites()[0] || "rondreis-planner";

    console.log(`[TC Search] Action: ${action}, Microsite: ${microsite}`);

    let results: any;

    switch (action) {
      case "search-accommodations":
        results = await searchAccommodations(microsite, body);
        break;

      case "list-accommodations":
        results = await listAccommodations(microsite, body);
        break;

      case "search-transports":
        results = await searchTransports(microsite, body);
        break;

      case "search-transfers":
        results = await searchTransfers(microsite, body);
        break;

      case "search-tickets":
        results = await searchTickets(microsite, body);
        break;

      case "list-destinations":
        results = await listDestinations(microsite);
        break;

      case "list-transport-bases":
        results = await listTransportBases(microsite, body);
        break;

      case "list-microsites":
        results = getConfiguredMicrosites().map(id => ({ id }));
        break;

      case "debug-quote": {
        // Raw debug: call TC API and return exact response
        const debugMs = body.micrositeId || getConfiguredMicrosites()[0];
        const debugToken = await getTcToken(debugMs);
        const debugAdults = body.adults || 2;
        const debugPersons = [];
        for (let i = 0; i < debugAdults; i++) debugPersons.push({ age: 30 });
        const debugBody: any = {
          checkIn: body.checkIn,
          checkOut: body.checkOut,
          distributions: [{ persons: debugPersons }],
          language: "NL",
          sourceMarket: "NL",
          filter: { bestCombinations: true, includeOnRequestOptions: true },
          timeout: 20000,
        };
        if (body.destination) debugBody.destinationId = body.destination;
        if (body.accommodations) debugBody.accommodations = body.accommodations;

        const debugResp = await fetch(`${TC_API_BASE}/booking/accommodations/quote`, {
          method: "POST",
          headers: { "Content-Type": "application/json", "auth-token": debugToken, "Accept-Encoding": "gzip" },
          body: JSON.stringify(debugBody),
        });
        const debugText = await debugResp.text();
        return new Response(
          JSON.stringify({
            debug: true,
            microsite: debugMs,
            token: debugToken.substring(0, 30) + "...",
            requestBody: debugBody,
            responseStatus: debugResp.status,
            responseBody: debugText.substring(0, 2000),
          }),
          { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
        );
      }

      default:
        return new Response(
          JSON.stringify({ error: `Onbekende actie: ${action}. Gebruik: search-accommodations, search-transports, search-transfers, search-tickets, list-destinations, list-transport-bases, list-microsites` }),
          { status: 400, headers: { ...corsHeaders, "Content-Type": "application/json" } }
        );
    }

    return new Response(
      JSON.stringify({ success: true, action, micrositeId: microsite, count: results.length, results }),
      { status: 200, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );

  } catch (error: any) {
    console.error("[TC Search] Error:", error);
    return new Response(
      JSON.stringify({ error: error.message || "Search failed" }),
      { status: 500, headers: { ...corsHeaders, "Content-Type": "application/json" } }
    );
  }
});
