# 🏭 Theme Factory - Styling Opties Plan

## 🎯 Concept:

**Basis Systeem** (Huidige plugin) = Universeel, werkt overal
**Theme Factory** = Per theme custom layouts & styling opties

---

## 💡 Voorgestelde Styling Opties:

### **1. Layout Opties** ✅

#### **Grid View (Verticaal)**
```
┌────┐ ┌────┐ ┌────┐
│Img │ │Img │ │Img │
│Info│ │Info│ │Info│
└────┘ └────┘ └────┘
```

**Instellingen:**
- Aantal kolommen: 2, 3, 4
- Card hoogte: Auto, Fixed
- Gap size: Small, Medium, Large

#### **List View (Horizontaal)** ✅
```
┌──────────────────┐
│Img│ Title│Price│→│
├──────────────────┤
│Img│ Title│Price│→│
└──────────────────┘
```

**Instellingen:**
- Image positie: Left, Right
- Image grootte: Small, Medium, Large
- Content alignment: Top, Center, Bottom

---

### **2. Card Styling Opties**

#### **Modern (Huidige)** ✅
- Rounded corners (20px)
- Shadow on hover
- Smooth animations
- Gradient price tag

#### **Classic**
- Square corners
- Border instead of shadow
- Subtle hover
- Solid color price tag

#### **Minimal**
- Geen shadow
- Thin borders
- Small rounded corners
- Text-only price

#### **Luxury**
- Gold accents
- Large shadows
- Fancy fonts
- Gradient overlays

---

### **3. Color Schemes**

**Per Theme Aanpasbaar:**

```php
// Voorbeeld: GoWild Theme
$color_scheme = [
    'primary' => '#667eea',      // Buttons, links
    'secondary' => '#764ba2',    // Accents
    'price_bg' => '#667eea',     // Price tag background
    'card_hover' => '#f7fafc',   // Card hover state
    'text_primary' => '#2d3748', // Titles
    'text_secondary' => '#718096' // Descriptions
];

// Voorbeeld: Tripex Theme  
$color_scheme = [
    'primary' => '#066168',
    'secondary' => '#85D200',
    // etc...
];
```

---

### **4. Typography Opties**

**Font Pairings:**

```css
/* Modern (Huidige) */
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto;

/* Classic Serif */
font-family: 'Playfair Display', Georgia, serif;

/* Bold Sans */
font-family: 'Montserrat', 'Helvetica Neue', sans-serif;

/* Elegant */
font-family: 'Cormorant Garamond', 'Times New Roman', serif;
```

**Sizes:**
- Title: 1.5rem, 1.75rem, 2rem
- Body: 0.9rem, 1rem, 1.1rem
- Price: 1.1rem, 1.3rem, 1.5rem

---

### **5. Content Display Opties**

#### **Wat tonen op card:**
- [ ] Thumbnail afbeelding
- [ ] Titel
- [ ] Prijs
- [ ] Bestemmingen (start → eind)
- [ ] Samenvatting/Excerpt ✅ NIEUW!
- [ ] Aantal nachten
- [ ] Aantal vluchten
- [ ] Taxonomieën (Type, Thema, etc.)
- [ ] Favorite button
- [ ] Vertrekdatum
- [ ] Beschikbaarheid status

**Excerpt lengte:**
- Kort: 10 woorden
- Medium: 20 woorden ✅ Huidige
- Lang: 40 woorden

---

### **6. Interactie Opties**

**Hover Effects:**
- [ ] Lift up (translateY) ✅ Huidige
- [ ] Scale
- [ ] Glow shadow
- [ ] Image zoom ✅ Huidige
- [ ] Show map overlay
- [ ] Show quickview popup

**Click Actie:**
- [ ] Ga naar detail pagina ✅ Huidige
- [ ] Open in modal
- [ ] Open in nieuwe tab

---

### **7. Filter Bar Styling**

**Layout:**
- Horizontal (1 row) ✅ Advanced template
- Vertical (sidebar)
- Collapsible
- Sticky on scroll

**Stijl:**
- Compact ✅ Advanced template
- Spacious
- In hero banner
- Floating bar

---

### **8. Mobile Responsive Opties**

**Stack Behavior:**
- Auto (adaptive)
- Always 1 column
- Always 2 columns
- Hide filters, show button

---

## 🔧 Implementatie Strategie:

### **Optie A: Shortcode Attributes**
```php
[rbs_travel_listing 
    layout="grid" 
    columns="3"
    card_style="modern"
    show_excerpt="true"
    excerpt_length="20"
    color_scheme="gowild"
]
```

