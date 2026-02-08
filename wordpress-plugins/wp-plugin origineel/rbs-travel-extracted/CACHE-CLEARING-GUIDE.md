# 🔥 COMPLETE CACHE CLEARING GUIDE

## WAAROM ZIEN WE GEEN WIJZIGINGEN?

WordPress en servers gebruiken meerdere lagen van caching. **Alle** moeten gecleared worden!

---

## 📋 STAP-VOOR-STAP INSTRUCTIES

### ✅ **STAP 1: PLUGIN INSTALLEREN**

1. **WordPress Admin** → Plugins → Add New → Upload Plugin
2. Selecteer: `rbs-travel-v2.7.0-CACHE-FIX.zip`
3. Klik: **"Install Now"**
4. Klik: **"Activate Plugin"**
5. **Controleer versie:** Plugins → Installed Plugins → rbsTravel moet **Version 2.7.0** tonen

---

### ✅ **STAP 2: WORDPRESS CACHE CLEAREN**

#### **A. Object Cache (als je Redis/Memcached hebt)**
```
WordPress Admin → Tools → Redis/Memcached → "Flush Cache"
```

#### **B. Transient Cache**
```
WordPress Admin → Tools → WP-Optimize → Database → "Clean all transients"
```

#### **C. Opcode Cache (PHP OPcache)**
Via plugin of handmatig:
```
WordPress Admin → Plugins → Add New
Zoek: "OPcache Reset"
Installeer & Activate
Tools → OPcache Reset → "Reset OPcache"
```

---

### ✅ **STAP 3: CACHING PLUGINS CLEAREN**

**Welke caching plugins gebruik je?**

#### **WP Super Cache:**
```
Settings → WP Super Cache → "Delete Cache"
```

#### **W3 Total Cache:**
```
Performance → Dashboard → "Empty All Caches"
```

#### **WP Rocket:**
```
Settings → WP Rocket → "Clear Cache"
```

#### **LiteSpeed Cache:**
```
LiteSpeed Cache → Toolbox → "Purge All"
```

#### **WP Fastest Cache:**
```
WP Fastest Cache → "Delete Cache"
```

---

### ✅ **STAP 4: CLOUDFLARE / CDN CACHE CLEAREN**

**Als je Cloudflare gebruikt:**

1. Log in op **Cloudflare Dashboard**
2. Selecteer je domein: **flyendrive.online**
3. Klik: **Caching** → **Configuration**
4. Scroll naar: **Purge Cache**
5. Klik: **"Purge Everything"**
6. Bevestig: **"Purge Everything"**

**Wacht 30 seconden** voordat je de pagina test!

---

### ✅ **STAP 5: BROWSER CACHE CLEAREN**

#### **Chrome / Edge:**
1. Druk: **CTRL+SHIFT+DELETE**
2. Selecteer: **"Cached images and files"**
3. Time range: **"Last hour"**
4. Klik: **"Clear data"**

#### **Firefox:**
1. Druk: **CTRL+SHIFT+DELETE**
2. Selecteer: **"Cache"**
3. Klik: **"Clear Now"**

---

### ✅ **STAP 6: GEFORCEERDE REFRESH**

**Methode 1: URL Parameter (AANBEVOLEN!)**
```
https://flyendrive.online/jouw-reis-url/?nocache=1
```
Voeg **?nocache=1** toe aan het einde van de URL!

**Methode 2: Hard Refresh**
- **Windows:** CTRL+SHIFT+R of CTRL+F5
- **Mac:** CMD+SHIFT+R

---

### ✅ **STAP 7: VERIFICATIE**

**Check of v2.7.0 actief is:**

1. Open reis pagina
2. Druk: **CTRL+U** (view source)
3. Zoek in de HTML naar:
   ```html
   <!-- RBS Travel Plugin Template v2.7.0 - Hotels at 13:01, Cars formatted HH:MM, Cruise ports filtered -->
   ```

**Als je deze regel ZIET:**
✅ **Correcte versie is geladen!**

**Als je deze regel NIET ziet:**
❌ **Cache is nog niet gecleared** → Herhaal stappen 2-6

---

## 🔧 EXTRA: SERVER-SIDE CACHE

**Als NIETS werkt, vraag je hosting provider om:**

1. **PHP OPcache** te clearen
2. **Nginx FastCGI cache** te clearen (als je Nginx gebruikt)
3. **Varnish cache** te clearen (als je Varnish gebruikt)

---

## ✅ FINALE CHECK

**Na alle stappen:**

1. Open: `https://flyendrive.online/jouw-reis/?nocache=1`
2. Check versie in source: moet **v2.7.0** zijn
3. Check timeline:
   - ✅ Hotel direct onder destination
   - ✅ Auto tijd zonder seconden (12:00 ipv 12:00:00)
   - ✅ Cruise aanlegplaatsen zonder Miami/Key West

**Als het NU werkt:**
🎉 **CACHE WAS HET PROBLEEM!**

**Als het NIET werkt:**
📸 **Maak screenshot van:**
1. WordPress Plugins lijst (toon versienummer)
2. HTML source (CTRL+U) met de versie comment
3. Timeline weergave
4. Console errors (F12 → Console tab)

→ Stuur deze naar mij!

---

## 🚀 SNELSTE OPLOSSING

**Als je geen tijd hebt voor alle stappen:**

```
1. Upload v2.7.0
2. Clear Cloudflare cache (als je het gebruikt)
3. Open: https://flyendrive.online/jouw-reis/?nocache=1
```

Dit forceert een verse load zonder cache!
