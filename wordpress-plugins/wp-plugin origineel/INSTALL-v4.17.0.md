# 📦 RBS Travel v4.17.0 - Installatie Instructies

## ⚡ SNELSTART (5 minuten)

### Stap 1: Upload Plugin
1. Log in op WordPress admin
2. Ga naar **Plugins → Add New → Upload Plugin**
3. Kies bestand: `rbs-travel-v4.17.0.zip`
4. Klik **Install Now**
5. Klik **Activate Plugin**

### Stap 2: Configureer Fallback Expert (Optioneel)
1. Ga naar **RBS Travel → Settings**
2. Scroll naar "Fallback Expert" sectie
3. Vul in:
   - Naam (bijv. "Ons Expert Team")
   - Titel (bijv. "Reisadviseurs")
   - Beschrijving
   - Upload foto
4. Klik **Save Settings**

### Stap 3: Expert Toewijzen aan Reis
1. Ga naar **RBS Travel → All Travels**
2. Open een reis (klik op titel)
3. Kijk naar rechter sidebar
4. Zie **"👤 Kies Reisexpert"** box
5. Selecteer expert uit dropdown
6. Klik **Update** (boven of onder)
7. Klaar! ✅

### Stap 4: Test Frontend
1. Klik **"View Travel"** (boven in edit pagina)
2. Pagina laadt → Expert verschijnt in sidebar
3. Geen white page meer! 🎉

---

## 🎯 EXPERT SELECTOR - HOE WERKT HET?

### In WordPress Admin:
```
┌─────────────────────────────────────────┐
│  Reis Edit Pagina                       │
├─────────────────────────────────────────┤
│                                         │
│  [Titel]                      SIDEBAR   │
│  [Content]                    ┌────────┐│
│  [Timeline]                   │ 👤 KIES││
│  [Map]                        │ EXPERT ││
│                               ├────────┤│
│                               │Dropdown││
│                               │┌──────┐││
│                               ││Expert││││
│                               ││List  ││││
│                               │└──────┘││
│                               │        ││
│                               │Preview:││
│                               │ [Foto] ││
│                               │ Naam   ││
│                               │ Title  ││
│                               └────────┘│
└─────────────────────────────────────────┘
```

### Op Frontend:
```
┌─────────────────────────────────────────┐
│  Reispagina                             │
├─────────────────────────────────────────┤
│                                         │
│  MAIN CONTENT          SIDEBAR          │
│  ┌────────────┐        ┌──────────────┐│
│  │ Timeline   │        │ Contact Form ││
│  │ Dag 1      │        └──────────────┘│
│  │ Dag 2      │        ┌──────────────┐│
│  │ Dag 3      │        │ 👤 EXPERT    ││
│  │ ...        │        │ ┌──────────┐ ││
│  └────────────┘        │ │  [Foto]  │ ││
│                        │ │  Naam    │ ││
│                        │ │  Title   │ ││
│                        │ │  Bio     │ ││
│                        │ └──────────┘ ││
│                        │ [Ontmoet]    ││
│                        └──────────────┘│
└─────────────────────────────────────────┘
```

---

## 🔧 CONFIGURATIE OPTIES

### Optie 1: Expert per Reis (AANBEVOLEN)
**Gebruik wanneer:** Elke reis een specifieke expert heeft

**Setup:**
1. Open reis edit pagina
2. Selecteer expert uit dropdown
3. Opslaan
4. Klaar!

**Voordeel:** Persoonlijk contact per bestemming

---

### Optie 2: Fallback Expert (ALTIJD GOED IDEE)
**Gebruik wanneer:** Geen specifieke expert, of als backup

**Setup:**
1. RBS Travel → Settings
2. Configureer fallback expert
3. Alle reizen zonder expert gebruiken deze
4. Klaar!

**Voordeel:** Altijd een expert, geen lege widgets

---

### Optie 3: Geen Expert
**Gebruik wanneer:** Je wilt helemaal geen expert tonen

**Setup:**
1. Laat expert dropdown leeg
2. GEEN fallback instellen
3. Widget toont: "Neem contact op"
4. Klaar!

**Voordeel:** Simpel, geen onderhoud

---

## 📸 EXPERT FOTO'S TOEVOEGEN

### Voor Expert Posts:
1. Ga naar **RBS Travel → Experts**
2. Open een expert
3. Rechter sidebar: **Featured Image**
4. Klik **Set featured image**
5. Upload foto (aanbevolen: 400x400px, vierkant)
6. Klik **Set featured image**
7. **Update** expert

### Voor Fallback Expert:
1. RBS Travel → Settings
2. Scroll naar "Fallback Expert"
3. Klik **Upload Image** bij foto veld
4. Selecteer foto
5. **Save Settings**

---

## ✅ TESTING CHECKLIST

Print deze lijst en vink af tijdens installatie:

### Backend Tests:
- [ ] Plugin geactiveerd zonder errors
- [ ] Settings pagina laadt correct
- [ ] Expert meta box verschijnt op reis edit pagina
- [ ] Dropdown toont alle experts
- [ ] Expert preview werkt
- [ ] Save werkt (expert blijft geselecteerd na refresh)

