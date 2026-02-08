<?php
/**
 * Documentation Page Template
 * Shows usage instructions for the RRP System plugin
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1>📖 RRP System Documentatie</h1>
    
    <style>
        .rrp-docs {
            max-width: 900px;
            margin-top: 20px;
        }
        .rrp-docs-section {
            background: #fff;
            border-radius: 12px;
            padding: 25px 30px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #e5e7eb;
        }
        .rrp-docs-section h2 {
            margin: 0 0 20px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            font-size: 20px;
            color: #1a1a2e;
        }
        .rrp-docs-section h3 {
            margin: 25px 0 12px 0;
            font-size: 16px;
            color: #374151;
        }
        .rrp-docs-section p {
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 15px;
        }
        .rrp-docs-section ul {
            margin: 10px 0 20px 20px;
            line-height: 1.8;
        }
        .rrp-docs-section li {
            margin-bottom: 8px;
            color: #4b5563;
        }
        .rrp-docs-section code {
            background: #f3f4f6;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
            color: #dc2626;
            border: 1px solid #e5e7eb;
        }
        .rrp-docs-section pre {
            background: #1f2937;
            color: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 15px 0;
        }
        .rrp-docs-section pre code {
            background: none;
            border: none;
            color: #10b981;
            padding: 0;
        }
        .rrp-docs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .rrp-docs-table th,
        .rrp-docs-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .rrp-docs-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .rrp-docs-table td code {
            font-size: 12px;
        }
        .rrp-tip {
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px 20px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }
        .rrp-tip strong {
            color: #059669;
        }
        .rrp-warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
        }
        .rrp-warning strong {
            color: #d97706;
        }
        .rrp-version-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
        }
    </style>
    
    <div class="rrp-docs">
        
        <!-- Version Info -->
        <div class="rrp-docs-section">
            <h2>🚀 Welkom bij RRP System <span class="rrp-version-badge">v5.9.0</span></h2>
            <p>Het RRP System (Rondreis Planner) is een complete oplossing voor het beheren en presenteren van reizen op je WordPress website.</p>
        </div>
        
        <!-- Shortcodes -->
        <div class="rrp-docs-section">
            <h2>📝 Shortcodes</h2>
            
            <h3>Uitgelichte Reizen</h3>
            <p>Toon een selectie van reizen op je homepage of andere pagina's:</p>
            
            <pre><code>[uitgelichte_reizen ids="123,456,789"]</code></pre>
            
            <p><strong>Met alle opties:</strong></p>
            <pre><code>[uitgelichte_reizen ids="123,456,789" title="Onze Topbestemmingen" columns="3" show_price="yes" show_days="yes" button_text="Bekijk reis"]</code></pre>
            
            <table class="rrp-docs-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Standaard</th>
                        <th>Beschrijving</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>ids</code></td>
                        <td><em>verplicht</em></td>
                        <td>Komma-gescheiden reis IDs</td>
                    </tr>
                    <tr>
                        <td><code>title</code></td>
                        <td>leeg</td>
                        <td>Titel boven de sectie</td>
                    </tr>
                    <tr>
                        <td><code>columns</code></td>
                        <td>3</td>
                        <td>Aantal kolommen (1-4)</td>
                    </tr>
                    <tr>
                        <td><code>show_price</code></td>
                        <td>yes</td>
                        <td>Toon prijs per persoon</td>
                    </tr>
                    <tr>
                        <td><code>show_days</code></td>
                        <td>yes</td>
                        <td>Toon aantal dagen</td>
                    </tr>
                    <tr>
                        <td><code>show_button</code></td>
                        <td>yes</td>
                        <td>Toon "Bekijk reis" knop</td>
                    </tr>
                    <tr>
                        <td><code>button_text</code></td>
                        <td>Bekijk reis</td>
                        <td>Tekst op de knop</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="rrp-tip">
                <strong>💡 Reis ID vinden:</strong> Ga naar <strong>RRP System → Reisideeën</strong>, hover over een reis. Het ID staat in de URL: <code>post=123</code>
            </div>
            
            <h3>Reis Zoeken Widget</h3>
            <p>Plaats een zoek/filter widget op je homepage die bezoekers naar de reizenpagina stuurt:</p>
            
            <pre><code>[reis_zoeken]</code></pre>
            
            <p><strong>Met alle opties:</strong></p>
            <pre><code>[reis_zoeken title="Vind jouw droomreis" style="card" show_country="yes" show_theme="yes" show_days="yes"]</code></pre>
            
            <table class="rrp-docs-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Standaard</th>
                        <th>Beschrijving</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>title</code></td>
                        <td>Vind jouw droomreis</td>
                        <td>Titel boven de widget</td>
                    </tr>
                    <tr>
                        <td><code>subtitle</code></td>
                        <td>Zoek op bestemming...</td>
                        <td>Subtitel onder de titel</td>
                    </tr>
                    <tr>
                        <td><code>style</code></td>
                        <td>card</td>
                        <td><code>card</code> (blauw blok), <code>inline</code> (wit, horizontaal), <code>minimal</code> (alleen velden)</td>
                    </tr>
                    <tr>
                        <td><code>show_country</code></td>
                        <td>yes</td>
                        <td>Toon land/bestemming filter</td>
                    </tr>
                    <tr>
                        <td><code>show_theme</code></td>
                        <td>yes</td>
                        <td>Toon reistype filter</td>
                    </tr>
                    <tr>
                        <td><code>show_days</code></td>
                        <td>yes</td>
                        <td>Toon reisduur filter</td>
                    </tr>
                    <tr>
                        <td><code>button_text</code></td>
                        <td>Zoek Reizen</td>
                        <td>Tekst op de zoekknop</td>
                    </tr>
                    <tr>
                        <td><code>redirect_url</code></td>
                        <td>reizen archief</td>
                        <td>Aangepaste URL voor resultaten</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="rrp-tip">
                <strong>💡 Stijlen:</strong><br>
                • <code>style="card"</code> - Mooi blauw blok, ideaal voor homepage sidebar<br>
                • <code>style="inline"</code> - Wit blok met horizontale layout<br>
                • <code>style="minimal"</code> - Alleen de velden, zonder styling
            </div>
        </div>
        
        <!-- Hero Styles -->
        <div class="rrp-docs-section">
            <h2>🎨 Hero Stijlen</h2>
            <p>Bij elke reis kun je kiezen uit verschillende hero stijlen:</p>
            
            <table class="rrp-docs-table">
                <thead>
                    <tr>
                        <th>Stijl</th>
                        <th>Beschrijving</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>📸 <strong>Foto Slideshow</strong></td>
                        <td>Automatische slideshow met alle bestemmingsfoto's (aanbevolen)</td>
                    </tr>
                    <tr>
                        <td>🖼️ <strong>Foto Grid</strong></td>
                        <td>4 foto's naast elkaar in een grid</td>
                    </tr>
                    <tr>
                        <td>🏞️ <strong>Enkele Grote Foto</strong></td>
                        <td>Eén grote foto met titel eronder</td>
                    </tr>
                    <tr>
                        <td>🎬 <strong>YouTube Video</strong></td>
                        <td>Embedded YouTube video als hero</td>
                    </tr>
                    <tr>
                        <td>📐 <strong>Breed Formaat</strong></td>
                        <td>Brede foto, minder hoog</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="rrp-tip">
                <strong>💡 YouTube Video:</strong> Plak de volledige YouTube URL of alleen de video ID.<br>
                Voorbeeld: <code>https://www.youtube.com/watch?v=dQw4w9WgXcQ</code> of <code>dQw4w9WgXcQ</code>
            </div>
        </div>
        
        <!-- Sidebar Widget -->
        <div class="rrp-docs-section">
            <h2>📦 Sidebar Widget</h2>
            <p>In de <strong>Instellingen</strong> kun je een sidebar widget configureren die op elke reispagina wordt getoond:</p>
            
            <ul>
                <li><strong>Titel:</strong> De koptekst van de widget</li>
                <li><strong>Inhoud:</strong> Vrije tekst met ondersteuning voor HTML en shortcodes</li>
                <li><strong>Inschakelen:</strong> Vink aan om de widget te tonen</li>
            </ul>
            
            <p>Ideaal voor:</p>
            <ul>
                <li>Contactinformatie van je reisbureau</li>
                <li>Een call-to-action blok</li>
                <li>Sociale media links</li>
                <li>Speciale aanbiedingen</li>
            </ul>
        </div>
        
        <!-- Button Settings -->
        <div class="rrp-docs-section">
            <h2>🔘 Knop Instellingen</h2>
            <p>Configureer de actieknoppen op reispagina's in <strong>Instellingen → Knop Instellingen</strong>:</p>
            
            <table class="rrp-docs-table">
                <thead>
                    <tr>
                        <th>Knop</th>
                        <th>Placeholder</th>
                        <th>Beschrijving</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Boek Nu</td>
                        <td><code>{travel_url}</code></td>
                        <td>Link naar boekingspagina. Gebruik <code>{travel_url}</code> voor de originele reis URL.</td>
                    </tr>
                    <tr>
                        <td>Meer Info</td>
                        <td>-</td>
                        <td>Link naar contactpagina of meer informatie</td>
                    </tr>
                    <tr>
                        <td>Aanpassen</td>
                        <td><code>{travel_url}</code></td>
                        <td>Link naar aanpassingspagina</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- API Settings -->
        <div class="rrp-docs-section">
            <h2>🔑 API Instellingen</h2>
            <p>Configureer de Travel Compositor API verbinding:</p>
            
            <ul>
                <li><strong>API Gebruikersnaam:</strong> Je Travel Compositor gebruikersnaam</li>
                <li><strong>API Wachtwoord:</strong> Je Travel Compositor wachtwoord</li>
                <li><strong>Microsite ID:</strong> Je microsite identificatie</li>
            </ul>
            
            <div class="rrp-warning">
                <strong>⚠️ Multisite:</strong> In een WordPress Multisite omgeving worden API credentials centraal beheerd door de Network Admin.
            </div>
        </div>
        
        <!-- BOLT Catalog -->
        <div class="rrp-docs-section">
            <h2>📚 BOLT Catalogus</h2>
            <p>Super admins kunnen reizen markeren voor de BOLT catalogus:</p>
            
            <ol>
                <li>Bewerk een reis</li>
                <li>Zoek de metabox <strong>📚 BOLT Catalogus</strong></li>
                <li>Vink <strong>In BOLT Catalogus</strong> aan</li>
                <li>Opslaan</li>
            </ol>
            
            <p>Gemarkeerde reizen zijn beschikbaar voor alle brands in BOLT.</p>
        </div>
        
        <!-- Support -->
        <div class="rrp-docs-section">
            <h2>🆘 Ondersteuning</h2>
            <p>Heb je vragen of problemen? Neem contact op:</p>
            <ul>
                <li>📧 Email: <a href="mailto:support@rondreis-planner.nl">support@rondreis-planner.nl</a></li>
                <li>🌐 Website: <a href="https://rondreis-planner.nl" target="_blank">rondreis-planner.nl</a></li>
            </ul>
        </div>
        
    </div>
</div>
