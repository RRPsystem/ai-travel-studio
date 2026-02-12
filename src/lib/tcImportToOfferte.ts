import { supabase } from './supabase';
import { OfferteItem, OfferteDestination } from '../types/offerte';

// Microsites with credentials configured in Supabase secrets
export const TC_MICROSITES: { id: string; name: string; emoji: string }[] = [
  { id: 'rondreis-planner', name: 'Rondreis Planner', emoji: '🌍' },
  { id: 'reisbureaunederland', name: 'Reisbureau Nederland', emoji: '🇳🇱' },
  { id: 'symphonytravel', name: 'Symphony Travel', emoji: '🎵' },
  { id: 'pacificislandtravel', name: 'Pacific Island Travel', emoji: '🏝️' },
  { id: 'newreisplan', name: 'New Reisplan', emoji: '✈️' },
];

export interface TcImportResult {
  title: string;
  subtitle: string;
  introText: string;
  heroImage: string;
  destinations: OfferteDestination[];
  items: OfferteItem[];
  totalPrice: number;
  numberOfTravelers: number;
  currency: string;
}

/**
 * Fetch a travel from Travel Compositor API via the import-travel-compositor Edge Function
 * and map it to Offerte fields (items, destinations, meta).
 */
export async function importTcTravel(travelId: string, micrositeId?: string): Promise<TcImportResult> {
  if (!supabase) throw new Error('Supabase niet geconfigureerd');
  if (!travelId.trim()) throw new Error('Voer een Travel Compositor ID in');

  console.log('[TC Import] Fetching travel', travelId, micrositeId ? `from ${micrositeId}` : '(auto-detect)');

  const body: Record<string, string> = { travelId: travelId.trim() };
  if (micrositeId) body.micrositeId = micrositeId;

  const response = await supabase.functions.invoke('import-travel-compositor', { body });

  const { data, error } = response;
  console.log('[TC Import] Response:', { data, error });

  // supabase.functions.invoke puts the parsed JSON body in `data` even on error status codes
  if (error) {
    // data often contains the real error message from the Edge Function
    if (data && typeof data === 'object') {
      if (data.error) throw new Error(data.error);
      if (data.message) throw new Error(data.message);
    }
    // Try to parse error.context if available (Supabase wraps the body there sometimes)
    if (error.context) {
      try {
        const body = await error.context.json();
        if (body?.error) throw new Error(body.error);
        if (body?.message) throw new Error(body.message);
      } catch (_) { /* ignore parse errors */ }
    }
    throw new Error(error.message || 'Fout bij ophalen reis van Travel Compositor');
  }
  if (!data || data.error) throw new Error(data?.error || data?.message || 'Reis niet gevonden');
  if (!data.title) throw new Error(`Reis ${travelId} gevonden maar bevat geen titel. Controleer het ID en de microsite.`);

  return mapTcDataToOfferte(data);
}

