# 🧪 RBS Travel Plugin - Testing Checklist

**Datum:** 7 December 2024  
**Doel:** Controleren of alle recente updates correct werken

---

## ✅ PHASE 1: BASIC FUNCTIONALITY

### **1.1 Plugin Activatie**
- [ ] Plugin is geactiveerd zonder errors
- [ ] Admin menu items zijn zichtbaar
- [ ] Settings pagina laadt correct

### **1.2 Database**
- [ ] Post type `rbs-travel-idea` bestaat
- [ ] Taxonomies zijn aangemaakt:
  - [ ] `location` 
  - [ ] `tour-type`
  - [ ] `tour-theme`
  - [ ] `tour-service`

**Test Command:**
```php
// In WordPress Admin → Tools → Site Health → Info → Database
// Check wp_posts table for post_type 'rbs-travel-idea'
// Check wp_term_taxonomy table for taxonomies
```

---

## ✅ PHASE 2: IMPORT FUNCTIONALITY

### **2.1 Ideas Import (Existing)**
- [ ] Go to: RBS Travel → Remote Travels
- [ ] Check if travels are listed
- [ ] Import 1 travel idea
- [ ] Verify in Posts list
- [ ] Check meta fields are saved

**Expected Meta Fields:**
```
travel_description
travel_price_per_person
travel_number_of_nights
travel_departure_date
travel_destinations
travel_transports
travel_hotels
travel_cruises ← NEW!
```

### **2.2 Cruise Import (NEW)**
- [ ] Import a travel that contains cruise
- [ ] Verify `travel_cruises` meta field exists
- [ ] Check if "Cruise" term added to `tour-type` taxonomy
- [ ] Verify cruise data structure:

**Expected Cruise Data:**
```json
{
  "id": "cruise_12345",
  "shipId": "ship_harmony",
  "shipName": "Harmony of the Seas",
  "cruiseLine": "ROYAL_CARIBBEAN",
  "nights": 7,
  "region": "MEDITERRANEAN"
}
```

**Test Query:**
```php
// In WordPress Admin → Tools → WP-CLI or via wp_ajax
$post_id = 123; // Replace with actual post ID
$cruises = get_post_meta($post_id, 'travel_cruises', true);
var_dump($cruises);
```

---

## ✅ PHASE 3: REST API

### **3.1 Ideas Endpoint**

**Test URL:**
```
https://your-site.com/wp-json/rbs-travel/v1/ideas
```

**Expected Response:**
```json
{
  "ideas": [
    {
      "id": 123,
      "title": "Mediterranean Cruise",
      "cruises": [...],
      "has_cruise": true,
      "tour_types": ["Vliegreis", "Cruise"]
    }
  ],
  "total": 10,
  "pages": 1
}
```

**Tests:**
- [ ] Endpoint responds (200 OK)
- [ ] Returns array of ideas
- [ ] Each idea has `cruises` field
- [ ] Each idea has `has_cruise` boolean
- [ ] Cruise data is properly formatted

### **3.2 Filters Endpoint**

**Test URL:**
```
https://your-site.com/wp-json/rbs-travel/v1/filters
```

**Expected Response:**
```json
{
  "locations": ["Barcelona", "Rome"],
  "tour_types": ["Vliegreis", "Cruise"],
  "tour_themes": ["Adventure", "Family"],
  "price_range": {
    "min": 500,
    "max": 5000
  }
}
```

**Tests:**
- [ ] Returns available filters
- [ ] "Cruise" appears in tour_types
- [ ] All taxonomies present

### **3.3 Filter by Cruise**

**Test URL:**
```
https://your-site.com/wp-json/rbs-travel/v1/ideas?tour_type=Cruise
```

**Tests:**
- [ ] Only returns ideas with cruises
- [ ] All results have `has_cruise: true`
- [ ] No non-cruise ideas in results

---

## ✅ PHASE 4: FRONTEND TEMPLATES

### **4.1 Travel Listing Page**

