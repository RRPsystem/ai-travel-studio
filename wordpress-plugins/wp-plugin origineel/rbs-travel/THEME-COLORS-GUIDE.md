# 🎨 WordPress Theme Colors Integratie - Handleiding

## ✅ WAT HEB IK GEBOUWD:

Een **automatisch theme color detection systeem** dat WordPress theme kleuren ophaalt en toepast op de travel listing!

---

## 🔍 HOE HET WERKT:

### **3 Detectie Methodes (Automatisch!):**

1. **theme.json** (Block Themes / Gutenberg)
2. **WordPress Customizer** (Classic Themes)
3. **Editor Color Palette** (Alle themes)

**Fallback:** Als geen kleuren gevonden → Default kleuren (paars/violet)

---

## 📋 GEBRUIK IN TEMPLATES:

### **Advanced Template (Geüpdatete versie):**

```php
<?php
// Auto-detect theme colors
$theme_colors = RBS_Travel_Theme_Colors::get_theme_colors();

// Output CSS variables
RBS_Travel_Theme_Colors::output_theme_color_css();
?>
```

**Genereert:**
```css
:root {
    --rbs-theme-primary: #066168;    /* Van theme */
    --rbs-theme-secondary: #85D200;  /* Van theme */
    --rbs-primary: var(--rbs-theme-primary);
    --rbs-secondary: var(--rbs-theme-secondary);
}
```

---

## 🎨 WORDPRESS THEME SETUP:

### **Methode 1: theme.json (Nieuwste, Block Themes)**

```json
{
  "version": 2,
  "settings": {
    "color": {
      "palette": [
        {
          "slug": "primary",
          "color": "#066168",
          "name": "Primary"
        },
        {
          "slug": "secondary",
          "color": "#85D200",
          "name": "Secondary"
        },
        {
          "slug": "accent",
          "color": "#F7941D",
          "name": "Accent"
        }
      ]
    }
  }
}
```

**Bestand:** `/wp-content/themes/jouw-theme/theme.json`

---

### **Methode 2: Customizer (Classic Themes)**

```php
// In theme's functions.php
function mytheme_customize_register($wp_customize) {
    
    // Primary Color
    $wp_customize->add_setting('primary_color', array(
        'default' => '#066168',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'primary_color', array(
        'label' => __('Primary Color', 'mytheme'),
        'section' => 'colors',
    )));
    
    // Secondary Color
    $wp_customize->add_setting('secondary_color', array(
        'default' => '#85D200',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'secondary_color', array(
        'label' => __('Secondary Color', 'mytheme'),
        'section' => 'colors',
    )));
}
add_action('customize_register', 'mytheme_customize_register');
```

**Gebruikers kunnen dit instellen via:**
`Appearance → Customize → Colors`

---

### **Methode 3: Editor Color Palette (Universeel)**

```php
// In theme's functions.php
add_theme_support('editor-color-palette', array(
    array(
        'name'  => __('Primary', 'mytheme'),
        'slug'  => 'primary',
        'color' => '#066168',
    ),
    array(
        'name'  => __('Secondary', 'mytheme'),
        'slug'  => 'secondary',
        'color' => '#85D200',
    ),
    array(
        'name'  => __('Accent', 'mytheme'),
        'slug'  => 'accent',
        'color' => '#F7941D',
    ),
));
```

---

## 🧪 TESTEN:

### **Test 1: Check Gedetecteerde Kleuren**

```php
// Voeg toe aan template (tijdelijk):
$colors = RBS_Travel_Theme_Colors::get_theme_colors();
echo '<pre>';
print_r($colors);
echo '</pre>';
```

**Verwachte output:**
```
Array (
    [primary] => #066168
    [secondary] => #85D200
    [accent] => #F7941D
    [text] => #2d3748
    [background] => #ffffff
)
```

---

### **Test 2: Inspect CSS Variables**

1. Open pagina met Advanced template
2. Open browser DevTools (F12)
3. Ga naar Elements tab
4. Zoek `<style id="rbs-travel-theme-colors">`
5. Check of CSS variables correct zijn:

```css
:root {
    --rbs-theme-primary: #066168;
    --rbs-theme-secondary: #85D200;
    --rbs-primary: var(--rbs-theme-primary);
    --rbs-secondary: var(--rbs-theme-secondary);
}
```

---

## 🎯 WELKE ELEMENTEN GEBRUIKEN THEME COLORS:

### **Primary Color:**
- ✅ Search/filter focus borders
- ✅ Search button gradient (start)
- ✅ Price tags
- ✅ Destination text
- ✅ Stats numbers
- ✅ View switcher active state
- ✅ Pagination active/hover
- ✅ Card destination icons

### **Secondary Color:**
- ✅ Search button gradient (end)