function mapTcDataToOfferte(tc: any): TcImportResult {
  const items: OfferteItem[] = [];

  // Access raw TC data for richer field extraction
  const rawHotels = tc.rawTcData?.hotels || [];
  const rawCars = tc.rawTcData?.cars || [];
  const rawCruises = tc.rawTcData?.cruises || [];
  const rawTransports = tc.rawTcData?.transports || [];

  // Log ALL raw field names for debugging
  console.log('[TC Map] Formatted hotel keys:', tc.hotels?.[0] ? Object.keys(tc.hotels[0]) : 'none');
  console.log('[TC Map] Raw TC hotel keys:', rawHotels[0] ? Object.keys(rawHotels[0]) : 'none');
  console.log('[TC Map] Raw TC hotel[0] full:', rawHotels[0] ? JSON.stringify(rawHotels[0]).substring(0, 800) : 'none');
  console.log('[TC Map] Raw TC car keys:', rawCars[0] ? Object.keys(rawCars[0]) : 'none');
  console.log('[TC Map] Raw TC car[0] full:', rawCars[0] ? JSON.stringify(rawCars[0]).substring(0, 500) : 'none');
  console.log('[TC Map] Raw TC cruise keys:', rawCruises[0] ? Object.keys(rawCruises[0]) : 'none');
  console.log('[TC Map] Raw TC cruise[0] full:', rawCruises[0] ? JSON.stringify(rawCruises[0]).substring(0, 500) : 'none');
  console.log('[TC Map] Raw TC transport keys:', rawTransports[0] ? Object.keys(rawTransports[0]) : 'none');
  console.log('[TC Map] Raw TC transport[0] full:', rawTransports[0] ? JSON.stringify(rawTransports[0]).substring(0, 500) : 'none');

  // --- Flights ---
  const flights = tc.flights || [];
  for (const f of flights) {
    items.push({
      id: crypto.randomUUID(),
      type: 'flight',
      title: buildFlightTitle(f),
      subtitle: [safeStr(f.company), safeStr(f.transportNumber)].filter(Boolean).join(' '),
      departure_airport: safeStr(f.departureCity || f.departure || f.originCode),
      arrival_airport: safeStr(f.arrivalCity || f.arrival || f.targetCode),
      departure_time: safeStr(f.departureTime),
      arrival_time: safeStr(f.arrivalTime),
      airline: safeStr(f.company),
      flight_number: safeStr(f.transportNumber),
      date_start: safeStr(f.departureDate),
      date_end: safeStr(f.arrivalDate),
      price: extractPrice(f),
      sort_order: 0, // will be reassigned after chronological sort
    });
  }

  // --- Hotels ---
  // Use formatted hotels but also check raw TC data for missing fields
  const hotels = tc.hotels || [];
  for (let hi = 0; hi < hotels.length; hi++) {
    const h = hotels[hi];
    const raw = rawHotels[hi] || {}; // Original TC API hotel object
    const rawHd = raw.hotelData || {};
    const hotelData = h.hotelData || h;
    const name = hotelData.name || h.name || rawHd.name || 'Hotel';
    const nights = h.nights || hotelData.nights || raw.nights || 0;
    const stars = parseStars(hotelData.category || h.category || rawHd.category);
    const rawImages = hotelData.images || h.images || rawHd.images || [];
    const imageUrls: string[] = rawImages
      .map((img: any) => typeof img === 'string' ? img : img?.url || '')
      .filter((url: string) => url);
    const firstImage = imageUrls[0] || '';

    // Extract facilities as string array
    const rawFacilities = hotelData.facilities || h.facilities || rawHd.facilities || {};
    let facilityList: string[] = [];
    if (Array.isArray(rawFacilities)) {
      facilityList = rawFacilities.map((f: any) => typeof f === 'string' ? f : f.name || '').filter(Boolean);
    } else if (typeof rawFacilities === 'object') {
      for (const [key, val] of Object.entries(rawFacilities)) {
        if (Array.isArray(val)) {
          facilityList.push(...val.map((v: any) => typeof v === 'string' ? v : v.name || '').filter(Boolean));
        } else if (val === true || val === 'true') {
          facilityList.push(key);
        } else if (typeof val === 'string' && val) {
          facilityList.push(val);
        }
      }
    }

    // Extract location: formatted first, then raw TC data
    const location = safeStr(
      h.city || hotelData.city || h.destination || hotelData.destination || hotelData.address || h.address ||
      rawHd.city || raw.destination || rawHd.destination || rawHd.address || raw.city
    );

    // Extract dates: formatted first, then raw TC hotel data (try ALL possible field names)
    const dateStart = safeStr(
      h.checkIn || hotelData.checkIn || h.startDate || h.dateFrom ||
      raw.checkIn || raw.startDate || raw.dateFrom || raw.checkinDate || raw.check_in
    );
    const dateEnd = safeStr(
      h.checkOut || hotelData.checkOut || h.endDate || h.dateTo ||
      raw.checkOut || raw.endDate || raw.dateTo || raw.checkoutDate || raw.check_out
    );

    // Extract room type: formatted first, then raw
    const roomType = safeStr(
      h.roomType || hotelData.roomType || h.roomDescription || h.room ||
      raw.roomDescription || raw.roomType || raw.room || raw.selectedRoom || raw.roomName || rawHd.roomType
    );

    // Extract meal plan: formatted first, then raw
    const boardType = safeStr(
      h.mealPlan || hotelData.mealPlan || h.mealPlanDescription ||
      raw.mealPlan || raw.mealPlanDescription || raw.board || rawHd.mealPlan
    );

    items.push({
      id: crypto.randomUUID(),
      type: 'hotel',
      title: name,
      hotel_name: name,
      description: stripHtml(hotelData.shortDescription || hotelData.description || h.description || rawHd.shortDescription || rawHd.description || ''),
      image_url: firstImage,
      images: imageUrls.slice(0, 10),
      facilities: facilityList.length > 0 ? facilityList : undefined,
      location,
      nights,
      star_rating: stars,
      board_type: boardType,
      room_type: roomType,
      price: extractPrice(h),
      date_start: dateStart,
      date_end: dateEnd,
      sort_order: 0,
    });
  }

  // --- Transfers (non-flight transports) ---
  const transfers = tc.transfers || [];
  for (const t of transfers) {
    items.push({
      id: crypto.randomUUID(),
      type: 'transfer',
      title: buildTransferTitle(t),
      transfer_type: safeStr(t.transportType || t.type || 'transfer'),
      pickup_location: safeStr(t.departureCity || t.departure || t.origin),
      dropoff_location: safeStr(t.arrivalCity || t.arrival || t.target),
      date_start: safeStr(t.departureDate || t.startDate),
      departure_time: safeStr(t.departureTime),
      arrival_time: safeStr(t.arrivalTime),
      price: extractPrice(t),
      sort_order: 0,
    });
  }

  // --- Car rentals ---
  const cars = tc.carRentals || [];
  for (const c of cars) {
    // Log all car fields for debugging
    console.log('[TC Map] Car rental raw data:', JSON.stringify(c).substring(0, 500));
    items.push({
      id: crypto.randomUUID(),
      type: 'car_rental',
      title: safeStr(c.name || c.carType || c.vehicleType || c.category || 'Huurauto'),
      subtitle: safeStr(c.company || c.supplier || c.rentalCompany),
      supplier: safeStr(c.company || c.supplier || c.rentalCompany),
      date_start: safeStr(c.pickupDate || c.startDate || c.dateFrom || c.checkIn),
      date_end: safeStr(c.dropoffDate || c.endDate || c.dateTo || c.checkOut),
      pickup_location: safeStr(c.pickupLocation || c.pickupOffice || c.pickup || c.departureCity),
      dropoff_location: safeStr(c.dropoffLocation || c.dropoffOffice || c.dropoff || c.arrivalCity),
      price: extractPrice(c),
      sort_order: 0,
    });
  }

  // --- Cruises ---
  const cruises = tc.cruises || [];
  for (const cr of cruises) {
    // Log all cruise fields for debugging
    console.log('[TC Map] Cruise raw data:', JSON.stringify(cr).substring(0, 500));
    items.push({
      id: crypto.randomUUID(),
      type: 'cruise',
      title: safeStr(cr.name || cr.shipName || 'Cruise'),
      subtitle: safeStr(cr.cruiseLine || cr.company),
      supplier: safeStr(cr.cruiseLine || cr.company),
      nights: cr.nights || cr.duration || 0,
      date_start: safeStr(cr.departureDate || cr.startDate || cr.dateFrom || cr.checkIn),
      date_end: safeStr(cr.arrivalDate || cr.endDate || cr.dateTo || cr.checkOut),
      location: safeStr(cr.departurePort || cr.embarkation || cr.departure),
      description: stripHtml(cr.description || cr.itinerary || ''),
      price: extractPrice(cr),
      sort_order: 0,
    });
  }

  // --- Activities / Tickets ---
  const activities = tc.activities || [];
  for (const a of activities) {
    items.push({
      id: crypto.randomUUID(),
      type: 'activity',
      title: a.name || a.title || 'Activiteit',
      description: stripHtml(a.description || ''),
      location: a.destination || a.location || '',
      date_start: a.date || a.startDate || '',
      activity_duration: a.duration || '',
      price: extractPrice(a),
      sort_order: 0,
    });
  }

  // =============================================================
  // ORDERING + DATE CALCULATION
  // =============================================================
  // Strategy: Use DESTINATIONS array as chronological backbone.
  // Match each hotel/cruise to a destination by name.
  // Build timeline: outbound flights → accommodations in dest order → car → return flights.
  // Calculate dates by walking through: each check-in = previous check-out.
  // =============================================================

  const destNames = (tc.destinations || []).map((d: any) => (d.name || '').toLowerCase().trim());
  console.log(`[TC Sort] Destinations: ${destNames.join(' → ')}`);

  // Helper: fuzzy match a location string to a destination index
  const matchDest = (loc: string): number => {
    if (!loc) return -1;
    const l = loc.toLowerCase().trim();
    // Exact includes match
    let idx = destNames.findIndex((d: string) => d && l && (d.includes(l) || l.includes(d)));
    if (idx >= 0) return idx;
    // Try first word match (e.g. "Key West FL" matches "Key West")
    const firstWord = l.split(/[\s,]+/)[0];
    if (firstWord.length >= 3) {
      idx = destNames.findIndex((d: string) => d && d.includes(firstWord));
    }
    return idx;
  };

  // Separate items by type
  const flightItems = items.filter(i => i.type === 'flight');
  const hotelItems = items.filter(i => i.type === 'hotel');
  const cruiseItems = items.filter(i => i.type === 'cruise');
  const carItems = items.filter(i => i.type === 'car_rental');
  const transferItems = items.filter(i => i.type === 'transfer');
  const activityItems = items.filter(i => i.type === 'activity');

  // Match hotels to destinations
  const hotelWithDest = hotelItems.map(h => {
    const loc = typeof h.location === 'string' ? h.location : '';
    const di = matchDest(loc);
    console.log(`[TC Sort] Hotel "${h.title}" loc="${loc}" → dest[${di}]${di >= 0 ? ` (${destNames[di]})` : ''}`);
    return { item: h, destIdx: di };
  });

  // Match cruises to destinations (by departure port)
  const cruiseWithDest = cruiseItems.map(c => {
    const loc = typeof c.location === 'string' ? c.location : '';
    const di = matchDest(loc);
    console.log(`[TC Sort] Cruise "${c.title}" departure="${loc}" → dest[${di}]${di >= 0 ? ` (${destNames[di]})` : ''}`);
    return { item: c, destIdx: di };
  });

  // Build ordered accommodations by walking through destinations
  const orderedAccom: OfferteItem[] = [];
  const usedHotels = new Set<string>();
  const usedCruises = new Set<string>();

  for (let di = 0; di < destNames.length; di++) {
    // Check hotels for this destination
    for (const hd of hotelWithDest) {
      if (hd.destIdx === di && !usedHotels.has(hd.item.id)) {
        orderedAccom.push(hd.item);
        usedHotels.add(hd.item.id);
      }
    }
    // Check cruises for this destination
    for (const cd of cruiseWithDest) {
      if (cd.destIdx === di && !usedCruises.has(cd.item.id)) {
        orderedAccom.push(cd.item);
        usedCruises.add(cd.item.id);
      }
    }
  }
  // Append any unmatched accommodations at end (shouldn't happen)
  for (const hd of hotelWithDest) {
    if (!usedHotels.has(hd.item.id)) orderedAccom.push(hd.item);
  }
  for (const cd of cruiseWithDest) {
    if (!usedCruises.has(cd.item.id)) orderedAccom.push(cd.item);
  }

  console.log(`[TC Sort] Accommodation order: ${orderedAccom.map(a => `${a.type}:${a.title}`).join(' → ')}`);

  // Split flights into outbound (first half) and return (second half)
  const midpoint = Math.ceil(flightItems.length / 2);
  const outboundFlights = flightItems.slice(0, midpoint);
  const returnFlights = flightItems.slice(midpoint);

  // Build final timeline
  const timeline: OfferteItem[] = [
    ...outboundFlights,
    ...orderedAccom,
    ...carItems,
    ...transferItems,
    ...activityItems,
    ...returnFlights,
  ];

  // Move car rental to AFTER the last accommodation before it needs to start
  // (car rental typically starts after cruise ends, when land travel begins)

  // Assign sort_order
  timeline.forEach((item, idx) => { item.sort_order = idx; });

  // --- CALCULATE DATES by walking through timeline ---
  // Each accommodation's check-in = previous accommodation's check-out
  const tripStartDate = findTripStartDate(timeline, tc);
  if (tripStartDate) {
    console.log('[TC Sort] Trip start date:', tripStartDate.toISOString().split('T')[0]);

    // Walk through accommodations and calculate dates sequentially
    let currentDate = new Date(tripStartDate);
    for (const item of orderedAccom) {
      const nights = item.nights || 1;
      const checkIn = new Date(currentDate);
      const checkOut = new Date(currentDate);
      checkOut.setDate(checkOut.getDate() + nights);

      item.date_start = checkIn.toISOString().split('T')[0];
      item.date_end = checkOut.toISOString().split('T')[0];
      console.log(`[TC Sort] ${item.type} "${item.title}" → ${item.date_start} to ${item.date_end} (${nights}n)`);

      currentDate = new Date(checkOut); // next item starts when this one ends
    }

    // Car rental: spans from after cruise to return flight
    for (const car of carItems) {
      // Find last cruise end or first post-cruise hotel start
      const lastCruise = orderedAccom.filter(a => a.type === 'cruise').pop();
      const lastAccom = orderedAccom[orderedAccom.length - 1];
      const carStart = lastCruise?.date_end || orderedAccom.find(a => orderedAccom.indexOf(a) > orderedAccom.indexOf(lastCruise!))?.date_start || tripStartDate.toISOString().split('T')[0];
      const carEnd = lastAccom?.date_end || currentDate.toISOString().split('T')[0];

      if (!car.date_start || car.date_start === '') {
        car.date_start = carStart;
        car.date_end = carEnd;
        console.log(`[TC Sort] Car "${car.title}" → ${car.date_start} to ${car.date_end}`);
      }
    }
  }

  // Replace items array with correctly ordered timeline
  items.length = 0;
  items.push(...timeline);

  // --- Destinations ---
  const destinations: OfferteDestination[] = (tc.destinations || []).map((d: any, i: number) => ({
    name: d.name || `Bestemming ${i + 1}`,
    country: d.country || '',
    description: stripHtml(d.description || ''),
    highlights: d.highlights || [],
    images: d.images || d.imageUrls || [],
    lat: d.geolocation?.latitude || 0,
    lng: d.geolocation?.longitude || 0,
    order: i,
  }));

  // --- Meta ---
  const countryList = (tc.countries || []).join(', ');
  const subtitle = [
    tc.numberOfDays ? `${tc.numberOfDays} dagen` : '',
    tc.numberOfNights ? `${tc.numberOfNights} nachten` : '',
    countryList,
  ].filter(Boolean).join(' · ');

  const totalPrice = tc.pricePerPerson || tc.priceBreakdown?.hotels || 0;
  const adults = tc.travelers?.adults || 2;

  return {
    title: tc.title || `Reis ${tc.id}`,
    subtitle,
    introText: stripHtml(tc.introText || tc.description || ''),
    heroImage: tc.heroImage || tc.images?.[0] || '',
    destinations,
    items,
    totalPrice: items.reduce((sum, item) => sum + (item.price || 0), 0) || totalPrice,
    numberOfTravelers: adults,
    currency: tc.currency || 'EUR',
  };
}

