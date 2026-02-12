# TC Hotel Quote Edge Function

Deze Edge Function roept de Travel Compositor API aan om hotels te zoeken met prijzen en beschikbaarheid.

## Endpoint
`POST /tc-hotel-quote`

## Request Body
```json
{
  "micrositeId": "rondreis-planner",  // Optioneel, gebruikt eerste geconfigureerde microsite als niet opgegeven
  "checkIn": "2026-07-15",            // Verplicht: YYYY-MM-DD
  "checkOut": "2026-07-18",           // Verplicht: YYYY-MM-DD
  "adults": 2,                        // Optioneel, default: 2
  "children": 0,                      // Optioneel, default: 0
  "childAges": [],                    // Optioneel, array van leeftijden
  "destination": "AMS",               // Verplicht als geen accommodationCodes
  "accommodationCodes": ["1000", "1003"], // Optioneel, specifieke hotel IDs
  "bestCombinations": true,           // Optioneel, default: true
  "maxCombinations": 5,               // Optioneel, default: 5
  "includeOnRequestOptions": true     // Optioneel, default: true
}
```

## Response
```json
{
  "success": true,
  "microsite": "rondreis-planner",
  "checkIn": "2026-07-15",
  "checkOut": "2026-07-18",
  "count": 3,
  "accommodations": [
    {
      "type": "hotel",
      "id": "1000",
      "name": "Hotel Palas Pineda",
      "stars": 4,
      "location": "La Pineda",
      "country": "ES",
      "geolocation": { "latitude": 41.06788, "longitude": 1.17602 },
      "price": 134.05,
      "currency": "EUR",
      "mealPlan": "Bed & Breakfast",
      "roomType": "Double Room",
      "provider": "Expedia",
      "onRequest": false,
      "combinations": [
        {
          "combinationKey": "eyJhbGci...",
          "rooms": [{ "description": "Double Room" }],
          "mealPlan": { "id": "BB", "description": "Bed & Breakfast" },
          "price": { "amount": 134.05, "currency": "EUR" },
          "cancellationPolicies": [...],
          "currentCancellationType": { "type": "REFUNDABLE", "deadline": "2026-07-10" }
        }
      ]
    }
  ]
}
```

## Errors
- `400`: Ontbrekende verplichte velden (checkIn, checkOut, destination)
- `401`: Authenticatie mislukt - controleer TC credentials
- `500`: Server error

## Deployment
```bash
npx supabase functions deploy tc-hotel-quote --no-verify-jwt
```

## Testing
```bash
curl -X POST https://huaaogdxxdcakxryecnw.supabase.co/functions/v1/tc-hotel-quote \
  -H "Content-Type: application/json" \
  -H "apikey: YOUR_ANON_KEY" \
  -d '{
    "checkIn": "2026-07-15",
    "checkOut": "2026-07-18",
    "destination": "AMS",
    "adults": 2
  }'
```