### **Fallback:**
Als theme geen colors heeft → Default paars/violet (#667eea, #764ba2)

---

## 💡 VOOR THEME FACTORY:

### **Per Theme Preset Files:**

**GoWild Theme** (`gowild-colors.json`):
```json
{
  "theme_name": "GoWild",
  "colors": {
    "primary": "#228B22",
    "secondary": "#4CAF50",
    "accent": "#8BC34A",
    "text": "#1B5E20",
    "background": "#F1F8E9"
  }
}
```

**Tripex Theme** (`tripex-colors.json`):
```json
{
  "theme_name": "Tripex",
  "colors": {
    "primary": "#066168",
    "secondary": "#85D200",
    "accent": "#ccf2f9",
    "text": "#346065",
    "background": "#EFFFFF"
  }
}
```

---

## 🔧 ADVANCED: Custom Override

### **Optie A: Via Shortcode**
```php
[rbs_travel_listing 
    primary_color="#FF5733"
    secondary_color="#33FF57"
]
```

### **Optie B: Via Template Parameter**
```php
<?php
// Override theme colors
$custom_colors = array(
    'primary' => '#FF5733',
    'secondary' => '#33FF57'
);

// Output custom CSS
?>
<style>
:root {
    --rbs-primary: <?php echo $custom_colors['primary']; ?>;
    --rbs-secondary: <?php echo $custom_colors['secondary']; ?>;
}
</style>
```

### **Optie C: Via Plugin Settings Page**
```
WordPress Admin → rbsTravel → Settings → Theme Colors

[Color Picker] Primary Color
[Color Picker] Secondary Color
[Color Picker] Accent Color

[ ] Override theme colors (use custom)
```

---

## 🎨 CSS VARIABLES REFERENTIE:

### **Theme Colors (Auto-detected):**
```css
--rbs-theme-primary     /* Van WordPress theme */
--rbs-theme-secondary   /* Van WordPress theme */
--rbs-theme-accent      /* Van WordPress theme */
--rbs-theme-text        /* Van WordPress theme */
--rbs-theme-background  /* Van WordPress theme */
```

### **Plugin Colors (Mapped):**
```css
--rbs-primary           /* = var(--rbs-theme-primary) */
--rbs-secondary         /* = var(--rbs-theme-secondary) */
--rbs-accent            /* = var(--rbs-theme-accent) */
--rbs-text              /* = var(--rbs-theme-text) */
--rbs-background        /* = var(--rbs-theme-background) */
```

### **Gebruik in CSS:**
```css
.mijn-element {
    color: var(--rbs-primary);
    background: var(--rbs-secondary);
    border-color: var(--rbs-accent);
}

/* Met fallback: */
.mijn-element {
    color: var(--rbs-primary, #667eea);
}
```

---

## 📦 FILES OVERZICHT:

**Nieuwe file:**
```
✅ includes/theme-colors.php
   - RBS_Travel_Theme_Colors class
   - Auto-detection logic
   - CSS generation
```

**Geüpdatete files:**
```
✅ rbs-travel.php
   - Include theme-colors.php

✅ templates/page-travel-listing-advanced.php
   - Theme colors detection
   - CSS variables usage
```

---

## 🚀 VOLGENDE STAPPEN:

### **1. Basis Test:**
```
1. Update plugin
2. Test Advanced template
3. Check of kleuren matchen met theme
```

### **2. Theme-Specifieke Presets:**
```
1. Maak preset JSON files per theme
2. Auto-load based on active theme
3. Override systeem bouwen
```

### **3. Settings Page:**
```
Admin panel:
- Color pickers voor manual override
- Preview van current colors
- Reset naar theme defaults
```

---

## 💡 VOORBEELD THEMES:

### **Tripex Theme:**
```php
// Tripex gebruikt deze variabelen (variable.css):
:root {
    --primary: #066168;      /* Dark Teal */
    --secondary: #85D200;    /* Green */
    --primarylight: #ccf2f9; /* Light Blue */
}
```

**Plugin detecteert automatisch!** ✅

### **GoWild Theme (Voorbeeld):**
```php
// Als GoWild deze settings heeft:
$primary_color = get_theme_mod('gowild_primary', '#228B22');

// Plugin detecteert via customizer!
```

---

## ✅ SAMENVATTING:

**WAT WERKT NU:**
- ✅ Auto-detect theme colors (3 methodes)
- ✅ CSS variables system
- ✅ Advanced template gebruikt theme colors
- ✅ Fallback naar defaults

**VOLGENDE FEATURES:**
- ⏳ Per-theme preset JSON files
- ⏳ Settings page met color pickers
- ⏳ Visual theme preview
- ⏳ Export/import color schemes

**HOE TE GEBRUIKEN:**
1. Theme heeft colors ingesteld (via theme.json, Customizer, of Editor Palette)
2. Plugin detecteert automatisch
3. Template gebruikt CSS variables
4. Kleuren passen automatisch! 🎨

---

**STATUS: Theme Colors System Ready!** 🎉

Plugin past zich nu automatisch aan aan WordPress theme kleuren!
