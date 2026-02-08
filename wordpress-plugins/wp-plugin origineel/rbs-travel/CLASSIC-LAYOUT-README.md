# 🎨 Classic Tour Detail Layout

**Status:** KLAAR ✅  
**File:** `templates/frontend/single-rbs-travel-idea-classic.php`  
**Gebaseerd op:** Reference design images

---

## 📐 LAYOUT STRUCTUUR

### **1. PHOTO HEADER** ✅
- **4 foto's in grid** (2x2) of **1 grote foto**
- Automatisch uit destinations + hotels
- Click voor lightbox
- Photo count overlay (bijv. "+12 foto's")

### **2. TITLE BAR** ✅
- **Links:** Titel + Locatie
- **Rechts:** 3 badges:
  - 💰 **From:** Prijs (uit API)
  - ⏱️ **Duration:** Aantal dagen (uit API)
  - 🎯 **Tour Type:** Adventure/Cruise/etc. (uit themes of handmatig)

### **3. INTRO SECTION** ✅
- **"Explore Tours"** heading
- Post content (beschrijving)

### **4. TOUR AMENITIES / REISELEMENTEN** ✅
- **Icons voor wat er in zit:**
  - ✈️ Vliegtickets (als transports aanwezig)
  - 🚐 Transfers (als transfers aanwezig)
  - 🚢 Cruise (als cruises aanwezig)
  - 🏨 Hotels (als hotels aanwezig)
  - 🚗 Huurauto (als cars aanwezig)
  - 🍽️ Restaurant (standaard)
- Grid layout (3 kolommen)

### **5. TOUR PLAN (ACCORDION)** ✅
- **Day-by-day accordion**
- Format: "Day 1st", "Day 2nd", "Day 3rd - 6th", etc.
- **Per dag:**
  - Foto (links)
  - Details (rechts)
  - Beschrijving
  - Info items (land, nachten, sterren)
- **Ondersteunt:**
  - Destinations
  - Cruises (met cruise destinations als tags)
  - Hotels
- **Gedrag:** 1 dag open tegelijk (accordion)

### **6. INCLUDED/EXCLUDED** ✅
- 2 kolommen
- Check lists
- Dummy data (handmatig aanpasbaar)

### **7. SIDEBAR (RECHTS)** ✅

#### **Booking Card:**
- **Form fields:**
  - From (date)
  - Time (time)
  - Tickets (select)
- **Buttons:**
  - 🛒 **Book Now** (groen, naar Travel Compositor)
  - 📧 **Info aanvragen** (wit)
  - ✏️ **Reis aanpassen** (wit)

#### **Tour Information Card:**
- **Info items:**
  - 👥 Max Guests (uit API)
  - 👤 Min Guests (uit API)
  - 🎂 Min Age (12+, handmatig)
  - ✈️ Tour Location (land uit destinations)
  - 🌍 Languages Support (Nederlands, English)

---

## 🎨 DESIGN SPECS:

### **Kleuren:**
```css
Primary: #28a745 (Groen voor buttons)
Secondary: #6c757d (Grijs)
Text: #212529 (Donkergrijs)
Border: #dee2e6 (Lichtgrijs)
Background: #f8f9fa (Off-white)
```

### **Layout:**
- **Main:** 1200px max-width
- **Grid:** Content (1fr) + Sidebar (350px)
- **Gaps:** 40px tussen secties

### **Typography:**
- System font stack
- H1: 32px
- H2: 28px
- H3: 20px
- Body: 14-16px

---

## 📱 RESPONSIVE:

- **Desktop:** Sidebar rechts, photo grid 4 kolommen
- **Tablet:** Sidebar onder content, photo grid 2 kolommen
- **Mobile:** Alles verticaal, 1 kolom

---

## ✅ WAT WERKT:

- [x] 4-foto header grid
- [x] Single photo optie
- [x] Title bar met badges
- [x] Tour amenities (automatisch uit API)
- [x] Day-by-day accordion
  - [x] Destinations
  - [x] Cruises met tags
  - [x] Hotels
- [x] Included/Excluded
- [x] Booking form sidebar
- [x] Tour information sidebar
- [x] Responsive design
- [x] Lightbox voor foto's

---

