# 🔧 RRP System WordPress Plugin - Status & Te Fixen Issues

**Datum:** 11 December 2024  
**Plugin:** RRP System v4.19.0  
**Doel:** Overzicht huidige status en wat er nog gefixed moet worden

---

## ✅ WAT WERKT AL

### **1. Plugin Basis**
- ✅ Post type `rbs-travel-idea` geregistreerd
- ✅ 4 Taxonomies aangemaakt (location, tour-type, tour-theme, tour-service)
- ✅ Admin menu items
- ✅ Settings pagina met API credentials

### **2. Import Functionaliteit**
- ✅ Remote Travels pagina (import interface)
- ✅ Basis import van reizen
- ✅ Meta fields opslag (description, price, nights, etc.)
- ✅ Featured image download
- ✅ Destinations, Hotels, Transports import
- ✅ **Cruise data import** (travel_cruises meta field)
- ✅ Automatische taxonomie toewijzing (inclusief "Cruise" term)

### **3. REST API Endpoints**
- ✅ `/wp-json/rbs-travel/v1/ideas` - Lijst van reizen
- ✅ `/wp-json/rbs-travel/v1/ideas/{id}` - Enkele reis
- ✅ `/wp-json/rbs-travel/v1/ideas/slug/{slug}` - Reis op slug
- ✅ `/wp-json/rbs-travel/v1/filters` - Filter opties
- ✅ Cruise data wordt meegestuurd in API responses

### **4. Frontend Templates**
- ✅ Single travel templates (meerdere varianten)
- ✅ Travel listing page template
- ✅ Timeline view met cruises
- ✅ Cruise info display op detail pagina's

### **5. Theme System**
- ✅ Theme colors helper (includes/theme-colors.php)
- ✅ CSS variables voor branding

---

## 🐛 BEKENDE ISSUES DIE NOG GEFIXED MOETEN WORDEN

### **Issue 1: Cruise Import Verificatie**
**Status:** 🔍 NIET GETEST  
**Probleem:** Onzeker of cruise data correct wordt opgeslagen  

**Te testen:**
```php
// Test of cruise data correct wordt opgeslagen
$post_id = [IMPORT_POST_ID];
$cruises = get_post_meta($post_id, 'travel_cruises', true);
var_dump($cruises); // Should show array with cruise data
```

**Check:**
- [ ] `travel_cruises` meta field bestaat na import
- [ ] Cruise data heeft correcte structuur (shipId, shipName, nights, region)
- [ ] "Cruise" term automatisch toegevoegd aan tour-type taxonomy

**Mogelijk Fix:** Zie `includes/rbstravel-import.class.php` regel 302-339

---

### **Issue 2: REST API Cruise Filter**
**Status:** 🔍 NIET GETEST  
**Probleem:** Filter op "Cruise" tour-type mogelijk niet werkend

**Test URL:**
```
/wp-json/rbs-travel/v1/ideas?tour_type=Cruise
```

**Verwacht resultaat:**
- Alleen reizen met cruises worden getoond
- Elk resultaat heeft `has_cruise: true`

**Mogelijk probleem:** 
- Taxonomy slug mismatch ("Cruise" vs "cruise")
- Term niet correct toegewezen tijdens import

**Fix locatie:** `includes/api-rest-endpoints.php` regel 98-103

---

### **Issue 3: Frontend Cruise Display**
**Status:** ⚠️ GEDEELTELIJK  
**Probleem:** Cruise info mogelijk niet overal zichtbaar

**Te checken:**
- [ ] 🚢 Cruise badge zichtbaar op travel cards
- [ ] Ship naam getoond
- [ ] Aantal nachten getoond
- [ ] Cruise filter werkt in listing page

**Files te checken:**
- `templates/frontend/archive-rbs-travel-idea.php`
- `templates/frontend/listing-*.php`

---

### **Issue 4: Cruise-Specific REST Endpoints Ontbreken**
**Status:** ❌ NIET GEÏMPLEMENTEERD  
**Probleem:** Advanced cruise endpoints niet aanwezig

