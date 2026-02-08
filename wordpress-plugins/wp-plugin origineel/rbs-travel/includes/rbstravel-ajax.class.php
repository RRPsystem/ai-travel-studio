<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo:
 * - [ ] 
 */



if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Ajax')) {
    
    
    class RBS_TRAVEL_Ajax {
        


        public function __construct() {
	        $this->init();
        }

        
		private function init() {
			add_action('wp_ajax_rbstravel_view_info', array($this, 'ajax_view_info'));
			add_action('wp_ajax_rbstravel_view_details', array($this, 'ajax_view_details'));
			add_action('wp_ajax_rbstravel_import_single', array($this, 'ajax_import_single'));
			add_action('wp_ajax_rbstravel_import_with_progress', array($this, 'ajax_import_with_progress'));

			add_action('wp_ajax_rbstravel_get_travel_items', array($this, 'ajax_rbs_get_travel_items'));
			add_action('wp_ajax_nopriv_rbstravel_get_travel_items', array($this, 'ajax_rbs_get_travel_items')); 			
            
            
            add_action('wp_ajax_rbstravel_upload_map_image', array($this, 'ajax_rbs_upload_map_image'));
            
            // Photo management
            add_action('wp_ajax_rbs_save_photos', array($this, 'ajax_save_photos'));
            
            // Sync all travels
            add_action('wp_ajax_rbstravel_sync_all_travels', array($this, 'ajax_sync_all_travels'));
		}
		
		public function ajax_view_info() {
			$action = filter_input(INPUT_POST, 'action');
			$travel_id = filter_input(INPUT_POST, 'travel_id');
			
			$return = array();
			$return['success'] = false;
			$return['error'] = false;	
			$return['message'] = '';
			$return['result'] = null;
			
			
			if ($action !== 'rbstravel_view_info') {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid action: %s', 'rbs-travel'), $action);
				echo json_encode($return);
				exit;
			}
			
			if ($travel_id === null) {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid travel_id: %s', 'rbs-travel'), $travel_id);
				echo json_encode($return);
				exit;
			}
			
			
			$RBS_TRAVEL_Api = new RBS_TRAVEL_Api();
			$result = $RBS_TRAVEL_Api->GetInfo($travel_id);
			
			$return['success'] = true;
			$return['result'] = $result;
			echo json_encode($return);
			exit;
		}
		
		public function ajax_view_details() {
			$action = filter_input(INPUT_POST, 'action');
			$travel_id = filter_input(INPUT_POST, 'travel_id');
			
			$return = array();
			$return['success'] = false;
			$return['error'] = false;	
			$return['message'] = '';
			$return['result'] = null;
			
			
			if ($action !== 'rbstravel_view_details') {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid action: %s', 'rbs-travel'), $action);
				echo json_encode($return);
				exit;
			}
			
			if ($travel_id === null) {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid travel_id: %s', 'rbs-travel'), $travel_id);
				echo json_encode($return);
				exit;
			}
			
			
			$RBS_TRAVEL_Api = new RBS_TRAVEL_Api();
			$result = $RBS_TRAVEL_Api->GetDetails($travel_id);
			
			$return['success'] = true;
			$return['result'] = $result;
			echo json_encode($return);
			exit;
		}

		public function ajax_import_single() {
			$action = filter_input(INPUT_POST, 'action');
			$travel_id = filter_input(INPUT_POST, 'travel_id');
			
			$return = array();
			$return['success'] = false;
			$return['error'] = false;	
			$return['message'] = '';
			$return['result'] = null;
			
			
			if ($action !== 'rbstravel_import_single') {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid action: %s', 'rbs-travel'), $action);
				echo json_encode($return);
				exit;
			}
			
			if ($travel_id === null) {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Invalid travel_id: %s', 'rbs-travel'), $travel_id);
				echo json_encode($return);
				exit;
			}	    
			
			$return['travel_id'] = $travel_id;
			
			$RBS_TRAVEL_Api = new RBS_TRAVEL_Api();
			$travel_info = $RBS_TRAVEL_Api->GetInfo($travel_id);
			$travel_details = $RBS_TRAVEL_Api->GetDetails($travel_id);
			
			$result = RBS_TRAVEL_Import::CreatePost($travel_info, $travel_details);
			if (!is_numeric($result)) {
				$return['success'] = false;
				$return['error'] = true;
				$return['message'] = sprintf(__('AJAX: Could not create post for: %s / Error: %s', 'rbs-travel'), $travel_id, $result->get_error_data());
				$return['result'] = $result;
				echo json_encode($return);
				exit;
			}
			
			$return['success'] = true;
			$return['error'] = false;
			$return['message'] = '';
			$return['result'] = $result;
			echo json_encode($return);
			exit;	    
		}	

		/**
		 * AJAX Import with Progress
		 * Handles import via AJAX with proper response for progress UI
		 */
		public function ajax_import_with_progress() {
			// Verify nonce
			if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rbstravel_ajax_import')) {
				wp_send_json_error(array('message' => 'Beveiligingsfout. Vernieuw de pagina en probeer opnieuw.'));
				return;
			}
			
			$travel_id = isset($_POST['travel_id']) ? sanitize_text_field($_POST['travel_id']) : '';
			$api_set = isset($_POST['api_set']) ? sanitize_text_field($_POST['api_set']) : '';
			
			if (empty($travel_id)) {
				wp_send_json_error(array('message' => 'Geen reis ID opgegeven.'));
				return;
			}
			
			// Get API credentials - handle multisite vs single site
			if (is_multisite() && class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Network_Settings')) {
				// Multisite: Get credentials from network settings (with password for API calls)
				$api_credentials = RBS_TRAVEL_Network_Settings::get_site_api_credentials(true);
				$default_active_set = get_option('rbstravel_site_active_api', '');
				
				// If no active set selected, use first available
				if (empty($default_active_set) && !empty($api_credentials)) {
					$default_active_set = array_key_first($api_credentials);
				}
			} else {
				// Single site: use local credentials
				$api_credentials = RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
				$default_active_set = RBS_TRAVEL_Settings::GetSetting('active_api_set', '');
			}
			
			// Determine which API set to use
			$use_api_set = !empty($api_set) && isset($api_credentials[$api_set]) ? $api_set : $default_active_set;
			
			if (!isset($api_credentials[$use_api_set])) {
				wp_send_json_error(array('message' => 'API credentials niet gevonden. Controleer of deze site API toegang heeft in Network Admin.'));
				return;
			}
			
			$credentials = $api_credentials[$use_api_set];
			$api_name = isset($credentials['name']) ? $credentials['name'] : 'Unknown';
			
			// Temporarily override the active API set
			update_option('rbstravel_settings_temp_active_api_set', $use_api_set);
			
			try {
				// Create API instance
				$RBS_TRAVEL_Api = new RBS_TRAVEL_Api();
				
				// Get travel info and details from API
				$travel_info = $RBS_TRAVEL_Api->GetInfo($travel_id);
				$travel_details = $RBS_TRAVEL_Api->GetDetails($travel_id);
				
				// Check if we have valid data
				if (empty($travel_info) || !isset($travel_info['id'])) {
					delete_option('rbstravel_settings_temp_active_api_set');
					
					if (isset($travel_info['status']) && $travel_info['status'] == 'NOT_FOUND') {
						wp_send_json_error(array(
							'message' => '❌ <strong>Reis niet gevonden (404)</strong><br>' .
								'Reis ID: ' . esc_html($travel_id) . '<br>' .
								'API: ' . esc_html($api_name) . '<br>' .
								'Microsite: ' . esc_html($credentials['micrositeid'])
						));
					} else {
						wp_send_json_error(array('message' => '❌ Geen data ontvangen van API. Controleer het reis ID.'));
					}
					return;
				}
				
				// Create WordPress post
				$result = RBS_TRAVEL_Import::CreatePost($travel_info, $travel_details);
				
				// Cleanup
				delete_option('rbstravel_settings_temp_active_api_set');
				
				if (is_numeric($result) && $result > 0) {
					$post_edit_link = get_edit_post_link($result, 'raw');
					$message = '✅ <strong>Reis succesvol geïmporteerd!</strong><br>';
					$message .= '📝 ' . esc_html($travel_info['title']) . '<br>';
					$message .= '🔌 Via: ' . esc_html($api_name);
					if ($post_edit_link) {
						$message .= '<br><br><a href="' . esc_url($post_edit_link) . '" class="button button-primary">✏️ Reis bewerken</a>';
					}
					
					wp_send_json_success(array(
						'message' => $message,
						'post_id' => $result,
						'edit_link' => $post_edit_link
					));
				} else {
					$error_msg = is_wp_error($result) ? $result->get_error_message() : 'Onbekende fout';
					wp_send_json_error(array('message' => '❌ Fout bij importeren: ' . esc_html($error_msg)));
				}
				
			} catch (\Exception $e) {
				delete_option('rbstravel_settings_temp_active_api_set');
				wp_send_json_error(array('message' => '❌ Fout: ' . esc_html($e->getMessage())));
			} catch (\Error $e) {
				delete_option('rbstravel_settings_temp_active_api_set');
				wp_send_json_error(array('message' => '❌ PHP Error: ' . esc_html($e->getMessage())));
			}
		}

		public function ajax_rbs_get_travel_items() {
			$action = filter_input(INPUT_POST, 'action');

			
			// $a = $_POST['postdata'];			
			// $phpArray = json_decode(stripslashes($_POST['postdata']));
			// die($phpArray);
			// $b = $phpArray->person->name;
			// echo $phpArray[0]->name . ': ';
			// echo $phpArray[0]->value;

			$cleaned_datastring = json_decode(stripslashes($_POST['datastring']));
			foreach ($cleaned_datastring as $cleaned_datastring_item) {
				//echo $cleaned_datastring_item;
			}
			
			
			
			$a = $_POST['data'];			
			$phpArray = json_decode(stripslashes($_POST['data']));
			$postObject = json_decode(stripslashes($_POST['ajaxdata']));

			// echo json_encode($postObject);	

			// die();			

			$filter_terms = json_decode($postObject->destinations,true);
			$filter['pricerange'] = array();
			$filter['pricerange']['minprice'] = json_decode($postObject->minprice);
			$filter['pricerange']['maxprice'] = json_decode($postObject->maxprice);
			// $destinations = json_decode(stripslashes($_POST['destinations']));
			// var_dump($_POST['destinations']);
			// var_dump($destinations->person);


		
			$filtered_posts = rbstravel_get_ideas($args = array(), $json_encode = false, $filter_terms, $filter['pricerange']);
			$return = array(
                'post_ids' => array(),
                'html' => array()
            );
			foreach ($filtered_posts as $filtered_post) {
				$return[] = $filtered_post['post_id'];
                $idea = rbstravel_get_single_idea($filtered_post['post_id']);
                $return['html'][] = rbstravel_template_loader('frontend/idea-list-layout-single', array('idea' => $idea));
			}	
			echo json_encode($return);		
			die();

			// foreach ($destinations->destinations as $destitem) {
			// 	echo $destitem . 'xxx';
			// }
			

			echo json_encode($destinations);

			die();

			$filtered_posts = rbstravel_get_ideas($args = array(), $json_encode = false, $destinations);
			
			foreach ($filtered_posts as $filtered_post) {
				echo $filtered_post['post_id'] . '|';
			}

			die('xxx');
			

			$return = array(
				'name' => $phpArray->person->name,
				'city' => $phpArray->person->city,
				'age' => $phpArray->person->age,
				'destinations' => $destinations
			);

			echo json_encode($return);

			//echo json_encode($phpArray);
			// echo var_dump($b);
			die('');
			echo json_encode($a);
			die('xx');
			$return = array();
			$return['test_key'] = 'testvalue';			
			echo json_encode($_REQUEST['data']);
			die('xxxx');
			$return['test_key'] = $_REQUEST;
			echo json_encode($_REQUEST);
			
			echo json_encode($return);
			die();

			
		}	


        public function ajax_rbs_upload_map_image() {
            // echo print_r($_POST);
            // die();
        }
        
        /**
         * AJAX Handler: Save Photos for cruises, hotels, destinations, etc.
         */
        public function ajax_save_photos() {
            // Verify nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rbs_photo_nonce')) {
                wp_send_json_error(array('message' => 'Security check failed'));
                return;
            }
            
            // Get parameters
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
            $index = isset($_POST['index']) ? intval($_POST['index']) : 0;
            $photos = isset($_POST['photos']) ? array_map('esc_url_raw', $_POST['photos']) : array();
            
            // Validate
            if (!$post_id || !$type) {
                wp_send_json_error(array('message' => 'Invalid parameters'));
                return;
            }
            
            // Map type to meta key
            $type_map = array(
                'hotel' => 'travel_hotels',
                'destination' => 'travel_destinations',
                'cruise' => 'travel_cruises',
                'tripspot' => 'travel_trip_spots',
                'closedtour' => 'travel_closed_tours',
                'ticket' => 'travel_tickets',
                'transfer' => 'travel_transfers',
                'car' => 'travel_cars'
            );
            
            if (!isset($type_map[$type])) {
                wp_send_json_error(array('message' => 'Unknown type: ' . $type));
                return;
            }
            
            $meta_key = $type_map[$type];
            
            // Get current data
            $items = get_post_meta($post_id, $meta_key, true);
            
            if (!is_array($items)) {
                wp_send_json_error(array('message' => 'No items found for ' . $meta_key));
                return;
            }
            
            if (!isset($items[$index])) {
                wp_send_json_error(array('message' => 'Item index not found: ' . $index));
                return;
            }
            
            // Update photos based on type
            if ($type === 'hotel') {
                // Hotels store images in hotelData.images as array of {url: ...}
                if (!isset($items[$index]['hotelData'])) {
                    $items[$index]['hotelData'] = array();
                }
                $items[$index]['hotelData']['images'] = array_map(function($url) {
                    return array('url' => $url);
                }, $photos);
            } elseif ($type === 'destination') {
                // Destinations store images in imageUrls array
                $items[$index]['imageUrls'] = $photos;
            } elseif ($type === 'cruise') {
                // Cruises: store in images array at top level
                $items[$index]['images'] = $photos;
            } else {
                // Generic: store in images array
                $items[$index]['images'] = $photos;
            }
            
            // Save back to database
            update_post_meta($post_id, $meta_key, $items);
            
            wp_send_json_success(array(
                'message' => 'Photos saved successfully',
                'count' => count($photos)
            ));
        }
        
        /**
         * Sync all travels from a specific API set
         */
        public function ajax_sync_all_travels() {
            // Verify nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'rbstravel_sync_all')) {
                wp_send_json_error(array('message' => 'Invalid nonce'));
                return;
            }
            
            // Check user permissions
            if (!current_user_can('edit_posts')) {
                wp_send_json_error(array('message' => 'Insufficient permissions'));
                return;
            }
            
            $api_set = isset($_POST['api_set']) ? sanitize_text_field($_POST['api_set']) : '';
            
            if (empty($api_set)) {
                wp_send_json_error(array('message' => 'No API set specified'));
                return;
            }
            
            // Extend PHP timeout and memory for large imports
            set_time_limit(600); // 10 minutes
            ini_set('memory_limit', '512M'); // Increase memory limit
            
            $start_time = microtime(true);
            
            // Get API credentials
            if (is_multisite()) {
                $api_credentials = get_site_option('rbstravel_network_api_credentials', array());
            } else {
                $api_credentials = RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
            }
            
            if (!isset($api_credentials[$api_set])) {
                wp_send_json_error(array('message' => 'API set not found'));
                return;
            }
            
            // Temporarily set this as active API
            update_option('rbstravel_settings_temp_active_api_set', $api_set);
            
            try {
                // Create API instance
                $api = new RBS_TRAVEL_Api();
                
                // Get all travel ideas (set high limit to get all travels)
                // GetIdeas($search = null, $filters = array(), $first = 0, $limit = 5)
                $response = $api->GetIdeas(null, array(), 0, 1000);
                
                // Check if response has pagination info
                $total_results = 0;
                $ideas = array();
                
                if (isset($response['pagination']) && isset($response['idea'])) {
                    // API returns pagination structure
                    $total_results = $response['pagination']['totalResults'];
                    $ideas = $response['idea'];
                } elseif (is_array($response)) {
                    // Direct array of ideas
                    $ideas = $response;
                    $total_results = count($ideas);
                }
                
                if (empty($ideas) || !is_array($ideas)) {
                    delete_option('rbstravel_settings_temp_active_api_set');
                    wp_send_json_error(array(
                        'message' => 'No travels found or API error.',
                        'debug' => array(
                            'response_type' => gettype($response),
                            'has_pagination' => isset($response['pagination']),
                            'has_idea' => isset($response['idea'])
                        )
                    ));
                    return;
                }
                
                $imported = 0;
                $updated = 0;
                $failed = 0;
                $failed_ids = array();
                
                // Import each travel
                foreach ($ideas as $idea) {
                    if (!isset($idea['id'])) {
                        $failed++;
                        continue;
                    }
                    
                    $travel_id = $idea['id'];
                    
                    try {
                        // Check if we're approaching timeout (leave 30 seconds buffer)
                        $elapsed = microtime(true) - $start_time;
                        if ($elapsed > 570) { // 9.5 minutes
                            // Stop processing and return what we have
                            break;
                        }
                        
                        // Get full travel info and details
                        $travel_info = $api->GetInfo($travel_id);
                        $travel_details = $api->GetDetails($travel_id);
                        
                        if (empty($travel_info) || !isset($travel_info['id'])) {
                            $failed++;
                            $failed_ids[] = $travel_id;
                            continue;
                        }
                        
                        // Check if travel already exists
                        $existing = get_posts(array(
                            'post_type' => 'rbs-travel-idea',
                            'meta_key' => 'travel_id',
                            'meta_value' => $travel_id,
                            'posts_per_page' => 1,
                            'post_status' => 'any'
                        ));
                        
                        // Import or update - set status to 'publish' for sync
                        $result = RBS_TRAVEL_Import::CreatePost($travel_info, $travel_details, 0, 'publish');
                        
                        if (is_numeric($result) && $result > 0) {
                            if (!empty($existing)) {
                                $updated++;
                            } else {
                                $imported++;
                            }
                        } else {
                            $failed++;
                            $failed_ids[] = $travel_id;
                        }
                    } catch (\Exception $e) {
                        $failed++;
                        $failed_ids[] = $travel_id . ' (Error: ' . $e->getMessage() . ')';
                        continue;
                    }
                }
                
                delete_option('rbstravel_settings_temp_active_api_set');
                
                $duration = round(microtime(true) - $start_time, 2);
                $processed = $imported + $updated + $failed;
                $remaining = count($ideas) - $processed;
                $elapsed = microtime(true) - $start_time;
                
                wp_send_json_success(array(
                    'imported' => $imported,
                    'updated' => $updated,
                    'failed' => $failed,
                    'failed_ids' => $failed_ids,
                    'total' => count($ideas),
                    'total_in_api' => $total_results,
                    'processed' => $processed,
                    'remaining' => $remaining,
                    'duration' => $duration,
                    'timeout_reached' => ($elapsed > 570)
                ));
                
            } catch (\Exception $e) {
                delete_option('rbstravel_settings_temp_active_api_set');
                wp_send_json_error(array('message' => $e->getMessage()));
            }
        }
	}


    
    
}

$RBS_TRAVEL_Ajax = new RBS_TRAVEL_Ajax();