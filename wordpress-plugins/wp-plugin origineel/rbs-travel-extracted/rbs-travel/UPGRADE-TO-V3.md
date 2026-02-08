# 🚀 UPGRADE NAAR v3.0.0 - GEFORCEERDE UPDATE

## ⚠️ BELANGRIJK

v2.6.9 werkt NIET door **template caching**. Daarom deze MAJOR update naar **v3.0.0**.

---

## 📦 NIEUWE VERSIE

```
rbs-travel-v3.0.0-MAJOR-UPDATE.zip
```

**Locatie:**
```
c:\Users\info\CascadeProjects\website-builder\wordpress-plugin\rbs-travel-v3.0.0-MAJOR-UPDATE.zip
```

---

## ✅ NIEUWE FEATURES v3.0.0

### **1. GEFORCEERDE CACHE BYPASS**
```php
// ALWAYS set no-cache headers
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
```

→ **Cache wordt NU automatisch genegeerd!**

### **2. DUIDELIJKE VERSIE INDICATOR**
```html
<!-- ================================================ -->
<!-- RBS Travel Plugin v3.0.0 - MAJOR UPDATE -->
<!-- Template loaded at: 2025-12-09 18:43:12 -->
<!-- Timestamp: 1733767392 -->
<!-- FIXES: Hotels at 13:01, Cars HH:MM, Cruise ports filtered -->
<!-- ================================================ -->
```

→ **Je ziet meteen in de HTML source welke versie actief is!**

### **3. ALLE LAYOUT FIXES (van v2.6.9)**
- ✅ Hotels op tijd **13:01** (direct na destination 13:00)
- ✅ Auto tijd **zonder seconden** (12:00 ipv 12:00:00)  
- ✅ Cruise aanlegplaatsen **gefilterd** (geen embark/disembark)

---

## 🔥 INSTALLATIE INSTRUCTIES

### **STAP 1: COMPLETE VERWIJDERING**

**KRITIEK:** Verwijder ALLES van de oude plugin!

```
1. WordPress Admin → Plugins
2. Zoek: "rbsTravel"
3. Klik: "Deactiveren"
4. Klik: "Verwijderen"
5. Bevestig: "Ja, verwijder deze bestanden"
```

**Wacht 5 seconden!**

---

### **STAP 2: VERSE INSTALLATIE**

```
1. Plugins → Add New
2. Upload Plugin
3. Kies bestand: rbs-travel-v3.0.0-MAJOR-UPDATE.zip
4. Install Now
5. Activate Plugin
```

---

### **STAP 3: CLEAR ALLE CACHE**

#### **A. WP-Optimize Cache**
```
WordPress Admin → WP Optimize → Cache
→ Klik: "Purge all caches"
→ Klik: "Purge minify cache"
```

#### **B. Cloudflare (als je het gebruikt)**
```
Cloudflare Dashboard
→ Caching
→ Configuration
→ Purge Cache
→ Purge Everything
WACHT 30 SECONDEN!
```

#### **C. Browser Cache**
```
CTRL+SHIFT+DELETE
→ Selecteer: "Cached images and files"
→ Clear data
```

---

### **STAP 4: VERIFICATIE**

**Open in NIEUWE INCOGNITO VENSTER:**
```
https://flyendrive.online/?post_type=rbs-travel-idea&p=1397
```

**Druk: CTRL+U (view source)**

**Zoek naar (CTRL+F):**
```
v3.0.0
```

**VERWACHT RESULTAAT:**
```html
<!-- ================================================ -->
<!-- RBS Travel Plugin v3.0.0 - MAJOR UPDATE -->
<!-- Template loaded at: [HUIDIGE TIJD] -->
<!-- Timestamp: [UNIX TIMESTAMP] -->
<!-- FIXES: Hotels at 13:01, Cars HH:MM, Cruise ports filtered -->
<!-- ================================================ -->
```

---

## ✅ SUCCESS CRITERIA

**Als je dit ZIET in de HTML source:**

✅ **"v3.0.0"** → Correcte versie geladen!  
✅ **Timestamp is RECENT** → Template is vers!  
✅ **"MAJOR UPDATE"** → Nieuwe code actief!

**Dan check je de timeline:**

1. **Hotels positie:**
   ```
   Dag 1:
   13:00 → Miami FL (destination)
   13:01 → Embassy Suites (hotel) ← DIRECT ERONDER!
   ```

2. **Auto tijd:**
   ```
   12:00 → Huurauto Ophalen  ← ZONDER :00!
   ```

3. **Cruise aanlegplaatsen:**
   ```
   ⚓ Aanlegplaatsen:
   • Coco Cay
   • Falmouth, Jamaica
   • Labadee
   (GEEN Miami of Key West!)
   ```

