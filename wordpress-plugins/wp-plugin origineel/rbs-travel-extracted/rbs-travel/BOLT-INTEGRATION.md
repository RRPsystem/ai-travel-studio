# BOLT Integration - WordPress Travel Catalogus

## Overzicht

De WordPress RRP System plugin biedt een REST API endpoint waarmee BOLT reizen kan ophalen die door de admin zijn gemarkeerd voor de TravelC Web Catalogus.

---

## Stap 1: WordPress Admin markeert reizen

In WordPress kan de admin per reis aanvinken of deze beschikbaar moet zijn in de BOLT catalogus:

1. Ga naar **Reizen** → **Alle Reizen**
2. Bewerk een reis
3. Vink aan: **"Toon in TravelC Web Catalogus"** (of via bulk actions)
4. Dit zet de meta key `_in_bolt_catalog` op `1`

---

## Stap 2: BOLT haalt catalogus op via REST API

### Endpoint

```
GET https://{wordpress-site}/wp-json/rbs-travel/v1/catalog
```

### Optionele Query Parameters

| Parameter   | Type   | Beschrijving                          |
|-------------|--------|---------------------------------------|
| `continent` | string | Filter op continent slug (bijv. `europa`) |
| `country`   | string | Filter op land slug (bijv. `spanje`)      |

### Voorbeeld Request

```javascript
// Alle reizen in de catalogus
fetch('https://flyendrive.online/wp-json/rbs-travel/v1/catalog')
  .then(res => res.json())
  .then(data => console.log(data));

// Gefilterd op Europa
fetch('https://flyendrive.online/wp-json/rbs-travel/v1/catalog?continent=europa')
  .then(res => res.json())
  .then(data => console.log(data));
```

### Response Structuur

```json
{
  "total": 15,
  "filters": {
    "continents": [
      {
        "slug": "europa",
        "name": "Europa",
        "count": 8,
        "countries": [
          { "slug": "spanje", "name": "Spanje", "count": 3 },
          { "slug": "italie", "name": "Italië", "count": 5 }
        ]
      },
      {
        "slug": "noord-amerika",
        "name": "Noord-Amerika",
        "count": 7,
        "countries": [
          { "slug": "usa", "name": "USA", "count": 4 },
          { "slug": "canada", "name": "Canada", "count": 3 }
        ]
      }
    ]
  },
  "travels": [
    {
      "id": 1234,
      "travel_id": "TC-12345",
      "title": "Land & Cruise Florida",
      "continent": "Noord-Amerika",
      "country": "USA",
      "preview_url": "https://flyendrive.online/reis/land-cruise-florida/",
      "thumbnail": "https://flyendrive.online/wp-content/uploads/2024/florida.jpg",
      "price": "3801",
      "nights": "18"
    },
    {
      "id": 1235,
      "travel_id": "TC-67890",
      "title": "Hoogtepunten van Zuid-Afrika",
      "continent": "Afrika",
      "country": "Zuid-Afrika",
      "preview_url": "https://flyendrive.online/reis/hoogtepunten-zuid-afrika/",
      "thumbnail": "https://flyendrive.online/wp-content/uploads/2024/za.jpg",
      "price": "4213",
      "nights": "20"
    }
  ]
}
```

---

## Stap 3: BOLT toont catalogus aan gebruiker

### UI Flow

1. **Catalogus Browser**
   - Toon dropdown/filter voor Continent → Land
   - Toon grid/lijst met reizen (thumbnail, titel, prijs, nachten)
   - Elke reis heeft "Preview" link en "Selecteer" button

2. **Reis Selecteren**
   - Gebruiker klikt op reis
   - BOLT slaat `travel_id` (TC API ID) op bij de brand

3. **Reis Importeren naar Brand**
   - Gebruik de `travel_id` om de volledige reisdata op te halen via Travel Compositor API
   - Of gebruik de WordPress detail endpoint voor basis data

---

## Stap 4: Volledige reisdata ophalen (optioneel)

Als BOLT meer details nodig heeft dan de catalogus biedt:

### Via WordPress REST API

```
GET https://{wordpress-site}/wp-json/rbs-travel/v1/ideas/{id}
GET https://{wordpress-site}/wp-json/rbs-travel/v1/ideas/slug/{slug}
```

### Response bevat:
- Volledige beschrijving
- Alle afbeeldingen
- Tour plan (dag-voor-dag)
- Hotels, excursies, vluchten
- Route map URL
- Prijzen

---

## Stap 5: Reis koppelen aan Brand in BOLT

Wanneer een gebruiker een reis selecteert:

```javascript
// Voorbeeld: Reis opslaan bij brand in Supabase
const selectedTravel = {
  brand_id: 'uuid-van-brand',
  wordpress_id: 1234,           // WordPress post ID
  travel_id: 'TC-12345',        // Travel Compositor ID
  title: 'Land & Cruise Florida',
  source_url: 'https://flyendrive.online/reis/land-cruise-florida/',
  imported_at: new Date().toISOString()
};

// Insert in trips tabel of brand_travels koppeltabel
await supabase.from('trips').insert(selectedTravel);
```

---

## Configuratie in BOLT

BOLT moet weten welke WordPress site de catalogus host. Dit is de **TravelC Web Catalogus site** die in de Network Admin is ingesteld.

### Aanbevolen aanpak:

1. **Hardcoded URL** (simpel):
   ```javascript
   const CATALOG_API = 'https://flyendrive.online/wp-json/rbs-travel/v1/catalog';
   ```

2. **Configureerbaar per brand** (flexibel):
   - Sla de WordPress catalogus URL op in brand settings
   - Elke brand kan een andere bron hebben

---

## Samenvatting Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                        WORDPRESS                                 │
│  ┌─────────────┐    ┌──────────────────┐    ┌────────────────┐ │
│  │ Travel      │───▶│ Admin markeert   │───▶│ REST API       │ │
│  │ Compositor  │    │ voor catalogus   │    │ /catalog       │ │
│  │ Import      │    │ (_in_bolt_catalog)│    │                │ │
│  └─────────────┘    └──────────────────┘    └───────┬────────┘ │
└─────────────────────────────────────────────────────┼──────────┘
                                                      │
                                                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                          BOLT                                    │
│  ┌─────────────┐    ┌──────────────────┐    ┌────────────────┐ │
│  │ Fetch       │───▶│ Toon catalogus   │───▶│ User selecteert│ │
│  │ /catalog    │    │ met filters      │    │ reis           │ │
│  └─────────────┘    └──────────────────┘    └───────┬────────┘ │
│                                                      │          │
│  ┌─────────────┐    ┌──────────────────┐            │          │
│  │ Opslaan in  │◀───│ Koppel aan brand │◀───────────┘          │
│  │ Supabase    │    │                  │                       │
│  └─────────────┘    └──────────────────┘                       │
└─────────────────────────────────────────────────────────────────┘
```

---

## Checklist voor BOLT Implementatie

- [ ] Fetch `/catalog` endpoint
- [ ] Parse response en toon in UI
- [ ] Implementeer continent/land filters
- [ ] Toon reis preview (thumbnail, titel, prijs, nachten)
- [ ] "Preview" button opent `preview_url` in nieuw tabblad
- [ ] "Selecteer" button koppelt reis aan brand
- [ ] Sla `travel_id` en `wordpress_id` op voor toekomstig gebruik
- [ ] Optioneel: Fetch volledige details via `/ideas/{id}` endpoint

---

## Vragen?

De catalogus API is publiek toegankelijk (geen authenticatie nodig) omdat alleen reizen worden getoond die de admin expliciet heeft gemarkeerd.
