# 🎨 v4.1.0 - BRAND KLEUREN CUSTOMIZATION

## ✅ WAT IS NIEUW?

**Reisbureaus kunnen nu hun eigen huisstijl kleuren instellen!**

---

## 📦 NIEUWE VERSIE

```
rbs-travel-v4.1.0-BRAND-COLORS.zip
```

**Locatie:**
```
c:\Users\info\CascadeProjects\website-builder\wordpress-plugin\rbs-travel-v4.1.0-BRAND-COLORS.zip
```

---

## 🎨 NIEUWE FEATURES

### **1. KLEUREN INSTELLEN IN WORDPRESS**

**Waar:**
```
WordPress Admin → rbsTravel → Settings
→ Scroll naar: "🎨 Brand Kleuren"
```

**Beschikbare kleuren:**
- **Hoofdkleur (Primary)** - Voor knoppen, links, accenten
- **Accentkleur (Secondary)** - Voor secundaire elementen
- **Koppen Kleur** - Voor titels en koppen
- **Tekst Kleur** - Voor normale tekst

### **2. WORDPRESS COLOR PICKER**

- ✅ Klik op kleurveld → Color picker opent
- ✅ Visuele kleurkiezer
- ✅ Hex code input (#6366f1)
- ✅ Live preview (toekomstig)

### **3. AUTO APPLY**

Kleuren worden **automatisch toegepast** op:
- Knoppen ("Lees verder", "Meer info")
- Timeline titels
- Koppen (H1, H2, H3)
- Tekst in detail panelen

---

## 📋 HOE TE GEBRUIKEN

### **STAP 1: INSTALLEER v4.1.0**

```
WordPress → Plugins → Add New → Upload
→ rbs-travel-v4.1.0-BRAND-COLORS.zip
→ Install → Activate
```

### **STAP 2: OPEN SETTINGS**

```
WordPress Admin → rbsTravel → Settings
→ Scroll naar: "🎨 Brand Kleuren"
```

### **STAP 3: KIES KLEUREN**

**Voorbeeld voor blauw reisbureau:**
```
Hoofdkleur:    #0066CC  (blauw)
Accentkleur:   #00CC66  (groen)
Koppen:        #003366  (donkerblauw)
Tekst:         #666666  (grijs)
```

**Voorbeeld voor oranje reisbureau:**
```
Hoofdkleur:    #FF6600  (oranje)
Accentkleur:   #FFCC00  (geel)
Koppen:        #CC3300  (donkeroranje)
Tekst:         #333333  (donkergrijs)
```

### **STAP 4: SAVE & TEST**

```
1. Klik "Save Settings"
2. Open reis pagina
3. Hard refresh (CTRL+SHIFT+R)
4. Check kleuren!
```

---

## 🎯 TECHNISCHE DETAILS

### **Hoe Het Werkt:**

**1. Settings worden opgeslagen:**
```php
$current_settings['primary_color'] = '#6366f1';
$current_settings['secondary_color'] = '#10b981';
$current_settings['heading_color'] = '#1f2937';
$current_settings['text_color'] = '#4b5563';
```

**2. CSS wordt geïnjecteerd:**
```php
$primary_color = RBS_TRAVEL_Settings::GetSetting('primary_color', '#6366f1');

echo '<style id="rbs-travel-brand-colors">';
echo ':root { --rbs-primary: ' . $primary_color . '; }';
echo '.rbs-button { background: ' . $primary_color . ' !important; }';
echo '</style>';
```

**3. Kleuren overschrijven defaults:**
```css
/* Default (hardcoded) */
background: #6366f1;

/* Brand color override */
background: #FF6600 !important;  /* Reisbureau oranje */
```

---

## 🔧 VOOR DEVELOPERS

### **CSS Variables Toegevoegd:**

```css
:root {
  --rbs-primary: #6366f1;
  --rbs-secondary: #10b981;
  --rbs-heading: #1f2937;
  --rbs-text: #4b5563;
}
```

**Gebruik in custom CSS:**
```css
.mijn-custom-element {
  color: var(--rbs-primary);
  border-color: var(--rbs-secondary);
}
```

### **Selectors die worden overschreven:**

```css
/* Knoppen */
.rbs-button, a[onclick*="Detail"] { 
  background: var(--rbs-primary) !important; 
}

/* Titels */
.rbs-timeline-title, h1, h2, h3 { 
  color: var(--rbs-heading) !important; 
}

/* Tekst */
.rbs-timeline-details { 
  color: var(--rbs-text) !important; 
}
```

---

## 📊 STANDAARD KLEUREN

**Default (als geen kleuren ingesteld):**

| Element | Kleur | Hex |
|---------|-------|-----|
| Primary | Indigo Blue | #6366f1 |
| Secondary | Green | #10b981 |
| Heading | Dark Gray | #1f2937 |
| Text | Gray | #4b5563 |

**Deze zijn gekozen omdat:**
- ✅ Professioneel
- ✅ Goed contrast (toegankelijk)
- ✅ Passen bij reisbranche
- ✅ Werken op alle schermen

---

## 🎨 KLEUR COMBINATIE VOORBEELDEN

### **1. TRADITIONEEL BLAUW (KLM style)**
```
Primary:   #003E7E  (donkerblauw)
Secondary: #00A1DE  (lichtblauw)
Heading:   #002855  (navy)
Text:      #4A4A4A  (donkergrijs)
```

### **2. WARM ORANJE (Corendon style)**
```
Primary:   #FF6600  (oranje)
Secondary: #FFB800  (goudgeel)
Heading:   #CC3300  (donkeroranje)
Text:      #333333  (grijs)
```

### **3. MODERN GROEN (Eco travel)**
```
Primary:   #00A651  (groen)
Secondary: #78BE20  (lichtgroen)
Heading:   #006837  (donkergroen)
Text:      #3C3C3C  (grijs)
```

### **4. LUXE PAARS (Premium)**
```
Primary:   #6B2C91  (paars)
Secondary: #D4AF37  (goud)
Heading:   #4A1C6B  (donkerpaars)
Text:      #5A5A5A  (grijs)
```

---

## ✅ TESTEN CHECKLIST

Na het instellen van kleuren, test:

- [ ] Knoppen hebben juiste kleur (Lees verder, Meer info)
- [ ] Timeline titels hebben heading color
- [ ] Tekst heeft text color
- [ ] Kleuren blijven na page refresh
- [ ] Kleuren blijven na cache clear
- [ ] Contrast is voldoende (leesbaarheid)
- [ ] Werkt op desktop EN mobiel

---

## 🔄 UPGRADE INSTRUCTIES

**Van v4.0.x naar v4.1.0:**

```
1. Deactiveer oude versie
2. Verwijder oude versie
3. Upload v4.1.0
4. Activeer
5. Ga naar Settings → Brand Kleuren
6. Stel kleuren in
7. Save Settings
8. Test!
```

**GEEN data verlies** - alle reizen blijven intact!

---

## 🆕 TOEKOMSTIGE FEATURES (FASE 2)

**Wat komt er nog:**

1. **Layout Templates**
   - Compact vs Uitgebreid
   - Photo links/rechts/boven
   - Timeline stijlen

2. **Font Customization**
   - Eigen lettertype kiezen
   - Font sizes aanpassen

3. **Advanced Colors**
   - Meer kleur opties
   - Gradient support
   - Dark mode

4. **Live Preview**
   - Zie kleuren real-time
   - Geen page refresh nodig

---

## 📞 SUPPORT

**Problemen met kleuren?**

**Check 1: Versie**
```
WordPress → Plugins → rbsTravel
Moet tonen: "Versie 4.1.0"
```

**Check 2: HTML Source**
```
Open reis pagina → CTRL+U
Zoek: "v4.1.0"
Zoek: "rbs-travel-brand-colors"
```

**Als je dit ZIET:**
```html
<!-- 🚀 RBS TRAVEL v4.1.0 - BRAND COLORS 🎨 -->
<style id="rbs-travel-brand-colors">
:root {
  --rbs-primary: #FF6600;
  ...
}
</style>
```

→ **Kleuren zijn actief!**

**Als NIET:**
→ Cache probleem
→ Clear alle cache (WP-Optimize + Browser)
→ Hard refresh (CTRL+SHIFT+R)

---

## 🎉 KLAAR VOOR REISBUREAUS!

**Met v4.1.0 kan elk reisbureau:**
- ✅ Eigen huisstijl kleuren instellen
- ✅ In 2 minuten aangepast
- ✅ Professionele uitstraling
- ✅ Consistent met website

**FASE 1 COMPLEET!** 🚀

**Volgende stap:**
- Upload naar reisbureaus
- Gather feedback
- Implementeer FASE 2 (layouts, fonts, etc.)

**Veel plezier ermee!** 🎨