// --- Helpers ---

/** Find trip start date from first flight departure or TC meta data */
function findTripStartDate(items: OfferteItem[], tc: any): Date | null {
  // 1. Try first flight's departure date
  const firstFlight = items.find(i => i.type === 'flight' && i.date_start);
  if (firstFlight?.date_start) {
    const d = new Date(firstFlight.date_start);
    if (!isNaN(d.getTime())) {
      console.log('[TC Map] Trip start from first flight:', firstFlight.date_start);
      return d;
    }
  }
  
  // 2. Try raw transports departure date (flights come from raw TC data)
  const rawTransports = tc.rawTcData?.transports || tc.transports || [];
  for (const t of rawTransports) {
    if (t.departureDate) {
      const d = new Date(t.departureDate);
      if (!isNaN(d.getTime())) {
        console.log('[TC Map] Trip start from raw transport:', t.departureDate);
        return d;
      }
    }
  }
  
  // 3. Try TC meta departure date
  if (tc.departureDate) {
    const d = new Date(tc.departureDate);
    if (!isNaN(d.getTime())) return d;
  }
  
  // 4. Try any item with a valid date
  for (const item of items) {
    if (item.date_start) {
      const d = new Date(item.date_start);
      if (!isNaN(d.getTime())) return d;
    }
  }
  
  console.warn('[TC Map] Could not determine trip start date');
  return null;
}