**Wat ontbreekt:** (Zie TRAVEL-COMPOSITOR-CRUISE-API.md)
```php
// Deze endpoints bestaan NOG NIET:
GET /wp-json/rbs-travel/v1/cruises              // Cruise search
GET /wp-json/rbs-travel/v1/cruises/{id}/departures  // Price calendar
GET /wp-json/rbs-travel/v1/cruises/{id}/itinerary   // Dag-bij-dag route
GET /wp-json/rbs-travel/v1/ships                // Schepen lijst
GET /wp-json/rbs-travel/v1/ships/{id}           // Schip details
GET /wp-json/rbs-travel/v1/ports                // Haven lijst
GET /wp-json/rbs-travel/v1/ports/{id}           // Haven details
GET /wp-json/rbs-travel/v1/cruise-lines/{line}  // Cruise line info
```

**Prioriteit:** 🔴 HOOG (als cruise functionaliteit volledig moet zijn)

**Implementatie:** Zie TRAVEL-COMPOSITOR-CRUISE-API.md regel 475-535

---

### **Issue 5: Cache System Ontbreekt**
**Status:** ❌ NIET GEÏMPLEMENTEERD  
**Probleem:** Geen caching voor cruise data (API calls kunnen traag zijn)

**Wat nodig:**
```php
// Cache voor ports/ships om API calls te verminderen
function rbs_get_cached_ports() {
    $cache_key = 'rbs_cruise_ports_all';
    $ports = get_transient($cache_key);
    
    if (false === $ports) {
        $ports = rbs_fetch_ports_from_api();
        set_transient($cache_key, $ports, DAY_IN_SECONDS);
    }
    
    return $ports;
}
```

**Prioriteit:** 🟡 MEDIUM

---

### **Issue 6: Error Logging Te Uitgebreid**
**Status:** ⚠️ CLEANUP NEEDED  
**Probleem:** Te veel debug logging in productie

**Voorbeeld:** `includes/rbstravel-import.class.php`
- Regel 17-42: Uitgebreide debug logs
- Regel 143-347: Error logs bij elke import

**Fix:**
```php
// Alleen loggen als WP_DEBUG = true
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Debug info here...');
}
```

**Prioriteit:** 🟢 LAAG (maar wel cleanup voor productie)

---

### **Issue 7: Database Schema Ontbreekt**
**Status:** ❌ NIET GEÏMPLEMENTEERD  
**Probleem:** Geen dedicated tables voor cruise ports/ships

**Wat ontbreekt:**
```sql
-- Zie TRAVEL-COMPOSITOR-CRUISE-API.md regel 539-580
CREATE TABLE wp_rbs_cruise_ports (...)
CREATE TABLE wp_rbs_cruise_ships (...)
```

**Vraag:** Is dit nodig? Of is post meta voldoende?

**Prioriteit:** 🟡 MEDIUM (afhankelijk van data volume)

---

### **Issue 8: Frontend Templates Inconsistent**
**Status:** ⚠️ CLEANUP NEEDED  
**Probleem:** Te veel template varianten (single-rbs-travel-idea-*.php)

**Gevonden templates:**
- single-rbs-travel-idea.php
- single-rbs-travel-idea-modern.php
- single-rbs-travel-idea-classic.php
- single-rbs-travel-idea-timeline.php
- single-rbs-travel-idea-minimal.php
- single-rbs-travel-idea-DEBUG-OLD.php (moet weg!)
- single-rbs-travel-idea-safe.php
- single-rbs-travel-idea-v2.4.0.php

**Fix:** Consolideren naar 2-3 main templates + template selector

**Prioriteit:** 🟢 LAAG (werkt wel, maar rommelig)

---

## 📋 PRIORITEIT LIJST

### **🔴 HOOGSTE PRIORITEIT (Nu doen)**

1. **Test cruise import**
   - Import een reis met cruise data
   - Verify `travel_cruises` meta field
   - Check of "Cruise" taxonomy term wordt toegevoegd

