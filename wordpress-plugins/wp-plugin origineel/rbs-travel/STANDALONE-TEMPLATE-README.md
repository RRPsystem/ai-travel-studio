# 🚀 Standalone Page Template - Installation Guide

## ✅ De BESTE & VEILIGSTE Oplossing!

Deze oplossing gebruikt:
- **REST API** voor data (geen theme conflicts!)
- **Standalone HTML template** (volledige controle!)
- **JavaScript rendering** (modern & snel!)

---

## 📋 Installatie Stappen

### **Stap 1: API Activeren** ✅

De REST API is al geïnstalleerd in de plugin!

**Test de API:**
```
https://jouw-site.nl/wp-json/rbs-travel/v1/ideas
```

Je zou JSON data moeten zien met je reizen! 🎉

---

### **Stap 2: Page Template Installeren**

**Optie A: In huidige theme**
1. Ga naar: `wp-content/themes/JOUW-THEME/`
2. Kopieer het bestand: `page-travel-listing.php`
3. Upload naar je theme folder

**Optie B: Child theme maken (aangeraden!)**
```bash
wp-content/themes/
├── jouw-theme/           # Parent theme
└── jouw-theme-child/     # Child theme
    ├── style.css
    ├── functions.php
    └── page-travel-listing.php  # <-- Template hier!
```

**Child theme `style.css`:**
```css
/*
Theme Name: Jouw Theme Child
Template: jouw-theme
*/
```

**Child theme `functions.php`:**
```php
<?php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
});
```

---

### **Stap 3: WordPress Page Aanmaken**

1. **WordPress Admin → Pagina's → Nieuwe pagina**
2. **Titel:** "Reizen" (of wat je wilt)
3. **Rechts in de sidebar → Template:** Selecteer "Travel Listing (Speelse Style)"
4. **Publiceren!** 🎉

---

### **Stap 4: Detail Pagina (Optioneel)**

Als je ook een detail pagina wilt, pas de URL aan in `page-travel-listing.php`:

```php
// Regel 15:
$detail_base_url = home_url('/reizen-detail/'); // Pas dit aan!
```

Of maak een tweede template voor detail pages.

---

## 🎨 Aanpassingen

### **Kleuren Aanpassen**

In `page-travel-listing.php` zoek naar:

```css
/* Line 47 */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

Verander de hex kleuren naar jouw huisstijl!

### **Layout Aanpassen**

```css
/* Grid columns - Line 100 */
grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));

/* Wijzig 350px naar gewenste kaart breedte */
```

### **Aantal Items Per Pagina**

```javascript
/* Line 345 */
const response = await fetch(`${API_URL}?per_page=12&page=${page}`);

/* Wijzig 12 naar gewenst aantal */
```

---

## 🔧 API Endpoints

### **GET /wp-json/rbs-travel/v1/ideas**
Alle reizen ophalen

**Parameters:**
- `per_page` (default: 12) - Aantal per pagina
- `page` (default: 1) - Pagina nummer

**Response:**
```json
{
  "ideas": [
    {
      "id": 123,
      "slug": "bali-adventure",
      "title": "Bali Adventure",
      "description": "...",
      "image": "https://...",
      "price": 1299,
      "nights": 10,
      "destinations": [...],
      "transports": [...],
      "hotels": [...]
    }
  ],
  "total": 45,
  "pages": 4,
  "current_page": 1
}
```

### **GET /wp-json/rbs-travel/v1/ideas/{id}**
Enkele reis ophalen (by ID)

### **GET /wp-json/rbs-travel/v1/ideas/slug/{slug}**
Enkele reis ophalen (by slug)

---

## 🎯 Voordelen van deze Oplossing

✅ **Geen theme conflicts** - Staat volledig los
✅ **Volledige controle** - Edit alles wat je wilt
✅ **Modern design** - Responsive & smooth animations
✅ **Interactive maps** - Leaflet.js hover previews
✅ **Snelle loading** - API-driven rendering
✅ **SEO friendly** - WordPress head/footer included
✅ **Easy maintenance** - Gewoon HTML/CSS/JS editen

---

## 🐛 Troubleshooting

### **"Ik zie geen reizen"**

1. **Check API:**
   - Ga naar: `https://jouw-site.nl/wp-json/rbs-travel/v1/ideas`
   - Zie je JSON data? ✅
   - Zie je een error? Check plugin activatie

2. **Check Console (F12):**
   - Open developer tools
   - Kijk naar "Console" tab
   - Staan daar errors?

3. **Check Data:**
   - Heb je travel ideas aangemaakt in WordPress?
   - Zijn ze gepubliceerd (niet draft)?

### **"Template staat niet in lijst"**

1. Check of `page-travel-listing.php` in de juiste theme folder staat
2. Ga naar **WordPress Admin → Weergave → Thema's** en re-activate het theme
3. Clear cache (als je cache plugin hebt)

### **"Maps worden niet geladen"**

1. Check console (F12) voor JavaScript errors
2. Check of Leaflet.js geladen is (Network tab)
3. Controleer of destinations `lat` en `lng` coordinaten hebben

### **"Styling klopt niet"**

1. Check of er conflicterende CSS is van je theme
2. Probeer `!important` toe te voegen aan kritieke styles
3. Of: verhoog de CSS specificity

---

## 🚀 Uitbreidingen

### **Filter/Zoek Functie Toevoegen**

Voeg toe boven de grid:

```html
<div class="filters">
    <input type="text" id="search" placeholder="Zoek bestemming...">
    <select id="price-filter">
        <option value="">Alle prijzen</option>
        <option value="0-1000">€0 - €1000</option>
        <option value="1000-2000">€1000 - €2000</option>
        <option value="2000+">€2000+</option>
    </select>
</div>
```

### **Favoriet Systeem**

Gebruik localStorage:

```javascript
function toggleFavorite(id) {
    let favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    if (favorites.includes(id)) {
        favorites = favorites.filter(f => f !== id);
    } else {
        favorites.push(id);
    }
    localStorage.setItem('favorites', JSON.stringify(favorites));
}
```

### **Booking/Contact Form**

Voeg een modal toe met contact form die de travel ID meestuurt.

---

## 📞 Support

Heb je vragen? Check:
- WordPress debug log: `wp-content/debug.log`
- Browser console: F12 → Console tab
- Network tab: F12 → Network tab (API calls)

---

## 🎉 Klaar!

Je hebt nu een **moderne, veilige, en snelle** reizen listing pagina!

**Geniet ervan!** ✈️🌍💎
