<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo
 * - [ ] function to create a json object for a single travel idea (meta)
 * - [ ] ...
 */


//  die('RBS_TRAVEL_Frontend');

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Frontend')) {
        
    class RBS_TRAVEL_Frontend {

        function __construct() {
            
            add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'), 100);
            add_action('wp_head', array($this, 'add_font'),100);

        }
        

        public function enqueue_frontend_scripts() {
            // Enqueue favorites script on listing pages
            if (is_post_type_archive('rbs-travel-idea') || is_page_template('idealistlayout.html.php')) {
                $favorites_script = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'js/favorites.js?' . filemtime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'favorites.js');
                wp_enqueue_script('rbstravel-favorites', $favorites_script, array('jquery'), null, true);
            }
            
            if (is_singular('rbs-travel-idea')) {
                $this->leaflet_map();
            }
        } 


        private function leaflet_map() {
            $leaflet_styles = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            $leaflet_script = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            
            $leaflet_routing_styles = 'https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css';
            $leaflet_routing_script = 'https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js';
            
            $mapbox_styles = 'https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.css';
            $mapbox_script = 'https://api.mapbox.com/mapbox-gl-js/v2.8.1/mapbox-gl.js';
            // <!-- Leaflet Control Geocoder JS (for geocoding) -->
            // <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>


            // routes script, map-image script, ....
            //$mapbox_script = '???';


            $rbstravelmap_styles = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'css/rbstravel-maps.css?' . filemtime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'rbstravel-maps.css');
            $rbstravelmap_script = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'js/rbstravel-maps.js?' . filemtime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'rbstravel-maps.js');

            $rbsmaps_script = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'js/rbs-maps.js?' . filemtime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'rbstravel-maps.js');



            wp_enqueue_style('leaflet-css', $leaflet_styles);
            wp_enqueue_script('leaflet-js', $leaflet_script, array(), null, false);

            wp_enqueue_style('leaflet-routing-css', $leaflet_routing_styles);
            wp_enqueue_script('leaflet-routing-js', $leaflet_routing_script);

            wp_enqueue_style('mapbox-css', $mapbox_styles);
            wp_enqueue_script('mapbox-js', $mapbox_script);            

            wp_enqueue_style('rbstravel_map_styles', $rbstravelmap_styles);
            wp_enqueue_script('rbstravel_map_script', $rbstravelmap_script, array(), null, true);

            wp_enqueue_script('rbs_maps_script', $rbsmaps_script, array(), null, true);
            

        }

        function add_font() {
            $font_script = '<link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">';
      
            echo $font_script;
        }
        


    }

}




$RBS_TRAVEL_Frontend = new RBS_TRAVEL_Frontend();