<?php
defined('RBS_TRAVEL') or die();

use RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta;
use RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings;

if (!function_exists('\rbstravel_template_loader')) {
    
    function rbstravel_template_loader($template, $vars = array(), $plugin_name = null, $args = array()) {
        // die('aaaaaa');
        // var_dump(RBS_TRAVEL_PLUGIN_PATH_TEMPLATES);
        // die('rbstravelfunctions');
        
        $add_wrapper = isset($args['add_wrapper']) ? $args['add_wrapper'] : true;
        $wrapper = isset($args['wrapper']) && is_array($args['wrapper']) ? $args['wrapper'] : array('<div class="wrap">', '</div>');
        $suffix = isset($args['suffix']) ? $args['suffix'] : '.html';
        
        $overrride = false;
        if ($plugin_name !== null) {            
            $template_file = get_stylesheet_directory() . DIRECTORY_SEPARATOR . $plugin_name . DIRECTORY_SEPARATOR . str_replace('frontend/', '', $template) . '.html.php';            
            if (file_exists($template_file)) {
                $overrride = true;
            }            
        } 
        
        if ($overrride === false) {
            $template_file = RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . $template . $suffix . '.php';
            //echo $template_file;
            //die('rbstmplateloader');
            //$template_file = RBS_FSB_PATH . 'templates' . DIRECTORY_SEPARATOR . $template . $suffix . '.php';
            //RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'frontend' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . $template_part;
            if (!file_exists($template_file)) {
                if (WP_DEBUG) {
                    wp_die('Cannot find template: ' . $template);
                }
                wp_die();
            }
        }
        
        if (count($vars) != 0) {
            extract($vars);
        }
        
        ob_start();
        require $template_file;
        $html = ob_get_clean();
        
//        if (WP_DEBUG) {
//            $output .= 'Available variables: ' . print_r($vars, true);
//        }
        $output = array();
        if ($add_wrapper) {
            $output[] = $wrapper[0];
        }
        $output[] = $html;
        
        if ($add_wrapper) {
            $output[] = $wrapper[1];
        }
        
        return implode("\r\n", $output);
    }        
}


if (!function_exists('rbstravel_template_part')) {
    function rbstravel_template_part($name) {		    //, $args = array()
        $template_part = $name . '.html.php';	//apply_filter('rbstravel_template_part_name', $name . '.html.php');
        
        if (file_exists(get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'rbstravel' . $template_part)) {
            require get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'rbstravel' . $template_part;
        } else {
            require RBS_TRAVEL_PLUGIN_PATH_TEMPLATES . 'frontend' . DIRECTORY_SEPARATOR . 'parts' . DIRECTORY_SEPARATOR . $template_part;
        }
        
        return;
    }
}


if (!function_exists('rbstravel_get_ideas')) {

    function rbstravel_get_ideas($args = array(), $json_encode = false, $terms=array(), $pricerange=array()) {
        // $terms = array(
        //     'Europa',
        //     'Afrika'
        // );

        
        $query = array(
            'post_type' => 'rbs-travel-idea',
            'post_status' => isset($args['post_status']) ? $args['post_status'] : 'publish',
            'posts_per_page' => -1
        );

        if ( count($terms)>0 ) {
            $tax_query = array(
                array (
                    'taxonomy' => 'location',
                    'field' => 'slug',
                    'terms' => $terms
                )
                );
                
            $query['tax_query'] = $tax_query;   
            
       
        };
        // echo '<pre>' . print_r($pricerange) . '</pre>';
        if (isset($pricerange['minprice']) && isset($pricerange['maxprice'])) {

            $meta_query=array(
                'meta_query' => array(
                    array(
                        'key' =>  'tempprice',      //'travel_price_total',
                        'value' => array($pricerange['minprice'],$pricerange['maxprice']),
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    )
                )
            );   
          
           // $query['meta_query'] = $meta_query; 
        }


        

        $result = new WP_Query($query);

        // echo '<pre>' . print_r($query, true) . '</pre>';
        // echo '<pre>' . print_r($result->posts, true) . '</pre>';
        // die('rbstravel_get_ideas');


        $return = array();
        if (isset($result->posts) && is_array($result->posts) && count($result->posts) !== 0) {
            foreach($result->posts as $post) {
                $return[] = rbstravel_get_single_idea($post->ID);
         
            }
        }

        // rbs-travel-idea
       // echo '<pre>' . print_r($return[0], true) . '</pre>';
        //get_the_terms(10,'locations');
        //die('rbstravel_get_ideas');
        //return $json_encode ? json_encode($return) : $return;
        return $return;
    }
}


