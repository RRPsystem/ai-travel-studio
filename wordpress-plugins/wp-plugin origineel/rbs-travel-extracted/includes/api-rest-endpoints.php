<?php
/**
 * REST API Endpoints for RBS Travel
 * Provides JSON data for travel ideas
 * 
 * @package RBS_Travel
 */

namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_REST_API')) {
    
    class RBS_TRAVEL_REST_API {
        
        public function __construct() {
            error_log('RBS_TRAVEL_REST_API: Constructor called');
            add_action('rest_api_init', array($this, 'register_endpoints'));
        }
        
        /**
         * Register REST API endpoints
         */
        public function register_endpoints() {
            error_log('RBS_TRAVEL_REST_API: register_endpoints() called');
            
            // Get all travel ideas (listing)
            $result1 = register_rest_route('rbs-travel/v1', '/ideas', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_travel_ideas'),
                'permission_callback' => '__return_true'
            ));
            
            // Get single travel idea by ID
            register_rest_route('rbs-travel/v1', '/ideas/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_travel_idea'),
                'permission_callback' => '__return_true'
            ));
            
            // Get single travel idea by slug
            register_rest_route('rbs-travel/v1', '/ideas/slug/(?P<slug>[a-zA-Z0-9-]+)', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_travel_idea_by_slug'),
                'permission_callback' => '__return_true'
            ));
            
            // Get filter options (taxonomies)
            register_rest_route('rbs-travel/v1', '/filters', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_filter_options'),
                'permission_callback' => '__return_true'
            ));
            
            // BOLT Catalog endpoint - simplified list for brand selection
            register_rest_route('rbs-travel/v1', '/catalog', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_catalog_for_bolt'),
                'permission_callback' => '__return_true'
            ));
            
            // Publish selected travels to a subsite
            register_rest_route('rbs-travel/v1', '/publish', array(
                'methods' => 'POST',
                'callback' => array($this, 'publish_to_subsite'),
                'permission_callback' => array($this, 'check_publish_permission')
            ));
            
            // Webhook endpoint for Travel Compositor auto-import
            // Format: /wp-json/rbs-travel/v1/webhook/{api_set_id}
            register_rest_route('rbs-travel/v1', '/webhook/(?P<api_set>[a-zA-Z0-9_-]+)', array(
                'methods' => 'POST',
                'callback' => array($this, 'handle_webhook'),
                'permission_callback' => array($this, 'verify_webhook_signature')
            ));
        }
        
        /**
         * Get all travel ideas (for listing page)
         */
        public function get_travel_ideas($request) {
            $per_page = $request->get_param('per_page') ?: 12;
            $page = $request->get_param('page') ?: 1;
            $search = $request->get_param('search');
            $location = $request->get_param('location');
            $tour_type = $request->get_param('tour_type');
            $tour_theme = $request->get_param('tour_theme');
            $tour_service = $request->get_param('tour_service');
            $min_price = $request->get_param('min_price');
            $max_price = $request->get_param('max_price');
            $orderby = $request->get_param('orderby') ?: 'date';
            $order = $request->get_param('order') ?: 'DESC';
            
            $args = array(
                'post_type' => 'rbs-travel-idea',
                'posts_per_page' => $per_page,
                'paged' => $page,
                'post_status' => 'publish',
                'orderby' => $orderby,
                'order' => $order
            );
            
            // Search by title/content
            if ($search) {
                $args['s'] = sanitize_text_field($search);
            }
            
            // Taxonomy filters
            $tax_query = array('relation' => 'AND');
            
            if ($location) {
                $tax_query[] = array(
                    'taxonomy' => 'location',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($location)
                );
            }
            
            if ($tour_type) {
                $tax_query[] = array(
                    'taxonomy' => 'tour-type',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($tour_type)
                );
            }
            
            if ($tour_theme) {
                $tax_query[] = array(
                    'taxonomy' => 'tour-theme',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($tour_theme)
                );
            }
            
            if ($tour_service) {
                $tax_query[] = array(
                    'taxonomy' => 'tour-service',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($tour_service)
                );
            }
            
            if (count($tax_query) > 1) {
                $args['tax_query'] = $tax_query;
            }
            
            // Price filter via meta query
            if ($min_price || $max_price) {
                $meta_query = array('relation' => 'AND');
                
                if ($min_price) {
                    $meta_query[] = array(
                        'key' => 'travel_price',
                        'value' => floatval($min_price),
                        'compare' => '>=',
                        'type' => 'NUMERIC'
                    );
                }
                
                if ($max_price) {
                    $meta_query[] = array(
                        'key' => 'travel_price',
                        'value' => floatval($max_price),
                        'compare' => '<=',
                        'type' => 'NUMERIC'
                    );
                }
                
                $args['meta_query'] = $meta_query;
            }
            
            // Sort by price if requested
            if ($orderby === 'price') {
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = 'travel_price';
            }
            
            $query = new \WP_Query($args);
            $ideas = array();
            
            // Debug logging
            error_log('RBS Travel API: Query found ' . $query->found_posts . ' posts');
            
            foreach($query->posts as $post) {
                $destinations = get_post_meta($post->ID, 'travel_destinations', true);
                $transports = get_post_meta($post->ID, 'travel_transports', true);
                $hotels = get_post_meta($post->ID, 'travel_hotels', true);
                
                // Get first and last destination for route
                $start_destination = null;
                $end_destination = null;
                if (is_array($destinations) && count($destinations) > 0) {
                    $start_destination = $destinations[0];
                    $end_destination = $destinations[count($destinations) - 1];
                }
                
                // Generate excerpt
                $excerpt = '';
                $description = get_post_meta($post->ID, 'travel_description', true);
                if ($description) {
                    $excerpt = wp_trim_words($description, 20, '...');
                } else {
                    $excerpt = wp_trim_words($post->post_content, 20, '...');
                }
                
                // Get taxonomy terms for display
                $locations = wp_get_post_terms($post->ID, 'location', array('fields' => 'names'));
                $tour_types = wp_get_post_terms($post->ID, 'tour-type', array('fields' => 'names'));
                $tour_themes = wp_get_post_terms($post->ID, 'tour-theme', array('fields' => 'names'));
                
                // Get cruises
                $cruises = get_post_meta($post->ID, 'travel_cruises', true);
                $has_cruise = !empty($cruises) && is_array($cruises) && count($cruises) > 0;
                
                $ideas[] = array(
                    'id' => $post->ID,
                    'slug' => $post->post_name,
                    'title' => get_the_title($post->ID),
                    'excerpt' => $excerpt,
                    'description' => $description,
                    'image' => get_post_meta($post->ID, 'travel_image_url', true),
                    'price' => get_post_meta($post->ID, 'travel_price_per_person', true),
                    'nights' => get_post_meta($post->ID, 'travel_number_of_nights', true),
                    'departure_date' => get_post_meta($post->ID, 'travel_departure_date', true),
                    'destinations' => $destinations,
                    'start_destination' => $start_destination,
                    'end_destination' => $end_destination,
                    'transports' => $transports,
                    'hotels' => $hotels,
                    'cruises' => $cruises,
                    'has_cruise' => $has_cruise,
                    'locations' => !is_wp_error($locations) ? $locations : array(),
                    'tour_types' => !is_wp_error($tour_types) ? $tour_types : array(),
                    'tour_themes' => !is_wp_error($tour_themes) ? $tour_themes : array(),
                    'url' => get_permalink($post->ID)
                );
            }
            
            return array(
                'ideas' => $ideas,
                'total' => $query->found_posts,
                'pages' => $query->max_num_pages,
                'current_page' => $page
            );
        }
        
        /**
         * Get single travel idea by ID
         */
        public function get_travel_idea($request) {
            $id = $request->get_param('id');
            $post = get_post($id);
            
            if (!$post || $post->post_type !== 'rbs-travel-idea') {
                return new \WP_Error('not_found', 'Travel idea not found', array('status' => 404));
            }
            
            return $this->format_travel_idea($post);
        }
        
        /**
         * Get single travel idea by slug
         */
        public function get_travel_idea_by_slug($request) {
            $slug = $request->get_param('slug');
            
            $args = array(
                'post_type' => 'rbs-travel-idea',
                'name' => $slug,
                'posts_per_page' => 1
            );
            
            $query = new \WP_Query($args);
            
            if (!$query->have_posts()) {
                return new \WP_Error('not_found', 'Travel idea not found', array('status' => 404));
            }
            
            return $this->format_travel_idea($query->posts[0]);
        }
        
        /**
         * Format travel idea data
         */
        private function format_travel_idea($post) {
            $destinations = get_post_meta($post->ID, 'travel_destinations', true);
            $transports = get_post_meta($post->ID, 'travel_transports', true);
            $hotels = get_post_meta($post->ID, 'travel_hotels', true);
            $transfers = get_post_meta($post->ID, 'travel_transfers', true);
            $cars = get_post_meta($post->ID, 'travel_cars', true);
            $cruises = get_post_meta($post->ID, 'travel_cruises', true);
            
            return array(
                'id' => $post->ID,
                'slug' => $post->post_name,
                'title' => get_the_title($post->ID),
                'description' => get_post_meta($post->ID, 'travel_description', true),
                'large_title' => get_post_meta($post->ID, 'travel_large_title', true),
                'image' => get_post_meta($post->ID, 'travel_image_url', true),
                'price_per_person' => get_post_meta($post->ID, 'travel_price_per_person', true),
                'total_price' => get_post_meta($post->ID, 'travel_price_total', true),
                'nights' => get_post_meta($post->ID, 'travel_number_of_nights', true),
                'min_persons' => get_post_meta($post->ID, 'travel_min_persons', true),
                'max_persons' => get_post_meta($post->ID, 'travel_max_persons', true),
                'departure_date' => get_post_meta($post->ID, 'travel_departure_date', true),
                'creation_date' => get_post_meta($post->ID, 'travel_creation_date', true),
                'themes' => get_post_meta($post->ID, 'travel_themes', true),
                'remarks' => get_post_meta($post->ID, 'travel_remarks', true),
                'itinerary' => get_post_meta($post->ID, 'itinerary', true),
                'destinations' => $destinations,
                'transports' => $transports,
                'hotels' => $hotels,
                'transfers' => $transfers,
                'cars' => $cars,
                'cruises' => $cruises,
                'counters' => get_post_meta($post->ID, 'travel_counters', true),
                'url' => get_permalink($post->ID),
                'has_cruise' => !empty($cruises) && is_array($cruises) && count($cruises) > 0
            );
        }
        
        /**
         * Get filter options (all taxonomy terms)
         */
        public function get_filter_options($request) {
            $filters = array();
            
            // Get all locations
            $locations = get_terms(array(
                'taxonomy' => 'location',
                'hide_empty' => true
            ));
            
            $filters['locations'] = array();
            if (!is_wp_error($locations)) {
                foreach ($locations as $term) {
                    $filters['locations'][] = array(
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'count' => $term->count
                    );
                }
            }
            
            // Get all tour types
            $tour_types = get_terms(array(
                'taxonomy' => 'tour-type',
                'hide_empty' => true
            ));
            
            $filters['tour_types'] = array();
            if (!is_wp_error($tour_types)) {
                foreach ($tour_types as $term) {
                    $filters['tour_types'][] = array(
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'count' => $term->count
                    );
                }
            }
            
            // Get all tour themes
            $tour_themes = get_terms(array(
                'taxonomy' => 'tour-theme',
                'hide_empty' => true
            ));
            
            $filters['tour_themes'] = array();
            if (!is_wp_error($tour_themes)) {
                foreach ($tour_themes as $term) {
                    $filters['tour_themes'][] = array(
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'count' => $term->count
                    );
                }
            }
            
            // Get all services
            $tour_services = get_terms(array(
                'taxonomy' => 'tour-service',
                'hide_empty' => true
            ));
            
            $filters['tour_services'] = array();
            if (!is_wp_error($tour_services)) {
                foreach ($tour_services as $term) {
                    $filters['tour_services'][] = array(
                        'id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'count' => $term->count
                    );
                }
            }
            
            // Get price range
            global $wpdb;
            $price_range = $wpdb->get_row("
                SELECT 
                    MIN(CAST(meta_value AS DECIMAL(10,2))) as min_price,
                    MAX(CAST(meta_value AS DECIMAL(10,2))) as max_price
                FROM {$wpdb->postmeta}
                WHERE meta_key = 'travel_price'
                AND meta_value != ''
                AND meta_value IS NOT NULL
            ");
            
            $filters['price_range'] = array(
                'min' => $price_range ? floor($price_range->min_price) : 0,
                'max' => $price_range ? ceil($price_range->max_price) : 10000
            );
            
            return rest_ensure_response($filters);
        }
        
        /**
         * Get simplified catalog for BOLT brand selection
         * Returns: Continent, Land, Titel, Preview URL, travel_id
         */
        public function get_catalog_for_bolt($request) {
            $continent = $request->get_param('continent');
            $country = $request->get_param('country');
            
            $args = array(
                'post_type' => 'rbs-travel-idea',
                'posts_per_page' => -1, // All travels
                'post_status' => 'publish',
                'orderby' => 'title',
                'order' => 'ASC',
                // Only show travels marked for BOLT catalog by admin
                'meta_query' => array(
                    array(
                        'key' => '_in_bolt_catalog',
                        'value' => '1',
                        'compare' => '='
                    )
                )
            );
            
            // Filter by location taxonomy if provided
            $tax_query = array();
            if ($continent || $country) {
                $tax_query['relation'] = 'AND';
                
                if ($continent) {
                    $tax_query[] = array(
                        'taxonomy' => 'location',
                        'field' => 'slug',
                        'terms' => $continent
                    );
                }
                
                if ($country) {
                    $tax_query[] = array(
                        'taxonomy' => 'location',
                        'field' => 'slug',
                        'terms' => $country
                    );
                }
                
                $args['tax_query'] = $tax_query;
            }
            
            $query = new \WP_Query($args);
            $catalog = array();
            
            // Build continent/country hierarchy for filters
            $locations_hierarchy = $this->get_locations_hierarchy();
            
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $post_id = get_the_ID();
                    
                    // Get travel_id from meta (TC API ID)
                    $travel_id = get_post_meta($post_id, 'travel_idea_id', true);
                    
                    // Get location terms
                    $locations = wp_get_post_terms($post_id, 'location', array('fields' => 'all'));
                    $continent_name = '';
                    $country_name = '';
                    
                    if (!is_wp_error($locations) && !empty($locations)) {
                        foreach ($locations as $loc) {
                            if ($loc->parent == 0) {
                                $continent_name = $loc->name;
                            } else {
                                $country_name = $loc->name;
                            }
                        }
                    }
                    
                    // Get thumbnail
                    $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
                    if (!$thumbnail) {
                        $travel_images = get_post_meta($post_id, 'travel_images', true);
                        if (!empty($travel_images) && is_array($travel_images)) {
                            $thumbnail = $travel_images[0];
                        }
                    }
                    
                    $catalog[] = array(
                        'id' => $post_id,
                        'travel_id' => $travel_id, // TC API ID for re-import
                        'title' => get_the_title(),
                        'continent' => $continent_name,
                        'country' => $country_name,
                        'preview_url' => get_permalink($post_id),
                        'thumbnail' => $thumbnail ?: '',
                        'price' => get_post_meta($post_id, 'travel_price_per_person', true),
                        'nights' => get_post_meta($post_id, 'travel_number_of_nights', true)
                    );
                }
                wp_reset_postdata();
            }
            
            return rest_ensure_response(array(
                'total' => count($catalog),
                'filters' => $locations_hierarchy,
                'travels' => $catalog
            ));
        }
        
        /**
         * Get locations hierarchy (Continent => Countries)
         */
        private function get_locations_hierarchy() {
            $hierarchy = array();
            
            // Get all continents (parent = 0)
            $continents = get_terms(array(
                'taxonomy' => 'location',
                'parent' => 0,
                'hide_empty' => true
            ));
            
            if (!is_wp_error($continents)) {
                foreach ($continents as $continent) {
                    $countries = get_terms(array(
                        'taxonomy' => 'location',
                        'parent' => $continent->term_id,
                        'hide_empty' => true
                    ));
                    
                    $country_list = array();
                    if (!is_wp_error($countries)) {
                        foreach ($countries as $country) {
                            $country_list[] = array(
                                'slug' => $country->slug,
                                'name' => $country->name,
                                'count' => $country->count
                            );
                        }
                    }
                    
                    $hierarchy[] = array(
                        'slug' => $continent->slug,
                        'name' => $continent->name,
                        'count' => $continent->count,
                        'countries' => $country_list
                    );
                }
            }
            
            return $hierarchy;
        }
        
        /**
         * Check permission for publish endpoint
         */
        public function check_publish_permission($request) {
            // Require API key or valid authentication
            $api_key = $request->get_header('X-RBS-API-Key');
            $stored_key = get_site_option('rbstravel_bolt_api_key', '');
            
            if (!empty($stored_key) && $api_key === $stored_key) {
                return true;
            }
            
            // Fallback: check if user is logged in with proper capabilities
            return current_user_can('manage_options');
        }
        
        /**
         * Publish selected travels to a brand's subsite
         * Receives travel_ids from BOLT and triggers import on target site
         */
        public function publish_to_subsite($request) {
            $travel_ids = $request->get_param('travel_ids'); // Array of TC travel IDs
            $target_site_id = $request->get_param('site_id'); // WordPress multisite blog ID
            
            if (empty($travel_ids) || !is_array($travel_ids)) {
                return new \WP_Error('invalid_params', 'travel_ids array is required', array('status' => 400));
            }
            
            if (empty($target_site_id)) {
                return new \WP_Error('invalid_params', 'site_id is required', array('status' => 400));
            }
            
            // Verify site exists (multisite)
            if (is_multisite()) {
                $site = get_blog_details($target_site_id);
                if (!$site) {
                    return new \WP_Error('invalid_site', 'Target site not found', array('status' => 404));
                }
            }
            
            $results = array(
                'success' => array(),
                'failed' => array()
            );
            
            // Switch to target site and import each travel
            if (is_multisite()) {
                switch_to_blog($target_site_id);
            }
            
            foreach ($travel_ids as $travel_id) {
                try {
                    // Use existing import functionality
                    $post_id = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Import::import_travel($travel_id);
                    
                    if ($post_id && !is_wp_error($post_id)) {
                        $results['success'][] = array(
                            'travel_id' => $travel_id,
                            'post_id' => $post_id,
                            'url' => get_permalink($post_id)
                        );
                    } else {
                        $results['failed'][] = array(
                            'travel_id' => $travel_id,
                            'error' => is_wp_error($post_id) ? $post_id->get_error_message() : 'Import failed'
                        );
                    }
                } catch (\Exception $e) {
                    $results['failed'][] = array(
                        'travel_id' => $travel_id,
                        'error' => $e->getMessage()
                    );
                }
            }
            
            if (is_multisite()) {
                restore_current_blog();
            }
            
            return rest_ensure_response(array(
                'message' => sprintf('%d travels imported, %d failed', count($results['success']), count($results['failed'])),
                'results' => $results
            ));
        }
        
        /**
         * Verify webhook signature from Travel Compositor
         * Supports both X-Webhook-Signature (HMAC-SHA256) and X-Webhook-Api-Key
         * Endpoint is always accessible to allow TC to test it before enabling
         */
        public function verify_webhook_signature($request) {
            $api_set = $request->get_param('api_set');
            
            // Get webhook settings for this API set (support both single site and multisite)
            if (is_multisite()) {
                $api_credentials = get_site_option('rbstravel_network_api_credentials', array());
            } else {
                $api_credentials = RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
            }
            
            // Check if API set exists
            if (!isset($api_credentials[$api_set])) {
                error_log('RBS Travel Webhook: Unknown API set: ' . $api_set);
                return new \WP_Error('invalid_api_set', 'Unknown API set', array('status' => 404));
            }
            
            $webhook_config = isset($api_credentials[$api_set]['webhook']) ? $api_credentials[$api_set]['webhook'] : array();
            
            // Verify X-Webhook-Signature if secret is configured (HMAC-SHA256)
            $webhook_secret = isset($webhook_config['secret']) ? $webhook_config['secret'] : '';
            if (!empty($webhook_secret)) {
                $signature_header = $request->get_header('X-Webhook-Signature');
                
                if (!empty($signature_header)) {
                    // Get raw body for signature verification
                    $body = $request->get_body();
                    
                    // Calculate expected signature (HMAC-SHA256)
                    $expected_signature = 'sha256=' . hash_hmac('sha256', $body, $webhook_secret);
                    
                    if (!hash_equals($expected_signature, $signature_header)) {
                        error_log('RBS Travel Webhook: Invalid signature for API set: ' . $api_set);
                        error_log('Expected: ' . $expected_signature);
                        error_log('Received: ' . $signature_header);
                        return new \WP_Error('invalid_signature', 'Invalid webhook signature', array('status' => 403));
                    }
                }
            }
            
            // Verify X-Webhook-Api-Key if configured
            $api_key = isset($webhook_config['api_key']) ? $webhook_config['api_key'] : '';
            if (!empty($api_key)) {
                $provided_key = $request->get_header('X-Webhook-Api-Key');
                if ($provided_key !== $api_key) {
                    error_log('RBS Travel Webhook: Invalid API key for API set: ' . $api_set);
                    return new \WP_Error('invalid_api_key', 'Invalid webhook API key', array('status' => 403));
                }
            }
            
            // Allow webhook to proceed (enabled check happens in handler)
            return true;
        }
        
        /**
         * Handle incoming webhook from Travel Compositor
         * Format: {"ideaIds": ["31668846", "31598765"]}
         */
        public function handle_webhook($request) {
            $api_set = $request->get_param('api_set');
            $body = $request->get_json_params();
            
            error_log('RBS Travel Webhook: Received webhook for API set: ' . $api_set);
            error_log('RBS Travel Webhook: Body: ' . print_r($body, true));
            
            // Get webhook configuration (support both single site and multisite)
            if (is_multisite()) {
                $api_credentials = get_site_option('rbstravel_network_api_credentials', array());
            } else {
                $api_credentials = RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
            }
            $webhook_config = isset($api_credentials[$api_set]['webhook']) ? $api_credentials[$api_set]['webhook'] : array();
            
            // Check if webhook is enabled (only process if enabled, but allow test requests)
            $is_enabled = isset($webhook_config['enabled']) && $webhook_config['enabled'] === '1';
            
            // Extract ideaIds from Travel Compositor webhook payload
            $idea_ids = isset($body['ideaIds']) && is_array($body['ideaIds']) ? $body['ideaIds'] : array();
            
            if (empty($idea_ids)) {
                error_log('RBS Travel Webhook: No ideaIds in webhook payload');
                // Return success for test requests (TC sends empty payload to test)
                $response = array(
                    'success' => true,
                    'message' => 'Webhook endpoint is ready',
                    'enabled' => $is_enabled,
                    'api_set' => $api_set
                );
                $this->log_webhook($api_set, $body, 200, $response);
                return rest_ensure_response($response);
            }
            
            // If webhook is not enabled, acknowledge but don't process
            if (!$is_enabled) {
                error_log('RBS Travel Webhook: Webhook not enabled, skipping import');
                $response = array(
                    'success' => true,
                    'message' => 'Webhook received but not enabled',
                    'idea_count' => count($idea_ids)
                );
                $this->log_webhook($api_set, $body, 200, $response);
                return rest_ensure_response($response);
            }
            
            // Process each idea ID
            $results = array(
                'success' => array(),
                'failed' => array()
            );
            
            foreach ($idea_ids as $idea_id) {
                error_log('RBS Travel Webhook: Processing idea ID: ' . $idea_id);
                
                $result = $this->webhook_import_travel($api_set, $idea_id, $webhook_config);
                
                if (is_wp_error($result)) {
                    $results['failed'][] = array(
                        'idea_id' => $idea_id,
                        'error' => $result->get_error_message()
                    );
                } else {
                    $results['success'][] = array(
                        'idea_id' => $idea_id,
                        'post_id' => $result
                    );
                }
            }
            
            $response = array(
                'success' => true,
                'message' => sprintf('Processed %d ideas: %d succeeded, %d failed', 
                    count($idea_ids), 
                    count($results['success']), 
                    count($results['failed'])
                ),
                'results' => $results
            );
            
            $this->log_webhook($api_set, $body, 200, $response);
            
            return rest_ensure_response($response);
        }
        
        /**
         * Import/update travel via webhook
         * In multisite, imports to ALL subsites that have access to this API set
         */
        private function webhook_import_travel($api_set, $travel_id, $webhook_config) {
            error_log('RBS Travel Webhook: Importing travel: ' . $travel_id);
            
            // In multisite, find which sites have access to this API set
            $target_sites = array();
            if (is_multisite()) {
                $site_permissions = get_site_option('rbstravel_network_site_permissions', array());
                foreach ($site_permissions as $site_id => $allowed_apis) {
                    if (in_array($api_set, $allowed_apis)) {
                        $target_sites[] = $site_id;
                    }
                }
                
                if (empty($target_sites)) {
                    error_log('RBS Travel Webhook: No sites have access to API set: ' . $api_set);
                    return new \WP_Error('no_sites', 'No sites have access to this API set', array('status' => 400));
                }
                
                error_log('RBS Travel Webhook: Importing to sites: ' . implode(', ', $target_sites));
            } else {
                // Single site mode
                $target_sites = array(get_current_blog_id());
            }
            
            $results = array();
            
            foreach ($target_sites as $site_id) {
                // Switch to target site in multisite
                if (is_multisite()) {
                    switch_to_blog($site_id);
                }
                
                // Check if travel already exists on this site
                $existing_post = $this->find_travel_by_id($travel_id);
                
                // Temporarily set active API set to the webhook's API set
                $original_api_set = RBS_TRAVEL_Settings::GetSetting('active_api_set');
                RBS_TRAVEL_Settings::UpdateSetting('active_api_set', $api_set);
                
                try {
                    $post_id = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Import::import_travel($travel_id);
                    
                    if (is_wp_error($post_id)) {
                        error_log('RBS Travel Webhook: Import failed on site ' . $site_id . ': ' . $post_id->get_error_message());
                        RBS_TRAVEL_Settings::UpdateSetting('active_api_set', $original_api_set);
                        $results[$site_id] = array('error' => $post_id->get_error_message());
                        continue;
                    }
                    
                    // Set post status based on webhook config
                    $post_status = isset($webhook_config['post_status']) ? $webhook_config['post_status'] : 'draft';
                    wp_update_post(array(
                        'ID' => $post_id,
                        'post_status' => $post_status
                    ));
                    
                    // Add additional country/location taxonomy if configured
                    $default_country = isset($webhook_config['default_country']) ? $webhook_config['default_country'] : '';
                    if (!empty($default_country)) {
                        wp_set_object_terms($post_id, $default_country, 'location', true);
                    }
                    
                    // Mark as auto-imported
                    update_post_meta($post_id, '_auto_imported', '1');
                    update_post_meta($post_id, '_auto_import_api_set', $api_set);
                    update_post_meta($post_id, '_auto_import_date', current_time('mysql'));
                    
                    error_log('RBS Travel Webhook: Successfully imported travel ID ' . $travel_id . ' as post ID ' . $post_id . ' on site ' . $site_id);
                    
                    RBS_TRAVEL_Settings::UpdateSetting('active_api_set', $original_api_set);
                    
                    $results[$site_id] = array(
                        'action' => $existing_post ? 'updated' : 'created',
                        'post_id' => $post_id,
                        'url' => get_permalink($post_id),
                        'status' => $post_status
                    );
                    
                } catch (\Exception $e) {
                    error_log('RBS Travel Webhook: Exception during import on site ' . $site_id . ': ' . $e->getMessage());
                    RBS_TRAVEL_Settings::UpdateSetting('active_api_set', $original_api_set);
                    $results[$site_id] = array('error' => $e->getMessage());
                }
                
                // Restore to main site in multisite
                if (is_multisite()) {
                    restore_current_blog();
                }
            }
            
            return $results;
        }
        
        /**
         * Delete travel via webhook
         */
        private function webhook_delete_travel($travel_id) {
            $existing_post = $this->find_travel_by_id($travel_id);
            
            if (!$existing_post) {
                return array('action' => 'skipped', 'message' => 'Travel not found in WordPress');
            }
            
            // Check if auto-delete is enabled (could be a setting)
            // For now, just set to draft instead of deleting
            wp_update_post(array(
                'ID' => $existing_post->ID,
                'post_status' => 'draft'
            ));
            
            update_post_meta($existing_post->ID, '_auto_deleted', '1');
            update_post_meta($existing_post->ID, '_auto_delete_date', current_time('mysql'));
            
            return array(
                'action' => 'set_to_draft',
                'post_id' => $existing_post->ID,
                'message' => 'Travel set to draft (not deleted)'
            );
        }
        
        /**
         * Find existing travel post by travel_id
         */
        private function find_travel_by_id($travel_id) {
            $args = array(
                'post_type' => 'rbs-travel-idea',
                'posts_per_page' => 1,
                'post_status' => 'any',
                'meta_query' => array(
                    array(
                        'key' => 'travel_idea_id',
                        'value' => $travel_id,
                        'compare' => '='
                    )
                )
            );
            
            $query = new \WP_Query($args);
            return $query->have_posts() ? $query->posts[0] : null;
        }
        
        /**
         * Log webhook for debugging
         */
        private function log_webhook($api_set, $body, $status_code = 200, $response = null) {
            global $wpdb;
            
            // In multisite, always use base prefix (site 1) for webhook logs
            if (is_multisite()) {
                $table_name = $wpdb->base_prefix . 'rbstravel_webhook_log';
            } else {
                $table_name = $wpdb->prefix . 'rbstravel_webhook_log';
            }
            
            error_log('RBS Travel Webhook Log: Starting log_webhook for api_set: ' . $api_set);
            error_log('RBS Travel Webhook Log: Table name: ' . $table_name);
            error_log('RBS Travel Webhook Log: Current blog ID: ' . get_current_blog_id());
            
            // Create table if it doesn't exist
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
            $result = dbDelta($sql);
            error_log('RBS Travel Webhook Log: dbDelta result: ' . print_r($result, true));
            
            // Insert log entry
            $response_json = $response ? json_encode($response) : null;
            
            $insert_result = $wpdb->insert(
                $table_name,
                array(
                    'api_set' => $api_set,
                    'payload' => json_encode($body),
                    'status_code' => $status_code,
                    'response' => $response_json,
                    'created_at' => current_time('mysql')
                ),
                array('%s', '%s', '%d', $response_json ? '%s' : null, '%s')
            );
            
            if ($insert_result === false) {
                error_log('RBS Travel Webhook Log: Insert FAILED! Error: ' . $wpdb->last_error);
                error_log('RBS Travel Webhook Log: Last query: ' . $wpdb->last_query);
            } else {
                error_log('RBS Travel Webhook Log: Insert SUCCESS! Insert ID: ' . $wpdb->insert_id);
            }
            
            // Clean up old logs (keep last 100)
            $wpdb->query("DELETE FROM $table_name WHERE id NOT IN (SELECT id FROM (SELECT id FROM $table_name ORDER BY id DESC LIMIT 100) AS t)");
        }
    }
    
    // Initialize
    new RBS_TRAVEL_REST_API();
}
