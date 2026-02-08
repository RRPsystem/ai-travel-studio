# RBS Travel - Speelse Style Templates

## 📋 Overzicht

Deze templates bieden een moderne, speelse design voor je reizen plugin met:
- ✨ 3D cards met shadow effects
- 🗺️ Interactive OpenStreetMap kaarten
- 🏨 Clickable hotel cards met sliding panels  
- 📅 Dag-bij-dag programma met vluchten
- 📱 Volledig responsive design
- 🎨 Smooth animations en transitions

## 📁 Template Bestanden

### Detail Pagina
**Bestand:** `templates/frontend/single-rbs-travel-idea-speels.php`

**Features:**
- Hero image met gradient overlay
- 3 Info cards (vluchtduur, afstand, klimaat)
- Hoogtepunten sectie met destination cards
- Complete dag-bij-dag itinerary
- Heenvlucht bovenaan (Dag 1)
- Terugvlucht onderaan (Laatste dag)
- Clickable hotel cards → Sliding panel met details
- Kleine kaart in sidebar → Klik voor grote kaart
- Price display met booking button

### Listing Pagina
**Bestand:** `templates/frontend/archive-rbs-travel-idea.php`

**Features:**
- Modern grid layout (3 kolommen)
- Hover over card → Route kaart verschijnt
- OpenStreetMap met markers en route lijn
- Prijs display per reis
- Meta informatie (dagen, nachten, transport)
- Pagination
- Favorite button (hart icoon)
- Responsive (3 → 2 → 1 kolom)

## 🚀 Installatie

### Stap 1: Backup Maken
```bash
# Backup huidige templates
cp templates/frontend/single-rbs-travel-idea.php templates/frontend/single-rbs-travel-idea-backup.php
cp templates/frontend/archive-rbs-travel-idea.php templates/frontend/archive-rbs-travel-idea-backup.php
```

### Stap 2: Templates Activeren

**Optie A: Directe Replace (Aanbevolen)**
```bash
# Vervang oude met nieuwe templates
cp templates/frontend/single-rbs-travel-idea-speels.php templates/frontend/single-rbs-travel-idea.php
# Archive template is al op juiste plek
```

**Optie B: Via Plugin Code**
Pas `rbs-travel.php` aan:
```php
// Laad Speelse templates
add_filter('single_template', function($template) {
    if (is_singular('rbs-travel-idea')) {
        $speelse_template = plugin_dir_path(__FILE__) . 'templates/frontend/single-rbs-travel-idea-speels.php';
        if (file_exists($speelse_template)) {
            return $speelse_template;
        }
    }
    return $template;
});
```

### Stap 3: Afhankelijkheden Controleren

De templates gebruiken:
- ✅ **Leaflet.js** (voor kaarten) - via CDN geladen
- ✅ **jQuery** - WordPress default
- ✅ **WordPress functions** - get_header(), get_footer(), etc.

Geen extra plugins nodig! 🎉

## 📊 Travel Compositor Data

De templates gebruiken deze meta velden:

### Vereist
```php
travel_destinations     // Array met bestemmingen
travel_transports      // Array met vluchten
travel_hotels          // Array met hotels  
travel_price_per_person // Prijs per persoon
travel_number_of_nights // Aantal nachten
```

### Optioneel
```php
travel_large_title     // Subtitle in hero
travel_counters        // Extra statistieken
```

### Destination Structuur
```php
[
    'name' => 'Santorini',
    'fromDay' => 2,
    'toDay' => 4,
    'description' => '...',
    'imageUrls' => ['https://...'],
    'latitude' => 36.3932,
    'longitude' => 25.4615,
    'country' => 'Griekenland'
]
```

### Transport Structuur
```php
[
    'originDestinationCode' => 'AMS',
    'targetDestinationCode' => 'ATH',
    'company' => 'KLM',
    'transportNumber' => '1234',
    'departureTime' => '08:45',
    'arrivalTime' => '13:30',
    'stops' => 0
]
```

### Hotel Structuur
```php
[
    'fromDay' => 2,
    'toDay' => 4,
    'startDate' => '2024-06-15',
    'endDate' => '2024-06-18',
    'numberOfPersons' => 2,
    'hotelData' => [
        'product' => 'Acropolis View Hotel',
        'location' => 'Athene',
        'hotelCategory' => '4',
        'roomCode' => 'Deluxe',
        'boardTypeDescription' => 'Half Pension',
        'imageUrls' => ['https://...']
    ]
]
```

## 🎨 Customization

### Kleuren Aanpassen

