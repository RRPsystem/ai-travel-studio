=== Travel Compositor Widgets ===
Contributors: aiwebsitestudio
Tags: travel, booking, searchbox, flight, hotel, vacation
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Embeddable travel searchbox widgets voor hero sections - Vlucht + Hotel, Accommodatie, Fly & Drive, en meer.

== Description ==

Travel Compositor Widgets biedt een set van mooie, responsive zoekwidgets die je kunt plaatsen in je WordPress website. Perfect voor reisbureaus en travel websites.

**Beschikbare Widgets:**

* **Complete Searchbox** - Alle zoekopties in één widget met tabs
* **Vlucht + Hotel** - Combinatie zoeken
* **Accommodatie** - Alleen hotels zoeken
* **Vluchten** - Alleen vluchten zoeken
* **Huurauto** - Auto huren
* **Fly & Drive** - Vlucht + huurauto routes
* **AI Trip Planner** - AI-gestuurde reisplanning

**Features:**

* Responsive design - werkt op alle apparaten
* Moderne UI met glassmorphism effect
* Nederlandse datepicker
* Autocomplete voor luchthavens
* Reizigers/kamers selector
* Aanpasbare kleuren
* Eenvoudige shortcodes

== Installation ==

1. Upload de `travel-compositor-widgets` map naar `/wp-content/plugins/`
2. Activeer de plugin via 'Plugins' menu in WordPress
3. Ga naar Instellingen > TC Widgets om je microsite te configureren
4. Gebruik de shortcodes in je pagina's of posts

== Shortcodes ==

**Complete Searchbox:**
`[tc_searchbox]`
`[tc_searchbox tabs="tripplanner,ai,flighthotel,hotel"]`

**Vlucht + Hotel:**
`[tc_flight_hotel]`
`[tc_flight_hotel title="Boek je vakantie"]`

**Alleen Hotel:**
`[tc_hotel]`
`[tc_hotel title="Zoek accommodatie"]`

**Alleen Vluchten:**
`[tc_flight]`

**Huurauto:**
`[tc_car]`

**Fly & Drive:**
`[tc_routing]`

**AI Planner:**
`[tc_ai_planner]`
`[tc_ai_planner placeholder="Beschrijf je droomreis..."]`

== Parameters ==

Alle shortcodes ondersteunen:

* `title` - Custom titel boven de widget
* `primary_color` - Override de primaire kleur (bijv. "#ff6600")
* `class` - Extra CSS classes

De searchbox ondersteunt ook:
* `tabs` - Komma-gescheiden lijst van tabs (tripplanner,ai,flighthotel,hotel,routing,car,flight)

== Configuration ==

Ga naar **Instellingen > TC Widgets** om te configureren:

* **Microsite ID** - Je Travel Compositor microsite identifier
* **Base URL** - De URL van je microsite (bijv. https://rondreis-planner.nl)
* **Primaire kleur** - De hoofdkleur van de widgets
* **Taal** - NL, EN, DE of FR
* **Valuta** - EUR, USD of GBP

== Frequently Asked Questions ==

= Hoe krijg ik een Travel Compositor microsite? =

Neem contact op met Travel Compositor voor een microsite account.

= Kan ik de kleuren aanpassen? =

Ja, via de instellingen pagina of per shortcode met de `primary_color` parameter.

= Werkt dit met Elementor/Gutenberg? =

Ja, gebruik gewoon de shortcode in een Shortcode block of Text widget.

== Changelog ==

= 1.0.0 =
* Eerste release
* Complete searchbox met tabs
* Individuele widgets voor flight+hotel, hotel, flight, car, routing, AI

== Upgrade Notice ==

= 1.0.0 =
Eerste release van Travel Compositor Widgets.
