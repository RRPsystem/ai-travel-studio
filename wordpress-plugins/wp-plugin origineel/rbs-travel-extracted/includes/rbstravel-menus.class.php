<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();


if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Menus')) {
    
    
    class RBS_TRAVEL_Menus {
        
        
        
        function __construct() {
            add_action('admin_menu', array($this, 'admin_menus'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
        
        
        
        public function admin_menus() {
            //add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position)
            //add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position)

            add_menu_page('RRP System', 'RRP System', 'manage_options', 'rbstravel', array($this, 'plugin_settings'), 'dashicons-airplane', 50);
            
            
            
// @ref:            
//https://wordpress.stackexchange.com/questions/160224/show-custom-taxonomy-inside-custom-menu            
            	    
	         add_submenu_page('rbstravel', __('Importeren', 'rbs-travel'), __('Importeren', 'rbs-travel'), 'manage_options', 'rbstravel-remote-travel-ideas', array($this, 'plugin_remote_travel_ideas'));
	    

            add_submenu_page('rbstravel', __('Locations', 'rbs-travel'), __('Locations', 'rbs-travel'), 'manage_options', 'edit-tags.php?taxonomy=location');
            add_submenu_page('rbstravel', __('Types', 'rbs-travel'), __('Types', 'rbs-travel'), 'manage_options', 'edit-tags.php?taxonomy=tour-type');
            add_submenu_page('rbstravel', __('Themes', 'rbs-travel'), __('Themes', 'rbs-travel'), 'manage_options', 'edit-tags.php?taxonomy=tour-theme');
            add_submenu_page('rbstravel', __('Services', 'rbs-travel'), __('Services', 'rbs-travel'), 'manage_options', 'edit-tags.php?taxonomy=tour-service');
            
            // Reisexperts menu is handled by custom post type (see rbstravel-experts-posttype.class.php)

            add_submenu_page('rbstravel', __('Instellingen', 'rbs-travel'), __('Instellingen', 'rbs-travel'), 'manage_options', 'rbstravel-settings', array($this, 'plugin_settings'));
            
            add_submenu_page('rbstravel', __('Webhook Logs', 'rbs-travel'), __('🔗 Webhook Logs', 'rbs-travel'), 'manage_options', 'rbstravel-webhook-logs', array($this, 'plugin_webhook_logs'));
            
            add_submenu_page('rbstravel', __('Documentatie', 'rbs-travel'), __('📖 Documentatie', 'rbs-travel'), 'edit_posts', 'rbstravel-docs', array($this, 'plugin_documentation'));
	    
            
            //add_submenu_page('rbstravel', '_rbscase-import-cases', '_rbscase-import-cases', 'manage_options', '_rbscase-import-cases', array($this, 'plugin_import_cases'));
            //add_submenu_page('rbstravel', '_rbscase-export-results', '_rbscase-export-results', 'manage_options', '_rbscase-export-results', array($this, 'plugin_export_results'));

            add_action('admin_head', array( $this, 'admin_head'));
            
        }        
        
	
	
	
	
	
	
	public function plugin_remote_travel_ideas() {
	    require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'remote-travels-simple.html.php';
	}
	
	public function plugin_experts() {
	    echo '<div class="wrap">';
	    echo '<h1>👤 Reisexperts</h1>';
	    echo '<div style="background: #fff; padding: 30px; border-radius: 8px; margin-top: 20px;">';
	    echo '<h2 style="color: #667eea;">🚧 Binnenkort beschikbaar!</h2>';
	    echo '<p style="font-size: 16px; line-height: 1.6;">Deze functie wordt momenteel ontwikkeld. Hier kun je straks:</p>';
	    echo '<ul style="font-size: 15px; line-height: 2;">';
	    echo '<li>✅ <strong>Reisexperts toevoegen</strong> met foto, naam, specialisatie</li>';
	    echo '<li>✅ <strong>Experts koppelen aan landen</strong> (bijv. Japan expert, Amerika expert)</li>';
	    echo '<li>✅ <strong>Automatisch de juiste expert tonen</strong> op elke reispagina</li>';
	    echo '<li>✅ <strong>Expert contactgegevens</strong> beheren</li>';
	    echo '</ul>';
	    echo '<p style="margin-top: 30px; padding: 20px; background: #f0f9ff; border-left: 4px solid #3b82f6; border-radius: 4px;">';
	    echo '<strong>💡 Tip:</strong> Voor nu kun je een algemene expert tekst instellen in de sidebar onder <strong>Instellingen</strong>.';
	    echo '</p>';
	    echo '</div>';
	    echo '</div>';
	}

        
    
        public function plugin_settings() {     
            //echo '<h1>TEMP: rbsTravel - Settings</h1>';
	        require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'settings.html.php';
//            $RBSCASES_Settings = new \RBS_CASES\INCLUDES\RBSCASES_Settings();
//            $RBSCASES_Settings->SaveSettings();
// 
//            $vars = array();
//            $vars['rbscases_plugin_enabled'] = RBSCASES_Settings::GetSetting('rbscases_plugin_enabled', false);
//            $vars['rbscases_demo_mode'] = RBSCASES_Settings::GetSetting('rbscases_demo_mode', false);
//            $vars['rbscases_intro_page'] = RBSCASES_Settings::GetSetting('rbscases_intro_page', false);
//            $vars['rbscases_outro_page'] = RBSCASES_Settings::GetSetting('rbscases_outro_page', false);
//            $vars['rbscases_overview_link'] = RBSCASES_Settings::GetSetting('rbscases_overview_link', false);
//            $vars['rbscases_enable_captcha'] = RBSCASES_Settings::GetSetting('rbscases_enable_captcha', false);
//            $vars['rbscases_captcha_lifetime'] = RBSCASES_Settings::GetSetting('rbscases_captcha_lifetime', false);      
//            $vars['rbscases_cookie_lifetime'] = RBSCASES_Settings::GetSetting('rbscases_cookie_lifetime', false);      
//            $vars['rbscases_case_import_status'] = RBSCASES_Settings::GetSetting('rbscases_case_import_status', 'unpublished');
//            $vars['rbscases_default_num_of_scenarios'] = RBSCASES_Settings::GetSetting('rbscases_default_num_of_scenarios', 3);
//            $vars['rbscases_debug_mode'] = RBSCASES_Settings::GetSetting('rbscases_debug_mode', false);
//            $vars['rbscases_enable_logging'] = RBSCASES_Settings::GetSetting('rbscases_enable_logging', false);            
//            $vars['rbscases_csv_delimiter'] = RBSCASES_Settings::GetSetting('rbscases_csv_delimiter', ';');
//            $vars['rbscases_csv_enclosure'] = RBSCASES_Settings::GetSetting('rbscases_csv_enclosure', '"');
//            $vars['rbscases_csv_escape'] = RBSCASES_Settings::GetSetting('rbscases_csv_escape', '\\\\');            
//            $vars['rbscases_frontend_styling'] = RBSCASES_Settings::GetSetting('rbscases_frontend_styling', null);            
//            
//            echo \RBS_CASES\HELPERS\rbstravel_template_loader('backend' . DIRECTORY_SEPARATOR . 'settings', $vars);            
        }
        
        public function plugin_help() {
            echo '<h1>TEMP: rbsTravel - Help</h1>';
            //echo \RBS_CASES\HELPERS\rbstravel_template_loader('backend' . DIRECTORY_SEPARATOR . 'help');
        }        
 
        public function plugin_about() {      
            // echo '<h1>TEMP: rbsTravel - About</h1>';
            //echo \RBS_CASES\HELPERS\rbstravel_template_loader('backend' . DIRECTORY_SEPARATOR . 'about');  
            require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'about.html.php';
            
        }


        public function plugin_travelstudio() {
            require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'travelstudio.html.php';
        }
        
        public function plugin_documentation() {
            require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'documentation.html.php';
        }    
        
        public function plugin_webhook_logs() {
            require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'webhook-logs.html.php';
        }    
        
        public function plugin_test() {      
	        require_once RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'test.html.php';
        }        
        
        public function admin_enqueue_scripts($hook) {
            // DEBUG: Always load on admin pages to test
            // Load color picker and jQuery on settings page
            
            // Enqueue jQuery first
            wp_enqueue_script('jquery');
            
            // Enqueue WordPress color picker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_script('wp-color-picker');
            
            // Enqueue our custom admin settings script
            $script_url = RBS_TRAVEL_PLUGIN_URL_ASSETS . 'js/admin-settings.js';
            
            wp_enqueue_script(
                'rrp-admin-settings',
                $script_url,
                array('jquery', 'wp-color-picker'),
                '4.5.0',
                true
            );
            
            // DEBUG: Show script URL in HTML
            add_action('admin_footer', function() use ($script_url) {
                echo '<!-- RRP DEBUG: Script URL: ' . $script_url . ' -->';
                echo '<!-- RRP DEBUG: PLUGIN_URL_ASSETS: ' . RBS_TRAVEL_PLUGIN_URL_ASSETS . ' -->';
            });
        }
        
        public function admin_head() {
            global $post_type;

            if ($post_type === 'rbs-travel-idea' && is_admin() && get_current_screen()->is_block_editor() === false) { 
                $access_token = RBS_TRAVEL_Settings::GetSetting('mapbox_token', null);
                $return = array();
                $return[] = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>';        
                $return[] = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';
                $return[] = '<script src="https://rawgit.com/mapbox/leaflet-image/gh-pages/leaflet-image.js"></script>';   
                $return[] = '<script>const accessToken = \'' . $access_token . '\';</script>';
                echo implode("\r\n", $return);            
            }
        }
       
        
        
        
    }
    
    
}

if (is_admin()) {
    $RBS_TRAVEL_Menus = new RBS_TRAVEL_Menus();
}