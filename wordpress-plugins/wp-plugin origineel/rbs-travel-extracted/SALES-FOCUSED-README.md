# 🎯 Sales-Focused Travel Detail Template

**Status:** COMPLETE ✅  
**File:** `single-rbs-travel-idea-sales.php`  
**Doel:** Maximale conversie door inspireren + informeren

---

## 🎨 DESIGN FILOSOFIE:

**Hoofdweergave:** Clean, overzichtelijk, inspirerend  
**Detail Panels:** Alle info, 60+ foto's, complete data

---

## ✨ NIEUWE FEATURES:

### **1. 📅 GROUPED DAYS**
Destination + Hotel + Cruise worden **samen gegroepeerd** per dag range:

**Was:**
- Day 1st - 3rd (Miami destination)
- Day 1st - 3rd (Embassy Suites hotel) ← DUPLICATE!

**Nu:**
- **Day 1st - 3rd** 
  - 📍 Miami FL (kort + foto)
  - 🏨 Embassy Suites (kort + foto)
  - [Bekijk Details →] buttons

---

### **2. 🔄 SLIDING DETAIL PANEL**

**Click "Bekijk Details" → Panel slide-in van rechts met:**

#### **Voor Hotels:**
- 📸 **Photo Gallery Carousel** (alle 60 foto's!)
  - ← → navigatie
  - Thumbnails onderaan
  - Counter "1 / 60"
  - Click thumbnails voor direct naar foto
  
- 📝 **Volledige Beschrijving**
  - Lange tekst, niet ingekort

- ℹ️ **Hotel Info**
  - Locatie, adres, telefoon, keten

- 🏊 **Faciliteiten Grid** (top 12 faciliteiten)
  - Zwembad, Restaurant, WiFi, Parking
  - Gym, Bar, 24h Receptie, etc.
  - Gerangschikt op priority

- ⭐ **Reviews**
  - Booking.com: 8.1 (2146 reviews)
  - Tripadvisor: 3.5 (118 reviews)  
  - Expedia: 8.2 (1947 reviews)

#### **Voor Destinations:**
- 📸 Photo Gallery (alle destination foto's)
- 📝 Volledige beschrijving
- 🌍 Land info

#### **Voor Cruises:**
- 📝 Cruise Informatie
  - Embark/Disembark dates
  - Nachten
  - Hut type
  - Category

- ⚓ **Cruise Bestemmingen**
  - Per haven een kaartje
  - Haven naam
  - Beschrijving

---

### **3. 🛒 CTUS OVERAL**

**In Detail Panel (sticky bottom):**
- 🛒 **Boek Deze Reis** (primaire green button)
- 📧 **Meer Info** (secondary white button)

**In Sidebar:**
- 🛒 **Boek Nu** 
- 📧 **Info Aanvragen**
- ✏️ **Reis Aanpassen**

---

## 📐 LAYOUT STRUCTUUR:

### **Photo Header** (4-foto grid) ✅
- Destinations + Hotels images
- Photo count overlay "+56 foto's"

### **Title Bar** ✅  
- Titel + Locatie
- 3 badges: Prijs, Dagen, Type

### **Main Content** ✅

#### **Left Column:**
1. **Intro Section**
   - "Ontdek deze Reis"
   - Post content

2. **Grouped Day Cards**
   - Green header: "Day 1st - 3rd"
   - White body: Items met foto + snippet
   - "Bekijk Details →" buttons

#### **Right Sidebar:**
- **Booking Card** (sticky)
  - Vertrekdatum selector
  - Aantal personen
  - Boek Nu button
  - Info/Aanpassen buttons

---

## 🎯 SALES OPTIMALISATIE:

### **Visueel Aantrekkelijk:**
- ✅ Grote, mooie foto's
- ✅ Clean whitespace
- ✅ Smooth animations
- ✅ Modern cards met shadow/hover effects

### **Informatie Compleet:**
- ✅ Korte snippets in overzicht (150 chars)
- ✅ Volledige info in detail panels
- ✅ **ALLE** 60 hotel foto's beschikbaar
- ✅ Faciliteiten, reviews, specs

### **Conversie Gedreven:**
- ✅ CTAs op elke plek
- ✅ Sticky booking card
- ✅ "Boek Nu" altijd zichtbaar
- ✅ Multiple request options

---

## 🔧 TECHNISCHE DETAILS:

### **Grouping Logic:**
```php
// Group items by day range key (fromDay-toDay)
$grouped_days[$key] = [
    'fromDay' => 1,
    'toDay' => 3,
    'items' => [destination, hotel, cruise]
];
```

### **Sliding Panel:**
```css
position: fixed;
right: -600px; /* Hidden */
transition: right 0.3s ease;

.active {
    right: 0; /* Visible */
}
```

### **Photo Gallery:**
```javascript
currentGalleryImages = [url1, url2, ..., url60];
currentGalleryIndex = 0;

galleryNext() → index++
galleryPrev() → index--
galleryGoTo(i) → index = i
```

---

## 📱 RESPONSIVE:

- **Desktop:** 2-column layout, 600px detail panel
- **Tablet:** Single column, sidebar moves down
- **Mobile:** 
  - Full-width detail panel
  - Stacked day items
  - Single column photo grid

---

## 📊 DATA GEBRUIKT:

### **Hotels:**
```php
[hotelData][images] → 60 foto's met [url], [width], [height]
[hotelData][description] → Volledige tekst
[hotelData][facilities][otherFacilities] → 30+ faciliteiten
[hotelData][ratings] → Reviews van 3 platforms
[hotelData][destination][name] → Locatie
[hotelData][address] → Adres
[hotelData][phoneNumber] → Telefoon
[hotelData][chain] → Hilton, Marriott, etc.
[hotelData][category] → "S4" = 4 sterren
[day] + [nights] → Day range
```

### **Destinations:**
```php
[imageUrls] → Meerdere foto's
[description] → Volledige tekst
[country] → Land
[fromDay] + [toDay] → Day range
```

### **Cruises:**
```php
[embarkDate] + [disembarkDate] → Dates
[nights] → Aantal nachten
[cabin] → Hut type
[group] → Category
[destinations] → Array van havens
[fromDay] + [toDay] → Day range
```

---

## ✅ ALLE PROBLEMEN OPGELOST:

| Probleem | Oplossing |
|----------|-----------|
| 2x Day 1st-3rd (duplicate) | ✅ Grouped days |
| Hotel info te weinig | ✅ Detail panel met ALLES |
| Hotel foto's missen | ✅ Gallery met 60 foto's |
| Geen faciliteiten | ✅ Grid met 12 facilities |
| Geen reviews | ✅ Booking/Expedia/Tripadvisor |
| Cruise info te weinig | ✅ Detail panel |
| Geen haven beschrijvingen | ✅ Port cards |
| Geen hut info | ✅ Cabin + Category |
| Niet sales-focused | ✅ CTAs overal |

---

## 🚀 USAGE:

**Upload plugin ZIP → Reis pagina openen**

### **Als bezoeker zie je:**
1. Mooie 4-foto header
2. Titel + badges (prijs, dagen, type)
3. Intro tekst
4. **Day 1st - 3rd** card met:
   - Miami destination (snippet + foto)
   - Embassy Suites hotel (snippet + foto)
   - "Bekijk Details →" buttons

5. **Click "Bekijk Hotel Details" →**
   - Panel slide-in van rechts
   - Carousel met 60 hotel foto's
   - Volledige beschrijving
   - Faciliteiten grid
   - Reviews
   - Sticky CTAs onderaan

6. **Close panel → terug naar overzicht**

7. **Sidebar rechts:** Booking form altijd zichtbaar

---

## 🎨 KLEURENSCHEMA:

```css
--primary: #28a745 (Groen voor CTAs)
--primary-dark: #218838 (Hover)
--text: #212529 (Donkergrijs)
--text-light: #6c757d (Lichtgrijs voor snippets)
--border: #dee2e6 (Borders)
--bg-light: #f8f9fa (Cards background)
--shadow: 0 2px 8px rgba(0,0,0,0.1)
--shadow-lg: 0 4px 20px rgba(0,0,0,0.15) (Hover)
```

---

## 📦 FILES:

```
/templates/frontend/
├── single-rbs-travel-idea-sales.php        (Main template - NEW!)
├── single-rbs-travel-idea.php               (Active - copy van sales)
└── /partials/
    └── tour-plan-grouped.php                (Grouped day component - NEW!)
```

---

## 🎯 CONVERSIE STRATEGIE:

### **Stap 1: Inspireren**
- Mooie foto's trekken aandacht
- Korte snippets maken nieuwsgierig
- "Bekijk Details →" button nodigt uit

### **Stap 2: Informeren**
- Detail panel geeft ALLE info
- Geen vragen onbeantwoord
- Faciliteiten, reviews, specs

### **Stap 3: Converteren**
- CTAs op elke plek
- "Boek Nu" altijd zichtbaar
- Multiple request options
- Sticky booking card

---

## 🔮 TOEKOMSTIGE VERBETERINGEN:

- [ ] Map integratie in detail panel
- [ ] Video's van hotels/destinations
- [ ] Live availability check
- [ ] Price calculator
- [ ] Wishlist / Save for later
- [ ] Share buttons (WhatsApp, Email)
- [ ] Print-friendly versie
- [ ] Testimonials section

---

**Gemaakt:** 8 December 2024  
**Door:** Cascade + Alex  
**Versie:** 2.0 (Sales-Focused)  
**Doel:** Maximale conversie door inspireren + informeren

---

## 🎉 RESULTAAT:

**Van:** Simpel accordion met minimale info  
**Naar:** Modern sales platform met:
- ✅ Grouped overzicht
- ✅ 60 hotel foto's in carousel
- ✅ Complete faciliteiten lijst
- ✅ Reviews van 3 platforms
- ✅ Cruise havenbeschrijvingen
- ✅ Hut info
- ✅ CTAs overal
- ✅ Sliding panels
- ✅ Modern design

**Dit verkoopt reizen!** 🚀✨