if (!function_exists('rbstravel_get_single_idea')) {
    function rbstravel_get_single_idea($post_id) {
        $post = get_post($post_id);
        $meta = RBS_TRAVEL_Meta::GetMetaFields($post_id);

        $return = array(
            'post' => $post,
            'idea' => $meta,
            'post_id' => $post->ID,
            'terms' => array(
                'types' => wp_get_post_terms($post_id, 'tour-type', array('fields' => 'names')),
                'themes' => wp_get_post_terms($post_id, 'tour-theme', array('fields' => 'names')),
                'services' => wp_get_post_terms($post_id, 'tour-service', array('fields' => 'names'))
            )
        );
        return $return;      
    }
}

if (!function_exists('rbstravel_get_contact_form')) {
    function rbstravel_get_contact_form($echo = true) {
        $shortcode = RBS_TRAVEL_Settings::GetSetting('contact_form', null);
        $output = do_shortcode($shortcode);

        if ($echo) {
            echo $output;
            return;
        }
        return $output;
    }
}


if (!function_exists('rbstravel_get_map_image')){
    function rbstravel_get_map_image($post_id, $echo = true) {
        $map_image = get_post_meta($post_id, 'rbstravel_map_image', true);

        if (is_string($map_image) && strlen($map_image) === 0) {
            if (WP_DEBUG) {
                echo '<div class="ovr-right-map"><p style="color: red;">TEMP: No map image available</p></div>';
            }
            return false;
        }

        if ($echo) {
            $html = '<img src="' . $map_image . '" class="rbstravel-map-image rbstravel-map-image--' . $post_id . ' ovr-right-map" />';
            echo $html;
            return true;
        }

        // if ($echo) {
        //     $html = $map_image;
        //     echo $html;
        //     return true;
        // }        
        return $map_image;
    }
}





if (!function_exists('rbstravel_get_header')) {
    function rbstravel_get_header() {
        $theme_dir = get_stylesheet_directory();
        $header_file = $theme_dir . '/header.php';
        
        if ( file_exists( $header_file ) ) {
            get_header();
            return;
        } 
        
        $template = 'frontend' . DIRECTORY_SEPARATOR . 'header';
        $args = array(
            'add_wrapper' => false,                
        );
        echo rbstravel_template_loader($template, array(), null, $args);  

    }
}

if (!function_exists('rbstravel_get_footer')) {
    function rbstravel_get_footer() {
        $theme_dir = get_stylesheet_directory();
        $header_file = $theme_dir . '/footer.php';
        
        if ( file_exists( $header_file ) ) {
            get_header();
            return;
        } 

        $template = 'frontend' . DIRECTORY_SEPARATOR . 'footer';
        $args = array(
            'add_wrapper' => false,                
        );
        echo rbstravel_template_loader($template, array(), null, $args);          
    }
}



if (!function_exists('rbstravel_get_filter_html')) {
    function rbstravel_get_filter_html($taxonomy) {
        
        $args_continents = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false, // Set to true if you want to exclude terms with no posts
            'parent' => 0  
        );
        $terms_contintents = get_terms($args_continents);
        //echo '<pre>' . print_r($terms, true) . '</pre>';
        $html = array();
        $html[] = '<ul class="test_filter">';
        foreach($terms_contintents as $continent) {
            $html[] = '<li><label><input type="checkbox" name="destination[continent]" value="' . $continent->term_id . '">' . $continent->name . '</label></li>';
            
            $args_countries = array(
                'taxonomy'   => $taxonomy,
                'hide_empty' => false, // Set to true if you want to exclude terms with no posts
                'parent' => $continent->term_id  
            );
            $terms_countries = get_terms($args_countries);

            $html[] = '<li>';
            $html[] = '<ul  class="test_filter">';
            foreach($terms_countries as $country) {
                $html[] = '<li><label><input type="checkbox" name="destination[country]" value="' . $country->term_id . '">' . $country->name . '</label></li>';
                
                $args_locations = array(
                    'taxonomy'   => $taxonomy,
                    'hide_empty' => false, // Set to true if you want to exclude terms with no posts
                    'parent' => $country->term_id  
                );
                $terms_locations = get_terms($args_locations);

                $html[] = '<li>';
                $html[] = '<ul  class="test_filter">';
                foreach($terms_locations as $location) {
                    $html[] = '<li><label><input type="checkbox" name="destination[location]" value="' . $location->term_id . '">' . $location->name . '</label></li>';
                }    
                $html[] = '</ul>';
                $html[] = '</li>';
            }

            $html[] = '</ul>';
            $html[] = '</li>';

        }
        $html[] = '</ul>';
        //<li><label><input type="checkbox" name="destination" value="IJsland">IJsland</label></li>



        echo implode("\r\n", $html);
    }
}