=== RRP System ===
Contributors: rondreis-planner
Tags: Traveling, Holidays, Tours, Travel-Ideas, Booking, Destinations, Rondreizen
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 4.4.0
Requires PHP: 8.0
License: GPLv2 or later License
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Plugin URI: https://rondreis-planner.nl
Author URI: https://rondreis-planner.nl

== Description ==
RRP System (Rondreis Planner System) - Professional travel planning and booking management plugin. Import travel ideas from Travel Compositor API, customize itineraries, and publish beautiful travel pages with brand customization.

= Privacy Notices =

%PRIVACY NOTICES%

== Installation ==

%INSTALLATION INSTUCTIONS%

== Frequently Asked Questions ==

%FAQS%

== Screenshots ==

%SCREENSHOTS%

== Changelog ==

= 4.4.0 =
* Added support for multiple API credential sets
* Accordion-style UI for managing multiple APIs
* Active API selector dropdown
* Automatic migration from single to multiple credentials
* Add/remove API sets dynamically
* Passwords remain hidden (••••) for security
* Each API set has: Name, Username, Password, MicrositeID
* Switch between different APIs without re-entering credentials

= 4.3.0 =
* Cleaned up admin menu - removed TEST, Help, and About pages
* Renamed "Remote Travel Ideas" to "Importeren"
* Renamed "Settings" to "Instellingen" (Dutch)
* Simplified import page - only ID input field required
* Better UX for travel import process
* Added clear instructions on import page

= 4.2.0 =
* Rebranded: rbsTravel → RRP System (Rondreis Planner System)
* Updated plugin name, author, and descriptions
* All existing features remain unchanged
* Backward compatible with all previous versions

= 4.1.0 =
* Added brand color customization in settings
* Travel agencies can now customize colors (primary, secondary, heading, text)
* WordPress color picker integration
* CSS injection for custom branding

= 4.0.0 =
* Complete layout rebuild using table-based inline styles
* Fixed hotel alignment issues
* Fixed car rental time formatting (removed seconds)
* Fixed cruise itinerary display
* Eliminated theme CSS interference
* Transport improvements (city names instead of codes)

= 0.0.9 =
* Added taxonomy: tour-types
* Added taxonomy: tour-themes
* Added taxonomy: tour-services
* Added metabox: travel-idea-details
* Added metabox: travel-idea-prices
* Added meta fields: travel_price_total, travel_price_per_person, travel_number_of_nights, travel_min_persons, travel_max_persons
* Modified rbs-travel-idea post type columns 
* Modified import, using new taxonomies
* Modified import, using new meta fields
* Create template for single idea-list-layout item
* Modified idea-list-layout
* Added about-page 
* Added new helpers
* Added translations (only few for testing)
* Several (bug) fixes

= 0.0.8 =
* Added new rbs-maps javascript
* Added map metabox for local ideas
* Added feature to generate map
* Added feature to generate map image
* Added setting for mapbox api
* Added setting for contact-form shortcode
* Added new helpers

= 0.0.2 =
* Added helper function to get all published local ideas
* Added shortcode to output all ideas as json

= 0.0.1 =
* Initial release
