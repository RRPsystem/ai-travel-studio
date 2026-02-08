# CRITICAL ISSUES FOUND - V2.5.3 FIX NEEDED

## PROBLEEM 1: 🚨 SLIDING PANELS TONEN ALERT() POPUP

**Locatie**: `templates/frontend/single-rbs-travel-idea.php` regel 738, 851

**Huidige code**:
```php
onclick="alert('Meer info over ' . esc_js($name) . '); return false;"
```

**FIX REQUIRED**: Replace alle alert() calls met `showDestinationDetail(this)` en `showHotelDetail(this)`

Kopieer volledige JavaScript code van OLD-BACKUP inclusief:
- `showDestinationDetail()`
- `showHotelDetail()`  
- `showCruiseDetail()`
- `openDetailPanel()`
- `closeDetailPanel()`
- `initGallery()`
- `generate*Detail()` functies

Plus HTML panel structuur:
```html
<div class="detail-panel-overlay" id="detailPanelOverlay" onclick="closeDetailPanel()"></div>
<div class="detail-panel" id="detailPanel">
    <div class="detail-panel-header">
        <div class="detail-panel-title" id="detailPanelTitle"></div>
        <button class="detail-panel-close" onclick="closeDetailPanel()">×</button>
    </div>
    <div class="detail-panel-body" id="detailPanelBody"></div>
</div>
```

---

## PROBLEEM 2: 🏨 HOTELS STAAN ONDERAAN TIMELINE

**Diagnose**: Hotels hebben waarschijnlijk `day = 0` of verkeerde day waarde

**Debug stappen**:
1. Check WordPress debug.log voor hotel debug output
2. Verifieer dat hotels een correcte `day` waarde krijgen in `transform_hotel_data()`
3. Check of hotels BINNEN cruise periode vallen (mogelijk worden ze gefilterd)

**Mogelijke oorzaak**: Hotels tijdens cruise periode worden overgeslagen

**FIX**: Zorg dat hotels BUITEN cruise dagen worden toegevoegd aan timeline

---

## PROBLEEM 3: 📸 CRUISE FOTO'S TOEVOEGEN WERKT NIET

**Status**: Transform functie is al correct - images worden al opgeslagen

**Photo Manager Issue**: De "Foto Toevoegen" knop werkt niet voor cruises

**FIX REQUIRED in Photo Manager**:
- Zorg dat ajax_save_photos() ook cruise images correct handled
- Cruise images worden opgeslagen als simple array (niet als objects met 'url')

---

## PROBLEEM 4: ⏱️ PUBLICEREN DUURT LANG + ERROR

**Oorzaken**:
1. **Syntax errors in v2.5.1** → Upgrade naar v2.5.2  
2. **Rewrite flush tijdens save** → Vertraagt opslaan
3. **REST API timeout** → Server configuratie probleem

**IMMEDIATE FIX**: Upload v2.5.2 (bevat syntax fixes)

---

## ACTION PLAN

1. ✅ Upload v2.5.2 EERST (syntax errors fix)
2. 🔧 Maak v2.5.3 met sliding panels fix
3. 🐛 Debug hotel day assignment (check logs)
4. 📸 Test cruise photo upload
5. ⚡ Optimaliseer save performance

---

## FILES TO EDIT FOR V2.5.3

1. `templates/frontend/single-rbs-travel-idea.php`
   - Add JavaScript sliding panel functions from OLD-BACKUP
   - Replace alert() calls
   - Add panel HTML structure at end of file
   
2. `includes/rbstravel-import.class.php`
   - Add logging to `transform_hotel_data()` for day assignment
   - Verify hotels get correct day values

3. Check Photo Manager metabox
   - Verify cruise photos can be added/saved
