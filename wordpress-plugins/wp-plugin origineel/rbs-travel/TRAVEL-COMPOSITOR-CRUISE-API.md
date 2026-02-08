# 🚢 Travel Compositor - Cruise API Complete Reference

## 📋 TABLE OF CONTENTS

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [All Endpoints](#all-endpoints)
4. [Endpoint Details](#endpoint-details)
5. [Data Structures](#data-structures)
6. [Use Cases](#use-cases)
7. [Implementation Guide](#implementation-guide)

---

## 🎯 OVERVIEW

Travel Compositor Cruise API provides 8 endpoints for complete cruise management:
- Search & filter cruises
- Price calendars
- Ship details
- Port information
- Itineraries
- Cruise line characteristics

**Base URL:** `https://api.travelcompositor.com` (example)

---

## 🔐 AUTHENTICATION

All requests require authentication token in header:

```http
GET /cruise/{micrositeId}
Headers:
  auth-token: YOUR_API_TOKEN_HERE
  Accept: application/json
```

---

## 📡 ALL ENDPOINTS

### **1. Cruise Search**
```
GET /cruise/{micrositeId}
```
Main search endpoint with extensive filtering options.

**Parameters:**
- `region` - Filter by geographical region (40+ options)
- `month` - Filter by month (YYYY-MM format)
- `originPort` - Filter by departure port
- `nights` - Filter by duration
- `cruiseLine` - Filter by cruise line
- `ship` - Filter by specific ship
- `first` - Pagination start (default: 0)
- `limit` - Results per page (default: 20)

**Response:**
```json
{
  "cruiseDataSheet": [
    {
      "id": "cruise_12345",
      "shipId": "ship_harmony",
      "shipName": "Harmony of the Seas",
      "cruiseLine": "ROYAL_CARIBBEAN",
      "stars": 5,
      "selectedCategory": "Balcony",
      "year": "2024",
      "group": "Mediterranean",
      "cabin": "Balcony",
      "originPort": "Barcelona",
      "departure": "2024-06-15",
      "arrival": "2024-06-22",
      "nights": 7,
      "region": "WESTERN_MEDITERRANEAN",
      "month": "2024-06",
      "mandatory": false,
      "fixed": true
    }
  ]
}
```

---

### **2. Cruise Departures (Price Calendar)**
```
GET /cruise/{micrositeId}/{cruiseId}/departures
```
Returns price calendar for a specific cruise.

**Response:**
```json
{
  "departure": [
    {
      "id": "departure_001",
      "date": "2024-06-15",
      "prices": [
        {
          "group": "Inside",
          "price": {
            "amount": 1200.00,
            "currency": "EUR"
          }
        },
        {
          "group": "Balcony",
          "price": {
            "amount": 1800.00,
            "currency": "EUR"
          }
        }
      ]
    }
  ]
}
```

---

### **3. Cruise Itinerary**
```
GET /cruise/{cruiseId}/itinerary
```
Returns day-by-day route and destinations.

**Response:**
```json
{
  "id": "itinerary_123",
  "shipImage": "https://.../ship.jpg",
  "itinerarySteps": [
    {
      "day": 1,
      "destination": "Barcelona",
      "departure": "18:00",
      "arrival": "14:00"
    },
    {
      "day": 2,
      "destination": "Marseille",
      "departure": "20:00",
      "arrival": "08:00"
    },
    {
      "day": 3,
      "destination": "At Sea",
      "departure": null,
      "arrival": null
    }
  ]
}
```

---

### **4. Cruise Line Characteristics**
```
GET /cruise/{micrositeId}/cruiseLine?cruiseLine=ROYAL_CARIBBEAN&lang=NL
```
Returns what's included/excluded and special benefits.

**Supported Cruise Lines:**
- ROYAL_CARIBBEAN
- COSTA_CRUCEROS
- MSC
- CELEBRITY_CRUISES
- CARNIVAL
- HOLLAND (Holland America)
- NCL (Norwegian Cruise Line)
- SEABOURN
- PRINCESS
- CUNARD
- REGENT
- OCEANIA
- CELESTYAL
- AZAMARA_CRUISES
- AIDA
- TUI_CRUISES

**Response:**
```json
{
  "id": "ROYAL_CARIBBEAN",
  "included": [
    "All meals",
    "Entertainment shows",
    "Fitness center access",
    "Kids club"
  ],
  "excluded": [
    "Alcoholic beverages",
    "Specialty restaurants",
    "Shore excursions",
    "Spa treatments"
  ],
  "benefits": [
    {
      "name": "Crown & Anchor Society",
      "description": "Loyalty program with exclusive perks"
    },
    {
      "name": "FlowRider Surf Simulator",
      "description": "Unique onboard surf experience"
    }
  ]
}
```

---

### **5. Port Detail**
```
GET /cruise/port/{cruisePortId}?lang=NL
```
Returns detailed information about a specific port.

**Response:**
```json
{
  "id": "port_barcelona_123",
  "name": "Barcelona",
  "destination": "Spain",
  "latitude": 41.3851,
  "longitude": 2.1734,
  "description": "Barcelona is one of Europe's most vibrant cities...",
  "imageUrl": "https://.../barcelona.jpg"
}
```

---

### **6. All Ports**
```
GET /cruise/port?first=0&limit=100
```
Returns list of all available cruise ports.

**Response:**
```json
{
  "cruisePort": [
    {
      "id": "port_barcelona",
      "destination": "Spain",
      "latitude": 41.3851,
      "longitude": 2.1734
    },
    {
      "id": "port_miami",
      "destination": "USA",
      "latitude": 25.7617,
      "longitude": -80.1918
    }
  ]
}
```

---

### **7. Ship Detail**
```
GET /cruise/{micrositeId}/ship/{shipId}?lang=NL
```
Returns complete ship specifications and facilities.

**Response (extensive):**
```json
{
  "id": "ship_harmony_seas",
  "cruiseLine": "ROYAL_CARIBBEAN",
  "name": "Harmony of the Seas",
  "mainImage": "https://.../harmony.jpg",
  "stars": 5,
  
  "speed": 22,
  "length": 362,
  "beam": 66,
  "tonnage": 226963,
  "inaugurated": 2016,
  "renovated": 2019,
  "decksNumber": 16,
  
  "occupancy": 6780,
  "crew": 2100,
  "stateRooms": 2747,
  "interiorStateRooms": 1186,
  "exteriorStateRooms": 1561,
  
  "swimmingPools": 4,
  "lounges": 8,
  "restaurants": 20,
  
  "cinema": true,
  "library": true,
  "internet": true,
  "spa": true,
  "casino": true,
  "theater": true,
  "gym": true,
  "service24h": true,
  "nursery": true,
  
  "description": "Harmony of the Seas is...",
  
  "images": [
    "https://.../image1.jpg",
    "https://.../image2.jpg"
  ],
  
  "categories": [
    {
      "id": "cat_inside",
      "name": "Inside Cabin",
      "description": "Cozy interior cabin",
      "group": "Standard",
      "imageUrl": "https://.../inside.jpg"
    }
  ],
  
  "decks": [
    {
      "id": "deck_3",
      "name": "Deck 3 - Main Deck",
      "imageUrl": "https://.../deck3-plan.jpg"
    }
  ]
}
```

---

### **8. All Ships**
```
GET /cruise/{micrositeId}/ship?cruiseLine=ROYAL_CARIBBEAN&first=0&limit=100
```
Returns list of all ships, optionally filtered by cruise line.

**Response:**
```json
{
  "cruiseShip": [
    {
      "id": "ship_harmony_seas",
      "cruiseLine": "ROYAL_CARIBBEAN",
      "name": "Harmony of the Seas"
    },
    {
      "id": "ship_symphony_seas",
      "cruiseLine": "ROYAL_CARIBBEAN",
      "name": "Symphony of the Seas"
    }
  ]
}
```

---

## 🌍 REGIONS

Available region filters for cruise search:

```javascript
const CRUISE_REGIONS = [
  'ADRIATIC',              // Adriatische Zee
  'AFRICA',                // Afrika
  'ALASKA',                // Alaska
  'ASIA',                  // Azië
  'ARABIAN_SEA',          // Arabische Zee
  'AUSTRALIA',            // Australië
  'BAHAMAS',              // Bahamas
  'BERMUDA',              // Bermuda
  'CANADA',               // Canada
  'CANARY_ISLANDS',       // Canarische Eilanden
  'CARIBBEAN',            // Caribisch Gebied
  'CENTRAL_AMERICA',      // Midden-Amerika
  'EASTERN_MEDITERRANEAN', // Oostelijke Middellandse Zee
  'EASTERN_EUROPE',       // Oost-Europa
  'EUROPE',               // Europa
  'GALAPAGOS',            // Galapagos
  'GREEK_ISLANDS',        // Griekse Eilanden
  'HAWAII',               // Hawaï
  'INDIAN_OCEAN',         // Indische Oceaan
  'MEDITERRANEAN',        // Middellandse Zee
  'MEXICO',               // Mexico
  'MIDDLE_EAST',          // Midden-Oosten
  'NORTH_AMERICA',        // Noord-Amerika
  'NORTHERN_EUROPE',      // Noord-Europa
  'PACIFIC_NORTHWEST',    // Pacific Northwest
  'PANAMA_CANAL',         // Panamakanaal
  'PERSIAN_GULF',         // Perzische Golf
  'RED_SEA',              // Rode Zee
  'ROUND_WORLD',          // Rond de Wereld
  'SOUTH_AFRICA',         // Zuid-Afrika
  'SOUTH_AMERICA',        // Zuid-Amerika
  'SOUTH_PACIFIC',        // Zuid-Pacific
  'SOUTHERN_EUROPE',      // Zuid-Europa
  'TRANSATLANTIC',        // Transatlantisch
  'TRANSPACIFIC',         // Transpacific
  'WESTERN_EUROPE',       // West-Europa
  'WESTERN_MEDITERRANEAN' // Westelijke Middellandse Zee
];
```

---

## 🎯 USE CASES

### **Use Case 1: Cruise Search Page**
```
1. User: "Show me Mediterranean cruises in June"
   → API: GET /cruise/{id}?region=MEDITERRANEAN&month=2024-06
   
2. Display: List of 50 cruises
   
3. User: Filters by "7 nights" + "MSC"
   → API: GET /cruise/{id}?region=MEDITERRANEAN&month=2024-06&nights=7&cruiseLine=MSC
   
4. Display: Filtered list of 12 cruises
```

### **Use Case 2: Cruise Detail Page**
```
1. User clicks: "MSC Meraviglia - 7 nights"
   → Fetch cruise details
   
2. Load itinerary:
   → API: GET /cruise/{cruiseId}/itinerary
   → Display: Day-by-day route with map
   
3. Load departures:
   → API: GET /cruise/{micrositeId}/{cruiseId}/departures
   → Display: Calendar with prices per cabin type
   
4. Load ship info:
   → API: GET /cruise/{micrositeId}/ship/{shipId}
   → Display: Ship facilities, specs, photos
```

### **Use Case 3: Port Information**
```
1. User clicks: "Marseille" in itinerary
   → API: GET /cruise/port/{marseilleId}
   → Display: Port info, photo, description, coordinates
   
2. Show on map:
   → Use latitude/longitude to plot marker
   
3. Related cruises:
   → API: GET /cruise/{id}?originPort=marseille
   → Display: All cruises departing from or visiting Marseille
```

### **Use Case 4: Ship Fleet Overview**
```
1. User: "Show me all Royal Caribbean ships"
   → API: GET /cruise/{id}/ship?cruiseLine=ROYAL_CARIBBEAN
   → Display: List of all RC ships
   
2. User clicks: "Harmony of the Seas"
   → API: GET /cruise/{id}/ship/{harmonyId}
   → Display: Full ship profile with specs, facilities, deck plans
```

---

## 💻 IMPLEMENTATION GUIDE

### **WordPress REST API Endpoints**

```php
// Register cruise endpoints
function rbs_register_cruise_endpoints() {
    // Search cruises
    register_rest_route('rbs-travel/v1', '/cruises', [
        'methods' => 'GET',
        'callback' => 'rbs_get_cruises',
        'permission_callback' => '__return_true'
    ]);
    
    // Get cruise departures (price calendar)
    register_rest_route('rbs-travel/v1', '/cruises/(?P<id>[^/]+)/departures', [
        'methods' => 'GET',
        'callback' => 'rbs_get_cruise_departures',
        'permission_callback' => '__return_true'
    ]);
    
    // Get cruise itinerary
    register_rest_route('rbs-travel/v1', '/cruises/(?P<id>[^/]+)/itinerary', [
        'methods' => 'GET',
        'callback' => 'rbs_get_cruise_itinerary',
        'permission_callback' => '__return_true'
    ]);
    
    // Get all ports
    register_rest_route('rbs-travel/v1', '/ports', [
        'methods' => 'GET',
        'callback' => 'rbs_get_ports',
        'permission_callback' => '__return_true'
    ]);
    
    // Get port detail
    register_rest_route('rbs-travel/v1', '/ports/(?P<id>[^/]+)', [
        'methods' => 'GET',
        'callback' => 'rbs_get_port',
        'permission_callback' => '__return_true'
    ]);
    
    // Get all ships
    register_rest_route('rbs-travel/v1', '/ships', [
        'methods' => 'GET',
        'callback' => 'rbs_get_ships',
        'permission_callback' => '__return_true'
    ]);
    
    // Get ship detail
    register_rest_route('rbs-travel/v1', '/ships/(?P<id>[^/]+)', [
        'methods' => 'GET',
        'callback' => 'rbs_get_ship',
        'permission_callback' => '__return_true'
    ]);
    
    // Get cruise line characteristics
    register_rest_route('rbs-travel/v1', '/cruise-lines/(?P<line>[^/]+)', [
        'methods' => 'GET',
        'callback' => 'rbs_get_cruise_line',
        'permission_callback' => '__return_true'
    ]);
}
add_action('rest_api_init', 'rbs_register_cruise_endpoints');
```

### **Database Schema**

```sql
-- Cruise post type (already exists as rbs-travel-idea)
-- Add cruise-specific meta fields:

-- Meta keys for cruises:
travel_cruises             -- Array of cruise data
travel_cruise_id           -- External cruise ID
travel_cruise_ship_id      -- Ship ID
travel_cruise_ship_name    -- Ship name
travel_cruise_line         -- Cruise line
travel_cruise_region       -- Region
travel_cruise_origin_port  -- Departure port
travel_cruise_nights       -- Duration in nights
travel_cruise_departures   -- Available departure dates with prices
travel_cruise_itinerary    -- Day-by-day itinerary
travel_cruise_categories   -- Available cabin categories

-- Optional: Separate post types
CREATE TABLE wp_rbs_cruise_ports (
    id BIGINT PRIMARY KEY,
    port_id VARCHAR(255),
    name VARCHAR(255),
    destination VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    description TEXT,
    image_url VARCHAR(500)
);

CREATE TABLE wp_rbs_cruise_ships (
    id BIGINT PRIMARY KEY,
    ship_id VARCHAR(255),
    cruise_line VARCHAR(100),
    name VARCHAR(255),
    occupancy INT,
    crew INT,
    stars INT,
    specifications JSON,
    facilities JSON,
    images JSON
);
```

### **Caching Strategy**

```php
// Cache frequently requested data
function rbs_get_cached_ports() {
    $cache_key = 'rbs_cruise_ports_all';
    $ports = get_transient($cache_key);
    
    if (false === $ports) {
        $ports = rbs_fetch_ports_from_api();
        set_transient($cache_key, $ports, DAY_IN_SECONDS);
    }
    
    return $ports;
}

// Clear cache on demand
function rbs_clear_cruise_cache() {
    delete_transient('rbs_cruise_ports_all');
    delete_transient('rbs_cruise_ships_all');
    delete_transient('rbs_cruise_lines_all');
}
```

---

## 🎨 FRONTEND TEMPLATES

### **Cruise Search Template**

```php
// Template: page-cruise-search.php
<?php get_header(); ?>

<div class="cruise-search-page">
    <!-- Search Filters -->
    <div class="cruise-filters">
        <select id="region">
            <option value="">Alle regio's</option>
            <option value="CARIBBEAN">Caribbean</option>
            <option value="MEDITERRANEAN">Middellandse Zee</option>
        </select>
        
        <input type="month" id="month" placeholder="Maand">
        <input type="text" id="port" list="ports" placeholder="Vertrekhaven">
        <select id="nights">
            <option value="">Alle duur</option>
            <option value="7">7 nachten</option>
            <option value="14">14 nachten</option>
        </select>
        
        <button id="search-cruises">Zoek Cruises</button>
    </div>
    
    <!-- Results -->
    <div id="cruise-results"></div>
    
    <!-- Pagination -->
    <div id="cruise-pagination"></div>
</div>

<script>
async function searchCruises() {
    const filters = {
        region: document.getElementById('region').value,
        month: document.getElementById('month').value,
        originPort: document.getElementById('port').value,
        nights: document.getElementById('nights').value
    };
    
    const params = new URLSearchParams(filters);
    const response = await fetch(`/wp-json/rbs-travel/v1/cruises?${params}`);
    const data = await response.json();
    
    renderCruiseResults(data.cruiseDataSheet);
}
</script>

<?php get_footer(); ?>
```

---

## 🔄 DATA FLOW

```
User Action → WordPress API → Travel Compositor API → Response → WordPress → Frontend

Example:
1. User searches "Caribbean cruises"
2. Frontend calls: GET /wp-json/rbs-travel/v1/cruises?region=CARIBBEAN
3. WordPress calls: GET travel-compositor.com/cruise/{id}?region=CARIBBEAN
4. Travel Compositor returns cruise data
5. WordPress caches and formats data
6. WordPress API returns to frontend
7. Frontend renders cruise cards
```

---

## ✅ CHECKLIST

**Implementation Checklist:**
- [ ] Register all REST API endpoints
- [ ] Create cache system for ports/ships
- [ ] Build cruise search template
- [ ] Build cruise detail template
- [ ] Build ship detail template
- [ ] Build port detail template
- [ ] Implement price calendar
- [ ] Implement interactive maps
- [ ] Test all endpoints
- [ ] Add error handling
- [ ] Optimize performance

---

## 📚 RESOURCES

**Documentation:**
- Travel Compositor API Docs: [Link]
- WordPress REST API: https://developer.wordpress.org/rest-api/
- Leaflet Maps: https://leafletjs.com/

**Support:**
- Travel Compositor Support: support@travelcompositor.com
- WordPress Codex: https://codex.wordpress.org/

---

## 🎉 CONCLUSION

With these 8 endpoints, you have everything needed to build a complete cruise booking platform:

✅ Search & filter cruises
✅ Display prices & availability
✅ Show detailed itineraries
✅ Present ship information
✅ Provide port details
✅ Compare cruise lines
✅ Interactive maps
✅ Cabin selection

**Ready to implement! 🚀**