## 🔧 HANDMATIGE VELDEN:

**Deze velden zijn nu dummy/default, maar kunnen handmatig worden ingevuld:**

1. **Tour Type** (regel 307):
   - Nu: Uit `travel_themes[0]` of 'Adventure'
   - TODO: Custom field voor tour type

2. **Included/Excluded** (regel 393):
   - Nu: Hardcoded lijst
   - TODO: Custom fields voor included/excluded items

3. **Min Age** (regel 531):
   - Nu: "12+"
   - TODO: Custom field

4. **Languages Support** (regel 541):
   - Nu: "Nederlands, English"
   - TODO: Custom field voor talen

5. **Amenities** (regel 331):
   - Nu: Automatisch + Restaurant (altijd)
   - TODO: Extra handmatige amenities

---

## 📦 FILES:

```
/templates/frontend/
├── single-rbs-travel-idea-classic.php       (Main template)
├── single-rbs-travel-idea.php               (Active - copy van classic)
├── single-rbs-travel-idea-modern.php        (Alternatief: Modern)
└── /partials/
    ├── tour-plan-accordion.php              (Accordion component)
    └── itinerary-timeline.php               (Timeline component - voor modern)
```

---

## 🚀 VOLGENDE STAPPEN:

### **Custom Fields toevoegen:**
```php
// In rbstravel-meta.class.php:
'travel_tour_type' => __('Tour Type', 'rbs-travel'),
'travel_min_age' => __('Minimum Age', 'rbs-travel'),
'travel_languages' => __('Languages', 'rbs-travel'),
'travel_included' => __('Included Items', 'rbs-travel'),
'travel_excluded' => __('Excluded Items', 'rbs-travel'),
'travel_amenities' => __('Extra Amenities', 'rbs-travel'),
```

### **Template Switcher:**
```php
// In settings of per post:
$template_style = get_option('travel_detail_template', 'classic');
// Opties: 'classic', 'modern', 'minimal'
```

---

## 💡 VERGELIJKING MET MODERN TEMPLATE:

| Feature | Classic | Modern |
|---------|---------|--------|
| **Header** | 4-photo grid | Hero + overlay |
| **Layout** | 2-column | Single column |
| **Itinerary** | Accordion | Vertical timeline |
| **Sidebar** | Always visible | Sticky |
| **Style** | Clean & structured | Visual & spacious |
| **Best for** | Professional agencies | Photo-heavy trips |

---

## ✅ HOTEL DATA FIXES (Dec 8, 2024):

### **Probleem:**
Hotels hadden `day` en `nights` in plaats van `fromDay`/`toDay`, waardoor ze als "Day 0th" verschenen.

### **Oplossing:**
```php
// Hotels now calculate fromDay/toDay from day + nights
$fromDay = isset($hotel['day']) ? $hotel['day'] : 0;
$toDay = $fromDay + (isset($hotel['nights']) ? $hotel['nights'] : 0);
```

### **Image Handling:**
Hotels gebruiken `images[]['url']` structuur in plaats van `imageUrls`:
```php
// Try both structures
if (!empty($hotel_data['imageUrls'][0])) {
    $hotel_image = $hotel_data['imageUrls'][0];
} elseif (!empty($hotel_data['images'][0]['url'])) {
    $hotel_image = $hotel_data['images'][0]['url'];
}
```

### **Stars/Category:**
Hotels hebben `category: "S4"` voor 4-sterren:
```php
// Extract stars from category (S4 = 4 stars)
$stars = intval(preg_replace('/[^0-9]/', '', $hotel_data['category']));
```

### **City/Location:**
Hotels hebben `destination['name']` voor locatie:
```php
$city = $hotel_data['destination']['name']; // "Miami FL"
```

## 🐛 KNOWN ISSUES:

- [ ] Cruise ship images niet beschikbaar (API 404)
- [ ] Included/Excluded is dummy data
- [x] Hotels fromDay/toDay - FIXED ✅
- [x] Hotels images - FIXED ✅
- [x] Hotels stars - FIXED ✅
- [x] Hotels city - FIXED ✅

---

**Gemaakt:** 8 December 2024  
**Door:** Cascade + Alex  
**Versie:** 1.0 (Classic Layout)  
**Based on:** Reference design images