**Create Test Page:**
1. Go to Pages → Add New
2. Title: "Test Reizen Overzicht"
3. Select Template: "Travel Listing (Advanced + Layout Options)"
4. Publish

**Tests:**
- [ ] Page loads without errors
- [ ] Filter bar is visible
- [ ] Search input works
- [ ] Dropdown filters populated
- [ ] Layout switcher works (Grid/List)
- [ ] Travel cards display correctly

### **4.2 Filter Functionality**

**Tests:**
- [ ] Search by keyword filters results
- [ ] Location filter works
- [ ] Tour type filter works
- [ ] Price range filter works
- [ ] Multiple filters work together
- [ ] "No results" message shows when appropriate

### **4.3 Cruise-Specific Display**

**For travels with cruises:**
- [ ] "🚢 Cruise" badge shows on card
- [ ] Cruise info visible in card
- [ ] Ship name displayed
- [ ] Cruise nights shown
- [ ] Filter by "Cruise" tour-type works

**Visual Check:**
```html
<!-- Should see something like: -->
<div class="travel-card">
  <div class="badges">
    <span class="badge">🚢 Cruise</span>
  </div>
  <div class="cruise-info">
    <span>Harmony of the Seas</span>
    <span>7 nachten</span>
  </div>
</div>
```

---

## ✅ PHASE 5: THEME COLORS

### **5.1 Theme Colors System**

**Check File Exists:**
- [ ] `includes/theme-colors.php` exists
- [ ] Class `RBS_Travel_Theme_Colors` is loaded

**Test:**
```php
// Add to functions.php temporarily:
$colors = RBS_Travel_Theme_Colors::get_theme_colors();
var_dump($colors);

// Expected output:
array(
  'primary' => '#667eea',
  'secondary' => '#764ba2',
  ...
)
```

### **5.2 CSS Variables**

**Check on Frontend:**
Open browser dev tools → Elements → Inspect `<body>` or `<html>` tag

**Expected CSS Variables:**
```css
:root {
  --rbs-primary: #667eea;
  --rbs-secondary: #764ba2;
  --rbs-accent: #f093fb;
  --rbs-neutral: #a0aec0;
  --rbs-success: #48bb78;
  --rbs-warning: #ed8936;
  --rbs-error: #f56565;
  --rbs-info: #4299e1;
}
```

**Tests:**
- [ ] CSS variables are in `<style>` tag in `<head>`
- [ ] Colors apply to buttons
- [ ] Gradients use theme colors
- [ ] Hover effects use theme colors

---

## ✅ PHASE 6: ADMIN INTERFACE

### **6.1 Remote Travels Page**

**Go to:** RBS Travel → Remote Travels

**Tests:**
- [ ] List of remote travels loads
- [ ] "Import" button visible for each travel
- [ ] Import status shows correctly
- [ ] Can import travel successfully
- [ ] Success message appears

### **6.2 Travel Ideas Posts List**

**Go to:** RBS Travel Ideas (post type)

**Tests:**
- [ ] Imported travels appear in list
- [ ] Featured images visible
- [ ] Taxonomies show in columns
- [ ] Quick edit works
- [ ] Trash/Delete works

### **6.3 Settings Page**

**Check if exists:**
- [ ] RBS Travel → Settings menu exists
- [ ] API credentials fields present
- [ ] Save settings works

---

## ✅ PHASE 7: PERFORMANCE & ERRORS

### **7.1 Error Log Check**

**Check WordPress Debug Log:**
```php
// Add to wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check: wp-content/debug.log
```

**Look for:**
- [ ] No PHP errors
- [ ] No warnings
- [ ] No deprecated notices
- [ ] Import logs show success

### **7.2 Performance**

**Test:**
- [ ] Page load time < 2 seconds
- [ ] REST API response < 1 second
- [ ] No slow queries
- [ ] Images load properly

**Tools:**
- Browser DevTools → Network tab
- Query Monitor plugin
- GTmetrix or PageSpeed Insights

---

## ✅ PHASE 8: BROWSER COMPATIBILITY