---

## ❌ ALS HET NOG NIET WERKT

### **Diagnose:**

**Check 1: WordPress Admin**
```
Plugins → Installed Plugins
rbsTravel moet tonen: "Versie 3.0.0"
```

**Als NIET 3.0.0:**
→ Plugin upload mislukt
→ Probeer FTP upload (zie hieronder)

**Check 2: HTML Source**
```
Open reis in incognito
CTRL+U
Zoek: "v3.0.0"
```

**Als NIET gevonden:**
→ Template cache probleem
→ PHP OPcache probleem
→ Volg "PLAN B" hieronder

---

## 🔧 PLAN B: FTP UPLOAD

**Als WordPress upload niet werkt:**

### **Via FTP Client (FileZilla):**

```
1. Download & extract: rbs-travel-v3.0.0-MAJOR-UPDATE.zip
2. Connect via FTP to: flyendrive.online
3. Navigeer naar: /wp-content/plugins/
4. VERWIJDER folder: rbs-travel/
5. Upload NIEUWE folder: rbs-travel/ (uit ZIP)
6. Set permissions: 755 (recursief)
```

### **Via DirectAdmin File Manager:**

```
1. Log in: DirectAdmin control panel
2. File Manager
3. Navigeer: domains/flyendrive.online/public_html/wp-content/plugins/
4. VERWIJDER: rbs-travel folder
5. Upload: rbs-travel-v3.0.0-MAJOR-UPDATE.zip
6. Extract: rbs-travel-v3.0.0-MAJOR-UPDATE.zip
7. Hernoem extracted folder naar: rbs-travel
```

---

## 🆘 LAATSTE REDMIDDEL

**Als NIETS werkt:**

### **Clear Server-Side Cache**

**Contact je hosting provider:**
```
Onderwerp: Please clear all server-side cache for flyendrive.online

Bericht:
Beste support,

Kunnen jullie de volgende cache clearen voor flyendrive.online:
- PHP OPcache
- Nginx FastCGI cache (als van toepassing)
- Varnish cache (als van toepassing)
- Alle server-side caching

Ik heb een WordPress plugin geüpdatet maar de oude versie blijft geladen.

Dank jullie wel!
```

---

## 📊 VERSION HISTORY

| Versie | Status | Probleem |
|--------|--------|----------|
| 2.6.5 | ❌ | Layout fix, maar cache issues |
| 2.6.6 | ❌ | Auto display, maar cache issues |
| 2.6.7 | ❌ | Auto layout + mileage, maar cache issues |
| 2.6.8 | ❌ | Cruise data fix, maar cache issues |
| 2.6.9 | ❌ | Timeline layout, maar TEMPLATE CACHE |
| 2.7.0 | ❌ | Cache bypass poging, niet geüpload |
| **3.0.0** | ✅ | **GEFORCEERDE no-cache headers + duidelijke versie indicator** |

---

## 🎯 WAAROM v3.0.0 WEL WERKT

### **Verschil met v2.6.9:**

**v2.6.9:**
```php
// Gebruikte ?nocache=1 parameter (moet je zelf toevoegen)
if (isset($_GET['nocache'])) {
    header('Cache-Control: no-cache');
}
```

**v3.0.0:**
```php
// ALTIJD no-cache headers (automatisch!)
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
```

→ **Cache wordt NU ALTIJD genegeerd!**

---

## 📞 SUPPORT

**Na installatie:**

1. **Stuur screenshot van:**
   - WordPress Plugins lijst (moet "Versie 3.0.0" tonen)
   - HTML source met versie indicator
   - Timeline weergave

2. **Rapporteer:**
   - Welke stappen je hebt gevolgd
   - Wat werkt / niet werkt
   - Welke cache heb je gecleared

**Dan kan ik precies zien wat er aan de hand is!**

---

## ✅ FINAL CHECKLIST

- [ ] Oude plugin COMPLEET verwijderd
- [ ] v3.0.0 ZIP geüpload
- [ ] Plugin geactiveerd
- [ ] WP-Optimize cache gecleared
- [ ] Cloudflare cache gecleared (als van toepassing)
- [ ] Browser cache gecleared
- [ ] Pagina geopend in **incognito mode**
- [ ] HTML source geopend (CTRL+U)
- [ ] Gezocht naar **"v3.0.0"** in source
- [ ] Timestamp is RECENT (vandaag)
- [ ] Timeline layout gecontroleerd

**Als ALLE boxen zijn aangevinkt:**
→ **v3.0.0 is succesvol geïnstalleerd!** 🎉

**Als v3.0.0 NIET in source staat:**
→ **Volg PLAN B (FTP upload)** of **contacteer hosting provider**