/** Safely convert TC API values to string — handles {code, name} objects */
function safeStr(val: any): string {
  if (!val) return '';
  if (typeof val === 'string') return val;
  if (typeof val === 'number') return String(val);
  if (typeof val === 'object' && val !== null) {
    return val.name || val.description || val.code || JSON.stringify(val);
  }
  return String(val);
}

function extractPrice(obj: any): number {
  if (typeof obj.price === 'number') return obj.price;
  if (obj.priceBreakdown?.totalPrice?.microsite?.amount) return obj.priceBreakdown.totalPrice.microsite.amount;
  if (obj.priceBreakdown?.totalPrice?.amount) return obj.priceBreakdown.totalPrice.amount;
  if (typeof obj.totalPrice === 'number') return obj.totalPrice;
  return 0;
}

function buildFlightTitle(f: any): string {
  const from = safeStr(f.departureCity || f.departure);
  const to = safeStr(f.arrivalCity || f.arrival);
  if (from && to) return `${from} → ${to}`;
  return f.company ? `Vlucht ${safeStr(f.company)}` : 'Vlucht';
}

function buildTransferTitle(t: any): string {
  const from = safeStr(t.departureCity || t.departure);
  const to = safeStr(t.arrivalCity || t.arrival);
  const type = safeStr(t.transportType || 'Transfer');
  if (from && to) return `${type}: ${from} → ${to}`;
  return type;
}

function parseStars(category: string | number | undefined): number {
  if (!category) return 0;
  if (typeof category === 'number') return category;
  const match = String(category).match(/(\d)/);
  return match ? parseInt(match[1]) : 0;
}

function stripHtml(html: string): string {
  if (!html) return '';
  return html
    .replace(/<[^>]*>/g, '')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&quot;/g, '"')
    .replace(/&#39;/g, "'")
    .replace(/&nbsp;/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}
