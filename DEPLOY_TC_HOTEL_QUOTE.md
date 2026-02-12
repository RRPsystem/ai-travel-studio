# Travel Compositor Hotel Quote - Deployment Guide

## Wat is er gemaakt?

Een nieuwe Edge Function (`tc-hotel-quote`) die de Travel Compositor API aanroept om hotels te zoeken met **live prijzen en beschikbaarheid**.

## Probleem dat wordt opgelost

De huidige implementatie heeft 2 problemen:
1. **POST /booking/accommodations/quote** geeft 401 UNAUTHORIZED voor alle microsites
2. Je wilt live hotel prijzen en beschikbaarheid, niet alleen statische hoteldata

## Oplossing

### 1. Nieuwe Edge Function: `tc-hotel-quote`

**Locatie:** `supabase/functions/tc-hotel-quote/index.ts`

**Functionaliteit:**
- Authenticatie met Travel Compositor API per microsite
- Token caching (2 uur geldig)
- Ondersteunt meerdere microsites via database (`tc_microsites` tabel)
- Roept POST /booking/accommodations/quote aan met correcte headers
- Retourneert hotels met prijzen, kamertypes, maaltijdplannen, annuleringsvoorwaarden

**Request voorbeeld:**
```json
{
  "checkIn": "2026-07-15",
  "checkOut": "2026-07-18",
  "destination": "AMS",
  "adults": 2,
  "children": 0,
  "bestCombinations": true,
  "maxCombinations": 5
}
```

**Response:**
```json
{
  "success": true,
  "count": 15,
  "accommodations": [
    {
      "id": "1000",
      "name": "Hotel Palas Pineda",
      "stars": 4,
      "price": 134.05,
      "currency": "EUR",
      "mealPlan": "Bed & Breakfast",
      "roomType": "Double Room",
      "combinations": [...]
    }
  ]
}
```

### 2. Frontend Service: `TravelCompositorService`

**Locatie:** `src/lib/apiServices.ts`

**Nieuwe export:** `tcService`

**Gebruik:**
```typescript
import { tcService } from '../lib/apiServices';

const result = await tcService.searchHotels({
  checkIn: '2026-07-15',
  checkOut: '2026-07-18',
  destination: 'AMS',
  adults: 2
});

if (result.success) {
  console.log(`Gevonden: ${result.count} hotels`);
  result.accommodations.forEach(hotel => {
    console.log(`${hotel.name}: €${hotel.price}`);
  });
}
```

## Deployment Stappen

### Stap 1: Deploy de Edge Function

```powershell
npx supabase functions deploy tc-hotel-quote --no-verify-jwt
```

**Verwachte output:**
```
Deploying tc-hotel-quote (project ref: huaaogdxxdcakxryecnw)
Bundled tc-hotel-quote in XXXms
Deployed tc-hotel-quote
```

### Stap 2: Test de authenticatie

De Edge Function test automatisch verschillende microsite ID formaten om te vinden welke werkt voor de booking API.

**Test commando:**
```powershell
# Via PowerShell
$body = @{
  checkIn = "2026-07-15"
  checkOut = "2026-07-18"
  destination = "AMS"
  adults = 2
} | ConvertTo-Json

Invoke-RestMethod -Uri "https://huaaogdxxdcakxryecnw.supabase.co/functions/v1/tc-hotel-quote" `
  -Method POST `
  -Headers @{
    "apikey" = $env:VITE_SUPABASE_ANON_KEY
    "Content-Type" = "application/json"
  } `
  -Body $body
