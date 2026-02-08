# 🚢 Cruises Module - API Update

## ✅ WAT IS TOEGEVOEGD:

De nieuwe **Cruises** module uit Travel Compositor is nu volledig geïntegreerd!

---

## 🔧 CHANGES OVERZICHT:

### **1. Import Functionaliteit** ✅

**File:** `includes/rbstravel-import.class.php`

**Wat is toegevoegd:**
```php
// Line 209: Cruise data import
$meta_fields['travel_cruises'] = isset($travel_details['cruises']) ? $travel_details['cruises'] : array();

// Line 115-117: Auto "Cruise" taxonomy
if (isset($travel_details['cruises']) && count($travel_details['cruises']) > 0) {
    $default_tour_types[] = 'Cruise';
}

// Line 250-259: Cruise images import
if (isset($travel_details['cruises']) && is_array($travel_details['cruises'])) {
    foreach($travel_details['cruises'] as $cruise) {
        // Import cruise images
    }
}
```

**Resultaat:**
- ✅ Cruises worden geïmporteerd vanuit Travel Compositor
- ✅ "Cruise" taxonomy wordt automatisch toegevoegd
- ✅ Cruise afbeeldingen worden geïmporteerd

---

### **2. REST API Extended** ✅

**File:** `includes/api-rest-endpoints.php`

**Nieuwe response fields:**
```json
{
  "id": 123,
  "title": "Middellandse Zee Cruise",
  "cruises": [
    {
      "id": "cruise_1",
      "cruiseData": {
        "shipName": "MS Europa",
        "cruiseLine": "Holland America Line",
        "cabinType": "Balcony",
        "deck": "8",
        "imageUrls": ["ship1.jpg", "cabin1.jpg"],
        "facilities": ["Pool", "Restaurant", "Spa"],
        "description": "Luxe cruise schip..."
      },
      "fromPort": {
        "name": "Barcelona",
        "country": "Spanje"
      },
      "toPort": {
        "name": "Rome",
        "country": "Italië"
      },
      "departureDate": "2024-06-15",
      "arrivalDate": "2024-06-22",
      "nights": 7,
      "price": {
        "amount": 1500,
        "currency": "EUR"
      }
    }
  ],
  "has_cruise": true,
  "tour_types": ["Cruise"]
}
```

---

## 🎯 CRUISE DATA STRUCTUUR:

### **Van Travel Compositor:**

```javascript
travel_details.cruises = [
  {
    id: "unique_cruise_id",
    
    // Cruise specifieke data
    cruiseData: {
      shipName: "MS Europa",           // Naam van het schip
      cruiseLine: "Holland America",   // Rederij
      cabinType: "Balcony",           // Type hut
      cabinNumber: "8042",            // Hut nummer
      deck: "8",                      // Dek nummer
      facilities: [...],              // Faciliteiten aan boord
      imageUrls: [...],               // Foto's schip/hut
      description: "...",             // Beschrijving
      shipInfo: {...},                // Extra schip info
    },
    
    // Vertrek haven
    fromPort: {
      name: "Barcelona",
      city: "Barcelona",
      country: "Spanje",
      coordinates: {...}
    },
    
    // Aankomst haven
    toPort: {
      name: "Rome (Civitavecchia)",
      city: "Rome",
      country: "Italië",
      coordinates: {...}
    },
    
    // Datums
    departureDate: "2024-06-15",
    arrivalDate: "2024-06-22",
    nights: 7,
    
    // Prijs
    price: {
      amount: 1500,
      currency: "EUR",
      priceType: "per person"
    },
    
    // Extra
    imageUrl: "main_image.jpg",      // Hoofd afbeelding
    remarks: "...",                   // Opmerkingen
  }
]
```

---

## 🚀 GEBRUIK IN FRONTEND:

### **Check of reis cruise bevat:**

```javascript
// API response
const travel = {
  id: 123,
  title: "Middellandse Zee Cruise",
  has_cruise: true,        // ← Makkelijk check!
  cruises: [...]
};

if (travel.has_cruise) {
  console.log('Deze reis bevat een cruise!');
  displayCruiseInfo(travel.cruises);
}
```

---

