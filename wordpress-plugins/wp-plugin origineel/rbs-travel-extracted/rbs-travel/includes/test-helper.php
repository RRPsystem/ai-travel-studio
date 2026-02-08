<?php
/**
 * RBS Travel - Test Helper
 * Diagnostic tools for testing plugin functionality
 * 
 * Usage: Add to wp-admin only, never in production!
 * Add to functions.php: require_once(plugin_dir_path(__FILE__) . 'includes/test-helper.php');
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

class RBS_Travel_Test_Helper {
    
    /**
     * Add admin menu for test page
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_test_menu']);
        add_action('wp_ajax_rbs_test_api', [__CLASS__, 'test_api']);
        add_action('wp_ajax_rbs_test_import', [__CLASS__, 'test_import']);
    }
    
    /**
     * Add test menu in admin
     */
    public static function add_test_menu() {
        add_submenu_page(
            'edit.php?post_type=rbs-travel-idea',
            'Test & Diagnostics',
            '🧪 Test',
            'manage_options',
            'rbs-travel-test',
            [__CLASS__, 'render_test_page']
        );
    }
    
    /**
     * Render test page
     */
    public static function render_test_page() {
        ?>
        <div class="wrap">
            <h1>🧪 RBS Travel - Test & Diagnostics</h1>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>1️⃣ Basic Checks</h2>
                <?php self::run_basic_checks(); ?>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>2️⃣ Database Status</h2>
                <?php self::check_database(); ?>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>3️⃣ REST API Test</h2>
                <?php self::test_rest_api(); ?>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>4️⃣ Import Test</h2>
                <?php self::check_import(); ?>
            </div>
            
            <div class="card" style="max-width: 800px; margin-top: 20px;">
                <h2>5️⃣ Cruise Data Test</h2>
                <?php self::check_cruise_data(); ?>
            </div>
        </div>
        
        <style>
            .test-result {
                padding: 10px;
                margin: 10px 0;
                border-radius: 5px;
                font-family: monospace;
            }
            .test-pass {
                background: #d4edda;
                border: 1px solid #c3e6cb;
                color: #155724;
            }
            .test-fail {
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                color: #721c24;
            }
            .test-warning {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
            }
        </style>
        <?php
    }
    
    /**
     * Run basic checks
     */
    private static function run_basic_checks() {
        echo '<div style="margin: 15px 0;">';
        
        // Check if plugin is active
        if (class_exists('RBS_TRAVEL_Settings')) {
            echo '<div class="test-result test-pass">✅ Plugin Class Loaded</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Plugin Class NOT Loaded</div>';
        }
        
        // Check post type
        if (post_type_exists('rbs-travel-idea')) {
            echo '<div class="test-result test-pass">✅ Post Type Registered</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Post Type NOT Registered</div>';
        }
        
        // Check taxonomies
        $taxonomies = ['location', 'tour-type', 'tour-theme', 'tour-service'];
        foreach ($taxonomies as $tax) {
            if (taxonomy_exists($tax)) {
                echo '<div class="test-result test-pass">✅ Taxonomy: ' . $tax . '</div>';
            } else {
                echo '<div class="test-result test-fail">❌ Taxonomy Missing: ' . $tax . '</div>';
            }
        }
        
        // Check theme colors
        if (class_exists('RBS_Travel_Theme_Colors')) {
            echo '<div class="test-result test-pass">✅ Theme Colors Class Available</div>';
            $colors = RBS_Travel_Theme_Colors::get_theme_colors();
            echo '<div class="test-result test-pass">Primary Color: ' . $colors['primary'] . '</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Theme Colors Class NOT Available</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Check database
     */
    private static function check_database() {
        global $wpdb;
        
        echo '<div style="margin: 15px 0;">';
        
        // Count posts
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'rbs-travel-idea' AND post_status = 'publish'");
        
        if ($count > 0) {
            echo '<div class="test-result test-pass">✅ ' . $count . ' Travel Ideas Published</div>';
        } else {
            echo '<div class="test-result test-warning">⚠️ No Travel Ideas Found (Import some first)</div>';
        }
        
        // Count with cruise
        $cruise_count = $wpdb->get_var("
            SELECT COUNT(DISTINCT p.ID) 
            FROM {$wpdb->posts} p 
            INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
            WHERE p.post_type = 'rbs-travel-idea' 
            AND p.post_status = 'publish'
            AND pm.meta_key = 'travel_cruises'
            AND pm.meta_value != ''
            AND pm.meta_value != '[]'
        ");
        
        if ($cruise_count > 0) {
            echo '<div class="test-result test-pass">✅ ' . $cruise_count . ' Travel Ideas with Cruises</div>';
        } else {
            echo '<div class="test-result test-warning">⚠️ No Cruises Found (Import cruise travel first)</div>';
        }
        
        // Check terms
        $cruise_term = term_exists('Cruise', 'tour-type');
        if ($cruise_term) {
            echo '<div class="test-result test-pass">✅ "Cruise" Term Exists in tour-type</div>';
        } else {
            echo '<div class="test-result test-warning">⚠️ "Cruise" Term Not Found</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Test REST API
     */
    private static function test_rest_api() {
        echo '<div style="margin: 15px 0;">';
        
        $api_url = rest_url('rbs-travel/v1/ideas');
        
        echo '<div class="test-result test-pass">';
        echo '<strong>API URL:</strong> <a href="' . $api_url . '" target="_blank">' . $api_url . '</a>';
        echo '</div>';
        
        // Test API call
        $response = wp_remote_get($api_url);
        
        if (is_wp_error($response)) {
            echo '<div class="test-result test-fail">❌ API Error: ' . $response->get_error_message() . '</div>';
        } else {
            $code = wp_remote_retrieve_response_code($response);
            if ($code === 200) {
                echo '<div class="test-result test-pass">✅ API Response: 200 OK</div>';
                
                $body = json_decode(wp_remote_retrieve_body($response), true);
                
                if (isset($body['ideas'])) {
                    $count = count($body['ideas']);
                    echo '<div class="test-result test-pass">✅ Ideas Returned: ' . $count . '</div>';
                    
                    // Check first idea structure
                    if ($count > 0) {
                        $first = $body['ideas'][0];
                        
                        // Check required fields
                        $required_fields = ['id', 'title', 'price', 'cruises', 'has_cruise'];
                        $missing = [];
                        
                        foreach ($required_fields as $field) {
                            if (!isset($first[$field])) {
                                $missing[] = $field;
                            }
                        }
                        
                        if (empty($missing)) {
                            echo '<div class="test-result test-pass">✅ All Required Fields Present</div>';
                        } else {
                            echo '<div class="test-result test-fail">❌ Missing Fields: ' . implode(', ', $missing) . '</div>';
                        }
                        
                        // Check cruise data
                        if (isset($first['has_cruise']) && $first['has_cruise']) {
                            echo '<div class="test-result test-pass">✅ Cruise Flag Detected</div>';
                            
                            if (!empty($first['cruises'])) {
                                $cruise = $first['cruises'][0];
                                echo '<div class="test-result test-pass">';
                                echo '✅ Cruise Data:<br>';
                                echo 'Ship: ' . ($cruise['shipName'] ?? 'N/A') . '<br>';
                                echo 'Line: ' . ($cruise['cruiseLine'] ?? 'N/A') . '<br>';
                                echo 'Nights: ' . ($cruise['nights'] ?? 'N/A');
                                echo '</div>';
                            }
                        }
                    }
                } else {
                    echo '<div class="test-result test-fail">❌ Invalid API Response Structure</div>';
                }
            } else {
                echo '<div class="test-result test-fail">❌ API Response Code: ' . $code . '</div>';
            }
        }
        
        echo '</div>';
    }
    
    /**
     * Check import functionality
     */
    private static function check_import() {
        echo '<div style="margin: 15px 0;">';
        
        if (class_exists('RBS_Travel_Import')) {
            echo '<div class="test-result test-pass">✅ Import Class Available</div>';
        } else {
            echo '<div class="test-result test-fail">❌ Import Class NOT Available</div>';
        }
        
        // Check if API credentials are set
        $api_url = get_option('rbstravel_api_url');
        $api_token = get_option('rbstravel_api_token');
        
        if ($api_url) {
            echo '<div class="test-result test-pass">✅ API URL Configured</div>';
        } else {
            echo '<div class="test-result test-fail">❌ API URL Not Configured</div>';
        }
        
        if ($api_token) {
            echo '<div class="test-result test-pass">✅ API Token Set</div>';
        } else {
            echo '<div class="test-result test-fail">❌ API Token Not Set</div>';
        }
        
        echo '</div>';
    }
    
    /**
     * Check cruise data
     */
    private static function check_cruise_data() {
        echo '<div style="margin: 15px 0;">';
        
        // Get latest post with cruise
        $args = [
            'post_type' => 'rbs-travel-idea',
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => 'travel_cruises',
                    'compare' => 'EXISTS'
                ]
            ]
        ];
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            $post = $query->posts[0];
            
            echo '<div class="test-result test-pass">✅ Found Post with Cruise Data: ' . $post->post_title . '</div>';
            
            // Get cruise data
            $cruises = get_post_meta($post->ID, 'travel_cruises', true);
            
            if (!empty($cruises) && is_array($cruises)) {
                echo '<div class="test-result test-pass">✅ Cruise Data Structure Valid</div>';
                
                echo '<div class="test-result test-pass">';
                echo '<strong>Sample Cruise Data:</strong><br>';
                echo '<pre>' . print_r($cruises[0], true) . '</pre>';
                echo '</div>';
            } else {
                echo '<div class="test-result test-fail">❌ Cruise Data Invalid or Empty</div>';
            }
            
            // Check taxonomy
            $terms = wp_get_post_terms($post->ID, 'tour-type', ['fields' => 'names']);
            if (in_array('Cruise', $terms)) {
                echo '<div class="test-result test-pass">✅ "Cruise" Taxonomy Assigned</div>';
            } else {
                echo '<div class="test-result test-warning">⚠️ "Cruise" Taxonomy Not Assigned</div>';
            }
            
        } else {
            echo '<div class="test-result test-warning">⚠️ No Posts with Cruise Data Found</div>';
            echo '<div class="test-result test-warning">💡 Import a travel that contains cruise data first</div>';
        }
        
        echo '</div>';
    }
}

// Initialize test helper (only in admin)
if (is_admin()) {
    RBS_Travel_Test_Helper::init();
}
