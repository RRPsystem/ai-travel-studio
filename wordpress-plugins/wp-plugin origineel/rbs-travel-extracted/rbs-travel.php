<?php
/**
 * Plugin Name: RRP System
 * Plugin URI: https://rondreis-planner.nl
 * Description: Rondreis Planner System - Travel planning and booking management
 * Author: Rondreis Planner System
 * Author URI: https://rondreis-planner.nl
 * Version: 5.14.56
 * Text Domain: rbs-travel
 * Domain Path: /languages
 * 
 * 
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

/* CHANGELOG:
 * 
 */

namespace RBS_TRAVEL;

if (!defined('ABSPATH')) 
    exit;


if (!class_exists('RBS_TRAVEL\\rbsTravel')) {
    
    class rbsTravel {                   
    
        private $plugin_objects; 
        private $plugin_options;
        
    
        public function __construct() {

            // Set plugin defines:
            $this->rbsDefines();
          
            // Load plugin files:
            $this->rbsLoad(); 

            // Initialize plugin:
            $this->rbsInit();        


            
        }
        
        
        /*** Set plugin defines ***/
        private function rbsDefines() {
            $wp_upload_dir = wp_upload_dir();
            
	    // Setup directory and uri variables (used in defines.php)
            $upload_directory = $wp_upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'rbs-travel' . DIRECTORY_SEPARATOR;
            $upload_url = $wp_upload_dir['baseurl'] . '/rbs-travel/';
            
	    // Load "defines" file:
	    require_once __DIR__ . DIRECTORY_SEPARATOR . 'defines.php';
            
	    // Create directories if not exist:
            if (!file_exists(RBS_TRAVEL_UPLOADS_PATH)) {
                mkdir(RBS_TRAVEL_UPLOADS_PATH, 0777, true);                
            }
            
            if (!file_exists(RBS_TRAVEL_UPLOADS_PATH_TMP)) {
                mkdir(RBS_TRAVEL_UPLOADS_PATH_TMP, 0777, true);                
            }            
            
            if (!file_exists(RBS_TRAVEL_UPLOADS_PATH_LOGS)) {
                mkdir(RBS_TRAVEL_UPLOADS_PATH_LOGS, 0777, true);                
            }    
            
            if (!file_exists(RBS_TRAVEL_UPLOADS_PATH_MAPS)) {
                mkdir(RBS_TRAVEL_UPLOADS_PATH_MAPS, 0777, true);                
            }

            
        }
        
        
        /*** Load the 'plugin php files': ***/
        private function rbsLoad() {
            /** Core Classes (must load first - used by helpers): **/
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-settings.class.php';
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-meta.class.php';
            
            /** Multisite Network Settings (if multisite): **/
            if (is_multisite()) {
                require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-network-settings.class.php';
            }
            
            /** Helpers (depend on settings/meta classes): **/
            require RBS_TRAVEL_PLUGIN_PATH_HELPERS . 'rbstravel-functions.php';
            // Disabled: Expert helper - replaced by configurable sidebar widget
            // require RBS_TRAVEL_PLUGIN_PATH_HELPERS . 'rbstravel-expert-helper.php';
       
            /** Includes: **/
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-menus.class.php';	    
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-posttype.class.php';
            // Disabled: Expert posttype - replaced by configurable sidebar widget
            // require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-experts-posttype.class.php';
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-ajax.class.php';
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-api.class.php';
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-import.class.php';
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-remote-travels.class.php';

            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-geocoding.class.php';
            
            /** Shortcodes: */
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-shortcodes.class.php';
            
            /** REST API Endpoints: */
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'api-rest-endpoints.php';
            
            /** Page Templates Registration: */
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'page-templates.php';
            
            /** Theme Colors Helper: */
            require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'theme-colors.php';
            
            /** Admin Notices: */
            if (is_admin()) {
                require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'admin-notices.php';
            }

            /** Shortcodes: */
            require RBS_TRAVEL_PLUGIN_PATH_SHORTCODES . 'rbstravel-sc-idealist.php';
            require RBS_TRAVEL_PLUGIN_PATH_SHORTCODES . 'rbstravel-sc-ideajson.php';

            if (!is_admin()) {
                require RBS_TRAVEL_PLUGIN_PATH_INCLUDES . 'rbstravel-frontend.class.php';
            }
        }
        
        
        /*** Set 'action' and 'filter' hooks: ***/
        private function rbsInit() {
            // Base hooks:            
            //add_action('init', array($this, 'plugin_init'));                // 'admin_init'  ???            
            //add_action('wp_head', array($this, 'rbs_styling'));
            
            
            // Backend "scripts":
            add_action('admin_enqueue_scripts', array($this, 'backend_scripts_and_styles'), 10, 1);            
            
            // Frontend "scripts":
            add_action('wp_enqueue_scripts', array($this, 'frontend_scripts_and_styles'), 10, 1);

            // Languages:
            add_action('plugins_loaded', array($this, 'rbs_load_textdomain'));
            
            
            // Widgets:
            //add_action('widgets_init', array($this, 'rbs_widgets_init'));
            
            
            
            /*** Other hooks: ***/
        }
        
        
//        /*** Load the 'plugin objects (classes)': ***/
//        public function plugin_init() {
//            
//            /* General classes/objects: */
//            //$this->plugin_objects['%oject_name%'] = new %ClassName%;
//            //$this->plugin_objects['%oject_name%'] = new %ClassName%;
//            
//            
//            //$this->plugin_objects['RBST_Questions'] = new RBST_Questions();
////            $this->plugin_objects['RBST_Calculator'] = new RBST_Calculator();
//            
//            
//            //$this->plugin_objects['RBST_Calculator_Widget'] = new RBST_Calculator_Widget();
//            
//            /* Backend classes/objects: */
//            //namespace RBS_QUESTIONNAIRE\HELPERS;
//            if (is_admin()) {                
//                $this->plugin_objects['RBST_Menus'] = new \RBS_TOOLS\INCLUDES\RBST_Menus();
//                
//                // Check and Run Export / Import:
//                $this->rbsExport();
//                $this->rbsImport();
//                
//            } else {
//                
//                
//                //$this->rbsDownload();
//            }
//
//            
//            
//            
//        }
        
	
	
	private function get_filectime($file_path) {
	    if (!file_exists($file_path)) {
		return false;
	    }
	    
	    return filectime($file_path);
	}

        
        
        /* Add "plugin" scripts and styles: */
        public function backend_scripts_and_styles($hook_suffix) {
            //die($hook_suffix);            //rbspdf_page_rbspdf-designer
            //if ($hook_suffix === 'tools_page_rbs-tools') {
            
                // Localize Script (vars):
                $localized_script = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    //'out_file' => $pdf_out_file,
                    'admin_script' => true,
		    'debug' => true
                );            


                /*** Backend (css & js) ***/         
             
                
//                wp_register_style('jquery-ui', plugin_dir_url( __FILE__ ) .'assets/css/jquery-ui.min.css'); 
//                wp_enqueue_style('jquery-ui');

//                wp_register_style('jquery-ui-theme', plugin_dir_url( __FILE__ ) .'assets/css/jquery-ui.theme.min.css'); 
//                wp_enqueue_style('jquery-ui-theme');            

//                wp_enqueue_script( 'jquery-ui-core');
//                wp_enqueue_script( 'jquery-ui-accordion' );   
                
                
//                // Fontawesome 6.2.1
//                wp_register_style('fontawesome-6.2.1', plugin_dir_url( __FILE__ ) .'assets/fontawesome-6.2.1/css/fontawesome.min.css'); 
//                wp_enqueue_style('fontawesome-6.2.1');
//                
//                wp_register_style('fontawesome-6.2.1-brands', plugin_dir_url( __FILE__ ) .'assets/fontawesome-6.2.1/css/brands.min.css'); 
//                wp_enqueue_style('fontawesome-6.2.1-brands'); 
//                
//                wp_register_style('fontawesome-6.2.1-solid', plugin_dir_url( __FILE__ ) .'assets/fontawesome-6.2.1/css/solid.min.css'); 
//                wp_enqueue_style('fontawesome-6.2.1-solid');                   
            
            
                
                //wp_enqueue_script('rbs-nav-tabs', RBSPDF_PLUGIN_URL_ASSETS .'/js/rbs-nav-tabs.js'); 
                                  
            
		
                wp_enqueue_style('rbstravel_backend_styles', plugin_dir_url( __FILE__ ) . 'assets/css/backend.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'backend.css'));
               
                $script_url = plugin_dir_url( __FILE__ ) . 'assets/js/backend.js?' .  $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'backend.js');               
                wp_enqueue_script('rbstravel_backend_script', $script_url, array('jquery'));
                wp_localize_script('rbstravel_backend_script', 'rbsTravel', $localized_script);      



                $rbsmaps_script = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'js/rbs-maps.js?' . filemtime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'rbstravel-maps.js');
                wp_enqueue_script('rbs_maps_script', $rbsmaps_script, array(), null, true);
              

                // Codemirror text editor:

                
//                $cm_settings2 = array();
//                $cm_settings2['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
//                wp_localize_script('jquery', 'cm_settings2', $cm_settings2);
                
//                $cm_settings = array();              
//                $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/html'));
//                wp_localize_script('jquery', 'cm_settings', $cm_settings);                
                

//                wp_enqueue_script('wp-theme-plugin-editor');
//                wp_enqueue_style('wp-codemirror');                
                         
                       
            //}
            
        }        
        
        
        
        public function frontend_scripts_and_styles($hook_suffix) {
            
//            // Localize Script (vars):
//            $localized_script = array(
//                'ajaxurl' => admin_url('admin-ajax.php'),
//                'admin_script' => false
//            );    
            
 
            
            /*** Frontend (css & js) ***/
            // ONLY load on rbs-travel post type pages to prevent CSS conflicts
            if (is_singular('rbs-travel-idea') || is_post_type_archive('rbs-travel-idea') || (function_exists('has_shortcode') && has_shortcode(get_post()->post_content ?? '', 'rbstravel_idealist'))) {
                wp_enqueue_style('rbsp_frontend_styles', plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'frontend.css'));
                wp_enqueue_script('rbsp_frontend_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'frontend.js'));
            }

            // wp_enqueue_style('rbsp_frontend_styles_overview_globals', plugin_dir_url( __FILE__ ) . 'assets/css/overview/globals.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'overview' . DIRECTORY_SEPARATOR . 'globals.css'));
            // wp_enqueue_style('rbsp_frontend_styles_styleguide', plugin_dir_url( __FILE__ ) . 'assets/css/styleguide.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'styleguide.css'));
            // wp_enqueue_style('rbsp_frontend_styles_overview_style', plugin_dir_url( __FILE__ ) . 'assets/css/overview/style.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'overview' . DIRECTORY_SEPARATOR . 'style.css'));
            
            // wp_enqueue_script('rbsp_frontend_scripts_overview', plugin_dir_url( __FILE__ ) . 'assets/js/frontend_overview.js?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'frontend_overview.js'));       
            // wp_enqueue_style('rbsp_frontend_styles_single_style', plugin_dir_url( __FILE__ ) . 'assets/css/single/style.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'single' . DIRECTORY_SEPARATOR . 'style.css'));
            
            // wp_enqueue_script('rbsp_frontend_scripts_overview', plugin_dir_url( __FILE__ ) . 'assets/js/frontend_single.js?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'frontend_single.js'));                
          
           
            //wp_enqueue_style('rbsp_frontend_styles_single_globals', plugin_dir_url( __FILE__ ) . 'assets/css/single/globals.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'single' . DIRECTORY_SEPARATOR . 'globals.css'));
            //wp_enqueue_style('rbsp_frontend_styles_single_styleguide', plugin_dir_url( __FILE__ ) . 'assets/css/single/styleguide.css?' . $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'css' . DIRECTORY_SEPARATOR . 'single' . DIRECTORY_SEPARATOR . 'styleguide.css'));
 
            //$script_url = plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js';               
            //wp_enqueue_script('rbsp_frontend_script', $script_url, array('jquery'));
            //wp_localize_script('rbsp_frontend_script', 'rbsJS', $localized_script);

            // $script_url = plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js?' .  $this->get_filectime(RBS_TRAVEL_PLUGIN_PATH_ASSETS . 'js' . DIRECTORY_SEPARATOR . 'frontend.js');               
            // wp_enqueue_script('rbstravel_frontend_script', $script_url, array('jquery'));
                       

        }
        
        
        
       public function rbs_load_textdomain() {      
           $path = dirname( plugin_basename(__FILE__)) . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR;
           $result = load_plugin_textdomain('rbs-travel', false, $path);
       }                
        

        
    }
    
    
/*** Create "plugin instance": ***/
$rbsTravel = new rbsTravel();

/*** Plugin Activation Hook - Flush rewrite rules and create webhook log table ***/
register_activation_hook(__FILE__, function() {
    global $wpdb;
    
    // Flush rewrite rules for REST API
    flush_rewrite_rules();
    
    // Create webhook log table
    $table_name = $wpdb->prefix . 'rbstravel_webhook_log';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        api_set varchar(50) NOT NULL,
        payload longtext NOT NULL,
        status_code int(11) DEFAULT 200,
        response longtext,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY api_set (api_set),
        KEY created_at (created_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    error_log('RBS Travel: Webhook log table created/verified: ' . $table_name);
});

}