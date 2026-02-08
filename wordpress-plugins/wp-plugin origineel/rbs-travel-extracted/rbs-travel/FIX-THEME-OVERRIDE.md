# 🔧 THEME OVERRIDE FIX - GoWilds Theme

## PROBLEEM
Het GoWilds theme negeert of override onze plugin template, wat resulteert in witte pagina.

## OPLOSSINGEN

### OPTIE 1: Forceer Plugin Template (Snelst)

1. Open: `wp-content/themes/gowilds/functions.php`

2. Voeg toe aan EINDE van bestand (VOOR `?>`):

```php
// Force RBS Travel plugin template
add_filter('template_include', function($template) {
    if (is_singular('rbs-travel-idea')) {
        $plugin_template = WP_PLUGIN_DIR . '/rbs-travel/templates/frontend/single-rbs-travel-idea.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}, 999);
```

3. Sla op en test!

---

### OPTIE 2: Verwijder Theme Override

1. Check of theme deze file heeft:
   ```
   wp-content/themes/gowilds/single-rbs-travel-idea.php
   ```

2. Als JA → Verwijder of hernoem naar:
   ```
   single-rbs-travel-idea.php.backup
   ```

3. Test reispagina

---

### OPTIE 3: Fix Theme CSS

Als pagina NIET wit maar content onzichtbaar:

1. Rechtermuisklik op pagina → Inspect (F12)

2. Check of content bestaat maar verborgen is:
   - `display: none`
   - `opacity: 0`
   - `visibility: hidden`

3. Voeg CSS override toe in theme:

```css
/* GoWilds theme override for RBS Travel */
body.single-rbs-travel-idea .entry-content {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
```

---

### OPTIE 4: Disable Theme Styles (Tijdelijk Test)

In plugin, voeg toe aan `single-rbs-travel-idea.php` NA `get_header()`:

```php
// Disable theme styles temporarily
wp_dequeue_style('gowilds-style');
wp_dequeue_style('gowilds-main');
```

---

## DEBUG STAPPEN

### 1. Check HTML Source
```
1. Ga naar witte pagina
2. Rechtermuisklik → "Paginabron bekijken"
3. Zoek naar: "TEMPLATE WERKT" of "RRP SYSTEM"
4. Als gevonden → Content IS er, maar CSS verbergt het
5. Als NIET gevonden → Theme laadt andere template
```

### 2. Check Browser Console
```
F12 → Console tab
Zie je JavaScript errors?
→ Theme JS kan rendering blokkeren
```

### 3. Check Network Tab
```
F12 → Network tab → Refresh pagina
Zijn alle resources geladen? (200 OK)
→ Blokkerende resources = wit scherm
```

---

## VERWACHT RESULTAAT

Na een van deze fixes:
✅ Reispagina toont content
✅ Timeline, hotels, etc. zichtbaar
✅ Expert widget werkt

---

## ALS NIETS WERKT

Upload deze debug versie van functions.php toevoeging:

```php
// DEBUG: Log template loading
add_action('template_redirect', function() {
    if (is_singular('rbs-travel-idea')) {
        error_log('Template redirect for rbs-travel-idea');
        error_log('Current template: ' . get_page_template());
    }
});

add_filter('template_include', function($template) {
    if (is_singular('rbs-travel-idea')) {
        error_log('Template include filter triggered');
        error_log('Template path: ' . $template);
        
        $plugin_template = WP_PLUGIN_DIR . '/rbs-travel/templates/frontend/single-rbs-travel-idea.php';
        error_log('Plugin template path: ' . $plugin_template);
        error_log('Plugin template exists: ' . (file_exists($plugin_template) ? 'YES' : 'NO'));
        
        if (file_exists($plugin_template)) {
            error_log('Using plugin template!');
            return $plugin_template;
        }
    }
    return $template;
}, 999);
```

Dan check debug.log voor template pad info!
