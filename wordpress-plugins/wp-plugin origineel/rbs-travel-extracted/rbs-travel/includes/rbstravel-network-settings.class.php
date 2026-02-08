<?php
/**
 * Network Settings Class for Multisite
 * 
 * Handles centralized API credential management at network level
 * with per-site permission assignment.
 * 
 * Security Model:
 * - Super Admins: Can see/edit all API credentials at network level
 * - Site Admins: Can only see names of assigned APIs, not credentials
 * - Sites can only use APIs that network admin has assigned to them
 */

namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Network_Settings')) {
    
    class RBS_TRAVEL_Network_Settings {
        
        public function __construct() {
            // Only initialize in multisite environment
            if (!is_multisite()) {
                return;
            }
            
            $this->init();
        }
        
        private function init() {
            // Add network admin menu
            add_action('network_admin_menu', array($this, 'add_network_menu'));
            
            // Handle network settings save
            add_action('network_admin_edit_rbstravel_network_settings', array($this, 'save_network_settings'));
            
            // Modify site settings page to show only assigned APIs
            add_filter('rbstravel_available_api_sets', array($this, 'filter_available_api_sets'), 10, 1);
        }
        
        /**
         * Add Network Admin Menu
         */
        public function add_network_menu() {
            add_menu_page(
                __('RRP Network Settings', 'rbs-travel'),
                __('RRP System', 'rbs-travel'),
                'manage_network_options',
                'rbstravel-network-settings',
                array($this, 'render_network_settings_page'),
                'dashicons-airplane',
                30
            );
        }
        
        /**
         * Get all API credentials stored at network level
         */
        public static function get_network_api_credentials() {
            if (!is_multisite()) {
                return array();
            }
            return get_site_option('rbstravel_network_api_credentials', array());
        }
        
        /**
         * Get API sets assigned to a specific site
         */
        public static function get_site_allowed_apis($site_id = null) {
            if (!is_multisite()) {
                return array();
            }
            
            if ($site_id === null) {
                $site_id = get_current_blog_id();
            }
            
            $site_permissions = get_site_option('rbstravel_network_site_permissions', array());
            return isset($site_permissions[$site_id]) ? $site_permissions[$site_id] : array();
        }
        
        /**
         * Check if a site has access to a specific API set
         */
        public static function site_can_use_api($api_set_id, $site_id = null) {
            if (!is_multisite()) {
                return true; // Non-multisite: allow all
            }
            
            $allowed = self::get_site_allowed_apis($site_id);
            return in_array($api_set_id, $allowed);
        }
        
        /**
         * Check if current site is the TravelC Web Catalogus site
         */
        public static function is_catalogus_site($site_id = null) {
            if (!is_multisite()) {
                return true; // Non-multisite: always allow
            }
            
            if ($site_id === null) {
                $site_id = get_current_blog_id();
            }
            
            $catalogus_site_id = get_site_option('rbstravel_travelc_catalogus_site', 0);
            return ($catalogus_site_id > 0 && $catalogus_site_id == $site_id);
        }
        
        /**
         * Get API credentials for current site (only allowed ones)
         * Returns credentials without exposing password to non-super-admins
         */
        public static function get_site_api_credentials($include_password = false) {
            if (!is_multisite()) {
                // Fallback to regular settings for non-multisite
                return RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
            }
            
            $network_credentials = self::get_network_api_credentials();
            $allowed_apis = self::get_site_allowed_apis();
            
            $site_credentials = array();
            foreach ($allowed_apis as $api_id) {
                if (isset($network_credentials[$api_id])) {
                    $cred = $network_credentials[$api_id];
                    
                    // Only include password for super admins or when explicitly requested for API calls
                    if (!$include_password && !is_super_admin()) {
                        $cred['password'] = '********'; // Mask password
                    }
                    
                    $site_credentials[$api_id] = $cred;
                }
            }
            
            return $site_credentials;
        }
        
        /**
         * Get active API credentials for API calls (includes password)
         * This is used internally for actual API requests
         */
        public static function get_active_api_for_site() {
            if (!is_multisite()) {
                return RBS_TRAVEL_Settings::GetActiveApiCredentials();
            }
            
            // Get credentials with password (for internal use)
            $credentials = self::get_site_api_credentials(true);
            
            // Check for temporary override FIRST (used during import with specific API selection)
            $temp_override = get_option('rbstravel_settings_temp_active_api_set', '');
            if (!empty($temp_override) && isset($credentials[$temp_override])) {
                return $credentials[$temp_override];
            }
            
            // Get site's selected active API
            $active_api = get_option('rbstravel_site_active_api', '');
            
            if (!empty($active_api) && isset($credentials[$active_api])) {
                return $credentials[$active_api];
            }
            
            // Return first available if none selected
            if (!empty($credentials)) {
                return reset($credentials);
            }
            
            return false;
        }
        
        /**
         * Filter available API sets for site settings page
         */
        public function filter_available_api_sets($api_sets) {
            if (!is_multisite()) {
                return $api_sets;
            }
            
            // Return only allowed APIs for this site
            return self::get_site_api_credentials(false);
        }
        
        /**
         * Render Network Settings Page
         */
        public function render_network_settings_page() {
            if (!is_super_admin()) {
                wp_die(__('Je hebt geen toegang tot deze pagina.', 'rbs-travel'));
            }
            
            $network_credentials = self::get_network_api_credentials();
            $site_permissions = get_site_option('rbstravel_network_site_permissions', array());
            $catalogus_site_id = get_site_option('rbstravel_travelc_catalogus_site', 0);
            
            // Get all sites in network
            $sites = get_sites(array('number' => 100));
            
            ?>
            <div class="wrap">
                <h1><?php _e('🌐 RRP System - Network Instellingen', 'rbs-travel'); ?></h1>
                
                <p class="description" style="font-size: 14px; margin-bottom: 20px;">
                    <?php _e('Beheer API credentials centraal en wijs ze toe aan specifieke sites. Site admins kunnen alleen de API\'s gebruiken die je hier toewijst, zonder de wachtwoorden te zien.', 'rbs-travel'); ?>
                </p>
                
                <form method="post" action="<?php echo esc_url(network_admin_url('edit.php?action=rbstravel_network_settings')); ?>">
                    <?php wp_nonce_field('rbstravel_network_settings_nonce'); ?>
                    
                    <!-- API Credentials Section -->
                    <div class="card" style="max-width: 100%; padding: 20px; margin-bottom: 20px;">
                        <h2 style="margin-top: 0;"><?php _e('🔑 API Credentials', 'rbs-travel'); ?></h2>
                        <p class="description"><?php _e('Deze credentials worden centraal beheerd. Alleen super admins kunnen deze zien en bewerken.', 'rbs-travel'); ?></p>
                        
                        <div id="network-api-credentials">
                            <?php 
                            if (empty($network_credentials)) {
                                $network_credentials = array('api_1' => array('name' => '', 'username' => '', 'password' => '', 'micrositeid' => ''));
                            }
                            
                            foreach ($network_credentials as $api_id => $cred): 
                            ?>
                            <div class="api-credential-set" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd;">
                                <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                                    <div style="flex: 1; min-width: 150px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Naam (ID)</label>
                                        <input type="text" name="network_api[<?php echo esc_attr($api_id); ?>][name]" value="<?php echo esc_attr($cred['name']); ?>" class="regular-text" placeholder="Bijv: Robas Travel" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1; min-width: 150px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Username</label>
                                        <input type="text" name="network_api[<?php echo esc_attr($api_id); ?>][username]" value="<?php echo esc_attr($cred['username']); ?>" class="regular-text" autocomplete="off" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1; min-width: 150px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Password</label>
                                        <input type="password" name="network_api[<?php echo esc_attr($api_id); ?>][password]" value="<?php echo esc_attr($cred['password']); ?>" class="regular-text" autocomplete="off" style="width: 100%;" />
                                    </div>
                                    <div style="flex: 1; min-width: 150px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">MicrositeID</label>
                                        <input type="text" name="network_api[<?php echo esc_attr($api_id); ?>][micrositeid]" value="<?php echo esc_attr($cred['micrositeid']); ?>" class="regular-text" style="width: 100%;" />
                                    </div>
                                    <div>
                                        <button type="button" class="button remove-api-set" onclick="this.closest('.api-credential-set').remove();">
                                            <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Webhook Settings -->
                                <?php 
                                $webhook_config = isset($cred['webhook']) ? $cred['webhook'] : array();
                                $webhook_enabled = isset($webhook_config['enabled']) ? $webhook_config['enabled'] : '0';
                                $webhook_secret = isset($webhook_config['secret']) ? $webhook_config['secret'] : '';
                                $webhook_api_key = isset($webhook_config['api_key']) ? $webhook_config['api_key'] : '';
                                $webhook_country = isset($webhook_config['default_country']) ? $webhook_config['default_country'] : '';
                                $webhook_status = isset($webhook_config['post_status']) ? $webhook_config['post_status'] : 'draft';
                                $webhook_url = rest_url('rbs-travel/v1/webhook/' . $api_id);
                                ?>
                                
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                                    <h4 style="margin: 0 0 10px 0; color: #0073aa;">🔗 Webhook Auto-Import</h4>
                                    
                                    <div style="margin-bottom: 10px;">
                                        <label style="display: flex; align-items: center; gap: 8px;">
                                            <input type="checkbox" name="network_api[<?php echo esc_attr($api_id); ?>][webhook][enabled]" value="1" <?php checked($webhook_enabled, '1'); ?> />
                                            <strong>Automatisch reizen importeren via webhook</strong>
                                        </label>
                                        <p style="margin: 5px 0 0 26px; color: #666; font-size: 13px;">Nieuwe reizen worden automatisch geïmporteerd wanneer ze in Travel Compositor worden aangemaakt of gewijzigd.</p>
                                    </div>
                                    
                                    <div style="margin-bottom: 10px;">
                                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Webhook URL</label>
                                        <input type="text" value="<?php echo esc_attr($webhook_url); ?>" readonly onclick="this.select();" style="width: 100%; max-width: 600px; background: #f0f0f0; font-family: monospace; font-size: 12px; padding: 8px;" />
                                        <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Gebruik deze URL in Travel Compositor webhook instellingen. Klik om te selecteren en kopiëren.</p>
                                    </div>
                                    
                                    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Webhook API Key (optioneel)</label>
                                            <input type="text" name="network_api[<?php echo esc_attr($api_id); ?>][webhook][api_key]" value="<?php echo esc_attr($webhook_api_key); ?>" placeholder="X-Webhook-Api-Key" style="width: 100%;" />
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">TC Header: X-Webhook-Api-Key</p>
                                        </div>
                                        
                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Webhook Secret (optioneel)</label>
                                            <input type="text" name="network_api[<?php echo esc_attr($api_id); ?>][webhook][secret]" value="<?php echo esc_attr($webhook_secret); ?>" placeholder="Voor HMAC-SHA256 signature" style="width: 100%;" />
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">TC Header: X-Webhook-Signature</p>
                                        </div>
                                        
                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Extra Land/Regio (optioneel)</label>
                                            <?php
                                            $locations = get_terms(array('taxonomy' => 'location', 'hide_empty' => false, 'orderby' => 'name'));
                                            ?>
                                            <select name="network_api[<?php echo esc_attr($api_id); ?>][webhook][default_country]" style="width: 100%;">
                                                <option value="">-- Geen extra land --</option>
                                                <?php if (!is_wp_error($locations) && !empty($locations)): ?>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?php echo esc_attr($location->slug); ?>" <?php selected($webhook_country, $location->slug); ?>>
                                                            <?php echo esc_html($location->name); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Landen worden auto-gedetecteerd uit bestemmingen</p>
                                        </div>
                                        
                                        <div style="flex: 1; min-width: 200px;">
                                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Post Status</label>
                                            <select name="network_api[<?php echo esc_attr($api_id); ?>][webhook][post_status]" style="width: 100%;">
                                                <option value="draft" <?php selected($webhook_status, 'draft'); ?>>Concept (Draft)</option>
                                                <option value="publish" <?php selected($webhook_status, 'publish'); ?>>Gepubliceerd</option>
                                            </select>
                                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Status van geïmporteerde reizen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="button" class="button button-secondary" onclick="addApiCredentialSet();">
                            <span class="dashicons dashicons-plus-alt" style="margin-top: 3px;"></span>
                            <?php _e('API Toevoegen', 'rbs-travel'); ?>
                        </button>
                    </div>
                    
                    <!-- Site Permissions Section -->
                    <div class="card" style="max-width: 100%; padding: 20px; margin-bottom: 20px;">
                        <h2 style="margin-top: 0;"><?php _e('🔐 Site Permissies', 'rbs-travel'); ?></h2>
                        <p class="description"><?php _e('Selecteer welke API\'s elke site mag gebruiken. Site admins zien alleen de namen, niet de credentials.', 'rbs-travel'); ?></p>
                        
                        <table class="wp-list-table widefat fixed striped" style="margin-top: 15px;">
                            <thead>
                                <tr>
                                    <th style="width: 200px;"><?php _e('Site', 'rbs-travel'); ?></th>
                                    <th><?php _e('Toegestane API\'s', 'rbs-travel'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sites as $site): 
                                    $site_id = $site->blog_id;
                                    $site_allowed = isset($site_permissions[$site_id]) ? $site_permissions[$site_id] : array();
                                    $site_details = get_blog_details($site_id);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($site_details->blogname); ?></strong>
                                        <br><small style="color: #666;"><?php echo esc_html($site_details->domain . $site_details->path); ?></small>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                                            <?php foreach ($network_credentials as $api_id => $cred): 
                                                if (empty($cred['name'])) continue;
                                                $checked = in_array($api_id, $site_allowed) ? 'checked' : '';
                                            ?>
                                            <label style="display: flex; align-items: center; gap: 5px; padding: 8px 12px; background: <?php echo $checked ? '#e7f3ff' : '#f5f5f5'; ?>; border-radius: 6px; cursor: pointer;">
                                                <input type="checkbox" name="site_permissions[<?php echo $site_id; ?>][]" value="<?php echo esc_attr($api_id); ?>" <?php echo $checked; ?> />
                                                <span><?php echo esc_html($cred['name']); ?></span>
                                            </label>
                                            <?php endforeach; ?>
                                            
                                            <?php if (empty(array_filter($network_credentials, function($c) { return !empty($c['name']); }))): ?>
                                                <em style="color: #999;"><?php _e('Voeg eerst API credentials toe hierboven', 'rbs-travel'); ?></em>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- TravelC Web Catalogus Section -->
                    <div class="card" style="max-width: 100%; padding: 20px; margin-bottom: 20px;">
                        <h2 style="margin-top: 0;"><?php _e('🌍 TravelC Web Catalogus', 'rbs-travel'); ?></h2>
                        <p class="description"><?php _e('Selecteer welke site reizen mag doorsturen naar de TravelC Web Catalogus. Alleen deze site krijgt de "Publiceer naar Catalogus" functie.', 'rbs-travel'); ?></p>
                        
                        <table class="form-table" style="margin-top: 15px;">
                            <tr>
                                <th scope="row">
                                    <label for="catalogus_site"><?php _e('Catalogus Beheer Site', 'rbs-travel'); ?></label>
                                </th>
                                <td>
                                    <select name="catalogus_site" id="catalogus_site" style="min-width: 300px;">
                                        <option value="0"><?php _e('-- Geen site geselecteerd --', 'rbs-travel'); ?></option>
                                        <?php foreach ($sites as $site): 
                                            $site_id = $site->blog_id;
                                            $site_details = get_blog_details($site_id);
                                            $selected = ($catalogus_site_id == $site_id) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $site_id; ?>" <?php echo $selected; ?>>
                                            <?php echo esc_html($site_details->blogname); ?> (<?php echo esc_html($site_details->domain . $site_details->path); ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="description" style="margin-top: 8px;">
                                        <?php _e('Deze site kan reizen publiceren naar de centrale TravelC Web Catalogus, zodat andere reisbureaus ze kunnen overnemen.', 'rbs-travel'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary button-large" value="<?php _e('Instellingen Opslaan', 'rbs-travel'); ?>" />
                    </p>
                </form>
            </div>
            
            <script>
            var apiCounter = <?php echo count($network_credentials); ?>;
            
            function addApiCredentialSet() {
                apiCounter++;
                var apiId = 'api_' + apiCounter;
                var html = `
                <div class="api-credential-set" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 8px; border: 1px solid #ddd;">
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Naam (ID)</label>
                            <input type="text" name="network_api[${apiId}][name]" value="" class="regular-text" placeholder="Bijv: Robas Travel" style="width: 100%;" />
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Username</label>
                            <input type="text" name="network_api[${apiId}][username]" value="" class="regular-text" autocomplete="off" style="width: 100%;" />
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Password</label>
                            <input type="password" name="network_api[${apiId}][password]" value="" class="regular-text" autocomplete="off" style="width: 100%;" />
                        </div>
                        <div style="flex: 1; min-width: 150px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">MicrositeID</label>
                            <input type="text" name="network_api[${apiId}][micrositeid]" value="" class="regular-text" style="width: 100%;" />
                        </div>
                        <div>
                            <button type="button" class="button remove-api-set" onclick="this.closest('.api-credential-set').remove();">
                                <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                        <h4 style="margin: 0 0 10px 0; color: #0073aa;">🔗 Webhook Auto-Import</h4>
                        <div style="margin-bottom: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px;">
                                <input type="checkbox" name="network_api[${apiId}][webhook][enabled]" value="1" />
                                <strong>Automatisch reizen importeren via webhook</strong>
                            </label>
                            <p style="margin: 5px 0 0 26px; color: #666; font-size: 13px;">Nieuwe reizen worden automatisch geïmporteerd wanneer ze in Travel Compositor worden aangemaakt of gewijzigd.</p>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Webhook URL</label>
                            <input type="text" value="<?php echo home_url(); ?>/wp-json/rbs-travel/v1/webhook/${apiId}" readonly onclick="this.select();" style="width: 100%; max-width: 600px; background: #f0f0f0; font-family: monospace; font-size: 12px; padding: 8px;" />
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 13px;">Gebruik deze URL in Travel Compositor webhook instellingen.</p>
                        </div>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                            <div style="flex: 1; min-width: 200px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Webhook Secret (optioneel)</label>
                                <input type="text" name="network_api[${apiId}][webhook][secret]" value="" placeholder="Optioneel: geheime sleutel" style="width: 100%;" />
                            </div>
                            <div style="flex: 1; min-width: 200px;">
                                <label style="display: block; font-weight: 600; margin-bottom: 5px;">Post Status</label>
                                <select name="network_api[${apiId}][webhook][post_status]" style="width: 100%;">
                                    <option value="draft">Concept (Draft)</option>
                                    <option value="publish">Gepubliceerd</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>`;
                
                document.getElementById('network-api-credentials').insertAdjacentHTML('beforeend', html);
            }
            </script>
            <?php
        }
        
        /**
         * Save Network Settings
         */
        public function save_network_settings() {
            if (!is_super_admin()) {
                wp_die(__('Je hebt geen toegang.', 'rbs-travel'));
            }
            
            check_admin_referer('rbstravel_network_settings_nonce');
            
            // Save API credentials
            $network_api = isset($_POST['network_api']) ? $_POST['network_api'] : array();
            $sanitized_credentials = array();
            
            foreach ($network_api as $api_id => $cred) {
                // Only save if name is not empty
                if (!empty($cred['name'])) {
                    $sanitized_credentials[$api_id] = array(
                        'name' => sanitize_text_field($cred['name']),
                        'username' => sanitize_text_field($cred['username']),
                        'password' => $cred['password'], // Store as-is
                        'micrositeid' => sanitize_text_field($cred['micrositeid']),
                        'webhook' => array(
                            'enabled' => isset($cred['webhook']['enabled']) ? '1' : '0',
                            'api_key' => isset($cred['webhook']['api_key']) ? sanitize_text_field($cred['webhook']['api_key']) : '',
                            'secret' => isset($cred['webhook']['secret']) ? sanitize_text_field($cred['webhook']['secret']) : '',
                            'default_country' => isset($cred['webhook']['default_country']) ? sanitize_text_field($cred['webhook']['default_country']) : '',
                            'post_status' => isset($cred['webhook']['post_status']) ? sanitize_text_field($cred['webhook']['post_status']) : 'draft'
                        )
                    );
                }
            }
            
            update_site_option('rbstravel_network_api_credentials', $sanitized_credentials);
            
            // Save site permissions
            $site_permissions = isset($_POST['site_permissions']) ? $_POST['site_permissions'] : array();
            $sanitized_permissions = array();
            
            foreach ($site_permissions as $site_id => $apis) {
                $sanitized_permissions[intval($site_id)] = array_map('sanitize_text_field', $apis);
            }
            
            update_site_option('rbstravel_network_site_permissions', $sanitized_permissions);
            
            // Save TravelC Web Catalogus site
            $catalogus_site = isset($_POST['catalogus_site']) ? intval($_POST['catalogus_site']) : 0;
            update_site_option('rbstravel_travelc_catalogus_site', $catalogus_site);
            
            // Redirect back
            wp_redirect(add_query_arg(array(
                'page' => 'rbstravel-network-settings',
                'updated' => 'true'
            ), network_admin_url('admin.php')));
            exit;
        }
    }
}

// Initialize
$RBS_TRAVEL_Network_Settings = new RBS_TRAVEL_Network_Settings();