```

### Stap 3: Controleer de logs

```powershell
npx supabase functions logs tc-hotel-quote --tail
```

**Wat te zoeken:**
- ✅ `[TC Hotel Quote] Authentication successful for {microsite}`
- ✅ `[TC Hotel Quote] Success! Got X accommodations`
- ❌ `[TC Hotel Quote] Auth failed (401)` → Credentials probleem
- ❌ `[TC Hotel Quote] Quote failed (401)` → Mogelijk verkeerd microsite ID formaat

## Troubleshooting

### 401 UNAUTHORIZED bij authenticatie

**Probleem:** Credentials niet gevonden of incorrect

**Oplossing:**
1. Controleer `tc_microsites` tabel in Supabase
2. Zorg dat `is_active = true`
3. Controleer username/password/microsite_id

**SQL Check:**
```sql
SELECT microsite_id, name, is_active 
FROM tc_microsites 
WHERE is_active = true;
```

### 401 UNAUTHORIZED bij quote (na succesvolle auth)

**Probleem:** De microsite ID heeft geen toegang tot de booking API

**Mogelijke oorzaken:**
1. Account heeft geen booking rechten
2. Verkeerd microsite ID formaat
3. Contract/licentie probleem met Travel Compositor

**Debug stappen:**
1. Test met verschillende microsite ID varianten (de Edge Function doet dit automatisch)
2. Neem contact op met je Travel Compositor account manager
3. Vraag om booking API toegang voor je microsite

### Geen hotels gevonden

**Probleem:** Query retourneert 0 resultaten

**Check:**
1. Is de destination ID correct? (gebruik GET /destination/{micrositeId} om lijst te krijgen)
2. Zijn de datums in de toekomst?
3. Heeft de microsite hotels in die bestemming?

## Integratie in OfferteItemPanel

**Volgende stap:** Voeg een "Zoek live prijzen" knop toe in OfferteItemPanel.tsx

```typescript
// In OfferteItemPanel.tsx
import { tcService } from '../../lib/apiServices';

const searchLiveHotels = async () => {
  setSearching(true);
  const result = await tcService.searchHotels({
    checkIn: '2026-07-15',
    checkOut: '2026-07-18',
    destination: searchQuery,
    adults: 2
  });
  
  if (result.success) {
    // Map naar TcSearchResult format
    const liveResults = result.accommodations.map(acc => ({
      type: 'hotel',
      id: acc.id,
      name: acc.name,
      stars: acc.stars,
      location: acc.location,
      price: acc.price,
      mealPlan: acc.mealPlan,
      roomType: acc.roomType,
      images: acc.images || [],
      // ... etc
    }));
    setSearchResults(liveResults);
  } else {
    setSearchError(result.error);
  }
  setSearching(false);
};
```

## Credentials Setup

De Edge Function haalt credentials op uit de `tc_microsites` tabel:

```sql
-- Voorbeeld: Voeg een microsite toe
INSERT INTO tc_microsites (microsite_id, name, username, password, is_active)
VALUES (
  'rondreis-planner',
  'Rondreis Planner',
  'jouw_username',
  'jouw_password',
  true
);
```

**Bestaande microsites:**
- `pacificislandtravel` (slot 3)
- `newreisplan` (slot 4)
- `symphonytravel` (slot 5)
- `rondreis-planner` (fallback)
- `reisbureaunederland` (slot 2)

## API Endpoints Gebruikt

1. **POST /authentication/authenticate**
   - Verkrijgt auth token (geldig 2 uur)
   - Headers: `Content-Type: application/json`, `Accept-Encoding: gzip`

2. **POST /booking/accommodations/quote**
   - Zoekt hotels met prijzen
   - Headers: `auth-token`, `Content-Type: application/json`, `Accept-Encoding: gzip`
   - Body: checkIn, checkOut, distributions, language, destinationId/accommodations

## Belangrijke Notes

1. **Geen bookings:** We gebruiken alleen Quote (stap 1) voor content en prijzen
2. **Token expiry:** Tokens zijn 2 uur geldig, daarna automatisch vernieuwd
3. **CombinationKey:** Elke hotel combinatie heeft een unieke key (40 min geldig)
4. **OnRequest:** Hotels kunnen "on request" zijn (geen directe beschikbaarheid)
5. **GZip verplicht:** Alle requests moeten `Accept-Encoding: gzip` header hebben

## Volgende Stappen

1. ✅ Deploy Edge Function
2. ⏳ Test authenticatie met verschillende microsites
3. ⏳ Integreer in OfferteItemPanel UI
4. ⏳ Voeg datum/personen selectie toe
5. ⏳ Test end-to-end flow

## Support

Bij problemen:
- Check Edge Function logs: `npx supabase functions logs tc-hotel-quote`
- Controleer Supabase dashboard voor errors
- Neem `traceId` uit response mee bij support vragen
