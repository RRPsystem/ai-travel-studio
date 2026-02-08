<?php
/**
 * Register Page Templates via Plugin
 * This allows templates to work regardless of theme
 * 
 * @package RBS_Travel
 */

namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Page_Templates')) {
    
    class RBS_TRAVEL_Page_Templates {
        
        /**
         * Templates array - using relative keys for database storage
         */
        protected $templates;
        
        /**
         * Template key to file mapping
         */
        protected $template_files = array(
            'rbs-travel-advanced' => 'page-travel-listing-advanced.php',
            'rbs-travel-traveler' => 'page-travel-listing-traveler.php',
            'rbs-travel-elementor' => 'page-travel-listing-elementor.php',
            'rbs-travel-with-theme' => 'page-travel-listing-with-theme.php',
        );
        
        public function __construct() {
            $this->templates = array();
            
            // Add filter to inject our templates
            add_filter('theme_page_templates', array($this, 'add_page_templates'));
            
            // Add filter to load template file - VERY high priority to override theme
            // GoWilds theme uses its own template system, so we need priority 9999
            add_filter('template_include', array($this, 'load_page_template'), 9999);
            
            // Also hook into page_template filter as backup
            add_filter('page_template', array($this, 'load_page_template'), 9999);
        }
        
        /**
         * Add our templates to the page template dropdown
         */
        public function add_page_templates($templates) {
            // Use simple keys that don't change with server paths
            $templates['rbs-travel-advanced'] = __('Travel Listing (Standaard)', 'rbs-travel');
            $templates['rbs-travel-traveler'] = __('Travel Listing (Traveler Style)', 'rbs-travel');
            $templates['rbs-travel-elementor'] = __('Travel Listing (Met Filters & Elementor)', 'rbs-travel');
            $templates['rbs-travel-with-theme'] = __('Travel Listing (Met Theme)', 'rbs-travel');
            
            // Also support legacy absolute path keys for backwards compatibility
            $templates[RBS_TRAVEL_PLUGIN_PATH . 'templates/page-travel-listing-advanced.php'] = __('Travel Listing (Advanced + Layout Options)', 'rbs-travel');
            $templates[RBS_TRAVEL_PLUGIN_PATH . 'templates/page-travel-listing-traveler.php'] = __('Travel Listing (Traveler Style)', 'rbs-travel');
            $templates[RBS_TRAVEL_PLUGIN_PATH . 'templates/page-travel-listing-elementor.php'] = __('Travel Listing (Met Filters & Elementor)', 'rbs-travel');
            $templates[RBS_TRAVEL_PLUGIN_PATH . 'templates/page-travel-listing-with-theme.php'] = __('Travel Listing (Met Theme)', 'rbs-travel');
            
            return $templates;
        }
        
        /**
         * Load the template file
         */
        public function load_page_template($template) {
            global $post;
            
            if (!$post) {
                return $template;
            }
            
            // Get the template from post meta
            $page_template = get_post_meta($post->ID, '_wp_page_template', true);
            
            // Debug output as HTML comment (visible in page source)
            if (current_user_can('administrator')) {
                add_action('wp_head', function() use ($page_template, $template) {
                    echo "\n<!-- RBS DEBUG v5.14.9: page_template meta = " . esc_html($page_template) . " -->\n";
                    echo "<!-- RBS DEBUG: RBS_TRAVEL_PLUGIN_PATH = " . esc_html(RBS_TRAVEL_PLUGIN_PATH) . " -->\n";
                }, 1);
            }
            
            if (empty($page_template) || $page_template === 'default') {
                return $template;
            }
            
            // Check for new simple key format
            if (isset($this->template_files[$page_template])) {
                $template_file = RBS_TRAVEL_PLUGIN_PATH . 'templates/' . $this->template_files[$page_template];
                if (file_exists($template_file)) {
                    return $template_file;
                }
            }
            
            // Check for legacy absolute path format (backwards compatibility)
            // Also handle paths that might have different server prefixes
            $template_basename = basename($page_template);
            $legacy_templates = array(
                'page-travel-listing-advanced.php',
                'page-travel-listing-traveler.php',
                'page-travel-listing-elementor.php',
                'page-travel-listing-with-theme.php'
            );
            
            if (in_array($template_basename, $legacy_templates)) {
                $template_file = RBS_TRAVEL_PLUGIN_PATH . 'templates/' . $template_basename;
                if (file_exists($template_file)) {
                    return $template_file;
                }
            }
            
            // Also check if the stored path contains our template directory
            if (strpos($page_template, 'rbs-travel/templates/') !== false || 
                strpos($page_template, 'rbs-travel\\templates\\') !== false) {
                $template_file = RBS_TRAVEL_PLUGIN_PATH . 'templates/' . $template_basename;
                if (file_exists($template_file)) {
                    return $template_file;
                }
            }
            
            return $template;
        }
    }
    
    // Initialize
    new RBS_TRAVEL_Page_Templates();
}