2. **Test REST API cruise filter**
   - Test `/wp-json/rbs-travel/v1/ideas?tour_type=Cruise`
   - Verify alleen cruise reizen worden getoond

3. **Test frontend cruise display**
   - Check of cruise badge zichtbaar is
   - Verify cruise info op detail pagina

### **🟡 MEDIUM PRIORITEIT (Volgende fase)**

4. **Implementeer advanced cruise endpoints** (als nodig)
   - Cruise departures (price calendar)
   - Cruise itinerary
   - Ship details
   - Port details

5. **Voeg cache system toe**
   - Cache ports/ships data
   - Cache cruise search results

6. **Database schema overwegen**
   - Beslissen: post meta vs dedicated tables?

### **🟢 LAGE PRIORITEIT (Cleanup)**

7. **Debug logging cleanup**
   - Verwijder excessive error_log calls
   - Gebruik WP_DEBUG conditionals

8. **Template cleanup**
   - Consolideer templates
   - Verwijder DEBUG/old files

---

## 🧪 TEST PROTOCOL

### **Quick Test (5 minuten)**
```bash
# 1. Activeer plugin (als nog niet actief)
# 2. Ga naar: RBS Travel → Remote Travels
# 3. Import 1 reis die een cruise bevat
# 4. Check post meta:
```
```php
$post_id = [IMPORTED_POST_ID];
$cruises = get_post_meta($post_id, 'travel_cruises', true);
echo '<pre>' . print_r($cruises, true) . '</pre>';
```
```bash
# 5. Test REST API:
curl https://your-site.com/wp-json/rbs-travel/v1/ideas
# Check of "has_cruise" field aanwezig is

# 6. Test filter:
curl https://your-site.com/wp-json/rbs-travel/v1/ideas?tour_type=Cruise
# Verify alleen cruise reizen
```

### **Full Test (30 minuten)**
- Volg complete TESTING-CHECKLIST.md
- Test alle 8 fasen
- Document resultaten

---

## 🔧 WAAR TE FIXEN

### **Belangrijkste Files:**

**Import Logic:**
- `includes/rbstravel-import.class.php` (regel 302-339 voor cruises)
- `includes/rbstravel-remote-travels.class.php`

**REST API:**
- `includes/api-rest-endpoints.php` (regel 1-402)
- Voeg nieuwe endpoints toe vanaf regel 54

**Frontend:**
- `templates/frontend/single-rbs-travel-idea.php`
- `templates/frontend/archive-rbs-travel-idea.php`
- `templates/frontend/listing-*.php`

**Cache System:**
- Maak nieuwe file: `includes/rbstravel-cache.class.php`

---

## 📞 VOLGENDE STAPPEN

**Wat moet er NU gebeuren?**

1. **Test de huidige implementatie** (gebruik Quick Test protocol)
2. **Identificeer welke issues echt problemen zijn**
3. **Prioriteer fixes op basis van test resultaten**
4. **Implementeer hoogste prioriteit fixes eerst**

**Vragen voor gebruiker:**

1. ❓ Moet de plugin cruise-specific endpoints ondersteunen? (Issue #4)
2. ❓ Is post meta voldoende of moet er dedicated database tables komen? (Issue #7)
3. ❓ Welke template variant is de "main" variant? (Issue #8)
4. ❓ Is er al een test website waar ik kan testen?

---

## 💡 OPMERKINGEN

### **Positief:**
- ✅ Code structuur is goed
- ✅ Cruise import is al geïmplementeerd
- ✅ REST API bevat cruise data
- ✅ Frontend templates tonen cruise info

### **Aandachtspunten:**
- ⚠️ Te veel error_log() calls (performance impact)
- ⚠️ Te veel template varianten (verwarrend)
- ⚠️ Geen caching (kan traag worden)
- ⚠️ Geen geautomatiseerde tests

### **Ontbrekend (maar misschien niet nodig):**
- Advanced cruise search
- Price calendar voor cruises
- Interactive maps voor cruise routes
- Cruise line comparison

---

**Klaar om te beginnen met fixes! Laat me weten welke issue je eerst wilt aanpakken! 🚀**
