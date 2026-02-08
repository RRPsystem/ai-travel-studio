<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo:
 * - [ ] import "travel_details"
 * - [ ] ?? save 'import images' as mapping??
 *	>> $images[ID] = imageUrl;
 * - [ ] check if images already exists, before download/import
 * - [ ] 
 */



if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Import')) {

    class RBS_TRAVEL_Import {
	
	
	
	
	
	
	
	public static function CreatePost($travel_info, $travel_details, $current_post_id = 0, $post_status = null) {
	    // DEBUG: Log import start
	    error_log('=== RBS TRAVEL IMPORT START ===');
	    error_log('Travel ID: ' . $travel_info['id']);
	    error_log('Travel Title: ' . $travel_info['title']);
	    error_log('Counters: ' . print_r($travel_info['counters'], true));
	    error_log('FULL TRAVEL_INFO: ' . print_r($travel_info, true));
	    error_log('Travel Details Keys: ' . implode(', ', array_keys($travel_details)));
	    if (isset($travel_details['cruises'])) {
	        error_log('FOUND cruises in travel_details: ' . count($travel_details['cruises']));
	    } else {
	        error_log('NO cruises key in travel_details');
	    }
	    
	    error_log('RBS Travel: Checking for existing mapping...');
	    $travel_mapping = RBS_TRAVEL_Settings::GetMapping($travel_info['id']);
	    error_log('RBS Travel: Mapping result: ' . print_r($travel_mapping, true));
	    
	    // Determine post ID - only use existing ID if valid
	    $post_id_to_use = 0;
	    if ($current_post_id > 0) {
	        // Update existing post
	        $post_id_to_use = $current_post_id;
	        error_log('RBS Travel: Updating existing post ID: ' . $post_id_to_use);
	    } elseif ($travel_mapping !== false && is_numeric($travel_mapping) && $travel_mapping > 0) {
	        // Check if mapped post still exists
	        if (get_post($travel_mapping) !== null) {
	            $post_id_to_use = $travel_mapping;
	            error_log('RBS Travel: Using mapped post ID: ' . $post_id_to_use);
	        } else {
	            error_log('RBS Travel: Mapped post ID ' . $travel_mapping . ' no longer exists, creating new');
	        }
	    } else {
	        error_log('RBS Travel: Creating new post (no valid mapping found)');
	    }

	    // Remove emojis from title for slug to prevent 404 errors
    $clean_title_for_slug = preg_replace('/[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]/u', '', $travel_info['title']);
    $clean_title_for_slug = trim($clean_title_for_slug);
    
    // Determine post status: use provided status, or fall back to setting, or default to draft
    $status_to_use = $post_status !== null ? $post_status : RBS_TRAVEL_Settings::GetSetting('default_post_status', 'draft');
    
    $postarr = array(
            'post_type' => 'rbs-travel-idea',
            'post_status' => $status_to_use,
            'post_title' => $travel_info['title'], // Keep emoji in title
            'post_name' => sanitize_title($clean_title_for_slug), // Slug without emoji
            'post_content' => isset($travel_info['description']) && strlen($travel_info['description']) !== 0 ? $travel_info['description'] : '',
            //'post_author' => null,
            //'post_date' => $data['creationDate']
	    );
	    
	    // Only add ID if we have a valid one (for updates)
	    if ($post_id_to_use > 0) {
	        $postarr['ID'] = $post_id_to_use;
	    }
	    //print_r($postarr);die();

	    error_log('RBS Travel: Creating WordPress post...');
	    $post_id = wp_insert_post($postarr, true);
	    
	    if (!is_numeric($post_id)) {
	        error_log('RBS Travel: ERROR - wp_insert_post failed: ' . print_r($post_id, true));
		    return $post_id;
	    }
	    error_log('RBS Travel: Post created successfully, ID: ' . $post_id);
	    
	    // Only download featured image if post doesn't have one yet
	    if (isset($travel_info['imageUrl']) && strlen($travel_info['imageUrl']) !== 0) {
	        $existing_thumbnail = get_post_thumbnail_id($post_id);
	        if (!$existing_thumbnail) {
	            error_log('RBS Travel: Downloading featured image...');
	            try {
	                $image = self::DownloadImage($travel_info['imageUrl']);
	                $attachment_id = self::InsertAttachment($image['attachment'], $image['file']);
	                set_post_thumbnail($post_id, $attachment_id);
	                error_log('RBS Travel: Featured image set successfully');
	            } catch (Exception $e) {
	                error_log('RBS Travel: ERROR downloading image - ' . $e->getMessage());
	            }
	        } else {
	            error_log('RBS Travel: Featured image already exists (ID: ' . $existing_thumbnail . '), skipping download');
	        }
	    }
	    
	    $meta_fields = array();
	    $meta_fields['travel_id'] = $travel_info['id'];
	    $meta_fields['travel_user'] = $travel_info['user'];
	    $meta_fields['travel_email'] = $travel_info['email'];
	    //$meta_fields['travel_title'] = $travel_info['title'];
	    $meta_fields['travel_large_title'] = $travel_info['largeTitle'];
	    $meta_fields['travel_description'] = isset($travel_info['description']) ? $travel_info['description'] : '';
	    $meta_fields['travel_remarks'] = $travel_info['remarks'];
	    $meta_fields['travel_image_url'] = $travel_info['imageUrl'];
	    $meta_fields['travel_creation_date'] = $travel_info['creationDate'];
	    $meta_fields['travel_departure_date'] = $travel_info['departureDate'];
	    $meta_fields['travel_idea_url'] = $travel_info['ideaUrl'];
	    
	    $meta_fields['travel_themes'] = $travel_info['themes'];
	    $meta_fields['travel_per_person_price'] = $travel_info['pricePerPerson'];
	    $meta_fields['travel_total_price'] = $travel_info['totalPrice'];
//	    $meta_fields['travel_destinations'] = $travel_info['destinations'];	    /** Get from travel_details **/
	    //$meta_fields['itinerary'] = $data['itinerary'];
	    $meta_fields['travel_user_b2c'] = $travel_info['userB2c'];
	    $meta_fields['travel_counters'] = $travel_info['counters'];

        

        $meta_fields['travel_price_total'] = isset($travel_info['totalPrice']['amount']) ? number_format($travel_info['totalPrice']['amount'], 2, '.', '') : number_format(0, 2, '.', '');
        $meta_fields['travel_price_per_person'] = isset($travel_info['pricePerPerson']['amount']) ? number_format($travel_info['pricePerPerson']['amount'], 2, '.', '') : number_format(0, 2, '.', '');
        
        $meta_fields['travel_number_of_nights'] = isset($travel_info['counters']['hotelNights']) ? $travel_info['counters']['hotelNights'] : 0;
        
        $number_of_persons = isset($travel_info['counters']['adults']) ? $travel_info['counters']['adults'] : 0;
        $number_of_persons += isset($travel_info['counters']['children']) ? $travel_info['counters']['children'] : 0;

        $meta_fields['travel_min_persons'] = $number_of_persons;
        $meta_fields['travel_max_persons'] = $number_of_persons;

        error_log('RBS Travel: Saving basic meta fields...');
	    foreach($meta_fields as $meta_key => $meta_value) {
		    update_post_meta($post_id, $meta_key, $meta_value);
	    }
	    error_log('RBS Travel: Basic meta fields saved successfully');
	    
	    error_log('RBS Travel: About to parse travel details for post ' . $post_id);
	    
	    try {
	        self::parse_travel_details($post_id, $travel_details, $travel_info);
	        error_log('RBS Travel: Successfully parsed travel details');
	    } catch (Exception $e) {
	        error_log('RBS Travel: FATAL ERROR in parse_travel_details - ' . $e->getMessage());
	        error_log('RBS Travel: Stack trace: ' . $e->getTraceAsString());
	        throw $e; // Re-throw to see in WordPress
	    }
	    
	    
	    if (RBS_TRAVEL_Settings::GetSetting('import_all_images', false) === 'yes') {
		    self::get_travel_details_images($travel_details);
	    }
	    

	    RBS_TRAVEL_Settings::SetMapping($travel_info['id'], $post_id);
	    

        self::update_travel_taxonomies($post_id, $travel_info, $travel_details);


	    return $post_id;
	    
	}

    private static function update_travel_taxonomies($post_id, $travel_info, $travel_details) {
        // Update Taxonomy: Tour-Type
        $default_tour_types = array('Vliegreis');
        
        // Add Cruise if cruises exist
        if (isset($travel_details['cruises']) && is_array($travel_details['cruises']) && count($travel_details['cruises']) > 0) {
            $default_tour_types[] = 'Cruise';
        }
        
        $term_ids = array();
        foreach ($default_tour_types as $term_name) {
            if (!term_exists($term_name, 'tour-type')) {
                wp_insert_term($term_name, 'tour-type');
            }
            $term = get_term_by('name', $term_name, 'tour-type');
            $term_ids[] = $term->term_id;
        }
        
        $result = wp_set_post_terms($post_id, $term_ids, 'tour-type');
        if (is_wp_error($result)) {
            error_log('Error assigning terms: ' . $result->get_error_message());
        } else {
            error_log('Terms successfully assigned to post ID ' . $post_id);
        }         


        // Update Taxonomy: Tour-Themes
        $tour_themes = isset($travel_info['themes']) && is_array($travel_info['themes']) ? $travel_info['themes'] : array();
        $term_ids = array();
        if (count($tour_themes) !== 0) {
            foreach ($tour_themes as $term_name) {
                if (!term_exists($term_name, 'tour-theme')) {
                    wp_insert_term($term_name, 'tour-theme');
                }
                $term = get_term_by('name', $term_name, 'tour-theme');
                $term_ids[] = $term->term_id;                
            }
            $result = wp_set_post_terms($post_id, $term_ids, 'tour-theme');
            if (is_wp_error($result)) {
                error_log('Error assigning terms: ' . $result->get_error_message());
            } else {
                error_log('Terms successfully assigned to post ID ' . $post_id);
            }    
        }


        
        
        // Update Taxonomy: Tour-Services (from accommodations)

        $accommodations = isset($travel_details['hotels']) && is_array($travel_details['hotels']) ? $travel_details['hotels'] : array();
        $tour_services = array();



        foreach($accommodations as $accommodation) {
            if (isset($accommodation['hotelData']['otherServices']) && is_array($accommodation['hotelData']['otherServices'])) {
                $tour_services = array_merge($tour_services, $accommodation['hotelData']['otherServices']);
            }
        }

        // echo '<pre>' . print_r($tour_services, true) . '</pre>';
        // die();

        $term_ids = array();
        if (count(array_unique($tour_services)) !== 0) {
            foreach (array_unique($tour_services) as $term_name) {
                if (!term_exists($term_name, 'tour-service')) {
                    wp_insert_term($term_name, 'tour-service');
                }
                $term = get_term_by('name', $term_name, 'tour-service');
                $term_ids[] = $term->term_id;                
            }
            $result = wp_set_post_terms($post_id, $term_ids, 'tour-service');
            if (is_wp_error($result)) {
                error_log('Error assigning terms: ' . $result->get_error_message());
            } else {
                error_log('Terms successfully assigned to post ID ' . $post_id);
            }    
        }        


        // Update Taxonomy: Location (from destinations)
        $destinations = isset($travel_details['destinations']) && is_array($travel_details['destinations']) ? $travel_details['destinations'] : array();
        $location_terms = array();
        
        // Extract unique countries from destinations
        foreach ($destinations as $destination) {
            if (isset($destination['country']) && !empty($destination['country'])) {
                $country_name = $destination['country'];
                if (!in_array($country_name, $location_terms)) {
                    $location_terms[] = $country_name;
                }
            }
        }
        
        // Assign location terms
        $term_ids = array();
        if (count($location_terms) > 0) {
            foreach ($location_terms as $term_name) {
                if (!term_exists($term_name, 'location')) {
                    wp_insert_term($term_name, 'location');
                }
                $term = get_term_by('name', $term_name, 'location');
                if ($term) {
                    $term_ids[] = $term->term_id;
                }
            }
            
            if (count($term_ids) > 0) {
                $result = wp_set_post_terms($post_id, $term_ids, 'location');
                if (is_wp_error($result)) {
                    error_log('Error assigning location terms: ' . $result->get_error_message());
                } else {
                    error_log('Location terms successfully assigned to post ID ' . $post_id . ': ' . implode(', ', $location_terms));
                }
            }
        }

    }
	
	
	private static function parse_travel_details($post_id, $travel_details, $travel_info) {
	    error_log('RBS Travel: parse_travel_details START');
	    $meta_fields = array();
	    
	    error_log('RBS Travel: Setting destinations...');
	    $meta_fields['travel_destinations'] = isset($travel_details['destinations']) ? $travel_details['destinations'] : array();
	    //$meta_fields['travel_trip_spots'] = $travel_details['tripSpots'];
	    //$meta_fields['travel_closed_tours'] = $travel_details['closedTours'];
	    
	    error_log('RBS Travel: Transforming transports...');
	    // Transform transports to ensure proper structure for timeline
	    $transports = isset($travel_details['transports']) ? $travel_details['transports'] : array();
	    if (!empty($transports) && is_array($transports)) {
	        try {
	            $transports = self::transform_transport_data($transports);
	        } catch (Exception $e) {
	            error_log('RBS Travel: Error transforming transports - ' . $e->getMessage());
	            // Keep original data if transform fails
	        }
	    }
	    $meta_fields['travel_transports'] = $transports;
	    error_log('RBS Travel: Transports done, count: ' . count($transports));
	    
	    $meta_fields['travel_transfers'] = isset($travel_details['transfers']) ? $travel_details['transfers'] : array();
	    error_log('RBS Travel: Transfers done');
	    
	    error_log('RBS Travel: Transforming hotels...');
	    // Transform hotels to ensure images are accessible
	    $hotels = isset($travel_details['hotels']) ? $travel_details['hotels'] : array();
	    if (!empty($hotels)) {
	        $hotels = self::transform_hotel_data($hotels);
	    }
	    $meta_fields['travel_hotels'] = $hotels;
	    error_log('RBS Travel: Hotels done, count: ' . count($hotels));
	    
	    //$meta_fields['travel_tickets'] = $travel_details['tickets'];
	    $meta_fields['travel_cars'] = isset($travel_details['cars']) ? $travel_details['cars'] : array();
	    error_log('RBS Travel: Cars done');
	    //$meta_fields['travel_insurances'] = $travel_details['insurances'];
	    //$meta_fields['travel_manuals'] = $travel_details['manuals'];
	    
	    error_log('RBS Travel: Processing cruises...');
	    // Extract cruise data - support both IDEAS and PACKAGES structure
	    $cruises = array();
	    
	    // Store raw details for inspection (only if WP_DEBUG is enabled)
	    if (defined('WP_DEBUG') && WP_DEBUG) {
	        update_post_meta($post_id, 'travel_details_raw', $travel_details);
	    }
	    
	    // Check for IDEAS format (direct cruises array)
	    if (isset($travel_details['cruises']) && is_array($travel_details['cruises'])) {
	        $cruises = $travel_details['cruises'];
	        error_log('RBS Travel: Found cruises in IDEAS format - Count: ' . count($cruises));
	    }
	    
	    // Check for PACKAGE format (might be nested or in components)
	    if (empty($cruises) && isset($travel_details['components']['cruises'])) {
	        $cruises = $travel_details['components']['cruises'];
	        error_log('RBS Travel: Found cruises in PACKAGE components format - Count: ' . count($cruises));
	    }
	    
	    if (empty($cruises)) {
	        error_log('RBS Travel: No cruises found in standard locations.');
	    }
	    
	    // Transform cruise data to expected format
	    if (!empty($cruises) && is_array($cruises)) {
	        error_log('RBS Travel: Transforming cruise data...');
	        try {
	            $cruises = self::transform_cruise_data($cruises, $travel_info, $travel_details);
	        } catch (Exception $e) {
	            error_log('RBS Travel: Error transforming cruises - ' . $e->getMessage());
	            // Keep original data if transform fails
	        }
	    }
	    
	    $meta_fields['travel_cruises'] = $cruises;
	    error_log('RBS Travel: Cruises done, count: ' . count($cruises));
    
    //$meta_fields['travel_ride_hailing'] = $travel_details['rideHailing'];
	    
//	    $meta_fields[''] = $travel_details[''];
	    
	    error_log('RBS Travel: Saving all meta fields...');
	    foreach($meta_fields as $meta_key => $meta_value) {
		update_post_meta($post_id, $meta_key, $meta_value);
	    }
	    error_log('RBS Travel: parse_travel_details COMPLETE');
	}
	
	
	private static function transform_cruise_data($cruises, $travel_info, $travel_details) {
	    $transformed = array();
	    
	    // Get departure date from travel_info to calculate fromDay/toDay
	    $travel_departure = isset($travel_info['departureDate']) ? $travel_info['departureDate'] : null;
	    
	    foreach ($cruises as $cruise) {
	        $transformed_cruise = array();
	        
	        // Map existing fields
	        $transformed_cruise['id'] = isset($cruise['id']) ? $cruise['id'] : '';
	        $transformed_cruise['shipId'] = isset($cruise['shipId']) ? $cruise['shipId'] : '';
	        $transformed_cruise['ship'] = isset($cruise['shipName']) ? $cruise['shipName'] : '';
	        $transformed_cruise['cruiseLine'] = isset($cruise['cruiseLine']) ? $cruise['cruiseLine'] : '';
	        $transformed_cruise['cabin'] = isset($cruise['cabin']) ? $cruise['cabin'] : '';
	        $transformed_cruise['category'] = isset($cruise['selectedCategory']) ? $cruise['selectedCategory'] : '';
	        $transformed_cruise['group'] = isset($cruise['group']) ? $cruise['group'] : '';
	        $transformed_cruise['nights'] = isset($cruise['nights']) ? $cruise['nights'] : 0;
	        
	        // Map departure/arrival to embarkDate/disembarkDate
	        $transformed_cruise['embarkDate'] = isset($cruise['departure']) ? substr($cruise['departure'], 0, 10) : '';
	        $transformed_cruise['disembarkDate'] = isset($cruise['arrival']) ? substr($cruise['arrival'], 0, 10) : '';
	        
	        // Map origin port (embark/disembark ports)
	        $transformed_cruise['embarkPort'] = isset($cruise['originPort']) ? $cruise['originPort'] : '';
	        $transformed_cruise['disembarkPort'] = isset($cruise['destinationPort']) ? $cruise['destinationPort'] : (isset($cruise['originPort']) ? $cruise['originPort'] : '');
	        
	        // Map cruise images - comprehensive search
	        // Priority 1: cruiseData.images array
	        if (isset($cruise['cruiseData']['images']) && is_array($cruise['cruiseData']['images'])) {
	            $transformed_cruise['images'] = $cruise['cruiseData']['images'];
	        }
	        // Priority 2: cruiseData.imageUrls array
	        elseif (isset($cruise['cruiseData']['imageUrls']) && is_array($cruise['cruiseData']['imageUrls'])) {
	            $transformed_cruise['images'] = $cruise['cruiseData']['imageUrls'];
	        }
	        // Priority 3: Top-level images array
	        elseif (isset($cruise['images']) && is_array($cruise['images'])) {
	            $transformed_cruise['images'] = $cruise['images'];
	        }
	        // Priority 4: Top-level imageUrls array
	        elseif (isset($cruise['imageUrls']) && is_array($cruise['imageUrls'])) {
	            $transformed_cruise['images'] = $cruise['imageUrls'];
	        }
	        // Priority 5: Single image fields in cruiseData
	        elseif (isset($cruise['cruiseData']['image'])) {
	            $transformed_cruise['images'] = array($cruise['cruiseData']['image']);
	        } elseif (isset($cruise['cruiseData']['imageUrl'])) {
	            $transformed_cruise['images'] = array($cruise['cruiseData']['imageUrl']);
	        } elseif (isset($cruise['cruiseData']['mainImage'])) {
	            $transformed_cruise['images'] = array($cruise['cruiseData']['mainImage']);
	        } elseif (isset($cruise['cruiseData']['headerImage'])) {
	            $transformed_cruise['images'] = array($cruise['cruiseData']['headerImage']);
	        } elseif (isset($cruise['cruiseData']['shipImage'])) {
	            $transformed_cruise['images'] = array($cruise['cruiseData']['shipImage']);
	        }
	        // Priority 6: Single image fields at top level
	        elseif (isset($cruise['image'])) {
	            $transformed_cruise['images'] = array($cruise['image']);
	        } elseif (isset($cruise['imageUrl'])) {
	            $transformed_cruise['images'] = array($cruise['imageUrl']);
	        } elseif (isset($cruise['mainImage'])) {
	            $transformed_cruise['images'] = array($cruise['mainImage']);
	        } elseif (isset($cruise['headerImage'])) {
	            $transformed_cruise['images'] = array($cruise['headerImage']);
	        } elseif (isset($cruise['shipImage'])) {
	            $transformed_cruise['images'] = array($cruise['shipImage']);
	        }
	        
	        // Log what we found for debugging
	        if (!empty($transformed_cruise['images'])) {
	            error_log('RBS Travel: Found ' . count($transformed_cruise['images']) . ' image(s) for cruise ' . ($cruise['id'] ?? 'unknown'));
	        } else {
	            error_log('RBS Travel: No images found for cruise ' . ($cruise['id'] ?? 'unknown'));
	        }
	        
	        // Also copy the full cruiseData for reference
	        if (isset($cruise['cruiseData'])) {
	            $transformed_cruise['cruiseData'] = $cruise['cruiseData'];
	        }
        
        // Generate itinerary URL for Travel Compositor
        if (isset($cruise['id'])) {
            $cruise_id = strtolower($cruise['id']);
            $transformed_cruise['itineraryUrl'] = '/nl/cruises/' . $cruise_id . '/itinerary?booking=true';
        }
	        
	        // Calculate fromDay and toDay based on travel departure date
	        if ($travel_departure && !empty($transformed_cruise['embarkDate']) && !empty($transformed_cruise['disembarkDate'])) {
	            try {
	                $travel_start = new \DateTime($travel_departure);
	                $cruise_start = new \DateTime($transformed_cruise['embarkDate']);
	                $cruise_end = new \DateTime($transformed_cruise['disembarkDate']);
	                
	                $diff_start = $travel_start->diff($cruise_start);
	                $diff_end = $travel_start->diff($cruise_end);
	                
	                $days_to_start = $diff_start->days !== false ? $diff_start->days : 0;
	                $days_to_end = $diff_end->days !== false ? $diff_end->days : 0;
	                
	                $transformed_cruise['fromDay'] = $days_to_start + 1; // +1 because day 1 is departure day
	                $transformed_cruise['toDay'] = $days_to_end + 1;
	            } catch (Exception $e) {
	                error_log('RBS Travel: Error calculating cruise days - ' . $e->getMessage());
	            }
	        }
	        
	        // Extract cruise ports and destinations
	        if (isset($transformed_cruise['fromDay']) && isset($transformed_cruise['toDay'])) {
	            $cruise_from_day = $transformed_cruise['fromDay'];
	            $cruise_to_day = $transformed_cruise['toDay'];
	            
	            $embark_port = '';
	            $disembark_port = '';
	            $cruise_destinations = array();
	            
	            // Find embark/disembark port from travel_details (destinations with fromDay/toDay)
	            if (isset($travel_details['destinations']) && is_array($travel_details['destinations'])) {
	                foreach ($travel_details['destinations'] as $dest) {
	                    $dest_name = isset($dest['name']) ? $dest['name'] : '';
	                    $dest_from = isset($dest['fromDay']) ? $dest['fromDay'] : 0;
	                    $dest_to = isset($dest['toDay']) ? $dest['toDay'] : 0;
	                    
	                    // Check if destination OVERLAPS with cruise period
	                    if ($dest_from <= $cruise_to_day && $dest_to >= $cruise_from_day) {
	                        // This is likely the embark/disembark port
	                        if (empty($embark_port) && $dest_from <= $cruise_from_day) {
	                            $embark_port = $dest_name;
	                        }
	                        if ($dest_to >= $cruise_to_day) {
	                            $disembark_port = $dest_name;
	                        }
	                    }
	                }
	            }
	            
	            // Get cruise destinations (ports of call) from travel_info destinations
	            // These are the actual cruise stops (Coco Cay, Falmouth, Labadee, etc.)
	            // EXCLUDE embark/disembark ports
	            if (isset($travel_info['destinations']) && is_array($travel_info['destinations'])) {
	                error_log('RBS Travel Cruise: Embark port = ' . $embark_port . ', Disembark port = ' . $disembark_port);
	                $cruise_port_keywords = array('coco', 'cay', 'bahama', 'jamaica', 'falmouth', 'labadee', 'haiti', 
	                                               'cayman', 'nassau', 'aruba', 'curacao', 'st.', 'saint', 'port', 'island');
	                
	                foreach ($travel_info['destinations'] as $dest) {
	                    $dest_name = isset($dest['name']) ? $dest['name'] : '';
	                    $dest_code = isset($dest['code']) ? strtolower($dest['code']) : '';
	                    $dest_name_lower = strtolower($dest_name);
	                    
	                    // Check if this looks like a cruise port (not a major city)
	                    $is_cruise_port = false;
	                    foreach ($cruise_port_keywords as $keyword) {
	                        if (stripos($dest_name_lower, $keyword) !== false || stripos($dest_code, $keyword) !== false) {
	                            $is_cruise_port = true;
	                            break;
	                        }
	                    }
	                    
	                    // Skip if not a cruise port, or if it's the embark/disembark port
	                    if (!$is_cruise_port || $dest_name == $embark_port || $dest_name == $disembark_port) {
	                        error_log('RBS Travel Cruise: Skipping destination ' . $dest_name . ' (is_cruise_port=' . ($is_cruise_port ? 'yes' : 'no') . ', is_embark=' . ($dest_name == $embark_port ? 'yes' : 'no') . ', is_disembark=' . ($dest_name == $disembark_port ? 'yes' : 'no') . ')');
	                        continue;
	                    }
	                    
	                    error_log('RBS Travel Cruise: Adding cruise port: ' . $dest_name);
	                    
	                    // Check if this destination falls WITHIN the cruise period in travel_details
	                    $is_during_cruise = false;
	                    $description = '';
	                    
	                    if (isset($travel_details['destinations']) && is_array($travel_details['destinations'])) {
	                        foreach ($travel_details['destinations'] as $detail_dest) {
	                            if (isset($detail_dest['code']) && isset($dest['code']) && $detail_dest['code'] == $dest['code']) {
	                                $detail_from = isset($detail_dest['fromDay']) ? $detail_dest['fromDay'] : 0;
	                                $detail_to = isset($detail_dest['toDay']) ? $detail_dest['toDay'] : 0;
	                                
	                                // Check if this destination is during the cruise period
	                                if ($detail_from > 0 && $detail_to > 0) {
	                                    // Destination must START and END within cruise period
	                                    if ($detail_from >= $cruise_from_day && $detail_to <= $cruise_to_day) {
	                                        $is_during_cruise = true;
	                                        $description = isset($detail_dest['description']) ? $detail_dest['description'] : '';
	                                        error_log('RBS Travel: Destination ' . $dest_name . ' is during cruise (day ' . $detail_from . '-' . $detail_to . ')');
	                                    } else {
	                                        error_log('RBS Travel: Skipping ' . $dest_name . ' - outside cruise period (day ' . $detail_from . '-' . $detail_to . ' vs ' . $cruise_from_day . '-' . $cruise_to_day . ')');
	                                    }
	                                } else {
	                                    // No fromDay/toDay = assume it's a cruise port (not in land portion)
	                                    $is_during_cruise = true;
	                                    $description = isset($detail_dest['description']) ? $detail_dest['description'] : '';
	                                }
	                                break;
	                            }
	                        }
	                    }
	                    
	                    // If no match in travel_details, assume it's a valid cruise port (from travel_info only)
	                    if (!$is_during_cruise && isset($travel_details['destinations'])) {
	                        // Check if this code exists in travel_details at all
	                        $exists_in_details = false;
	                        foreach ($travel_details['destinations'] as $detail_dest) {
	                            if (isset($detail_dest['code']) && isset($dest['code']) && $detail_dest['code'] == $dest['code']) {
	                                $exists_in_details = true;
	                                break;
	                            }
	                        }
	                        
	                        // If it doesn't exist in travel_details (with fromDay/toDay), it's probably a pure cruise port
	                        if (!$exists_in_details) {
	                            $is_during_cruise = true;
	                        }
	                    }
	                    
	                    if ($is_during_cruise) {
	                        // Generate a better default description if none exists
	                        if (empty($description)) {
	                            $description = self::generate_cruise_port_description($dest_name);
	                        }
	                        
	                        $cruise_destinations[] = array(
	                            'name' => $dest_name,
	                            'code' => isset($dest['code']) ? $dest['code'] : '',
	                            'description' => $description
	                        );
	                    }
	                }
	            }
	            
	            // Store the results
	            if (!empty($cruise_destinations)) {
	                $transformed_cruise['destinations'] = $cruise_destinations;
	                error_log('RBS Travel: Found ' . count($cruise_destinations) . ' cruise port destinations');
	            }
	            
	            if (!empty($embark_port) && empty($transformed_cruise['embarkPort'])) {
	                $transformed_cruise['embarkPort'] = $embark_port;
	            }
	            if (!empty($disembark_port) && empty($transformed_cruise['disembarkPort'])) {
	                $transformed_cruise['disembarkPort'] = $disembark_port;
	            }
	        }
	        
	        // If no name was set, create a default one
	        if (empty($transformed_cruise['name'])) {
	            $transformed_cruise['name'] = 'Cruise ' . ($transformed_cruise['nights'] > 0 ? $transformed_cruise['nights'] . ' nights' : '');
	        }
	        
	        // Add itinerary steps from CruiseItineraryVO if available
	        if (isset($cruise['itinerary']) && is_array($cruise['itinerary'])) {
	            $transformed_cruise['itinerary'] = $cruise['itinerary'];
	        } elseif (isset($cruise['itinerarySteps']) && is_array($cruise['itinerarySteps'])) {
	            $transformed_cruise['itinerary'] = $cruise['itinerarySteps'];
	        } elseif (isset($cruise['cruiseData']['itinerary']) && is_array($cruise['cruiseData']['itinerary'])) {
	            $transformed_cruise['itinerary'] = $cruise['cruiseData']['itinerary'];
	        } elseif (isset($cruise['cruiseData']['itinerarySteps']) && is_array($cruise['cruiseData']['itinerarySteps'])) {
	            $transformed_cruise['itinerary'] = $cruise['cruiseData']['itinerarySteps'];
	        } else {
	            // Generate basic itinerary from destinations if no itinerary provided
	            if (!empty($cruise_destinations)) {
	                $itinerary_steps = array();
	                $day_counter = 1;
	                
	                // Day 1: Embark port (shown)
	                $itinerary_steps[] = array(
	                    'day' => $day_counter++,
	                    'destination' => $embark_port,
	                    'departure' => '18:00',
	                    'arrival' => '-'
	                );
	                
	                // Port days (only actual cruise stops, NOT the embark/disembark ports)
	                foreach ($cruise_destinations as $port) {
	                    $itinerary_steps[] = array(
	                        'day' => $day_counter++,
	                        'destination' => $port['name'],
	                        'departure' => '18:00',
	                        'arrival' => '08:00'
	                    );
	                }
	                
	                // Last day: Disembark port (shown)
	                $itinerary_steps[] = array(
	                    'day' => $day_counter,
	                    'destination' => $disembark_port,
	                    'departure' => '-',
	                    'arrival' => '08:00'
	                );
	                
	                $transformed_cruise['itinerary'] = $itinerary_steps;
	            }
	        }
	        
	        // Extract additional cruise info - check top-level first, then cruiseData
	        // Ship ID (top-level)
	        if (isset($cruise['shipId'])) {
	            $transformed_cruise['shipId'] = $cruise['shipId'];
	            error_log('RBS Travel: Found shipId (top-level): ' . $cruise['shipId']);
	        }
	        
	        // Cruise line (top-level)
	        if (isset($cruise['cruiseLine'])) {
	            $transformed_cruise['cruiseLine'] = $cruise['cruiseLine'];
	            error_log('RBS Travel: Found cruiseLine (top-level): ' . $cruise['cruiseLine']);
	        }
	        
	        // Ship name - try to derive from shipId or look in cruiseData
	        if (isset($cruise['ship'])) {
	            $transformed_cruise['ship'] = $cruise['ship'];
	            error_log('RBS Travel: Found ship (top-level): ' . $cruise['ship']);
	        } elseif (isset($cruise['shipName'])) {
	            $transformed_cruise['ship'] = $cruise['shipName'];
	            error_log('RBS Travel: Found shipName (top-level): ' . $cruise['shipName']);
	        }
	        
	        // Region (top-level)
	        if (isset($cruise['region'])) {
	            $transformed_cruise['region'] = $cruise['region'];
	            error_log('RBS Travel: Found region (top-level): ' . $cruise['region']);
	        }
	        
	        // Also check cruiseData sub-object if it exists (fallback)
	        if (isset($cruise['cruiseData'])) {
	            $cruise_data = $cruise['cruiseData'];
	            error_log('RBS Travel: CruiseData found with keys: ' . implode(', ', array_keys($cruise_data)));
	            
	            // Override with cruiseData if values exist there
	            if (isset($cruise_data['shipName']) && !isset($transformed_cruise['ship'])) {
	                $transformed_cruise['ship'] = $cruise_data['shipName'];
	            }
	            if (isset($cruise_data['cruiseLine']) && !isset($transformed_cruise['cruiseLine'])) {
	                $transformed_cruise['cruiseLine'] = $cruise_data['cruiseLine'];
	            }
	            if (isset($cruise_data['shipId']) && !isset($transformed_cruise['shipId'])) {
	                $transformed_cruise['shipId'] = $cruise_data['shipId'];
	            }
	            if (isset($cruise_data['region']) && !isset($transformed_cruise['region'])) {
	                $transformed_cruise['region'] = $cruise_data['region'];
	            }
	        }
	        
	        $transformed[] = $transformed_cruise;
	    }
	    
	    error_log('RBS Travel: Transformed ' . count($transformed) . ' cruises');
	    return $transformed;
	}
	
	/**
	 * Transform hotel data to ensure images are accessible at the top level
	 */
	private static function transform_hotel_data($hotels) {
	    $transformed = array();
	    
	    foreach ($hotels as $hotel) {
	        $transformed_hotel = $hotel; // Keep all existing data
	        
	        // Copy images from hotelData to top level if not already present
	        if (empty($transformed_hotel['images'])) {
	            // Check hotelData.images FIRST (this is the most common location)
	            if (isset($hotel['hotelData']['images']) && is_array($hotel['hotelData']['images'])) {
	                $transformed_hotel['images'] = $hotel['hotelData']['images'];
	            } elseif (isset($hotel['hotelData']['imageUrls']) && is_array($hotel['hotelData']['imageUrls'])) {
	                $transformed_hotel['images'] = $hotel['hotelData']['imageUrls'];
	            } elseif (isset($hotel['imageUrls']) && is_array($hotel['imageUrls'])) {
	                $transformed_hotel['images'] = $hotel['imageUrls'];
	            } elseif (isset($hotel['images']) && is_array($hotel['images'])) {
	                $transformed_hotel['images'] = $hotel['images'];
	            } elseif (isset($hotel['imageUrl'])) {
	                $transformed_hotel['images'] = array($hotel['imageUrl']);
	            } elseif (isset($hotel['hotelData']['imageUrl'])) {
	                $transformed_hotel['images'] = array($hotel['hotelData']['imageUrl']);
	            }
	        }
	        
	        $transformed[] = $transformed_hotel;
	    }
	    
	    error_log('RBS Travel: Transformed ' . count($transformed) . ' hotels with images');
	    return $transformed;
	}
	
	/**
	 * Transform transport data to ensure proper structure for timeline
	 * Handles IdeaTransportVO with segments for multi-stop flights
	 */
	private static function transform_transport_data($transports) {
	    $transformed = array();
	    
	    foreach ($transports as $transport) {
	        $transformed_transport = array();
	        
	        // Core fields
	        $transformed_transport['id'] = isset($transport['id']) ? $transport['id'] : '';
	        $transformed_transport['day'] = isset($transport['day']) ? intval($transport['day']) : 0;
	        $transformed_transport['transportType'] = isset($transport['transportType']) ? $transport['transportType'] : 'FLIGHT';
	        
	        // Origin and destination
	        $transformed_transport['originCode'] = isset($transport['originCode']) ? $transport['originCode'] : '';
	        $transformed_transport['originDestinationCode'] = isset($transport['originDestinationCode']) ? $transport['originDestinationCode'] : '';
	        $transformed_transport['targetCode'] = isset($transport['targetCode']) ? $transport['targetCode'] : '';
	        $transformed_transport['targetDestinationCode'] = isset($transport['targetDestinationCode']) ? $transport['targetDestinationCode'] : '';
	        
	        // Times and dates
	        $transformed_transport['departureDate'] = isset($transport['departureDate']) ? $transport['departureDate'] : '';
	        $transformed_transport['departureTime'] = isset($transport['departureTime']) ? $transport['departureTime'] : '';
	        $transformed_transport['arrivalDate'] = isset($transport['arrivalDate']) ? $transport['arrivalDate'] : '';
	        $transformed_transport['arrivalTime'] = isset($transport['arrivalTime']) ? $transport['arrivalTime'] : '';
	        $transformed_transport['duration'] = isset($transport['duration']) ? $transport['duration'] : '';
	        
	        // Flight/Transport details
	        $transformed_transport['company'] = isset($transport['company']) ? $transport['company'] : '';
	        $transformed_transport['transportNumber'] = isset($transport['transportNumber']) ? $transport['transportNumber'] : '';
	        $transformed_transport['marketingAirlineCode'] = isset($transport['marketingAirlineCode']) ? $transport['marketingAirlineCode'] : '';
	        $transformed_transport['fare'] = isset($transport['fare']) ? $transport['fare'] : '';
	        $transformed_transport['baggageInfo'] = isset($transport['baggageInfo']) ? $transport['baggageInfo'] : '';
	        $transformed_transport['bookingReference'] = isset($transport['bookingReference']) ? $transport['bookingReference'] : '';
	        
	        // Segments for multi-stop flights
	        $transformed_transport['numberOfSegments'] = isset($transport['numberOfSegments']) ? intval($transport['numberOfSegments']) : 0;
	        $transformed_transport['segments'] = isset($transport['segment']) && is_array($transport['segment']) ? $transport['segment'] : array();
	        
	        // Additional info
	        $transformed_transport['mandatory'] = isset($transport['mandatory']) ? $transport['mandatory'] : false;
	        $transformed_transport['fixed'] = isset($transport['fixed']) ? $transport['fixed'] : false;
	        
	        // Keep original data for reference
	        $transformed_transport['_original'] = $transport;
	        
	        $transformed[] = $transformed_transport;
	    }
	    
	    error_log('RBS Travel: Transformed ' . count($transformed) . ' transports');
	    return $transformed;
	}
	
	
	private static function generate_cruise_port_description($port_name) {
	    $port_lower = strtolower($port_name);
	    
	    // Specific descriptions for common Caribbean ports
	    if (stripos($port_lower, 'coco cay') !== false) {
	        return '<p>Perfect Day at CocoCay is Royal Caribbean\'s private island paradise in the Bahamas. Geniet van kristalhelder water, witte zandstranden en spannende attracties zoals de hoogste waterglijbaan van Noord-Amerika!</p>';
	    }
	    if (stripos($port_lower, 'labadee') !== false) {
	        return '<p>Labadee is een exclusief paradijs in Haïti, alleen toegankelijk voor cruise gasten. Ontspan op prachtige stranden, ervaar de lokale cultuur en geniet van watersporten in deze tropische haven.</p>';
	    }
	    if (stripos($port_lower, 'falmouth') !== false || stripos($port_lower, 'jamaica') !== false) {
	        return '<p>Falmouth, Jamaica biedt een unieke mix van Georgische architectuur, levendige cultuur en prachtige stranden. Bezoek Dunn\'s River Falls, proef authentieke jerk chicken en ervaar de warme Jamaicaanse gastvrijheid.</p>';
	    }
	    if (stripos($port_lower, 'nassau') !== false || stripos($port_lower, 'bahamas') !== false) {
	        return '<p>Nassau, de hoofdstad van de Bahamas, combineert koloniale charme met caribische levendigheid. Verken historische forten, snorkel in helderblauw water en geniet van de beroemde Bahamaanse stranden.</p>';
	    }
	    if (stripos($port_lower, 'cozumel') !== false) {
	        return '<p>Cozumel, een tropisch eiland voor de kust van Mexico, staat bekend om zijn spectaculaire koraalriffen en Mayaanse ruïnes. Perfect voor snorkelen, duiken en het verkennen van de rijke Mexicaanse cultuur.</p>';
	    }
	    if (stripos($port_lower, 'grand cayman') !== false || stripos($port_lower, 'cayman') !== false) {
	        return '<p>Grand Cayman biedt wereldberoemde stranden zoals Seven Mile Beach, kristalhelder water en unieke ervaringen zoals zwemmen met pijlstaartroggen op Stingray City.</p>';
	    }
	    
	    // Generic description for other ports
	    return '<p>Deze prachtige haven is een van de hoogtepunten van uw cruise. Ontdek de lokale cultuur, geniet van de bezienswaardigheden en maak onvergetelijke herinneringen aan deze bijzondere bestemming.</p>';
}


private static function get_travel_details_images($travel_details) {
	    $images = array();
	    // destinations, transfers, hotels, cars, cruises....
	    
	    if (isset($travel_details['destinations']) && is_array($travel_details['destinations'])) {
	        foreach($travel_details['destinations'] as $destination) {
                if (isset($destination['imageUrls']) && is_array($destination['imageUrls'])) {
                    $images = array_merge($images, $destination['imageUrls']);
                }
	        }
	    }
	    
	    if (isset($travel_details['transfers']) && is_array($travel_details['transfers'])) {
	        foreach($travel_details['transfers'] as $transfer) {
                if (isset($transfer['imageUrl'])) {
                    $images[] = $transfer['imageUrl'];
                }
	        }
	    }	    
	    
	    if (isset($travel_details['hotels']) && is_array($travel_details['hotels'])) {
	        foreach($travel_details['hotels'] as $hotel) {
                if (isset($hotel['hotelData']['imageUrls']) && is_array($hotel['hotelData']['imageUrls'])) {
                    $images = array_merge($images, $hotel['hotelData']['imageUrls']);
                }
	        }
	    }	    
	    
	    if (isset($travel_details['cars']) && is_array($travel_details['cars'])) {
	        foreach($travel_details['cars'] as $car) {
                if (isset($car['imageUrl'])) {
                    $images[] = $car['imageUrl'];
                }
	        }
	    }
	    
	    // Cruise images - support both IDEAS and PACKAGES format
	    if (isset($travel_details['cruises']) && is_array($travel_details['cruises'])) {
	        foreach($travel_details['cruises'] as $cruise) {
	            if (isset($cruise['cruiseData']['imageUrls']) && is_array($cruise['cruiseData']['imageUrls'])) {
	                $images = array_merge($images, $cruise['cruiseData']['imageUrls']);
	            }
	            if (isset($cruise['imageUrl'])) {
	                $images[] = $cruise['imageUrl'];
	            }
	        }
	    }
	    
	    // Check for package components format
	    if (isset($travel_details['components']['cruises']) && is_array($travel_details['components']['cruises'])) {
	        foreach($travel_details['components']['cruises'] as $cruise) {
	            if (isset($cruise['cruiseData']['imageUrls']) && is_array($cruise['cruiseData']['imageUrls'])) {
	                $images = array_merge($images, $cruise['cruiseData']['imageUrls']);
	            }
	            if (isset($cruise['imageUrl'])) {
	                $images[] = $cruise['imageUrl'];
	            }
	        }
	    }
	    
	    
	    
	}
	
	
	
	public static function DownloadImage($url) {
	    $upload_dir = wp_upload_dir();
	    $file_data = file_get_contents($url);
	    $filename = basename($url);

	    if (wp_mkdir_p($upload_dir['path'])) {
	      $file = $upload_dir['path'] . '/' . $filename;
	    } else {
	      $file = $upload_dir['basedir'] . '/' . $filename;
	    }

	    file_put_contents($file, $file_data);

	    $wp_filetype = wp_check_filetype($filename, null);

	    $attachment = array(
	      'post_mime_type' => $wp_filetype['type'],
	      'post_title' => sanitize_file_name($filename),
	      'post_content' => '',
	      'post_status' => 'inherit'
	    );

	    return array (
		'attachment' => $attachment,
		'file' => $file
	    );
	}
	
	
	public static function InsertAttachment($attachment, $file) {
	    $attach_id = wp_insert_attachment($attachment, $file);
	    require_once( ABSPATH . 'wp-admin/includes/image.php' );
	    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
	    wp_update_attachment_metadata($attach_id, $attach_data);        

	    return $attach_id;        
	}	
	
    }

}