### **Display Cruise Info:**

```javascript
function displayCruiseInfo(cruises) {
  cruises.forEach(cruise => {
    const cruiseCard = `
      <div class="cruise-card">
        <h3>🚢 ${cruise.cruiseData.shipName}</h3>
        <p>Rederij: ${cruise.cruiseData.cruiseLine}</p>
        
        <div class="cruise-route">
          📍 ${cruise.fromPort.name} 
          → 
          📍 ${cruise.toPort.name}
        </div>
        
        <div class="cruise-details">
          🛏️ ${cruise.cruiseData.cabinType}
          🌙 ${cruise.nights} nachten
          💰 €${cruise.price.amount}
        </div>
        
        ${cruise.cruiseData.facilities ? `
          <div class="cruise-facilities">
            ${cruise.cruiseData.facilities.map(f => `<span class="badge">${f}</span>`).join('')}
          </div>
        ` : ''}
      </div>
    `;
    
    document.getElementById('cruise-container').innerHTML += cruiseCard;
  });
}
```

---

### **Filter op Cruise:**

```javascript
// Frontend filtering
const searchParams = new URLSearchParams({
  tour_type: 'cruise'  // ← Filter alleen cruises
});

fetch(`/wp-json/rbs-travel/v1/ideas?${searchParams}`)
  .then(res => res.json())
  .then(data => {
    // Alle cruise reizen
    console.log(data.ideas);
  });
```

---

## 🎨 TEMPLATE WEERGAVE:

### **In Travel Card:**

```javascript
function createTravelCard(idea) {
  const card = document.createElement('div');
  card.className = 'rbs-travel-card';
  
  // Show cruise badge
  const cruiseBadge = idea.has_cruise ? 
    '<span class="cruise-badge">🚢 Cruise</span>' : '';
  
  card.innerHTML = `
    <div class="rbs-card-image">
      <img src="${idea.image}" alt="${idea.title}">
      ${cruiseBadge}
      <div class="rbs-card-price-tag">€ ${idea.price}</div>
    </div>
    <div class="rbs-card-content">
      <h3>${idea.title}</h3>
      
      ${idea.has_cruise ? `
        <div class="cruise-info">
          <strong>🚢 ${idea.cruises[0].cruiseData.shipName}</strong>
          <p>${idea.cruises[0].fromPort.name} → ${idea.cruises[0].toPort.name}</p>
        </div>
      ` : ''}
      
      <p>${idea.excerpt}</p>
    </div>
  `;
  
  return card;
}
```

---

## 📊 TAXONOMIE AUTO-TAGGING:

**Bij import:**

```
Reis bevat cruise data?
  ↓ JA
Auto-add "Cruise" term aan taxonomy "tour-type"
  ↓
Zichtbaar in filters en admin
```

**In WordPress Admin:**
```
rbsTravel → Types → Cruise (automatisch aangemaakt)
```

**In Filters:**
```
GET /wp-json/rbs-travel/v1/filters

Response:
{
  "tour_types": [
    {
      "id": 5,
      "name": "Cruise",
      "slug": "cruise",
      "count": 12        ← Aantal cruise reizen
    }
  ]
}
```

---

## 🔍 FILTERING VOORBEELDEN:

### **Alleen Cruises:**
```
GET /wp-json/rbs-travel/v1/ideas?tour_type=cruise
```

### **Cruises naar Middellandse Zee:**
```
GET /wp-json/rbs-travel/v1/ideas?tour_type=cruise&location=middellandse-zee
```

### **Luxe Cruises (prijs > €2000):**
```
GET /wp-json/rbs-travel/v1/ideas?tour_type=cruise&min_price=2000
```

---

## 💡 EXTRA MOGELIJKHEDEN:

### **1. Cruise-Specifieke Filters:**

Kan toegevoegd worden:

```php
// In api-rest-endpoints.php
$ship_name = $request->get_param('ship_name');
$cruise_line = $request->get_param('cruise_line');
$cabin_type = $request->get_param('cabin_type');

if ($ship_name) {
    $meta_query[] = array(
        'key' => 'travel_cruises',
        'value' => $ship_name,
        'compare' => 'LIKE'
    );
}
```

