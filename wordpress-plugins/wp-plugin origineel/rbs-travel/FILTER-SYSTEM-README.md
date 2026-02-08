# 🔍 RBS Travel - Filter & Zoek Systeem

## 🎉 Nieuwe Features!

### ✅ Wat is er nieuw:

1. **🔍 Geavanceerd Filter Systeem**
   - Filter op Locatie/Bestemming
   - Filter op Type Reis (Cruise, Rondreis, etc.)
   - Filter op Thema (Avontuur, Luxe, etc.)
   - Filter op Services
   - Prijsrange filter (min/max)

2. **🔎 Zoekfunctionaliteit**
   - Zoek in reistitel en beschrijving
   - Real-time filtering

3. **📊 Sorteer Opties**
   - Nieuwste eerst
   - Prijs: Laag → Hoog
   - Prijs: Hoog → Laag
   - Naam (A-Z)

4. **🎨 Elementor Integratie**
   - Gebruik Elementor voor custom hero/banner
   - Maak eigen headers met Elementor
   - Template toont Elementor content automatisch

5. **⚡ Live AJAX Filtering**
   - Geen page refresh nodig
   - Snelle resultaten
   - Smooth user experience

---

## 📋 Drie Template Opties:

### **1. Travel Listing (Speelse Style)**
- ✨ Volledig standalone design
- 💜 Eigen paarse gradient header
- 🎯 **Beste voor:** Volledig custom landing page

### **2. Travel Listing (Met Theme)**
- 🎨 Theme header & footer
- 🔗 Menu navigatie werkt
- 🎯 **Beste voor:** Consistent met rest van site

### **3. Travel Listing (Met Filters & Elementor)** ← **NIEUW!**
- 🔍 Geavanceerde filters
- 🎨 Elementor support voor hero
- ⚡ Live AJAX filtering
- 🎯 **Beste voor:** Professionele "Zoek & Boek" ervaring

---

## 🚀 REST API Endpoints:

### **1. Get Travel Ideas (Met Filters)**
```
GET /wp-json/rbs-travel/v1/ideas
```

**Parameters:**
- `per_page` - Aantal resultaten (default: 12)
- `page` - Pagina nummer
- `search` - Zoekterm (zoekt in titel/content)
- `location` - Filter op location slug
- `tour_type` - Filter op type slug
- `tour_theme` - Filter op theme slug
- `tour_service` - Filter op service slug
- `min_price` - Minimum prijs
- `max_price` - Maximum prijs
- `orderby` - Sorteer op: `date`, `price`, `title`
- `order` - Volgorde: `ASC`, `DESC`

**Voorbeelden:**
```
# Alle reizen
/wp-json/rbs-travel/v1/ideas

# Zoek op "New York"
/wp-json/rbs-travel/v1/ideas?search=new york

# Filter op cruise type
/wp-json/rbs-travel/v1/ideas?tour_type=cruise

# Prijs tussen €1000-€3000
/wp-json/rbs-travel/v1/ideas?min_price=1000&max_price=3000

# Sorteer op prijs (laag-hoog)
/wp-json/rbs-travel/v1/ideas?orderby=price&order=ASC

# Combinatie filters
/wp-json/rbs-travel/v1/ideas?tour_type=cruise&location=europa&min_price=2000&orderby=price
```

### **2. Get Filter Options**
```
GET /wp-json/rbs-travel/v1/filters
```

**Response:**
```json
{
  "locations": [
    {
      "id": 5,
      "name": "Europa",
      "slug": "europa",
      "count": 12
    }
  ],
  "tour_types": [
    {
      "id": 3,
      "name": "Cruise",
      "slug": "cruise",
      "count": 8
    }
  ],
  "tour_themes": [
    {
      "id": 2,
      "name": "Avontuur",
      "slug": "avontuur",
      "count": 15
    }
  ],
  "tour_services": [
    {
      "id": 1,
      "name": "All-Inclusive",
      "slug": "all-inclusive",
      "count": 10
    }
  ],
  "price_range": {
    "min": 500,
    "max": 8000
  }
}
```

---

## 🎨 Elementor Integratie:

### **Stap 1: Pagina Aanmaken**
1. WordPress Admin → Pagina's → Nieuwe Pagina
2. Titel: "Reisoverzicht"
3. Template: **"Travel Listing (Met Filters & Elementor)"**

### **Stap 2: Hero Banner Met Elementor**
1. Klik "Bewerken met Elementor"
2. Voeg secties toe:
   - Hero banner met achtergrondafbeelding
   - Call-to-action tekst
   - Zoek formulier (optioneel)
   - USP's / Features
3. Publiceer

