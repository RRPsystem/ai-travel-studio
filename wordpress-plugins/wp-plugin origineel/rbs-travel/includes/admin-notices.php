<?php
/**
 * Admin Notices for RBS Travel
 * Shows important messages to admin users
 */

namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Admin_Notices')) {
    
    class RBS_TRAVEL_Admin_Notices {
        
        public function __construct() {
            add_action('admin_notices', array($this, 'show_permalink_notice'));
            add_action('admin_init', array($this, 'dismiss_notice'));
        }
        
        /**
         * Show notice to flush permalinks if API doesn't work
         */
        public function show_permalink_notice() {
            // Only show to admins
            if (!current_user_can('manage_options')) {
                return;
            }
            
            // Check if already dismissed
            if (get_option('rbs_travel_permalink_notice_dismissed')) {
                return;
            }
            
            // Test if REST API works
            $test_url = rest_url('rbs-travel/v1/ideas');
            $response = wp_remote_get($test_url);
            
            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                ?>
                <div class="notice notice-warning is-dismissible" data-notice="rbs-travel-permalink">
                    <h3>⚠️ RBS Travel Plugin - Actie Vereist</h3>
                    <p><strong>De REST API werkt nog niet correct.</strong></p>
                    <p>Om de Travel Listing pagina te laten werken, moet je de WordPress permalinks opnieuw opslaan:</p>
                    <ol>
                        <li>Ga naar <strong>Instellingen → Permalinks</strong></li>
                        <li>Klik onderaan op <strong>"Wijzigingen opslaan"</strong> (zonder iets te veranderen)</li>
                        <li>De pagina werkt nu correct!</li>
                    </ol>
                    <p>
                        <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button button-primary">Ga naar Permalink Instellingen</a>
                        <a href="<?php echo add_query_arg('rbs_travel_dismiss_notice', '1'); ?>" class="button">Deze melding verbergen</a>
                    </p>
                </div>
                <?php
            }
        }
        
        /**
         * Dismiss the notice
         */
        public function dismiss_notice() {
            if (isset($_GET['rbs_travel_dismiss_notice']) && current_user_can('manage_options')) {
                update_option('rbs_travel_permalink_notice_dismissed', true);
                wp_redirect(remove_query_arg('rbs_travel_dismiss_notice'));
                exit;
            }
        }
    }
    
    // Initialize
    new RBS_TRAVEL_Admin_Notices();
}
