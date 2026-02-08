<?php
/**
 * RBS Travel Shortcodes
 * Provides shortcodes for displaying travel content
 * 
 * @package RBS_Travel
 * @since 5.9.0
 */

if (!defined('ABSPATH')) exit;

class RBS_TRAVEL_Shortcodes {
    
    public function __construct() {
        add_shortcode('uitgelichte_reizen', array($this, 'featured_travels_shortcode'));
        add_shortcode('featured_travels', array($this, 'featured_travels_shortcode'));
        add_shortcode('reis_zoeken', array($this, 'travel_search_widget_shortcode'));
        add_shortcode('travel_search', array($this, 'travel_search_widget_shortcode'));
    }
    
    /**
     * Featured Travels Shortcode
     * Usage: [uitgelichte_reizen ids="123,456,789"] or [featured_travels ids="123,456,789"]
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function featured_travels_shortcode($atts) {
        $atts = shortcode_atts(array(
            'ids' => '',
            'columns' => 3,
            'title' => '',
            'show_price' => 'yes',
            'show_days' => 'yes',
            'show_button' => 'yes',
            'button_text' => 'Bekijk reis',
        ), $atts, 'uitgelichte_reizen');
        
        // Parse IDs
        $raw_ids = array_filter(array_map('trim', explode(',', $atts['ids'])));
        
        if (empty($raw_ids)) {
            if (current_user_can('edit_posts')) {
                return '<div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; margin: 20px 0;">
                    <strong>⚠️ Uitgelichte Reizen:</strong> Geen reis IDs opgegeven.<br>
                    <small>Gebruik: <code>[uitgelichte_reizen ids="123,456,789"]</code></small>
                </div>';
            }
            return '';
        }
        
        // Check if IDs are Travel Compositor IDs (8+ digits) or WordPress post IDs
        $first_id = intval($raw_ids[0]);
        $use_tc_ids = ($first_id > 10000000); // TC IDs are typically 8+ digits
        
        $travels = array();
        
        if ($use_tc_ids) {
            // Search by Travel Compositor ID stored in travel_id meta
            foreach ($raw_ids as $tc_id) {
                $tc_id = trim($tc_id);
                
                // Try direct meta key first
                $found = get_posts(array(
                    'post_type' => 'rbs-travel-idea',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        'relation' => 'OR',
                        array(
                            'key' => 'travel_id',
                            'value' => $tc_id,
                            'compare' => '='
                        ),
                        array(
                            'key' => 'travel_id',
                            'value' => intval($tc_id),
                            'compare' => '='
                        ),
                    )
                ));
                
                if (!empty($found)) {
                    $travels[] = $found[0];
                }
            }
        } else {
            // Use WordPress post IDs
            $ids = array_filter(array_map('intval', $raw_ids));
            if (!empty($ids)) {
                $travels = get_posts(array(
                    'post_type' => 'rbs-travel-idea',
                    'post__in' => $ids,
                    'orderby' => 'post__in',
                    'posts_per_page' => count($ids),
                    'post_status' => 'publish',
                ));
            }
        }
        
        if (empty($travels)) {
            if (current_user_can('edit_posts')) {
                $id_type = $use_tc_ids ? 'Travel Compositor' : 'WordPress';
                
                // Debug: Check if any travels exist with these IDs
                $debug_info = '';
                if ($use_tc_ids) {
                    global $wpdb;
                    $first_tc_id = trim($raw_ids[0]);
                    $meta_check = $wpdb->get_results($wpdb->prepare(
                        "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'travel_id' AND meta_value LIKE %s LIMIT 5",
                        '%' . $first_tc_id . '%'
                    ));
                    if (!empty($meta_check)) {
                        $debug_info = '<br><small>Debug: Gevonden meta waarden: ';
                        foreach ($meta_check as $row) {
                            $debug_info .= 'Post ' . $row->post_id . ' = "' . $row->meta_value . '", ';
                        }
                        $debug_info .= '</small>';
                    } else {
                        // Check what travel_id values exist
                        $sample = $wpdb->get_results(
                            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'travel_id' LIMIT 3"
                        );
                        if (!empty($sample)) {
                            $debug_info = '<br><small>Debug: Voorbeeld travel_id waarden in database: ';
                            foreach ($sample as $row) {
                                $debug_info .= '"' . $row->meta_value . '", ';
                            }
                            $debug_info .= '</small>';
                        }
                    }
                }
                
                return '<div style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; margin: 20px 0;">
                    <strong>⚠️ Uitgelichte Reizen:</strong> Geen reizen gevonden met ' . $id_type . ' IDs: ' . esc_html($atts['ids']) . '<br>
                    <small>Controleer of de reizen zijn geïmporteerd en gepubliceerd.</small>' . $debug_info . '
                </div>';
            }
            return '';
        }
        
        $columns = intval($atts['columns']);
        if ($columns < 1) $columns = 1;
        if ($columns > 4) $columns = 4;
        
        ob_start();
        ?>
        <style>
        .rbs-featured-travels {
            margin: 40px 0;
        }
        .rbs-featured-travels-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            color: #1a1a2e;
        }
        .rbs-featured-travels-grid {
            display: grid;
            grid-template-columns: repeat(<?php echo $columns; ?>, 1fr);
            gap: 30px;
        }
        @media (max-width: 992px) {
            .rbs-featured-travels-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .rbs-featured-travels-grid {
                grid-template-columns: 1fr;
            }
        }
        .rbs-travel-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .rbs-travel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        .rbs-travel-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .rbs-travel-card-content {
            padding: 20px;
        }
        .rbs-travel-card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #1a1a2e;
            line-height: 1.3;
        }
        .rbs-travel-card-title a {
            color: inherit;
            text-decoration: none;
        }
        .rbs-travel-card-title a:hover {
            color: #2271b1;
        }
        .rbs-travel-card-location {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .rbs-travel-card-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .rbs-travel-card-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: #444;
        }
        .rbs-travel-card-meta-item strong {
            font-weight: 700;
            color: #1a1a2e;
        }
        .rbs-travel-card-price {
            font-size: 22px;
            font-weight: 700;
            color: #059669;
            margin-bottom: 15px;
        }
        .rbs-travel-card-price small {
            font-size: 14px;
            font-weight: 400;
            color: #666;
        }
        .rbs-travel-card-button {
            display: inline-block;
            background: linear-gradient(135deg, #2271b1 0%, #135e96 100%);
            color: #fff !important;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }
        .rbs-travel-card-button:hover {
            background: linear-gradient(135deg, #135e96 0%, #0a4570 100%);
            transform: translateY(-2px);
        }
        </style>
        
        <div class="rbs-featured-travels">
            <?php if (!empty($atts['title'])): ?>
                <h2 class="rbs-featured-travels-title"><?php echo esc_html($atts['title']); ?></h2>
            <?php endif; ?>
            
            <div class="rbs-featured-travels-grid">
                <?php foreach ($travels as $travel): 
                    $meta = get_post_meta($travel->ID, 'rbstravel_travel_meta_fields', true);
                    $price = isset($meta['travel_price_per_person']) ? floatval($meta['travel_price_per_person']) : 0;
                    $days = isset($meta['travel_number_of_nights']) ? intval($meta['travel_number_of_nights']) : 0;
                    
                    // Get image
                    $image = '';
                    $hero_image = get_post_meta($travel->ID, 'travel_hero_image', true);
                    if (!empty($hero_image)) {
                        $image = $hero_image;
                    } elseif (!empty($meta['travel_images'][0])) {
                        $image = $meta['travel_images'][0];
                    } elseif (!empty($meta['travel_destinations'][0]['imageUrls'][0])) {
                        $image = $meta['travel_destinations'][0]['imageUrls'][0];
                    }
                    
                    // Get location
                    $location = '';
                    if (!empty($meta['travel_destinations'][0]['name'])) {
                        $location = $meta['travel_destinations'][0]['name'];
                    }
                    
                    $title = !empty($meta['travel_title']) ? $meta['travel_title'] : $travel->post_title;
                ?>
                <div class="rbs-travel-card">
                    <?php if ($image): ?>
                        <a href="<?php echo get_permalink($travel->ID); ?>">
                            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="rbs-travel-card-image">
                        </a>
                    <?php endif; ?>
                    
                    <div class="rbs-travel-card-content">
                        <h3 class="rbs-travel-card-title">
                            <a href="<?php echo get_permalink($travel->ID); ?>"><?php echo esc_html($title); ?></a>
                        </h3>
                        
                        <?php if ($location): ?>
                            <div class="rbs-travel-card-location">📍 <?php echo esc_html($location); ?></div>
                        <?php endif; ?>
                        
                        <div class="rbs-travel-card-meta">
                            <?php if ($atts['show_days'] === 'yes' && $days > 0): ?>
                                <div class="rbs-travel-card-meta-item">
                                    🗓️ <strong><?php echo $days; ?></strong> dagen
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($atts['show_price'] === 'yes' && $price > 0): ?>
                            <div class="rbs-travel-card-price">
                                €<?php echo number_format($price, 0, ',', '.'); ?> <small>p.p.</small>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($atts['show_button'] === 'yes'): ?>
                            <a href="<?php echo get_permalink($travel->ID); ?>" class="rbs-travel-card-button">
                                <?php echo esc_html($atts['button_text']); ?> →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Travel Search Widget Shortcode
     * Usage: [reis_zoeken] or [travel_search]
     * 
     * Creates a search/filter widget that redirects to the travel archive page
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function travel_search_widget_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Vind jouw droomreis',
            'subtitle' => 'Zoek op bestemming, thema of reisduur',
            'button_text' => 'Zoek Reizen',
            'show_country' => 'yes',
            'show_theme' => 'yes',
            'show_days' => 'yes',
            'style' => 'card', // card, inline, minimal
            'redirect_url' => '', // Custom URL, defaults to travel archive
        ), $atts, 'reis_zoeken');
        
        // Get all published travels to extract countries and themes
        $travels = get_posts(array(
            'post_type' => 'rbs-travel-idea',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));
        
        $countries = array();
        $themes = array();
        $max_days = 0;
        
        foreach ($travels as $travel) {
            $meta = get_post_meta($travel->ID, 'rbstravel_travel_meta_fields', true);
            
            // Extract countries from destinations
            if (!empty($meta['travel_destinations']) && is_array($meta['travel_destinations'])) {
                foreach ($meta['travel_destinations'] as $dest) {
                    if (!empty($dest['country'])) {
                        $country = trim($dest['country']);
                        if (!in_array($country, $countries)) {
                            $countries[] = $country;
                        }
                    }
                }
            }
            
            // Extract themes/tour types from taxonomy
            $travel_themes = wp_get_post_terms($travel->ID, 'travel-type', array('fields' => 'names'));
            if (!is_wp_error($travel_themes) && !empty($travel_themes)) {
                foreach ($travel_themes as $theme) {
                    if (!in_array($theme, $themes)) {
                        $themes[] = $theme;
                    }
                }
            }
            
            // Get max days
            if (!empty($meta['travel_number_of_nights'])) {
                $days = intval($meta['travel_number_of_nights']);
                if ($days > $max_days) {
                    $max_days = $days;
                }
            }
        }
        
        // Sort alphabetically
        sort($countries);
        sort($themes);
        
        // Get redirect URL
        $redirect_url = $atts['redirect_url'];
        if (empty($redirect_url)) {
            $redirect_url = get_post_type_archive_link('rbs-travel-idea');
            if (!$redirect_url) {
                $redirect_url = home_url('/reizen/');
            }
        }
        
        $style = $atts['style'];
        
        ob_start();
        ?>
        <style>
        .rbs-search-widget {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        /* Card Style */
        .rbs-search-widget.style-card {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 20px;
            padding: 30px;
            color: #fff;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
        }
        .rbs-search-widget.style-card .rbs-search-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #fff;
        }
        .rbs-search-widget.style-card .rbs-search-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin: 0 0 25px 0;
        }
        .rbs-search-widget.style-card .rbs-search-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .rbs-search-widget.style-card .rbs-search-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .rbs-search-widget.style-card .rbs-search-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }
        .rbs-search-widget.style-card .rbs-search-select {
            padding: 14px 16px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            background: rgba(255,255,255,0.95);
            color: #1a1a2e;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
        }
        .rbs-search-widget.style-card .rbs-search-select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
        }
        .rbs-search-widget.style-card .rbs-search-button {
            margin-top: 10px;
            padding: 16px 24px;
            background: #fff;
            color: #1d4ed8;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .rbs-search-widget.style-card .rbs-search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        
        /* Inline Style */
        .rbs-search-widget.style-inline {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .rbs-search-widget.style-inline .rbs-search-title {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 20px 0;
            color: #1a1a2e;
            text-align: center;
        }
        .rbs-search-widget.style-inline .rbs-search-subtitle {
            display: none;
        }
        .rbs-search-widget.style-inline .rbs-search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
        }
        .rbs-search-widget.style-inline .rbs-search-field {
            flex: 1;
            min-width: 150px;
        }
        .rbs-search-widget.style-inline .rbs-search-label {
            font-size: 11px;
            font-weight: 600;
            color: #666;
            margin-bottom: 6px;
            display: block;
        }
        .rbs-search-widget.style-inline .rbs-search-select {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            cursor: pointer;
        }
        .rbs-search-widget.style-inline .rbs-search-select:focus {
            outline: none;
            border-color: #3b82f6;
        }
        .rbs-search-widget.style-inline .rbs-search-button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        .rbs-search-widget.style-inline .rbs-search-button:hover {
            opacity: 0.9;
        }
        
        /* Minimal Style */
        .rbs-search-widget.style-minimal {
            padding: 0;
        }
        .rbs-search-widget.style-minimal .rbs-search-title,
        .rbs-search-widget.style-minimal .rbs-search-subtitle {
            display: none;
        }
        .rbs-search-widget.style-minimal .rbs-search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .rbs-search-widget.style-minimal .rbs-search-field {
            flex: 1;
            min-width: 120px;
        }
        .rbs-search-widget.style-minimal .rbs-search-label {
            display: none;
        }
        .rbs-search-widget.style-minimal .rbs-search-select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .rbs-search-widget.style-minimal .rbs-search-button {
            padding: 10px 20px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        
        @media (max-width: 600px) {
            .rbs-search-widget.style-inline .rbs-search-form {
                flex-direction: column;
            }
            .rbs-search-widget.style-inline .rbs-search-field {
                width: 100%;
            }
        }
        </style>
        
        <div class="rbs-search-widget style-<?php echo esc_attr($style); ?>">
            <?php if (!empty($atts['title'])): ?>
                <h3 class="rbs-search-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <?php if (!empty($atts['subtitle'])): ?>
                <p class="rbs-search-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
            <?php endif; ?>
            
            <form class="rbs-search-form" action="<?php echo esc_url($redirect_url); ?>" method="get">
                
                <?php if ($atts['show_country'] === 'yes' && !empty($countries)): ?>
                <div class="rbs-search-field">
                    <label class="rbs-search-label" for="rbs-country">🌍 Bestemming</label>
                    <select name="land" id="rbs-country" class="rbs-search-select">
                        <option value="">Alle bestemmingen</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?php echo esc_attr($country); ?>"><?php echo esc_html($country); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_theme'] === 'yes' && !empty($themes)): ?>
                <div class="rbs-search-field">
                    <label class="rbs-search-label" for="rbs-theme">🎯 Reistype</label>
                    <select name="type" id="rbs-theme" class="rbs-search-select">
                        <option value="">Alle reistypes</option>
                        <?php foreach ($themes as $theme): ?>
                            <option value="<?php echo esc_attr($theme); ?>"><?php echo esc_html($theme); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <?php if ($atts['show_days'] === 'yes' && $max_days > 0): ?>
                <div class="rbs-search-field">
                    <label class="rbs-search-label" for="rbs-days">📅 Reisduur</label>
                    <select name="dagen" id="rbs-days" class="rbs-search-select">
                        <option value="">Alle reisduren</option>
                        <option value="1-7">1-7 dagen</option>
                        <option value="8-14">8-14 dagen</option>
                        <option value="15-21">15-21 dagen</option>
                        <?php if ($max_days > 21): ?>
                            <option value="22+">22+ dagen</option>
                        <?php endif; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <button type="submit" class="rbs-search-button">
                    🔍 <?php echo esc_html($atts['button_text']); ?>
                </button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}

new RBS_TRAVEL_Shortcodes();
