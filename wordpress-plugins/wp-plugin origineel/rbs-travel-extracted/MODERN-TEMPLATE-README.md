# 🎨 Modern Card-based Travel Template

**Status:** EERSTE VERSIE KLAAR ✅  
**File:** `templates/frontend/single-rbs-travel-idea-modern.php`

---

## 📐 LAYOUT STRUCTUUR

### **VISUAL CARD-BASED (2x C)**
- **Volgorde:** Visueel / Photo heavy
- **Style:** Modern card-based

---

## 🧩 ALLE SECTIES:

### **1. HERO SECTION** ✅
- Grote hero image (70vh)
- Gradient overlay
- Title + Subtitle
- Floating info card (Prijs, Nachten, Vertrek)

### **2. PHOTO GALLERY** ✅
- Masonry grid (max 12 foto's)
- Destinations + Hotels
- Lightbox bij klik
- Hover effects

### **3. QUICK FACTS CARDS** ✅
- Bestemmingen count
- Hotels count
- Cruise nachten
- Vluchten count
- Huurauto count

### **4. BESCHRIJVING** ✅
- Post content
- In card format

### **5. DAG-VOOR-DAG TIMELINE** ✅
- Verticale timeline met dots
- Color-coded per type:
  - 🟢 Groen: Destinations
  - 🔵 Blauw: Cruises
  - 🟠 Oranje: Hotels
- Foto's inline
- Sub-items voor cruise ports
- fromDay/toDay labels

### **6. ACCORDION DETAILS** ✅
- Hotels met foto's + facilities
- Bestemmingen met beschrijving + foto's
- Vluchten met details
- Uitklapbaar (alleen 1 open tegelijk)

### **7. CTA SECTION** ✅
- Grote call-to-action
- Gradient background
- Link naar Travel Compositor

---

## 🎨 DESIGN SPECS:

### **Kleuren:**
```css
Primary: #2196F3 (Blauw)
Success: #4CAF50 (Groen)
Accent: #FF9800 (Oranje)
Text: #333333
Background: #F8F9FA
```

### **Cards:**
- Border-radius: 12px
- Shadow: 0 2px 8px rgba(0,0,0,0.08)
- Hover: Lift + shadow

### **Typography:**
- System font stack
- H1: 48px bold
- H2: 36px bold
- H3: 24px semibold
- Body: 16px

---

## 📱 RESPONSIVE:

- Desktop: Full experience
- Tablet: 2-kolom → 1-kolom
- Mobile: Compacte versie
  - Kleinere hero
  - Verticale info cards
  - Kleinere timeline dots

---

## 🔧 GEBRUIK:

### **Template activeren:**
1. Upload plugin met nieuwe template
2. Navigeer naar de reis detail pagina
3. Template wordt automatisch gebruikt

### **Template switcher (TODO):**
```php
// In de toekomst:
Settings::GetOption('travel_detail_template', 'modern');
// Opties: 'classic', 'modern', 'minimal', 'luxury'
```

---

## ✅ WAT WERKT:

- [x] Hero met floating info
- [x] Photo gallery met lightbox
- [x] Quick facts cards
- [x] Beschrijving
- [x] Timeline itinerary
  - [x] Destinations
  - [x] Cruises met ports
  - [x] Hotels
- [x] Accordion details
  - [x] Hotels
  - [x] Bestemmingen
  - [x] Vluchten
- [x] CTA section
- [x] Responsive design
- [x] Hover effects
- [x] Card shadows

---

## 🚧 TODO (Volgende Versies):

### **Template Features:**
- [ ] Cars section in accordion
- [ ] Transfers section
- [ ] Map integration (Google Maps)
- [ ] Reviews/testimonials section
- [ ] Similar trips carousel
- [ ] Share buttons
- [ ] Print-friendly version

### **Timeline Verbeteringen:**
- [ ] Hotels koppelen aan destinations
- [ ] Transports als timeline items
- [ ] Day-by-day activities
- [ ] Weather info per destination
- [ ] Best time to visit

### **Photo Features:**
- [ ] Lazy loading
- [ ] Progressive images
- [ ] Image optimization
- [ ] Full-screen gallery mode
- [ ] Image captions

### **Interactivity:**
- [ ] Sticky CTA button (mobile)
- [ ] Progress indicator (scroll)
- [ ] Smooth scroll to sections
- [ ] Whatsapp share button
- [ ] Email inquiry form

---

## 🎨 ANDERE TEMPLATES (TOEKOMST):

### **CLASSIC LAYOUT**
- Traditional 2-column
- Sidebar met info
- Tabs voor secties
- Less visual, more text

### **MINIMAL LAYOUT**
- Ultra-clean
- Monochrome
- Typography focus
- Subtle animations

### **LUXURY LAYOUT**
- Full-width hero slider
- Elegant transitions
- Gold accents
- Video backgrounds

---

## 📦 FILES:

```
/templates/frontend/
├── single-rbs-travel-idea-modern.php  (Main template)
└── /partials/
    └── itinerary-timeline.php         (Timeline component)
```

---

## 🐛 KNOWN ISSUES:

- [ ] Hotels hebben geen fromDay/toDay (moet berekend worden)
- [ ] Transport/Car dates moeten omgerekend worden naar dagen
- [ ] Cruise ship images niet beschikbaar (API 404)

---

## 💡 NOTES:

- **Accordion:** Alleen 1 open tegelijk voor rustige UX
- **Colors:** Easy theme-able via CSS variables
- **Photos:** Max 12 in gallery om performance te waarborgen
- **Timeline:** Sorted by fromDay automatisch
- **Mobile:** Focus op vertikaal scrollen

---

**Gemaakt:** 8 December 2024  
**Door:** Cascade + Alex  
**Versie:** 1.0 (Modern Visual)
