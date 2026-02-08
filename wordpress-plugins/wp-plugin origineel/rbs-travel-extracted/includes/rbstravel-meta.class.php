<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo:
 * - [ ] add labels to meta keys
 * - [ ]
 */


if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta')) {
    
    
    class RBS_TRAVEL_Meta {
	
	
	public static function GetMetaKeys() {
	    $meta_keys = array(
            'travel_id' => __('ID', 'rbs-travel'),
            'travel_user' => __('User', 'rbs-travel'),
            'travel_email' => __('Email', 'rbs-travel'),
            'travel_title' => __('Title', 'rbs-travel'),
            'travel_large_title' => __('Large Title', 'rbs-travel'),
            'travel_description' => __('Description', 'rbs-travel'),
            'travel_remarks' => __('Remarks', 'rbs-travel'),
            'travel_image_url' => __('Image URL', 'rbs-travel'),
            'travel_creation_date' => __(' Creation Date', 'rbs-travel'),
            'travel_departure_date' => __('Departure Date', 'rbs-travel'),
            'travel_idea_url' => __('Idea URL', 'rbs-travel'),
            'travel_themes' => __('Themes', 'rbs-travel'),
            'travel_per_person_price' => __('Price per Person', 'rbs-travel'),
            'travel_total_price' => __('Total Price', 'rbs-travel'),
            'itinerary' => __('Itinerary', 'rbs-travel'),
            'travel_user_b2c' => __('User B2C', 'rbs-travel'),
            'travel_counters' => __('Counters', 'rbs-travel'),
            
            'travel_destinations' => __('Destinations', 'rbs-travel'),
            //'travel_trip_spots, => __('Trip Spots', 'rbs-travel')',
            //'travel_closed_tours' => __('Closed Tours', 'rbs-travel'),
            'travel_transports' => __('Transports / Transportation', 'rbs-travel'),
            'travel_transfers' => __('Transfers', 'rbs-travel'),
            'travel_hotels' => __('Hotels', 'rbs-travel'),
            //'travel_tickets' => __('Tickets', 'rbs-travel'),
            'travel_cars' => __('(?Rental) Cars', 'rbs-travel'),
            'travel_cruises' => __('Cruises', 'rbs-travel'),
            //'travel_insurances' => __('Insurances', 'rbs-travel'),
            //'travel_manuals' => __('Manuals', 'rbs-travel'),
            //'travel_ride_hailing' => __('?? Ride Hailing??', 'rbs-travel')

            'travel_price_total' => __('Total Price', 'rbs-travel'),
            'travel_price_per_person' => __('Price per Person', 'rbs-travel'),

            'travel_number_of_nights' => __('Number of Nights', 'rbs-travel'),
            'travel_min_persons' => __('Minimum Persons', 'rbs-travel'),
            'travel_max_persons' => __('Maximum Persons', 'rbs-travel')

	    );
	    
	    return apply_filters('rbs_travel_get_meta_keys', $meta_keys);
	}
	
	
	public static function GetMetaFieldTypes() {
	    $meta_field_types = array(
            'travel_id' => array('type' => 'input', 'readonly' => true),
            'travel_user' => array('type' => 'input', 'readonly' => true),
            'travel_email' => array('type' => 'input', 'readonly' => true),
            'travel_title' => array('type' => 'input', 'readonly' => true),
            'travel_large_title' => array('type' => 'input', 'readonly' => true),
            'travel_description' => array('type' => 'textarea', 'readonly' => true),
            'travel_remarks' => array('type' => 'textarea', 'readonly' => true),
            'travel_image_url' => array('type' => 'input', 'readonly' => true),
            'travel_creation_date' => array('type' => 'input', 'readonly' => true),
            'travel_departure_date' => array('type' => 'input', 'readonly' => true),
            'travel_idea_url' => array('type' => 'input', 'readonly' => true),
            'travel_themes' => array('type' => 'textarea', 'readonly' => true),
            'travel_price_per_person' => array('type' => 'textarea', 'readonly' => true),
            'travel_total_price' => array('type' => 'textarea', 'readonly' => true),
            'itinerary' => array('type' => 'input', 'readonly' => true),
            'travel_user_b2c' => array('type' => 'input', 'readonly' => true),
            'travel_counters' => array('type' => 'input', 'readonly' => true),
            
            'travel_destinations' => array('type' => 'textarea', 'readonly' => true),
            //'travel_trip_spots, => __('Trip Spots', 'rbs-travel')',
            //'travel_closed_tours' => __('Closed Tours', 'rbs-travel'),
            'travel_transports' => array('type' => 'textarea', 'readonly' => true),
            'travel_transfers' => array('type' => 'textarea', 'readonly' => true),
            'travel_hotels' => array('type' => 'textarea', 'readonly' => true),
            //'travel_tickets' => __('Tickets', 'rbs-travel'),
            'travel_cars' => array('type' => 'textarea', 'readonly' => true),
            'travel_cruises' => array('type' => 'textarea', 'readonly' => true),
            //'travel_insurances' => __('Insurances', 'rbs-travel'),
            //'travel_manuals' => __('Manuals', 'rbs-travel'),
            //'travel_ride_hailing' => __('?? Ride Hailing??', 'rbs-travel')
	    );

	    return $meta_field_types;
	}
	

    private static function CheckMetaFields($post_id) {
        $meta = array(
            'travel_price_per_person' => get_post_meta($post_id, 'travel_price_per_person', true),
            'travel_per_person_price' => get_post_meta($post_id, 'travel_per_person_price', true),
            'travel_price_total' => get_post_meta($post_id, 'travel_price_total', true),
            'travel_total_price' => get_post_meta($post_id, 'travel_total_price', true),

            'travel_number_of_nights' => get_post_meta($post_id, 'travel_number_of_nights', true),
            'travel_min_persons' => get_post_meta($post_id, 'travel_min_persons', true),
            'travel_max_persons' => get_post_meta($post_id, 'travel_max_persons', true),
            'travel_counters' => get_post_meta($post_id, 'travel_counters', true)
        );

        // echo '<pre>' . print_r($meta, true) . '</pre>';
        // die();

        // Update 'price per person'
        if (isset($meta['travel_price_per_person']) && is_array($meta['travel_price_per_person'])) {
            $meta['travel_per_person_price'] = $meta['travel_price_per_person'];
            $meta['travel_price_per_person'] = $meta['travel_price_per_person']['amount'];

            update_post_meta($post_id, 'travel_per_person_price', $meta['travel_per_person_price']);
            update_post_meta($post_id, 'travel_price_per_person', $meta['travel_price_per_person']);
        } 

        // Update 'total price'
        if (isset($meta['travel_price_total']) && !is_numeric($meta['travel_price_total']) && is_array($meta['travel_total_price'])) {
            update_post_meta($post_id, 'travel_price_total', $meta['travel_total_price']['amount']);
        }          

        // Update 'number of nights'
        if (isset($meta['travel_number_of_nights']) && !is_numeric($meta['travel_number_of_nights']) && is_array($meta['travel_counters'])) {
            update_post_meta($post_id, 'travel_number_of_nights', $meta['travel_counters']['hotelNights']);
        }
        
        // Update 'min person'
        if (isset($meta['travel_min_persons']) && $meta['travel_min_persons'] == 0 && is_array($meta['travel_counters'])) {
            $number_of_persons = isset($meta['travel_counters']['adults']) ? $meta['travel_counters']['adults'] : 0;
            $number_of_persons += isset($meta['travel_counters']['children']) ? $meta['travel_counters']['children'] : 0;
            update_post_meta($post_id, 'travel_min_persons', $number_of_persons);
        } 
        
        // Update 'max pereson'
        if (isset($meta['travel_max_persons']) && $meta['travel_max_persons'] == 0 && is_array($meta['travel_counters'])) {
            $number_of_persons = isset($meta['travel_counters']['adults']) ? $meta['travel_counters']['adults'] : 0;
            $number_of_persons += isset($meta['travel_counters']['children']) ? $meta['travel_counters']['children'] : 0;            
            update_post_meta($post_id, 'travel_max_persons', $number_of_persons);
        }         
    }
	
	public static function GetMetaFields($post_id = null, $fields = array()) {
	    if ($post_id === null) {
            global $post;
            $post_id = $post->ID;
	    }

        self::CheckMetaFields($post_id);

	    $return = array();
        if (is_array($fields) && count($fields) === 0) {
            foreach(self::GetMetaKeys() as $meta_key => $meta_label) {
                $return[$meta_key] = get_post_meta($post_id, $meta_key, true);
            }
            return $return;
        }


        if (is_array($fields)) {
            $meta_field_keys = self::GetMetaKeys();
            foreach($fields as $field) {
                if (isset($meta_field_keys[$field])) {
                    if (count($fields) === 1) {
                        $return = get_post_meta($post_id, $field, true);
                    } else {
                        $return[$field] = get_post_meta($post_id, $field, true);
                    }                    
                }
            }

            return $return;
        }


        // echo '<pre>' . print_r($post_id) . '</pre>';
		// echo '<pre>' . print_r($fields) . '</pre>';
		// die('yyyy');

	    

        // return $return;
        return false;
	    
	}
	
	
    }
    
}