### Frontend Tests:
- [ ] Reispagina laadt zonder crashes
- [ ] Expert widget verschijnt in sidebar
- [ ] Foto laadt correct
- [ ] Naam en titel worden getoond
- [ ] "Ontmoet [Naam]" link werkt
- [ ] Fallback werkt als geen expert geselecteerd

### Edge Case Tests:
- [ ] Pagina werkt als expert verwijderd wordt
- [ ] Pagina werkt als expert unpublished wordt
- [ ] Fallback wordt getoond als expert niet bestaat
- [ ] Geen expert → Generieke tekst verschijnt

---

## 🚨 VEELVOORKOMENDE PROBLEMEN

### "Ik zie geen expert dropdown"
**Check:**
1. Is plugin geactiveerd?
2. Zit je op reis **edit** pagina? (niet overzichtspagina)
3. Post type = "rbs-travel-idea"? (niet normal post)

**Oplossing:**
- Refresh pagina (hard refresh: Ctrl+F5)
- Clear WordPress cache
- Deactiveer/activeer plugin opnieuw

---

### "Dropdown is leeg"
**Check:**
1. RBS Travel → Experts
2. Zijn er experts met status **"Published"**?

**Oplossing:**
- Publiceer minstens 1 expert
- Of: Gebruik alleen fallback expert

---

### "Expert verschijnt niet op frontend"
**Check:**
1. Is expert geselecteerd EN opgeslagen?
2. Is expert nog gepubliceerd?
3. Is fallback expert ingesteld?

**Oplossing:**
- Open reis, controleer expert selectie
- Controleer expert post status
- Stel fallback expert in als backup

---

### "Pagina is nog steeds wit"
**Check:**
1. `wp-content/debug.log` voor PHP errors
2. Browser console voor JS errors
3. Theme compatibility

**Oplossing:**
1. Enable WP_DEBUG in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

2. Check debug.log voor errors
3. Test met WordPress default theme (Twenty Twenty-Four)
4. Deactiveer andere plugins tijdelijk

---

## 🎓 ADVANCED: FALLBACK LOGICA

### Hoe het werkt:
```
┌─────────────────────────────────────────┐
│  rbstravel_get_expert_for_travel()      │
├─────────────────────────────────────────┤
│                                         │
│  1. Check rbs_assigned_expert meta      │
│     ├─ Bestaat? → Gebruik die expert    │
│     └─ Niet? → Ga naar stap 2          │
│                                         │
│  2. Check fallback expert settings      │
│     ├─ Ingesteld? → Gebruik fallback    │
│     └─ Niet? → Ga naar stap 3          │
│                                         │
│  3. Return false                        │
│     └─ Widget toont generieke tekst     │
│                                         │
└─────────────────────────────────────────┘
```

### Handmatig Testen in PHP:
```php
// Test expert ophalen
$expert = \RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel($post_id);
var_dump($expert);

// Expected output:
array(
  'id' => 123,
  'name' => 'John Doe',
  'title' => 'Afrika Specialist',
  'image_url' => 'https://...',
  'is_fallback' => false
)
```

---

## 📞 SUPPORT & DEBUG

### Enable Debug Mode:
```php
// Add to wp-config.php (BEFORE "That's all, stop editing!")
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

### Check Logs:
```
Locatie: /wp-content/debug.log

Zoek naar:
- "Expert widget error"
- "RBS Travel"
- "Fatal error"
- "Call to undefined"
```

### Test Helper Function:
```php
// In template of functions.php
add_action('wp_footer', function() {
    if (is_singular('rbs-travel-idea')) {
        global $post;
        $expert = \RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel($post->ID);
        echo '<!-- EXPERT DATA: ';
        print_r($expert);
        echo ' -->';
    }
});
```

### Database Check:
```sql
-- Check expert assignment
SELECT post_id, meta_value 
FROM wp_postmeta 
WHERE meta_key = 'rbs_assigned_expert';

-- Check expert posts
SELECT ID, post_title, post_status 
FROM wp_posts 
WHERE post_type = 'rbs-expert';
```

---

## 🎉 SUCCESS!

Als alles werkt zie je:
- ✅ Expert meta box in reis edit pagina
- ✅ Expert widget op reispagina
- ✅ Geen white page crashes meer
- ✅ Snelle page load
- ✅ Mooie expert presentatie

**Gefeliciteerd met je nieuwe expert selector! 🚀**

---

## 📋 QUICK REFERENCE

### Belangrijke Files:
```
rbs-travel/
├── rbs-travel.php (v4.17.0)
├── includes/
│   ├── rbstravel-posttype.class.php (Meta box + Save)
│   └── helpers/
│       └── rbstravel-expert-helper.php (Expert logica)
└── templates/
    └── frontend/
        └── single-rbs-travel-idea.php (Frontend display)
```

### Meta Keys:
- `rbs_assigned_expert` → Expert Post ID per reis
- `_expert_specialisation` → Expert titel/specialisatie
- `_expert_countries` → Expert landen (niet meer gebruikt)

### Functions:
- `rbstravel_get_expert_for_travel($post_id)` → Get expert data
- `rbstravel_render_expert_widget($post_id)` → Display widget
- `rbstravel_get_fallback_expert()` → Get fallback from settings

---

**Veel succes met de installatie! 💪**
