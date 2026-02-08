<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo:
 * - [ ] use 'official' register_settings / add_section / etc. from wordpress
 * - [ ] 
 */

/**
 * @settings:
 * - [ ] api connection info
 *      >> endpoint/port, user/pass?, tokens?, etc...
 * 
 */

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings')) {
    
    
    class RBS_TRAVEL_Settings {
        
        private $rbs_books_version;
        


        public function __construct() {            
            $this->check_version();
//            $this->load_settings();
	    $this->init();
	    
	    //$this->save_settings();
        }

        
	private function init() {
	    //add_action('admin_init', array($this, 'register_settings'));
	    add_action('admin_init', array($this, 'save_settings'));
	}
	
	
        private function check_version() {
//            $this->rbswctop_version = get_option('rbscases_version', null);
//            if ($this->rbswctop_version === null) {
//                //self::SetSetting('rbscases_frontend_styling', $this->load_default_styling());
//                //self::SetSetting('rbscases_allowed_file_types', $this->load_default_filetypes());
//                update_option('rbscases_version', RBS_CASES_VERSION);
//            }
            
        }

        
        private function load_settings() {          
            //$settings = get_option('rbs_books_settings', array());            
	    $settings = get_option('rbstravel_settings', array());            
            return $settings;
        }
        

        public function save_settings() {        
	    //echo '<pre>' . print_r($_POST, true) . '</pre>';
	    if (current_user_can('manage_options')) {
			$save_settings = filter_input(INPUT_POST, 'rbstravel_save_settings');
			if ($save_settings != 1) {		
			return;
			}
			
			$nonce = filter_input(INPUT_POST, '_wpnonce');
			if (\wp_verify_nonce($nonce) === false) {
			wp_die('TEMP: Invalid Nonce', 'rbs-travel');
			}
			
	
			
			$posted_settings = filter_input(INPUT_POST, 'rbstravel_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
			//echo '<pre>' . print_r($posted_settings, true) . '</pre>';
			
            $current_settings = $this->load_settings();
            
            // Save API credentials (multiple sets)
            if (isset($posted_settings['api_credentials']) && is_array($posted_settings['api_credentials'])) {
                $sanitized_credentials = array();
                foreach ($posted_settings['api_credentials'] as $set_id => $credential) {
                    $sanitized_credentials[$set_id] = array(
                        'name' => sanitize_text_field($credential['name']),
                        'username' => sanitize_text_field($credential['username']),
                        'password' => $credential['password'], // Store as-is (could encrypt if needed)
                        'micrositeid' => sanitize_text_field($credential['micrositeid'])
                    );
                }
                $current_settings['api_credentials'] = $sanitized_credentials;
            }
            
            // Save active API set
            $current_settings['active_api_set'] = isset($posted_settings['active_api_set']) ? sanitize_text_field($posted_settings['active_api_set']) : 'set_1';
            
            // Keep old single credentials for backward compatibility (but don't update them anymore)
            // This allows old code to still work while we transition
            
			$current_settings['tc_base_url'] = isset($posted_settings['tc_base_url']) ? trailingslashit(esc_url_raw($posted_settings['tc_base_url'])) : 'https://online.travelcompositor.com/';
				   
            $current_settings['mapbox_token'] = $posted_settings['mapbox_token'];

			$current_settings['contact_form'] = sanitize_text_field(stripslashes($posted_settings['contact_form']));
            
			// Brand colors
			$current_settings['primary_color'] = isset($posted_settings['primary_color']) ? sanitize_hex_color($posted_settings['primary_color']) : '#6366f1';
			$current_settings['secondary_color'] = isset($posted_settings['secondary_color']) ? sanitize_hex_color($posted_settings['secondary_color']) : '#10b981';
			$current_settings['heading_color'] = isset($posted_settings['heading_color']) ? sanitize_hex_color($posted_settings['heading_color']) : '#1f2937';
			$current_settings['text_color'] = isset($posted_settings['text_color']) ? sanitize_hex_color($posted_settings['text_color']) : '#4b5563';
            
			// Button links
			$current_settings['btn_book_url'] = isset($posted_settings['btn_book_url']) ? sanitize_text_field($posted_settings['btn_book_url']) : '{travel_url}';
			$current_settings['btn_info_url'] = isset($posted_settings['btn_info_url']) ? sanitize_text_field($posted_settings['btn_info_url']) : '/contact';
			$current_settings['btn_customize_url'] = isset($posted_settings['btn_customize_url']) ? sanitize_text_field($posted_settings['btn_customize_url']) : '{travel_url}';
            
			// Frontend settings
			$current_settings['default_metform_shortcode'] = isset($posted_settings['default_metform_shortcode']) ? sanitize_text_field($posted_settings['default_metform_shortcode']) : '';
			$current_settings['itinerary_layout'] = isset($posted_settings['itinerary_layout']) ? sanitize_text_field($posted_settings['itinerary_layout']) : 'cards';
			if (!in_array($current_settings['itinerary_layout'], array('cards', 'slider'), true)) {
				$current_settings['itinerary_layout'] = 'cards';
			}
			$current_settings['route_button_side'] = isset($posted_settings['route_button_side']) ? sanitize_text_field($posted_settings['route_button_side']) : 'right';
			if (!in_array($current_settings['route_button_side'], array('left', 'right'), true)) {
				$current_settings['route_button_side'] = 'right';
			}
            
			// Sidebar widget settings (replaces old expert settings)
			$current_settings['sidebar_widget_title'] = isset($posted_settings['sidebar_widget_title']) ? sanitize_text_field($posted_settings['sidebar_widget_title']) : '';
			$current_settings['sidebar_widget_content'] = isset($posted_settings['sidebar_widget_content']) ? wp_kses_post($posted_settings['sidebar_widget_content']) : '';
			$current_settings['sidebar_widget_enabled'] = isset($posted_settings['sidebar_widget_enabled']) ? '1' : '0';
            
			$current_settings['debug_enabled'] = isset($posted_settings['debug_enabled']) ? $posted_settings['debug_enabled'] : false;
			$current_settings['logging_enabled'] = isset($posted_settings['logging_enabled']) ? $posted_settings['logging_enabled'] : false;
			$current_settings['logging_directory'] = $posted_settings['logging_directory'];

			// Tour Plan Icons - allow spaces in filenames
			$icon_types = array('flight', 'destination', 'hotel', 'cruise', 'transfer', 'transport', 'activity');
			foreach ($icon_types as $icon_type) {
				$key = 'icon_' . $icon_type;
				$current_settings[$key] = isset($posted_settings[$key]) ? sanitize_text_field($posted_settings[$key]) : '';
			}

			update_option('rbstravel_settings', $current_settings);

		// wp_die('TEMP: No Access!', 'rbs-travel');
	    }
	    

            
        }
        
        
//        private function load_default_styling() {
//            $filename = RBSCASES_PLUGIN_PATH . 'assets' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'default.css';
//            $styling = file_get_contents($filename);
//            return $styling;
//        }
        
        
	
	public function register_settings() {

	    register_setting('rbs_books_options_group', 'rbstravel_settings');	    // array('type' => 'string')
	    
	    //add_settings_section($id, $title, $callback, $page, $args)
		
	    //register_setting('rbs_books_options_group', 'account_email', array('type' => 'string'));
	    //register_setting('rbs_books_options_group', 'account_password', array('type' => 'string'));
	    
	    
//	    register_setting('rbs_books_options_group', 'debug_enabled', array('type' => 'boolean'));
//	    register_setting('rbs_books_options_group', 'logging_enabled', array('type' => 'bool'));
//	    register_setting('rbs_books_options_group', 'logging_directory', array('type' => 'string'));
	}
	
	
	
	
        
//        public function SaveSettings() {
//            if (filter_input(INPUT_POST, 'update_settings') == 'Y') {                
//                $this->save_settings();                
//            }
//        }        
	
	
	
	
        
        
        
        public static function GetSetting($name, $default = false) {
//            $settings = get_option('rbs_books_settings', array());
	    $settings = get_option('rbstravel_settings', array());
            if (isset($settings[$name]) && !empty($settings[$name])) {
                return $settings[$name];
            }            
            return $default;
        }
        
        /**
         * Get the active API credentials
         * Returns the currently selected API credential set
         * 
         * In multisite: Uses network-level credentials with site permissions
         * In single site: Uses local credentials
         * 
         * @return array|false Array with 'name', 'username', 'password', 'micrositeid' or false if none found
         */
        public static function GetActiveApiCredentials() {
            // In multisite, use network settings
            if (is_multisite() && class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Network_Settings')) {
                return RBS_TRAVEL_Network_Settings::get_active_api_for_site();
            }
            
            // Single site: use local credentials
            $api_credentials = self::GetSetting('api_credentials', array());
            
            // Check for temporary override (used during import with specific API selection)
            $temp_override = get_option('rbstravel_settings_temp_active_api_set', '');
            if (!empty($temp_override) && isset($api_credentials[$temp_override])) {
                return $api_credentials[$temp_override];
            }
            
            $active_set = self::GetSetting('active_api_set', 'set_1');
            
            // Return the active set if it exists
            if (isset($api_credentials[$active_set])) {
                return $api_credentials[$active_set];
            }
            
            // Fallback to first available set
            if (!empty($api_credentials)) {
                return reset($api_credentials);
            }
            
            // Last fallback: try old single credential format for backward compatibility
            $old_user = self::GetSetting('api_user', '');
            $old_pass = self::GetSetting('api_pass', '');
            $old_micrositeid = self::GetSetting('api_micrositeid', '');
            
            if (!empty($old_user)) {
                return array(
                    'name' => 'Legacy API',
                    'username' => $old_user,
                    'password' => $old_pass,
                    'micrositeid' => $old_micrositeid
                );
            }
            
            return false;
        }
        
        public static function SetSetting($name, $value) {
//            $settings = get_option('rbs_books_settings', array());
	    $settings = get_option('rbstravel_settings', array());
            $settings[$name] = $value;
            update_option('rbstravel_settings', $settings);
        }      
	
	
	
	public static function GetMapping($travel_id = null) {
	    $mappings = get_option('rbstravel_mappings', array());
	    if ($travel_id === null) {
		return $mappings;
	    }
	    
	    if (isset($mappings[$travel_id])) {
		return $mappings[$travel_id];
	    }
	    return false;
	}
        
	public static function SetMapping($travel_id, $post_id) {
	    $mappings = get_option('rbstravel_mappings', array());
	    $mappings[$travel_id] = $post_id;
	    return update_option('rbstravel_mappings', $mappings);
	}
        
	public static function RemoveMapping($travel_id) {
	    $mappings = get_option('rbstravel_mappings', array());
	    if (isset($mappings[$travel_id])) {
		unset($mappings[$travel_id]);
		return update_option('rbstravel_mappings', $mappings);
	    }	    
	    return false;
	}
	
        
        
        


        
        
    }
    
    
}


$RBS_TRAVEL_Settings = new RBS_TRAVEL_Settings();