**Filters:**
- Ship name (MS Europa, etc.)
- Cruise line (Holland America, etc.)
- Cabin type (Inside, Ocean View, Balcony, Suite)
- Facilities (Pool, Spa, Theater, etc.)
- Departure port
- Destination regions

---

### **2. Cruise Detail Page:**

**Template suggestions:**

```php
// single-rbs-travel-idea-cruise.php
<?php if (has_cruise($post->ID)): ?>
  <div class="cruise-details">
    <h2>🚢 Cruise Details</h2>
    
    <!-- Ship info -->
    <!-- Route map -->
    <!-- Cabin details -->
    <!-- Facilities grid -->
    <!-- Deck plan -->
  </div>
<?php endif; ?>
```

---

### **3. Cruise Comparison:**

```javascript
GET /wp-json/rbs-travel/v1/compare?ids=1,2,3

// Compare cruise features side-by-side
- Ship specifications
- Cabin types
- Facilities
- Routes
- Prices
```

---

## 📦 CRUISE DATA VELDEN BESCHIKBAAR:

**Standaard:**
- ✅ `cruises` - Alle cruise data
- ✅ `has_cruise` - Boolean check
- ✅ Ship naam
- ✅ Rederij
- ✅ Cabine type
- ✅ Havens (vertrek/aankomst)
- ✅ Datums
- ✅ Prijs
- ✅ Afbeeldingen
- ✅ Faciliteiten

**Taxonomy:**
- ✅ Auto "Cruise" term in tour-type

---

## 🧪 TESTEN:

### **1. Import Test:**
```
1. Ga naar WordPress Admin → rbsTravel → Remote Travels
2. Importeer reis met cruise module
3. Check of "Cruise" taxonomy is toegevoegd
4. Check post meta: travel_cruises
```

### **2. API Test:**
```
GET /wp-json/rbs-travel/v1/ideas/{id}

Verwacht in response:
- cruises: [array met cruise data]
- has_cruise: true
- tour_types: ["Cruise"]
```

### **3. Filter Test:**
```
GET /wp-json/rbs-travel/v1/ideas?tour_type=cruise

Verwacht: Alleen reizen met cruise taxonomy
```

---

## ✅ CHECKLIST:

**Import:**
- [x] Cruises data opslaan
- [x] Cruise images importeren
- [x] Auto "Cruise" taxonomy

**API:**
- [x] Cruises in response
- [x] has_cruise boolean
- [x] Filter op cruise type

**Next Steps:**
- [ ] Cruise-specifieke filters (ship, cabin type)
- [ ] Cruise detail template
- [ ] Route map visualization
- [ ] Deck plan viewer
- [ ] Cruise comparison feature

---

## 📸 VOORBEELD RESPONSE:

```json
{
  "ideas": [
    {
      "id": 456,
      "title": "7-daagse Middellandse Zee Cruise",
      "excerpt": "Geniet van een luxe cruise...",
      "price": 1899,
      "has_cruise": true,
      "cruises": [
        {
          "cruiseData": {
            "shipName": "MS Europa",
            "cruiseLine": "Holland America Line",
            "cabinType": "Balcony",
            "facilities": ["Pool", "Spa", "Theater", "Casino"]
          },
          "fromPort": {
            "name": "Barcelona",
            "country": "Spanje"
          },
          "toPort": {
            "name": "Rome",
            "country": "Italië"
          },
          "nights": 7
        }
      ],
      "tour_types": ["Cruise"],
      "locations": ["Middellandse Zee"]
    }
  ]
}
```

---

## 🎉 SAMENVATTING:

**✅ Cruise Module is LIVE!**

**Wat werkt:**
- Import cruise data vanuit Travel Compositor
- API toont cruise informatie
- Auto-tagging met "Cruise" taxonomy
- Filter op cruise reizen
- Cruise afbeeldingen import

**Ready for:**
- 🚢 Cruise listing pagina's
- 🔍 Cruise-specifieke filters
- 📄 Cruise detail templates
- 🗺️ Route visualisaties
- ⚖️ Cruise vergelijkingen

**Test het nu met een Travel Compositor import! 🚀**
