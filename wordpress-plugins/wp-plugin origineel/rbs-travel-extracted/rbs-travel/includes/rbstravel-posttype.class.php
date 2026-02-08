<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();


/**
 * @todo
 * - [ ] add taxonomies??
 *	- book_type,
 *	- language
 *	- dyslexia?
 *	- ....
 * - [ ] 
 */

/**
 * @fields:
 * - ...
 */


if (!class_exists('RBS_TRAVEL\\INCLUDES\\RBS_TRAVEL_Posttype')) {
        
    class RBS_TRAVEL_Posttype {
        
    
  

        public function __construct() {
            $this->init();
        }
                
        
        private function init() {

            add_action('init', array($this, 'register_post_type'));            
            add_filter('manage_rbs-travel-idea_posts_columns', array($this, 'posts_columns'));
            add_action('manage_rbs-travel-idea_posts_custom_column', array($this, 'posts_custom_column'), 10, 2);           
            add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
            // add_action( 'init', array($this,'add_custom_taxonomies'),0);            
           
	        add_filter('single_template', array($this, 'single_template'));



//  TODO: re-import idea (overwritee)
            // add_action('post_submitbox_misc_actions', function () {
            //     global $post;
            //     if ($post->post_type === 'rbs-travel-idea') {
            //         <div style="margin-bottom: 10px; padding: 6px 10px 8px;">
            //             <a href="#" class="button " id="custom-button">
            //                  __('Re-import idea', 'rbs-travel');
            //             </a>
            //         </div>
            //      
            //     }
            // });         
	    
	    
	    
	    
            
//            add_action('manage_posts_extra_tablenav', array($this, 'manage_posts_extra_tablenav'));
//            
//            
//            
//            
//            add_action('edit_form_after_title', array($this, 'field_case_id'));
//            add_action('edit_form_after_editor', array($this, 'field_case_sources'));
//            
//            
//            
//            add_action('restrict_manage_posts', array($this, 'restrict_manage_posts'), 10, 2);
//            /** Issue with 'parse_query' >> also used in other query, should only be used on the 'results list' **/
//            /** but on that page is also another query used to get all user codes... **/
//            /** maybe filter: "pre_get_posts" ?? **/
//            //add_filter('parse_query', array($this, 'parse_query'));
//                
//            
//            
//            //add_filter('wp_insert_post_data' , array($this, 'wp_insert_post_data'), 99); 
           add_action('save_post', array($this, 'save_post'));  
//            //add_action('post_edit_form_tag', array($this, 'update_edit_form'));            
//            
//            
//            
           add_action('init', array($this, 'register_taxonomy'));
           
           // AJAX handlers for Photo Manager
           add_action('wp_ajax_rbs_save_photo', array($this, 'ajax_save_photo'));
           add_action('wp_ajax_rbs_remove_photo', array($this, 'ajax_remove_photo'));
           add_action('wp_ajax_rbs_reorder_photos', array($this, 'ajax_reorder_photos'));
           
           // Fix metabox scroll issue - REMOVED: method was missing
           // add_action('admin_footer', array($this, 'fix_metabox_scroll'));
//            
//            
//            

            
        }
        
        
        
        
        
        
        public function register_post_type() {            
            /*** (Local) Ideas: ***/
            $labels = array(
                'name'                  => __( 'Travel Ideas', 'rbs-travel' ),
                'singular_name'         => __( 'Travel Idea', 'rbs-travel' ),
                'menu_name'             => __( 'rbsTravel', 'rbs-travel' ),
                'name_admin_bar'        => __( 'rbsTravel', 'rbs-travel' ),
                'archives'              => __( 'Item Archives', 'rbs-travel' ),
                'attributes'            => __( 'Item Attributes', 'rbs-travel' ),
                'parent_item_colon'     => __( 'Parent Item:', 'rbs-travel' ),
                'all_items'             => __( 'Travel Ideas', 'rbs-travel' ),
                'add_new_item'          => __( 'Add New Idea (remove)', 'rbs-travel' ),
                'add_new'               => __( 'Add New', 'rbs-travel' ),
                'new_item'              => __( 'New Travel Idea (remove)', 'rbs-travel' ),
                'edit_item'             => __( 'Edit Travel Idea (disable)', 'rbs-travel' ),
                'update_item'           => __( 'Update Travel Idea (disable)', 'rbs-travel' ),
                'view_item'             => __( 'View Travel Idea', 'rbs-travel' ),
                'view_items'            => __( 'View Travel Ideas', 'rbs-travel' ),
                'search_items'          => __( 'Search Travel Ideas', 'rbs-travel' ),
                'not_found'             => __( 'Not found', 'rbs-travel' ),
                'not_found_in_trash'    => __( 'Not found in Trash', 'rbs-travel' ),
                'featured_image'        => __( 'Featured Image', 'rbs-travel' ),
                'set_featured_image'    => __( 'Set featured image', 'rbs-travel' ),
                'remove_featured_image' => __( 'Remove featured image', 'rbs-travel' ),
                'use_featured_image'    => __( 'Use as featured image', 'rbs-travel' ),
                'insert_into_item'      => __( 'Insert into Travel Idea', 'rbs-travel' ),
                'uploaded_to_this_item' => __( 'Uploaded to this item', 'rbs-travel' ),
                'items_list'            => __( 'Travel Idea list', 'rbs-travel' ),
                'items_list_navigation' => __( 'Travel Idea list navigation', 'rbs-travel' ),
                'filter_items_list'     => __( 'Filter Travel Idea list', 'rbs-travel' ),
            );
            
            $args = array(
                'label'                 => __('Travel Ideas', 'rbs-travel' ),
                'description'           => __('rbsTravel local travel ideas', 'rbs-travel' ),
                'labels'                => $labels,
                'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),       //, 'custom-fields'
                'hierarchical'          => false,   
                'taxonomies' => array( 'location'),
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => 'rbstravel',       //?page=rbsq-questions   //'edit.php?post_type=rbs_questions'
                'menu_position'         => 5,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'page',
                'show_in_rest'          => false,
                'rewrite'               => array('slug' => 'reizen')
            );
            register_post_type('rbs-travel-idea', $args);             

        }               
        
        
        
        public function posts_columns($columns) {
            $return = array();
            $return['cb'] = $columns['cb'];
            $return['title'] = $columns['title'];
            $return['idea_id'] = __('Idea ID', 'rbs-travel');
            $return['destinations'] = __('Destinations', 'rbs-travel');                        
            $return['nights'] = __('Nights', 'rbs-travel');        
            $return['price_per_person'] = __('Price per Person', 'rbs-travel');
            $return['total_price'] = __('Total Price', 'rbs-travel');
            $return['date'] = $columns['date'];
            return $return;           
        }
        
        public function posts_custom_column($column, $post_id) {
            $idea = rbstravel_get_single_idea($post_id);

            if ($column === 'idea_id') {
                echo $idea['idea']['travel_id'];
            }            

            if ($column === 'destinations') {
                $destinations = $idea['idea']['travel_destinations'];
                // echo get_post_meta($post_id, 'book_type', true);
                $html = array();
                foreach($destinations as $destination) {
                    $html[] = '<p style="margin: 0;">' . $destination['name'] . '</p>';
                }
                echo implode("\r\n", $html);
            }
            
            if ($column === 'nights') {
                echo $idea['idea']['travel_number_of_nights'];
            }
	    
            if ($column === 'price_per_person') {
                echo $idea['idea']['travel_price_per_person'];
            }

            if ($column === 'total_price') {
                echo $idea['idea']['travel_price_total'];
            }	    
            
        }
              
        
        
        
        public function restrict_manage_posts($post_type, $which) {
            
            
            
            // $html = array();
            
            
            // if ($post_type === 'rbscase') {
                
            
            // } elseif ($post_type === 'rbscase-result') {
                
            //     $result_filter = filter_input(INPUT_GET, 'rcscases_result_filter', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
            //     //echo '<pre>' . print_r($result_filter, true) . '</pre>';//die();
                
                
            //     // Case filter:
            //     $selected_case_id = isset($result_filter['case']) ? $result_filter['case'] : -1;
            //     $html[] = '<select name="rcscases_result_filter[case]">';
            //     $html[] = '<option value="-1">' . __('All cases', 'rbs-travel') . '</option>';
            //     foreach(RBSCASES_Cases::GetCases() as $idx => $case) {
            //         $selected = $selected_case_id == $case['post']->ID ? 'selected' : '';
            //         $html[] = '<option value="' . $case['post']->ID . '" ' . $selected . '>';
            //         $html[] = $case['case_title'];
            //         if ($case['case_id'] != null && strlen($case['case_id']) != 0) {
            //             $html[] = ' [' . $case['case_id'] . ']';
            //         }
            //         $html[] = '</option>';
            //     }            
            //     $html[] = '</select>';         
                
                
                
            //     // User code filter:
            //     $selected_user_code = isset($result_filter['user_code']) ? $result_filter['user_code'] : -1;
            //     $html[] = '<select name="rcscases_result_filter[user_code]">';
            //     $html[] = '<option value="-1">' . __('All user codes', 'rbs-travel') . '</option>';
            //     foreach(RBSCASES_Results::GetUserCodes() as $idx => $value) {
            //         $selected = $selected_user_code == $value ? 'selected' : '';
            //         $html[] = '<option value="' . $value . '" ' . $selected . '>';
            //         $html[] = $value;
            //         $html[] = '</option>';
            //     }            
            //     $html[] = '</select>';                  
                
            // }
                
                
            
            
            
            // echo implode("\r\n", $html);
            
        }
        
        
        public function parse_query(\WP_Query $query) {
            
            $result_filter = filter_input(INPUT_GET, 'rcscases_result_filter', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
           
            
            
            
            if (is_admin() && $query->query_vars['post_type'] === 'rbscase-result') {
                echo '<pre>' . print_r($query, true) . '</pre>';//wp_die();
                if (isset($result_filter['case']) && strlen($result_filter['case']) !== 0) {

                    $meta_query = array(
                        'key' => 'rbscase_case_id',
                        'value' => $result_filter['case'],
                        'compare' => '='
                    );

                    //

                    $query->set('meta_query', array($meta_query));

                    //wp_die('parse_query');
                }            


                if (isset($result_filter['user_code']) && strlen($result_filter['user_code']) !== 0) {
                    //rbscase_user_code

                }
            
            }
            
            return $query;
        }
        
        
        
        
        
        
        public function register_taxonomy() {

            // !! Is "destinations" not better?
            // Taxonomy: Locations          
            $labels = array(
                'name' => __( 'Locations', 'rbs-travel' ),
                'singular_name' => __( 'Location', 'rbs-travel' ),
                'search_items' =>  __( 'Search Locations', 'rbs-travel' ),
                'all_items' => __( 'All Locations', 'rbs-travel' ),
                'parent_item' => __( 'Parent Location', 'rbs-travel' ),
                'parent_item_colon' => __( 'Parent Location:', 'rbs-travel' ),
                'edit_item' => __( 'Edit Location', 'rbs-travel' ),
                'update_item' => __( 'Update Location', 'rbs-travel' ),
                'add_new_item' => __( 'Add New Location', 'rbs-travel' ),
                'new_item_name' => __( 'New Location Name', 'rbs-travel' ),
                'menu_name' => __( 'Locations','rbs-travel' ),  

                // 'view_item'                  => __( 'View Item', 'rbs-publish' ),
                // 'separate_items_with_commas' => __( 'Separate items with commas', 'rbs-publish' ),
                // 'add_or_remove_items'        => __( 'Add or remove items', 'rbs-publish' ),
                // 'choose_from_most_used'      => __( 'Choose from the most used', 'rbs-publish' ),
                // 'popular_items'              => __( 'Popular Items', 'rbs-publish' ),
                // 'not_found'                  => __( 'Not Found', 'rbs-publish' ),
                // 'no_terms'                   => __( 'No items', 'rbs-publish' ),
                // 'items_list'                 => __( 'Items list', 'rbs-publish' ),
                // 'items_list_navigation'      => __( 'Items list navigation', 'rbs-publish' ),
            );
            $args = array(
                'labels'  => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'rewrite' => array(
                    'slug' => 'locations',
                    'with_front' => false,
                    'hierarchical' => true
                )
            );
            register_taxonomy('location', array('rbs-travel-idea'), $args);


            // Taxonomy: Tour (Holiday) Type
            $labels = array(
                'name' => __( 'Tour Types', 'rbs-travel' ),
                'singular_name' => __( 'Tour Type', 'rbs-travel' ),
                'search_items' =>  __( 'Search Tour Types', 'rbs-travel' ),
                'all_items' => __( 'All Tour Types', 'rbs-travel' ),
                // 'parent_item' => __( 'Parent Tour Types', 'rbs-travel' ),
                // 'parent_item_colon' => __( 'Parent Location:', 'rbs-travel' ),
                'edit_item' => __( 'Edit Tour Type', 'rbs-travel' ),
                'update_item' => __( 'Update Tour Type', 'rbs-travel' ),
                'add_new_item' => __( 'Add New Tour Type', 'rbs-travel' ),
                'new_item_name' => __( 'New Tour Type', 'rbs-travel' ),
                'menu_name' => __( 'Tour Types','rbs-travel' )
            );
            $args = array(
                'labels'  => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'rewrite' => array(
                    'slug' => 'tour-type',
                    'with_front' => false,
                    'hierarchical' => true
                )
            );
            register_taxonomy('tour-type', array('rbs-travel-idea'), $args);


            // Taxonomy: Themes
            $labels = array(
                'name' => __( 'Tour Themes', 'rbs-travel' ),
                'singular_name' => __( 'Tour Theme', 'rbs-travel' ),
                'search_items' =>  __( 'Search Tour Themes', 'rbs-travel' ),
                'all_items' => __( 'All Tour Themes', 'rbs-travel' ),
                // 'parent_item' => __( 'Parent Tour Types', 'rbs-travel' ),
                // 'parent_item_colon' => __( 'Parent Location:', 'rbs-travel' ),
                'edit_item' => __( 'Edit Tour Theme', 'rbs-travel' ),
                'update_item' => __( 'Update Tour Theme', 'rbs-travel' ),
                'add_new_item' => __( 'Add New Tour Theme', 'rbs-travel' ),
                'new_item_name' => __( 'New Tour Theme', 'rbs-travel' ),
                'menu_name' => __( 'Tour Themes','rbs-travel' )
            );
            $args = array(
                'labels'  => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'rewrite' => array(
                    'slug' => 'tour-themes',
                    'with_front' => false,
                    'hierarchical' => true
                )
            );
            register_taxonomy('tour-theme', array('rbs-travel-idea'), $args);


            // Taxonomy: Services (accommodations)
            $labels = array(
                'name' => __( 'Tour Services', 'rbs-travel' ),
                'singular_name' => __( 'Tour Service', 'rbs-travel' ),
                'search_items' =>  __( 'Search Tour Services', 'rbs-travel' ),
                'all_items' => __( 'All Tour Services', 'rbs-travel' ),
                // 'parent_item' => __( 'Parent Tour Types', 'rbs-travel' ),
                // 'parent_item_colon' => __( 'Parent Location:', 'rbs-travel' ),
                'edit_item' => __( 'Edit Tour Service', 'rbs-travel' ),
                'update_item' => __( 'Update Tour Service', 'rbs-travel' ),
                'add_new_item' => __( 'Add New Tour Service', 'rbs-travel' ),
                'new_item_name' => __( 'New Tour Service', 'rbs-travel' ),
                'menu_name' => __( 'Tour Services','rbs-travel' )
            );
            $args = array(
                'labels'  => $labels,
                'hierarchical' => true,
                'public' => true,
                'show_ui' => true,
                'show_admin_column' => false,
                'show_in_menu' => true,
                'show_in_nav_menus' => false,
                'show_tagcloud' => false,
                'rewrite' => array(
                    'slug' => 'tour-service',
                    'with_front' => false,
                    'hierarchical' => true
                )
            );
            register_taxonomy('tour-service', array('rbs-travel-idea'), $args);            
        }        
        
        
        
        
        
        
        
        
        
        
        public function add_meta_boxes($post_type) {
            if ($post_type === 'rbs-travel-idea') {
                add_meta_box('rbstravel_hero_settings', __('🖼️ Hero Instellingen', 'rbs-travel'), array($this, 'render_metabox_hero_settings'), null, 'advanced', 'high');
                add_meta_box('rbstravel_photo_manager', __('📸 Photo Management', 'rbs-travel'), array($this, 'render_metabox_photo_manager'), null, 'advanced', 'high');
                add_meta_box('rbstravel_travel_idea_destinations', __('Travel Idea Destinations', 'rbs-travel'), array($this, 'render_metabox_travel_idea_destinations'), null, 'advanced', 'high');
                add_meta_box('rbstravel_travel_idea_meta', __('Travel Idea Fields', 'rbs-travel'), array($this, 'render_metabox_travel_idea_meta'), null, 'advanced', 'high');
                // Removed: Travel Idea Maps metabox - now using dynamic Leaflet map instead of static images


                add_meta_box('rbstravel_travel_idea_details', __('Travel Idea Details', 'rbs-travel'), array($this, 'render_metabox_travel_idea_details'), null, 'side', 'default');
                add_meta_box('rbstravel_travel_idea_prices', __('Travel Idea Prices', 'rbs-travel'), array($this, 'render_metabox_travel_idea_prices'), null, 'side', 'default');
                
                // Catalog toggle - only for super admins in multisite, or admins in single site
                if ((is_multisite() && is_super_admin()) || (!is_multisite() && current_user_can('manage_options'))) {
                    add_meta_box('rbstravel_catalog_settings', __('📚 TravelC Web Catalogus', 'rbs-travel'), array($this, 'render_metabox_catalog_settings'), null, 'side', 'high');
                }
              
            }        
        }
        
        
        
        
        public function manage_posts_extra_tablenav($which) {
            global $current_screen;
            //var_dump($current_screen);
            
            if (isset($current_screen->id) && $current_screen->id === 'edit-rbscase') {
                if ($which === 'top') {
                    echo '<div id="rbscases-case-list-actions" style="clear: both; float: left; margin: 10px 0px;">';
                    //echo '<button type="submit" name="test">HERE</button>';
                    echo '<a href="' . admin_url() . 'admin.php?page=_rbscase-import-cases" class="button">';
                    echo __('Import Cases', 'rbs-travel');
                    echo '</a>';
                    echo '</div>';
                }                
            }
            
            if (isset($current_screen->id) && $current_screen->id === 'edit-rbscase-result') {
                if ($which === 'top') {
                    echo '<div id="rbscases-result-list-actions" style="clear: both; float: left; margin: 10px 0px;">';
                    
                    //echo '<a href="' . admin_url() . 'admin.php?page=_rbscase-export-results" class="button">';
                    echo '<button name="rbscases-export-results" value="1" class="button">';
                    echo __('Export Results', 'rbs-travel');
                    echo '</button>';
                    //echo '</a>';
                    echo '</div>';
                }                
            }            
            

        }
        
        /**
         * AJAX Handler: Save Photo
         * Adds a new photo to a cruise, hotel, or destination
         */
        public function ajax_save_photo() {
            // Security check
            check_ajax_referer('rbs_photo_nonce', 'nonce');
            
            // Get parameters
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            $index = isset($_POST['index']) ? intval($_POST['index']) : 0;
            $image_url = isset($_POST['image_url']) ? esc_url_raw($_POST['image_url']) : '';
            
            // Validate
            if (!$post_id || !$type || !$image_url) {
                wp_send_json_error(array('message' => 'Invalid parameters'));
                return;
            }
            
            // Map type to meta key
            $meta_key = 'travel_' . $type . 's'; // cruise -> travel_cruises, hotel -> travel_hotels, etc.
            
            // Get current data
            $items = get_post_meta($post_id, $meta_key, true);
            
            if (!is_array($items)) {
                wp_send_json_error(array('message' => 'No items found'));
                return;
            }
            
            if (!isset($items[$index])) {
                wp_send_json_error(array('message' => 'Item not found'));
                return;
            }
            
            // Initialize images array if not exists
            if (!isset($items[$index]['images']) || !is_array($items[$index]['images'])) {
                $items[$index]['images'] = array();
            }
            
            // Add photo
            $items[$index]['images'][] = $image_url;
            
            // Save back to database
            update_post_meta($post_id, $meta_key, $items);
            
            wp_send_json_success(array(
                'message' => 'Photo added successfully',
                'image_url' => $image_url
            ));
        }
        
        /**
         * AJAX Handler: Remove Photo
         * Removes a photo from a cruise, hotel, or destination
         */
        public function ajax_remove_photo() {
            // Security check
            check_ajax_referer('rbs_photo_nonce', 'nonce');
            
            // Get parameters
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            $index = isset($_POST['index']) ? intval($_POST['index']) : 0;
            $img_index = isset($_POST['img_index']) ? intval($_POST['img_index']) : 0;
            
            // Validate
            if (!$post_id || !$type) {
                wp_send_json_error(array('message' => 'Invalid parameters'));
                return;
            }
            
            // Map type to meta key
            $meta_key = 'travel_' . $type . 's';
            
            // Get current data
            $items = get_post_meta($post_id, $meta_key, true);
            
            if (!is_array($items) || !isset($items[$index])) {
                wp_send_json_error(array('message' => 'Item not found'));
                return;
            }
            
            // Remove photo
            if (isset($items[$index]['images'][$img_index])) {
                array_splice($items[$index]['images'], $img_index, 1);
                
                // Re-index array
                $items[$index]['images'] = array_values($items[$index]['images']);
                
                // Save back to database
                update_post_meta($post_id, $meta_key, $items);
                
                wp_send_json_success(array('message' => 'Photo removed successfully'));
            } else {
                wp_send_json_error(array('message' => 'Photo not found'));
            }
        }
        
        /**
         * AJAX Handler: Reorder Photos
         * Changes the order of photos for a cruise, hotel, or destination
         */
        public function ajax_reorder_photos() {
            // Disabled for now
            wp_send_json_error(array('message' => 'Reordering disabled'));
        }
        
        
        public function render_metabox_photo_manager($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'photo-manager-simple.html.php';
            if (!file_exists($template)) {
                echo '<div style="padding: 20px; color: red;">Template not found: ' . esc_html($template) . '</div>';
                return;
            }
            require $template;
        }

        public function render_metabox_travel_idea_meta($post) {                 
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'travel-idea-meta.html.php';
            require $template;
        }        
                

        public function render_metabox_travel_idea_destinations($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'travel-idea-destinations.html.php';
            require $template;            
        }
                   
        public function render_metabox_travel_idea_map($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'travel-idea-maps.html.php';
            require $template;            
        }

        public function render_metabox_hero_settings($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'hero-settings.html.php';
            require $template;            
        }

        public function render_metabox_travel_idea_details($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'travel-idea-details.html.php';
            require $template;       
        }

        public function render_metabox_travel_idea_prices($post) {
            $template = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'backend' . DIRECTORY_SEPARATOR . 'meta_boxes' . DIRECTORY_SEPARATOR . 'travel-idea-prices.html.php';
            require $template;       
        }
        
        /**
         * Render Catalog Settings Metabox
         * Only visible to super admins - allows marking travel for TravelC Web Catalogus
         */
        public function render_metabox_catalog_settings($post) {
            $in_catalog = get_post_meta($post->ID, '_in_bolt_catalog', true);
            $is_checked = $in_catalog === '1' || $in_catalog === 'yes';
            ?>
            <div style="padding: 10px 0;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="in_bolt_catalog" value="1" <?php checked($is_checked); ?> style="width: 20px; height: 20px;" />
                    <span style="font-size: 14px; font-weight: 600;">Toon in TravelC Web Catalogus</span>
                </label>
                <p class="description" style="margin-top: 10px;">
                    <?php if ($is_checked): ?>
                        <span style="color: #059669;">✓ Deze reis is zichtbaar in de TravelC Web Catalogus en kan door brands worden geselecteerd.</span>
                    <?php else: ?>
                        <span style="color: #6b7280;">Vink aan om deze reis beschikbaar te maken in de TravelC Web Catalogus.</span>
                    <?php endif; ?>
                </p>
                <p class="description" style="margin-top: 5px; font-size: 11px; color: #9ca3af;">
                    Alleen super admins kunnen reizen aan de catalogus toevoegen.
                </p>
            </div>
            <?php
        }
        
        
        public function save_post($post_id) {
            if (isset($_POST['num_nights'])) {
                update_post_meta($post_id, 'travel_number_of_nights', sanitize_text_field($_POST['num_nights']));
            }

            if (isset($_POST['min_persons'])) {
                update_post_meta($post_id, 'travel_min_persons', sanitize_text_field($_POST['min_persons']));
            }

            if (isset($_POST['max_persons'])) {
                update_post_meta($post_id, 'travel_max_persons', sanitize_text_field($_POST['max_persons']));
            }

            if (isset($_POST['total_price'])) {
                update_post_meta($post_id, 'travel_price_total', sanitize_text_field($_POST['total_price']));
            }

            if (isset($_POST['price_per_person'])) {
                update_post_meta($post_id, 'travel_price_per_person', sanitize_text_field($_POST['price_per_person']));
            }
            
            // Save hero settings
            if (isset($_POST['travel_hero_style'])) {
                update_post_meta($post_id, 'travel_hero_style', sanitize_text_field($_POST['travel_hero_style']));
            }
            
            // Save hero image - prefer custom URL if set, otherwise use selected image
            if (isset($_POST['travel_hero_image_custom']) && !empty($_POST['travel_hero_image_custom'])) {
                update_post_meta($post_id, 'travel_hero_image', esc_url_raw($_POST['travel_hero_image_custom']));
            } elseif (isset($_POST['travel_hero_image'])) {
                update_post_meta($post_id, 'travel_hero_image', esc_url_raw($_POST['travel_hero_image']));
            }
            
            // Save YouTube URL for hero
            if (isset($_POST['travel_hero_youtube'])) {
                update_post_meta($post_id, 'travel_hero_youtube', sanitize_text_field($_POST['travel_hero_youtube']));
            }
            
            // Save YouTube delay for hero
            if (isset($_POST['travel_hero_youtube_delay'])) {
                update_post_meta($post_id, 'travel_hero_youtube_delay', intval($_POST['travel_hero_youtube_delay']));
            }
            
            // Save BOLT catalog setting - only super admins can change this
            if ((is_multisite() && is_super_admin()) || (!is_multisite() && current_user_can('manage_options'))) {
                $in_catalog = isset($_POST['in_bolt_catalog']) ? '1' : '0';
                update_post_meta($post_id, '_in_bolt_catalog', $in_catalog);
            }

            $this->save_base64_image($post_id);
        }
        
        private function save_base64_image($post_id) {
            $base64_string = isset($_POST['base64_map_image']) ? $_POST['base64_map_image'] : null;
            
            if (!is_string($base64_string)) {
                return false;
            } 

            if ($base64_string === null) {
                return false;
            }

            if (strlen($base64_string) === 0) {
                return false;
            } 
           
            // Remove the prefix (data:image/png;base64,) if present
            if (strpos($base64_string, 'data:image/png;base64,') === 0) {
                $base64_string = substr($base64_string, strlen('data:image/png;base64,'));
            }
        
            // Decode the base64 string
            $image_data = base64_decode($base64_string);

            $image_file = 'rbstravel-map-image--' . $post_id . '.png';
            $file_path = RBS_TRAVEL_UPLOADS_PATH_MAPS . $image_file;
        
            // Save the image to a file
            if (file_put_contents($file_path, $image_data)) {
                update_post_meta($post_id, 'rbstravel_map_image', RBS_TRAVEL_UPLOAD_URL . 'maps/' .$image_file . '?timestamp=' . time());
            } else {
                wp_die('Error saving the map image');
            }            
        }
        
        public function single_template($template) {
            global $post;
	    
            global $travel_meta_fields;
            $travel_meta_fields = RBS_TRAVEL_Meta::GetMetaFields();

            
            if ($post->post_type === 'rbs-travel-idea') {               
                $template_name = 'single-rbs-travel-idea.php';

                if (file_exists(get_stylesheet_directory() . DIRECTORY_SEPARATOR . $template_name)) {
                    return get_stylesheet_directory() . DIRECTORY_SEPARATOR . $template_name;
                }

                return RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'frontend' . DIRECTORY_SEPARATOR . $template_name;

            }
            
            
            return $template;
        }


        /**
         * Add custom taxonomies
         *
         * Additional custom taxonomies can be defined here
         * https://codex.wordpress.org/Function_Reference/register_taxonomy
         */
        // function add_custom_taxonomies() {
        //     // Add new "Locations" taxonomy to Posts
        //     //rbs-travel-idea
        //     //'rbs-travel'
        //     register_taxonomy('location', 'rbs-travel-idea', array(
        //     // Hierarchical taxonomy (like categories)
        //     'hierarchical' => true,
        //     // This array of options controls the labels displayed in the WordPress Admin UI
        //     'labels' => array(
        //         'name' => __( 'Locations', 'taxonomy general name' ),
        //         'singular_name' => __( 'Location', 'taxonomy singular name' ),
        //         'search_items' =>  __( 'Search Locations' ),
        //         'all_items' => __( 'All Locations' ),
        //         'parent_item' => __( 'Parent Location' ),
        //         'parent_item_colon' => __( 'Parent Location:' ),
        //         'edit_item' => __( 'Edit Location' ),
        //         'update_item' => __( 'Update Location' ),
        //         'add_new_item' => __( 'Add New Location' ),
        //         'new_item_name' => __( 'New Location Name' ),
        //         'menu_name' => __( 'Locations' ),
        //     ),
        //     // Control the slugs used for this taxonomy
        //     'rewrite' => array(
        //         'slug' => 'locations', // This controls the base slug that will display before each term
        //         'with_front' => false, // Don't display the category base before "/locations/"
        //         'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
        //     ),
        //     ));
        // }
        
        
        
        
    }
    
}

$RBS_TRAVEL_Posttype = new RBS_TRAVEL_Posttype();