### **Optie B: Template Parameters**
```php
<?php 
// In theme template file
$template_options = array(
    'layout' => 'grid',
    'columns' => 3,
    'card_style' => 'modern',
    'show_excerpt' => true,
    'excerpt_length' => 20,
    'colors' => array(
        'primary' => '#667eea',
        'secondary' => '#764ba2'
    )
);

rbs_travel_render_listing($template_options);
?>
```

### **Optie C: WordPress Customizer** ⭐ BESTE
```
Appearance → Customize → Travel Listings

Layout:
  ☐ Grid View (3 kolommen)
  ☐ List View

Card Style:
  ☐ Modern
  ☐ Classic  
  ☐ Minimal
  ☐ Luxury

Colors:
  Primary: [Color Picker]
  Secondary: [Color Picker]
  
Content:
  [x] Show Excerpt
  Excerpt Length: [20] woorden
  
  [x] Show Nights
  [x] Show Flights
  [ ] Show Taxonomies
```

---

## 📦 Per Theme Packages:

### **GoWild Theme Package**
```
✅ Grid view (3 kolommen)
✅ Modern card style
✅ Nature color scheme (greens/blues)
✅ Show excerpt (20 woorden)
✅ Adventure focused icons
✅ Large images
```

### **Tripex Theme Package**
```
✅ List view (horizontaal)
✅ Luxury card style
✅ Teal/Green color scheme
✅ Show excerpt (30 woorden)
✅ Premium typography
✅ Fancy hover effects
```

### **Custom Theme Creator**
```
Website builder kiest:
1. Layout (Grid/List)
2. Card style
3. Kleurenschema
4. Font pairing
5. Content opties
→ Opgeslagen als preset
→ Exporteerbaar als JSON
```

---

## 🎨 CSS Variables Aanpak:

```css
:root {
    /* Layout */
    --rbs-columns: 3;
    --rbs-gap: 2rem;
    --rbs-card-radius: 20px;
    
    /* Colors */
    --rbs-primary: #667eea;
    --rbs-secondary: #764ba2;
    --rbs-text: #2d3748;
    --rbs-text-light: #718096;
    
    /* Typography */
    --rbs-font-title: 1.5rem;
    --rbs-font-body: 1rem;
    --rbs-font-price: 1.1rem;
    
    /* Effects */
    --rbs-shadow: 0 10px 30px rgba(0,0,0,0.08);
    --rbs-shadow-hover: 0 20px 40px rgba(0,0,0,0.15);
}

/* Theme overrides via inline CSS of custom stylesheet */
.gowild-theme {
    --rbs-primary: #228B22;
    --rbs-secondary: #4CAF50;
}
```

---

## 🚀 Fase 1: Basis (NU KLAAR!) ✅

- [x] REST API met filtering
- [x] Grid view (verticaal)
- [x] List view (horizontaal)
- [x] View switcher button
- [x] Modern card style
- [x] Excerpt support
- [x] Destinations display fix

---

## 🎯 Fase 2: Styling Opties (VOLGENDE)

- [ ] CSS variables systeem
- [ ] 3-4 card style presets
- [ ] Color scheme switcher
- [ ] Font pairing options
- [ ] Content display toggles

---

## 💎 Fase 3: Theme Factory Integration

- [ ] Per theme preset files
- [ ] Theme package generator
- [ ] WordPress Customizer integration
- [ ] Export/Import presets
- [ ] Visual theme builder

---

## 🔮 Toekomst Features:

- [ ] Comparison view (side-by-side)
- [ ] Map view (pins on map)
- [ ] Calendar view (per vertrekdatum)
- [ ] Saved searches
- [ ] Wishlist/Favorites persistent
- [ ] Email alerts voor nieuwe reizen
- [ ] Price drop notifications
- [ ] Social sharing buttons
- [ ] Reviews & ratings integration

---

## ✅ Huidige Status:

**JA, website bouwers kunnen styling opties krijgen!**

**Methodes:**
1. ✅ **Layout switcher** (Grid/List) - Klaar!
2. ⏳ **CSS variables** - Kan snel
3. ⏳ **Theme presets** - Volgende stap
4. ⏳ **WordPress Customizer** - Meest user-friendly

**Aanbeveling:** Start met CSS variables + theme preset JSON files.

Website bouwer upload theme, plugin leest preset, past styling toe! 🎨