In beide templates, zoek naar deze CSS variabelen:

**Primary Gradient:**
```css
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```
Vervang `#667eea` en `#764ba2` met je brand kleuren.

**Accent Groen:**
```css
background: linear-gradient(135deg, #10b981 0%, #059669 100%);
```

**Info Card Kleuren:**
```css
/* Blauw */
background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);

/* Groen */
background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);

/* Geel */
background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
```

### Fonts Aanpassen

Vervang:
```css
font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

Met bijvoorbeeld:
```css
font-family: 'Montserrat', -apple-system, sans-serif;
```

Dan in `<head>`:
```html
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&display=swap" rel="stylesheet">
```

### Card Grid Aanpassen

**Aantal kolommen wijzigen:**
```css
/* Van 3 naar 4 kolommen */
.rbs-travel-grid {
    grid-template-columns: repeat(4, 1fr);
}
```

**Gap tussen cards:**
```css
.rbs-travel-grid {
    gap: 3rem; /* Vergroot voor meer ruimte */
}
```

## 🐛 Troubleshooting

### Kaarten tonen niet

**Probleem:** Lege kaarten of geen markers

**Oplossing:**
1. Check of destinations latitude/longitude hebben:
```php
var_dump($destinations[0]['latitude']); // Moet nummer zijn
```

2. Check console voor Leaflet errors
3. Verify CDN links werken:
```
https://unpkg.com/leaflet@1.9.4/dist/leaflet.css
https://unpkg.com/leaflet@1.9.4/dist/leaflet.js
```

### Hotels tonen niet in dag-bij-dag

**Probleem:** Geen hotel cards zichtbaar

**Oplossing:**
1. Check of hotels fromDay/toDay matchen met destinations:
```php
// Hotel moet overlappen met destination
if ($hotel['fromDay'] <= $destination['fromDay'] && 
    $hotel['toDay'] >= $destination['fromDay']) {
    // Match!
}
```

2. Verify hotel data aanwezig is:
```php
var_dump($meta_fields['travel_hotels']);
```

### Sliding panels werken niet

**Probleem:** Klik op hotel/kaart doet niets

**Oplossing:**
1. Check of jQuery geladen is:
```javascript
console.log(jQuery.fn.jquery); // Should output version
```

2. Check console voor JavaScript errors

3. Verify hotel-data script tags:
```html
<script type="application/json" id="hotel-data-0">
```

### Prices tonen €0,00

**Probleem:** Alle prijzen zijn nul

**Oplossing:**
Check meta field format:
```php
// Moet numeriek zijn, niet string
$price = (float)$meta_fields['travel_price_per_person'];
echo number_format($price, 2, ',', '.');
```

## 📱 Responsive Breakpoints

**Desktop:** > 1024px - 3 kolommen
**Tablet:** 641px - 1024px - 2 kolommen  
**Mobile:** < 640px - 1 kolom

Aanpassen:
```css
@media (max-width: 1024px) {
    .rbs-travel-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
```

## ⚡ Performance Tips

### Kaarten Lazy Loading
De kaarten worden pas geladen bij hover:
```javascript
// Eerste hover = initialize
// Volgende hovers = reuse map
```

### Image Optimization
Gebruik geoptimaliseerde afbeeldingen:
```php
// In Travel Compositor import
$image_url = add_query_arg(['w' => 800, 'q' => 80], $original_url);
```

### CSS/JS Minification
Voor productie:
```bash
# Minify CSS (optioneel)
cssnano < style.css > style.min.css
```

## 🔄 Updates

### Versie 1.0 (Speelse Style)
- ✅ Multi-destination support
- ✅ Interactive maps
- ✅ Hotel panels
- ✅ Responsive design
- ✅ Smooth animations

### Toekomstige Features
- 🔲 Layout selector in WordPress admin
- 🔲 Color scheme picker
- 🔲 Font selector
- 🔲 Multiple layout styles (Classic, Luxury, etc.)

## 📞 Support

Voor vragen of problemen:
1. Check eerst deze README
2. Inspecteer browser console voor errors
3. Verify Travel Compositor data format
4. Test in default WordPress theme

## 🎯 Volgende Stappen

Na installatie:
1. ✅ Test listing page met meerdere reizen
2. ✅ Test detail page met complete reis
3. ✅ Test op mobile devices
4. ✅ Customize brand colors
5. ✅ Add booking form integration
6. ✅ Setup analytics tracking

## 📄 Licentie

Deze templates zijn onderdeel van de RBS Travel plugin.

---

**Gemaakt met ❤️ voor moderne reisbureau websites**