**Test in:**
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Chrome
- [ ] Mobile Safari

**Check:**
- [ ] Layout displays correctly
- [ ] Filters work
- [ ] Cards are responsive
- [ ] No console errors

---

## 🐛 KNOWN ISSUES TO CHECK

### **Issue 1: Cruise Import**
**Problem:** Cruise data niet correct opgeslagen  
**Check:**
```php
$post_id = 123;
$cruises = get_post_meta($post_id, 'travel_cruises', true);
if (empty($cruises)) {
    echo "❌ No cruise data found!";
} else {
    echo "✅ Cruise data exists: " . count($cruises) . " cruises";
}
```

### **Issue 2: Taxonomy Not Added**
**Problem:** "Cruise" term niet automatisch toegevoegd  
**Check:**
```php
$post_id = 123;
$terms = wp_get_post_terms($post_id, 'tour-type', ['fields' => 'names']);
if (in_array('Cruise', $terms)) {
    echo "✅ Cruise term present";
} else {
    echo "❌ Cruise term missing";
}
```

### **Issue 3: REST API Missing Data**
**Problem:** REST API geeft geen cruise data terug  
**Check:**
```bash
curl https://your-site.com/wp-json/rbs-travel/v1/ideas | jq '.ideas[0].cruises'
# Should return cruise array, not null
```

### **Issue 4: Filter Not Working**
**Problem:** Filter op "Cruise" geeft geen resultaten  
**Check:**
1. Go to: `/wp-json/rbs-travel/v1/ideas?tour_type=Cruise`
2. Should return only cruise travels
3. If empty, check if "Cruise" term exists in taxonomy

---

## 📊 TEST RESULTS TEMPLATE

**Copy this and fill out during testing:**

```
# Test Results - [Date]

## Phase 1: Basic Functionality
- Plugin Activation: ✅ / ❌
- Database Setup: ✅ / ❌
- Notes: _______________

## Phase 2: Import
- Ideas Import: ✅ / ❌
- Cruise Import: ✅ / ❌
- Notes: _______________

## Phase 3: REST API
- Ideas Endpoint: ✅ / ❌
- Filters Endpoint: ✅ / ❌
- Cruise Filter: ✅ / ❌
- Notes: _______________

## Phase 4: Frontend
- Listing Page: ✅ / ❌
- Filters Work: ✅ / ❌
- Cruise Display: ✅ / ❌
- Notes: _______________

## Phase 5: Theme Colors
- CSS Variables: ✅ / ❌
- Colors Applied: ✅ / ❌
- Notes: _______________

## Phase 6: Admin
- Remote Travels: ✅ / ❌
- Posts List: ✅ / ❌
- Settings: ✅ / ❌
- Notes: _______________

## Phase 7: Performance
- Error Log: ✅ / ❌
- Load Times: ✅ / ❌
- Notes: _______________

## Phase 8: Browsers
- Desktop: ✅ / ❌
- Mobile: ✅ / ❌
- Notes: _______________

## Summary
Total Tests: ____ / 50
Pass Rate: ____%
Critical Issues: ____
Minor Issues: ____
Ready for Production: YES / NO

## Next Steps
1. _______________
2. _______________
3. _______________
```

---

## 🚀 QUICK START TESTING

**Minimal Test (5 minutes):**
1. ✅ Activate plugin
2. ✅ Import 1 travel with cruise
3. ✅ Check REST API: `/wp-json/rbs-travel/v1/ideas`
4. ✅ Create test page with template
5. ✅ Check if cruise shows on page

**Full Test (30 minutes):**
- Run all phases above
- Document results
- Fix critical issues

---

## 📞 SUPPORT

**Als er issues zijn:**
1. Check WordPress debug log
2. Check browser console
3. Test REST API directly
4. Share error messages with me!

**Ik help je met:**
- 🐛 Debuggen van errors
- 🔧 Fixen van issues
- 📝 Uitbreiden van functionaliteit
- 🎨 Verbeteren van UI

**Klaar om te testen! 🧪😊**