### **Stap 3: Resultaat**
```
┌────────────────────────────────────┐
│ [THEME HEADER]                     │
├────────────────────────────────────┤
│                                    │
│  [ELEMENTOR HERO BANNER]           │
│  "Vind jouw droomreis"             │
│  [Grote achtergrond foto]          │
│                                    │
├────────────────────────────────────┤
│                                    │
│  🔍 ZOEK & FILTER BAR              │
│  [Zoek] [Filters] [Prijs]          │
│                                    │
│  📊 12 reizen gevonden             │
│  [Card] [Card] [Card]              │
│  [Card] [Card] [Card]              │
│                                    │
├────────────────────────────────────┤
│ [THEME FOOTER]                     │
└────────────────────────────────────┘
```

---

## 🎯 Filter Gebruik:

### **Voor Gebruikers:**

1. **Zoeken:**
   - Type bestemming of reistitel
   - Druk Enter of klik "Zoek Reizen"

2. **Filteren:**
   - Selecteer bestemming uit dropdown
   - Kies reistype (Cruise, Rondreis, etc.)
   - Selecteer thema (Avontuur, Luxe, etc.)
   - Kies service niveau
   - Stel prijsrange in (€500 - €3000)
   - Klik "Zoek Reizen"

3. **Sorteren:**
   - Gebruik "Sorteer op" dropdown
   - Kies: Nieuwste, Prijs, Naam

4. **Reset:**
   - Klik "Reset Filters" om alles te wissen

---

## 💡 Taxonomieën Beheren:

### **WordPress Admin:**

**Locaties/Bestemmingen:**
- rbsTravel → Locations
- Voeg toe: Europa, Azië, Amerika, etc.

**Reistypes:**
- rbsTravel → Types
- Voeg toe: Cruise, Rondreis, Fly & Drive, etc.

**Thema's:**
- rbsTravel → Themes
- Voeg toe: Avontuur, Luxe, Budget, Familie, etc.

**Services:**
- rbsTravel → Services
- Voeg toe: All-Inclusive, Half-Pension, etc.

**Travel Ideas:**
- Wijs taxonomieën toe aan elke reis
- Deze verschijnen automatisch in filters!

---

## 🔧 Aanpassingen:

### **Kleuren Aanpassen:**

In het template CSS (regel 200-250):
```css
/* Primary color gradient */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Hover color */
background: #667eea;
```

Wijzig naar jouw brand kleuren!

### **Filter Labels:**

In template HTML (regel 500-600):
```html
<label>📍 Bestemming</label>
<label>🎨 Type Reis</label>
```

Pas emoji's en teksten aan!

---

## 📱 Responsive Design:

✅ **Mobile-First**
- Filters stapelen verticaal op mobiel
- Cards 1 kolom op klein scherm
- Touch-friendly buttons

✅ **Tablet**
- 2 kolommen grid
- Filters blijven zichtbaar

✅ **Desktop**
- 3-4 kolommen grid
- Alle filters naast elkaar

---

## 🚀 Performance:

✅ **Optimalisaties:**
- Alleen nodige scripts laden
- CSS scoped (geen conflicts)
- AJAX filtering (geen page reload)
- Lazy loading afbeeldingen
- Gecachte taxonomy queries

---

## 🎓 Voor Developers:

### **Custom Filtering:**

Voeg extra filters toe in template JavaScript:
```javascript
currentFilters = {
    search: document.getElementById('rbs-search-input').value,
    location: document.getElementById('rbs-filter-location').value,
    // ... bestaande filters ...
    
    // Voeg je eigen filter toe:
    my_custom_filter: document.getElementById('my-filter').value
};
```

Pas API endpoint aan in `api-rest-endpoints.php`:
```php
$my_filter = $request->get_param('my_custom_filter');

if ($my_filter) {
    $tax_query[] = array(
        'taxonomy' => 'my-taxonomy',
        'field' => 'slug',
        'terms' => sanitize_text_field($my_filter)
    );
}
```

---

## ✅ Checklist voor Gebruik:

- [ ] Plugin geactiveerd
- [ ] Taxonomieën aangemaakt (Locations, Types, Themes, Services)
- [ ] Travel Ideas gepubliceerd met taxonomieën toegewezen
- [ ] Pagina aangemaakt met "Met Filters & Elementor" template
- [ ] Elementor hero banner toegevoegd (optioneel)
- [ ] Permalinks geflusht (Instellingen → Permalinks → Opslaan)
- [ ] API test: `/wp-json/rbs-travel/v1/filters`
- [ ] Frontend test: Filters proberen

---

## 🎉 Resultaat:

**Je hebt nu een professioneel "Zoek & Boek" systeem met:**
- 🔍 Geavanceerde filtering
- 🎨 Mooie UI met smooth interactions
- ⚡ Snelle AJAX updates
- 📱 Fully responsive
- 🎭 Elementor support
- 🌍 Universeel werkend op elke WordPress site

**Ready voor productie!** 🚀
