# VERSION CHECK INSTRUCTIES

## Controleer welke versie actief is:

### Methode 1: WordPress Admin
1. Ga naar: **Plugins → Geïnstalleerde plugins**
2. Zoek: **rbsTravel**
3. Kijk naar versienummer onder de naam
4. Moet zijn: **Versie 2.6.9**

### Methode 2: In de code
1. Open reis pagina in browser
2. Druk: **CTRL+U** (view source)
3. Scroll helemaal naar beneden
4. Zoek naar: `<!-- RBS Travel Plugin v`
5. Of zoek in de HTML source naar "13:01" (nieuwe hotel tijd)

### Methode 3: Direct in file
1. FTP of File Manager
2. Ga naar: `/wp-content/plugins/rbs-travel/rbs-travel.php`
3. Open bestand
4. Regel 8 moet zijn: `* Version: 2.6.9`

## Als versie NIET 2.6.9 is:
→ Plugin niet correct geüpload
→ Volg "GEFORCEERDE INSTALLATIE" hieronder

## Als versie WEL 2.6.9 is, maar layout nog fout:
→ Template caching probleem
→ Volg "CACHE OPLOSSINGEN" hieronder
