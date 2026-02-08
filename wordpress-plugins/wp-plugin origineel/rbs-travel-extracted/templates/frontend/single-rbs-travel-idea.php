<?php
/**
 * Template: Travel Idea Detail Page - Clean Rebuild
 * Version: 5.0.0
 */

if (!defined('ABSPATH')) exit;

// Get travel data using the correct method (same as old template)
global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

// Get brand colors from settings
$primary_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('primary_color', '#4a6cf7');
$secondary_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('secondary_color', '#6366f1');

// Get TC base URL from settings for cruise itinerary links
$tc_base_url = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('tc_base_url', 'https://online.travelcompositor.com/');

// Get button links from settings
$travel_url = isset($travel_meta_fields['travel_idea_url']) ? $travel_meta_fields['travel_idea_url'] : '';
$btn_book_url = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('btn_book_url', '{travel_url}');
$btn_info_url = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('btn_info_url', '/contact');
$btn_customize_url = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('btn_customize_url', '{travel_url}');

// Frontend layout settings
$itinerary_layout = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('itinerary_layout', 'cards');
$route_button_side = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('route_button_side', 'right');

// Sidebar widget settings
$widget_enabled = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('sidebar_widget_enabled', '1');
$widget_title = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('sidebar_widget_title', '');
$widget_content = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('sidebar_widget_content', '');

// Replace {travel_url} placeholder with actual travel URL
$btn_book_url = str_replace('{travel_url}', $travel_url, $btn_book_url);
$btn_customize_url = str_replace('{travel_url}', $travel_url, $btn_customize_url);

// Get hero style and image from post meta
$hero_style = get_post_meta(get_the_ID(), 'travel_hero_style', true);
$hero_image = get_post_meta(get_the_ID(), 'travel_hero_image', true);
$hero_youtube = get_post_meta(get_the_ID(), 'travel_hero_youtube', true);
$hero_youtube_delay = get_post_meta(get_the_ID(), 'travel_hero_youtube_delay', true);
if ($hero_youtube_delay === '' || $hero_youtube_delay === false) $hero_youtube_delay = 0;

// Default hero style
if (empty($hero_style)) {
    $hero_style = 'single';
}

// Extract YouTube video ID if URL is provided
$youtube_video_id = '';
if (!empty($hero_youtube)) {
    $youtube_video_id = $hero_youtube;
    // Extract video ID from full URL if needed
    if (strpos($hero_youtube, 'youtube.com') !== false || strpos($hero_youtube, 'youtu.be') !== false) {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $hero_youtube, $matches);
        if (!empty($matches[1])) {
            $youtube_video_id = $matches[1];
        }
    }
}

// Get main image - first try hero_image from settings, then fallback to other sources
$main_image = '';
if (!empty($hero_image)) {
    $main_image = $hero_image;
}
// Try travel_images array
if (empty($main_image) && !empty($travel_meta_fields['travel_images']) && is_array($travel_meta_fields['travel_images'])) {
    $main_image = $travel_meta_fields['travel_images'][0];
}
// Try first destination image
if (empty($main_image) && !empty($travel_meta_fields['travel_destinations']) && is_array($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $dest) {
        if (!empty($dest['imageUrls']) && is_array($dest['imageUrls']) && !empty($dest['imageUrls'][0])) {
            $main_image = $dest['imageUrls'][0];
            break;
        }
    }
}
// Try first hotel image
if (empty($main_image) && !empty($travel_meta_fields['travel_hotels']) && is_array($travel_meta_fields['travel_hotels'])) {
    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
        if (!empty($hotel['images']) && is_array($hotel['images'])) {
            foreach ($hotel['images'] as $img) {
                $imgUrl = is_array($img) ? (isset($img['url']) ? $img['url'] : '') : $img;
                if (!empty($imgUrl)) {
                    $main_image = $imgUrl;
                    break 2;
                }
            }
        }
    }
}

// Collect all images for grid style
$all_images = array();
if (!empty($travel_meta_fields['travel_images']) && is_array($travel_meta_fields['travel_images'])) {
    $all_images = array_merge($all_images, $travel_meta_fields['travel_images']);
}
if (!empty($travel_meta_fields['travel_destinations']) && is_array($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $dest) {
        if (!empty($dest['imageUrls']) && is_array($dest['imageUrls'])) {
            $all_images = array_merge($all_images, $dest['imageUrls']);
        }
    }
}
$all_images = array_slice(array_unique($all_images), 0, 4); // Get first 4 unique images for grid

// Get location from first destination
$location = '';
if (!empty($travel_meta_fields['travel_destinations']) && is_array($travel_meta_fields['travel_destinations'])) {
    if (!empty($travel_meta_fields['travel_destinations'][0]['name'])) {
        $location = $travel_meta_fields['travel_destinations'][0]['name'];
    }
}

// Build destinations for map
$map_destinations = array();
if (!empty($travel_meta_fields['travel_destinations']) && is_array($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $dest) {
        $lat = isset($dest['geolocation']['latitude']) ? floatval($dest['geolocation']['latitude']) : 0;
        $lng = isset($dest['geolocation']['longitude']) ? floatval($dest['geolocation']['longitude']) : 0;
        if ($lat != 0 && $lng != 0) {
            $map_destinations[] = array(
                'name' => isset($dest['name']) ? $dest['name'] : '',
                'lat' => $lat,
                'lng' => $lng,
                'image' => isset($dest['imageUrls'][0]) ? $dest['imageUrls'][0] : ''
            );
        }
    }
}

rbstravel_get_header();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* ========================================
           CSS RESET & VARIABLES
           ======================================== */
        :root {
            --primary: <?php echo esc_attr($primary_color); ?>;
            --secondary: <?php echo esc_attr($secondary_color); ?>;
            --text: #374151;
            --text-light: #6b7280;
            --bg: #f9fafb;
            --white: #ffffff;
            --border: #e5e7eb;
            --radius: 12px;
        }
        
        * { box-sizing: border-box; }
        
        /* Remove default spacing to allow hero to sit at top */
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        /* Reset WordPress theme content area padding/margin */
        #content,
        .site-content,
        .content-area,
        main,
        article {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* ========================================
           HERO SECTION - Multiple Styles
           ======================================== */
        
        /* Style: Single Photo */
        .travel-hero-single {
            width: 100%;
            height: 450px;
            background: url('<?php echo esc_url($main_image); ?>') center/cover;
            position: relative;
        }
        
        .travel-hero-single::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(transparent, rgba(0,0,0,0.3));
        }
        
        /* Style: Wide (shorter height) */
        .travel-hero-wide {
            width: 100%;
            height: 300px;
            background: url('<?php echo esc_url($main_image); ?>') center/cover;
            position: relative;
        }
        
        /* Style: YouTube Video */
        .travel-hero-youtube {
            width: 100%;
            height: 56.25vw; /* 16:9 aspect ratio based on viewport width */
            max-height: 600px;
            min-height: 400px;
            background: #000;
            position: relative;
            overflow: hidden;
        }
        
        .travel-hero-youtube iframe,
        .travel-hero-youtube #youtubePlayer {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 177.78vh; /* 16:9 ratio */
            height: 100vh;
            min-width: 100%;
            min-height: 100%;
            transform: translate(-50%, -50%);
            border: none;
            pointer-events: none;
        }
        
        /* Style: Grid (4 photos) */
        .travel-hero-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            max-height: 350px;
            overflow: hidden;
        }
        
        .travel-hero-grid .grid-item {
            aspect-ratio: 1;
            overflow: hidden;
            position: relative;
        }
        
        .travel-hero-grid .grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .travel-hero-grid .grid-item:hover img {
            transform: scale(1.05);
        }
        
        /* Style: Slideshow */
        .travel-hero-slideshow {
            width: 100%;
            height: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .travel-hero-slideshow .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .travel-hero-slideshow .slide.active {
            opacity: 1;
        }
        
        .travel-hero-slideshow .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .travel-hero-slideshow .slide-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .travel-hero-slideshow .slide-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .travel-hero-slideshow .slide-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        @media (max-width: 768px) {
            .travel-hero-single {
                height: 300px;
            }
            .travel-hero-wide {
                height: 200px;
            }
            .travel-hero-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .travel-hero-slideshow {
                height: 280px;
            }
        }
        
        .travel-title-bar {
            background: white;
            padding: 25px 0;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .travel-title-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .travel-title-left h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: var(--text);
        }
        
        .travel-title-location {
            color: #999999;
            font-size: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .travel-title-right {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .route-icon-btn {
            width: 44px;
            height: 44px;
            min-width: 44px;
            border-radius: 999px;
            border: 1px solid var(--primary);
            background: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            line-height: 0;
            flex: 0 0 44px;
            appearance: none;
            -webkit-appearance: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .route-icon-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(16,24,40,0.12);
        }

        .route-icon-btn svg {
            width: 22px;
            height: 22px;
            display: block;
            fill: none;
            stroke: #ffffff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        
        .travel-stat {
            text-align: center;
        }
        
        .travel-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .travel-stat-label {
            font-size: 13px;
            color: var(--text-light);
        }
        
        .travel-stat.days .travel-stat-value {
            color: var(--text);
        }
        
        @media (max-width: 768px) {
            .travel-hero-photo {
                height: 300px;
            }
            .travel-title-container {
                flex-direction: column;
                align-items: flex-start;
            }
            .travel-title-left h1 {
                font-size: 26px;
            }
        }
        
        /* ========================================
           MAIN LAYOUT - TWO COLUMNS
           ======================================== */
        .travel-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .travel-container.layout-slider {
            max-width: 100%;
            gap: 0;
        }

        .travel-container.layout-slider .travel-main {
            max-width: 1200px;
            margin: 0 auto;
        }

        .travel-intro-grid {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .travel-intro-grid {
                grid-template-columns: 1fr;
            }
        }
        
        .travel-main {
            flex: 1;
            min-width: 0;
        }
        
        .travel-sidebar {
            width: 350px;
            flex-shrink: 0;
        }
        
        .travel-sidebar-inner {
            position: sticky;
            top: 20px;
        }

        .travel-bottom-section {
            margin-top: 30px;
        }

        .travel-bottom-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .travel-bottom-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 900px) {
            .travel-container {
                flex-direction: column;
            }
            .travel-sidebar {
                width: 100%;
                order: -1;
            }
            .travel-sidebar-inner {
                position: relative;
                top: 0;
            }
        }
        
        /* ========================================
           INTRO SECTION
           ======================================== */
        .travel-intro {
            margin-bottom: 20px;
        }
        
        .travel-intro h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 15px 0;
        }
        
        .travel-intro-text {
            color: var(--text);
            line-height: 1.7;
        }
        
        .travel-intro-text p {
            margin: 0 0 12px 0;
        }
        
        .travel-intro-text p:last-child {
            margin-bottom: 0;
        }
        
        /* ========================================
           BOOKING CARD (SIDEBAR)
           ======================================== */
        .booking-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .booking-card h3 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 20px 0;
            color: var(--text);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 6px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
        }
        
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 10px;
            transition: opacity 0.2s;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            margin-bottom: 8px;
            transition: background 0.2s;
        }
        
        .btn-secondary:hover {
            background: var(--bg);
        }
        
        /* ========================================
           DAY-BY-DAY PROGRAM
           ======================================== */
        .tour-plan {
            margin-top: 0;
        }
        
        .tour-plan h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 20px 0;
        }
        
        /* ========================================
           ROUTE MAP SLIDING PANEL
           ======================================== */
        .route-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 9998;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .route-overlay.active {
            opacity: 1;
        }
        
        .route-panel {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80vw;
            max-width: 900px;
            height: 100vh;
            background: var(--white);
            z-index: 9999;
            box-shadow: -10px 0 40px rgba(0,0,0,0.3);
            transition: right 0.4s ease;
            display: flex;
            flex-direction: column;
        }
        
        .route-panel.active {
            right: 0;
        }
        
        .route-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--primary);
            color: white;
            flex-shrink: 0;
        }
        
        .route-panel-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        
        .route-panel-close {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 24px;
            padding: 5px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .route-panel-close:hover {
            background: rgba(255,255,255,0.3);
        }
        
        .route-panel-body {
            flex: 1;
            position: relative;
            min-height: 0;
            overflow: hidden;
        }
        
        #routeMap {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100% !important;
            height: 100% !important;
        }
        
        .route-legend {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
            z-index: 1000;
            font-size: 13px;
        }
        
        .route-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }
        
        .route-legend-item:last-child {
            margin-bottom: 0;
        }
        
        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--primary);
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        
        .legend-line {
            width: 20px;
            height: 3px;
            background: var(--primary);
            border-radius: 2px;
        }
        
        /* ========================================
           DETAIL PANEL (for hotels, etc)
           ======================================== */
        .detail-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .detail-overlay.active {
            opacity: 1;
        }
        
        .detail-panel {
            position: fixed;
            top: 0;
            right: -100%;
            width: 500px;
            max-width: 90vw;
            height: 100vh;
            background: var(--white);
            z-index: 9999;
            box-shadow: -5px 0 30px rgba(0,0,0,0.2);
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .detail-panel.active {
            right: 0;
        }
        
        .detail-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        
        .detail-panel-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
        }
        
        .detail-panel-close {
            background: none;
            border: none;
            font-size: 28px;
            color: var(--text-light);
            cursor: pointer;
            padding: 0 5px;
        }
        
        .detail-panel-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .detail-section {
            margin-bottom: 20px;
        }
        
        .detail-section h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 10px 0;
        }
        
        .detail-gallery {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .detail-gallery img {
            width: 100%;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
        }
        
        .detail-map {
            width: 100%;
            height: 250px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        /* Photo Gallery - Full width in sliding panel */
        .photo-gallery {
            position: relative;
            margin: -20px -20px 15px -20px; /* Negative margin to extend to panel edges */
            width: calc(100% + 40px);
        }
        
        .gallery-main-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 0; /* No border radius for full-width effect */
        }
        
        .gallery-nav {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            pointer-events: none;
        }
        
        .gallery-nav-btn {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            pointer-events: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .gallery-nav-btn:hover {
            background: white;
        }
        
        .gallery-counter {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .gallery-thumbnails {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 10px 0;
        }
        
        .gallery-thumbnail {
            width: 60px;
            height: 45px;
            object-fit: cover;
            border-radius: 4px;
            cursor: pointer;
            opacity: 0.6;
            transition: opacity 0.2s;
            flex-shrink: 0;
        }
        
        .gallery-thumbnail:hover,
        .gallery-thumbnail.active {
            opacity: 1;
        }
        
        .gallery-thumbnail.active {
            border: 2px solid var(--primary);
        }
        
        /* Info Items */
        .info-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .info-item-row {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--text);
        }
        
        /* Facilities Grid */
        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }
        
        .facility-item {
            padding: 8px;
            background: #f5f5f5;
            border-radius: 6px;
            font-size: 13px;
        }
        
        /* Cruise Ports */
        .cruise-ports {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .port-card {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid var(--primary);
        }
        
        .detail-description {
            line-height: 1.6;
            color: var(--text);
        }
        
        /* Leaflet popup styling - larger, no scrollbar */
        .leaflet-popup-content {
            margin: 15px 20px !important;
            max-height: none !important;
            overflow: visible !important;
            width: 280px !important;
            min-width: 280px !important;
        }
        
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            overflow: hidden !important;
            min-width: 300px;
        }
        
        .leaflet-popup-content img {
            display: block;
            margin-top: 10px;
            border-radius: 8px;
            width: 100%;
            max-width: 260px;
            height: auto;
        }
        
        .leaflet-popup-content strong {
            font-size: 16px;
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<!-- HERO SECTION - Dynamic based on style -->
<?php if ($hero_style === 'slideshow' && count($all_images) >= 2): ?>
    <div class="travel-hero-slideshow" id="heroSlideshow">
        <?php foreach ($all_images as $index => $img): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo esc_url($img); ?>" alt="Travel photo">
            </div>
        <?php endforeach; ?>
        <div class="slide-nav">
            <?php foreach ($all_images as $index => $img): ?>
                <span class="slide-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></span>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    (function() {
        var slideshow = document.getElementById('heroSlideshow');
        if (!slideshow) return;
        var slides = slideshow.querySelectorAll('.slide');
        var dots = slideshow.querySelectorAll('.slide-dot');
        var current = 0;
        var total = slides.length;
        
        function showSlide(index) {
            slides.forEach(function(s, i) { 
                s.classList.toggle('active', i === index); 
            });
            dots.forEach(function(d, i) { 
                d.classList.toggle('active', i === index); 
            });
            current = index;
        }
        
        // Auto advance every 4 seconds
        setInterval(function() {
            showSlide((current + 1) % total);
        }, 4000);
        
        // Click on dots
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                showSlide(parseInt(this.dataset.index));
            });
        });
    })();
    </script>
<?php elseif ($hero_style === 'grid' && count($all_images) >= 4): ?>
    <div class="travel-hero-grid">
        <?php foreach (array_slice($all_images, 0, 4) as $img): ?>
            <div class="grid-item">
                <img src="<?php echo esc_url($img); ?>" alt="Travel photo">
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif ($hero_style === 'wide'): ?>
    <div class="travel-hero-wide"></div>
<?php elseif ($hero_style === 'youtube' && !empty($youtube_video_id)): ?>
    <div class="travel-hero-youtube" id="youtubeHeroContainer">
        <div id="youtubePlayer"></div>
    </div>
    <script>
    // YouTube IFrame API
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    
    var ytPlayer;
    var ytDelay = <?php echo intval($hero_youtube_delay); ?>;
    var ytVideoId = '<?php echo esc_js($youtube_video_id); ?>';
    
    function onYouTubeIframeAPIReady() {
        // Start video immediately - use 'start' parameter to skip to specific second
        createYouTubePlayer();
    }
    
    function createYouTubePlayer() {
        ytPlayer = new YT.Player('youtubePlayer', {
            videoId: ytVideoId,
            playerVars: {
                'autoplay': 1,
                'mute': 1,
                'controls': 0,
                'showinfo': 0,
                'rel': 0,
                'modestbranding': 1,
                'iv_load_policy': 3,
                'disablekb': 1,
                'fs': 0,
                'playsinline': 1,
                'loop': 1,
                'playlist': ytVideoId,
                'start': ytDelay // Start video at this second (e.g., 10 = start at 0:10)
            },
            events: {
                'onReady': function(event) {
                    event.target.playVideo();
                },
                'onStateChange': function(event) {
                    // Loop video when it ends
                    if (event.data === YT.PlayerState.ENDED) {
                        event.target.seekTo(ytDelay); // Loop back to start position
                        event.target.playVideo();
                    }
                }
            }
        });
    }
    </script>
<?php else: ?>
    <div class="travel-hero-single"></div>
<?php endif; ?>

<!-- TITLE BAR -->
<div class="travel-title-bar">
    <div class="travel-title-container">
        <div class="travel-title-left">
            <h1><?php echo esc_html(!empty($travel_meta_fields['travel_title']) ? $travel_meta_fields['travel_title'] : get_the_title()); ?></h1>
            <?php if ($location): ?>
                <div class="travel-title-location">📍 <?php echo esc_html($location); ?></div>
            <?php endif; ?>
            <?php if (count($map_destinations) >= 2 && $route_button_side === 'left'): ?>
                <button type="button" class="route-icon-btn" onclick="openRouteMap()" aria-label="Bekijk de route" title="Bekijk de route" style="margin-top: 12px;">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 18l-2 2V4l2-2 6 2 6-2 2 2v16l-2 2-6-2-6 2Z" />
                        <path d="M12 4v16" />
                        <path d="M18 4v16" />
                    </svg>
                </button>
            <?php endif; ?>
        </div>
        <div class="travel-title-right">
            <div class="travel-stat">
                <div class="travel-stat-value">€<?php echo number_format(floatval($travel_meta_fields['travel_price_per_person']), 0, ',', '.'); ?></div>
                <div class="travel-stat-label">per persoon</div>
            </div>
            <div class="travel-stat days">
                <div class="travel-stat-value"><?php echo intval($travel_meta_fields['travel_number_of_nights']); ?></div>
                <div class="travel-stat-label">dagen</div>
            </div>
            <?php if (count($map_destinations) >= 2 && $route_button_side === 'right'): ?>
                <button type="button" class="route-icon-btn" onclick="openRouteMap()" aria-label="Bekijk de route" title="Bekijk de route">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 18l-2 2V4l2-2 6 2 6-2 2 2v16l-2 2-6-2-6 2Z" />
                        <path d="M12 4v16" />
                        <path d="M18 4v16" />
                    </svg>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="travel-container <?php echo $itinerary_layout === 'slider' ? 'layout-slider' : ''; ?>">
    <!-- LEFT: Main Content -->
    <div class="travel-main">
        <!-- Intro -->
        <section class="travel-intro">
            <?php if ($itinerary_layout === 'slider'): ?>
                <div class="travel-intro-grid">
                    <div>
                        <h2>Ontdek deze Reis</h2>
                        <div class="travel-intro-text">
                            <?php the_content(); ?>
                        </div>
                    </div>

                    <div>
                        <div class="booking-card">
                            <!-- Price & Days Summary -->
                            <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                                <div style="text-align: center;">
                                    <div style="font-size: 24px; font-weight: 700; color: var(--primary);">€<?php echo number_format(floatval($travel_meta_fields['travel_price_per_person']), 0, ',', '.'); ?></div>
                                    <div style="font-size: 12px; color: var(--text-light);">per persoon</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 24px; font-weight: 700; color: var(--text);"><?php echo intval($travel_meta_fields['travel_number_of_nights']); ?></div>
                                    <div style="font-size: 12px; color: var(--text-light);">dagen</div>
                                </div>
                            </div>

                            <h3 style="margin-top: 0;">Boek deze Reis</h3>
                            <form>
                                <div class="form-group">
                                    <label class="form-label">Vertrekdatum:</label>
                                    <input type="date" class="form-input" value="<?php echo esc_attr($travel_meta_fields['travel_departure_date']); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Aantal personen:</label>
                                    <select class="form-input">
                                        <option>1 persoon</option>
                                        <option>2 personen</option>
                                        <option>3 personen</option>
                                        <option>4 personen</option>
                                    </select>
                                </div>
                                <button type="button" class="btn-primary" onclick="window.location.href='<?php echo esc_url($btn_book_url); ?>'">
                                    Boek Nu
                                </button>
                                <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo esc_url($btn_info_url); ?>'">Info Aanvragen</button>
                                <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo esc_url($btn_customize_url); ?>'">Reis Aanpassen</button>
                            </form>

                            <?php if ($widget_enabled === '1' && !empty($widget_content)): ?>
                            <div class="sidebar-widget" style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-top: 20px;">
                                <?php if (!empty($widget_title)): ?>
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: var(--heading-color, #1f2937);"><?php echo esc_html($widget_title); ?></h3>
                                <?php endif; ?>
                                <div class="sidebar-widget-content">
                                    <?php echo do_shortcode(wpautop($widget_content)); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <h2>Ontdek deze Reis</h2>
                <div class="travel-intro-text">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- Day-by-day Program -->
        <?php if ($itinerary_layout === 'slider'): ?>
            <?php include(dirname(__FILE__) . '/partials/tour-plan-slider.php'); ?>
        <?php else: ?>
            <?php include(dirname(__FILE__) . '/partials/tour-plan-grouped.php'); ?>
        <?php endif; ?>

        
    </div>
    
    <!-- RIGHT: Sidebar -->
    <?php if ($itinerary_layout !== 'slider'): ?>
    <aside class="travel-sidebar">
        <div class="travel-sidebar-inner">
            <!-- Route Map Preview Card -->
            <?php if (count($map_destinations) >= 2 && $itinerary_layout !== 'slider'): ?>
            <div class="booking-card route-preview-card" onclick="openRouteMap()" style="cursor: pointer; padding: 0; overflow: hidden; margin-bottom: 20px;">
                <div id="miniRouteMap" style="width: 100%; height: 180px;"></div>
                <div style="padding: 16px 14px; text-align: center; background: var(--primary); color: white; font-weight: 600; margin-top: 15px;">
                    🗺️ Bekijk de Route
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Booking Card -->
            <div class="booking-card">
                <!-- Price & Days Summary -->
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                    <div style="text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--primary);">€<?php echo number_format(floatval($travel_meta_fields['travel_price_per_person']), 0, ',', '.'); ?></div>
                        <div style="font-size: 12px; color: var(--text-light);">per persoon</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 24px; font-weight: 700; color: var(--text);"><?php echo intval($travel_meta_fields['travel_number_of_nights']); ?></div>
                        <div style="font-size: 12px; color: var(--text-light);">dagen</div>
                    </div>
                </div>
                
                <h3 style="margin-top: 0;">Boek deze Reis</h3>
                <form>
                    <div class="form-group">
                        <label class="form-label">Vertrekdatum:</label>
                        <input type="date" class="form-input" value="<?php echo esc_attr($travel_meta_fields['travel_departure_date']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Aantal personen:</label>
                        <select class="form-input">
                            <option>1 persoon</option>
                            <option>2 personen</option>
                            <option>3 personen</option>
                            <option>4 personen</option>
                        </select>
                    </div>
                    <button type="button" class="btn-primary" onclick="window.location.href='<?php echo esc_url($btn_book_url); ?>'">
                        Boek Nu
                    </button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo esc_url($btn_info_url); ?>'">Info Aanvragen</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='<?php echo esc_url($btn_customize_url); ?>'">Reis Aanpassen</button>
                </form>
            </div>
            
            <!-- Sidebar Widget -->
            <?php if ($widget_enabled === '1' && !empty($widget_content)):
            ?>
            <div class="sidebar-widget" style="background: #f8fafc; border-radius: 12px; padding: 20px; margin-top: 20px;">
                <?php if (!empty($widget_title)): ?>
                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: var(--heading-color, #1f2937);"><?php echo esc_html($widget_title); ?></h3>
                <?php endif; ?>
                <div class="sidebar-widget-content">
                    <?php echo do_shortcode(wpautop($widget_content)); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </aside>
    <?php endif; ?>
</div>

<!-- DETAIL PANEL -->
<div class="detail-overlay" id="detailOverlay" onclick="closeDetailPanel()"></div>
<div class="detail-panel" id="detailPanel">
    <div class="detail-panel-header">
        <div class="detail-panel-title" id="detailTitle"></div>
        <button class="detail-panel-close" onclick="closeDetailPanel()">×</button>
    </div>
    <div class="detail-panel-body" id="detailBody"></div>
</div>

<!-- ROUTE MAP PANEL -->
<div class="route-overlay" id="routeOverlay" onclick="closeRouteMap()"></div>
<div class="route-panel" id="routePanel">
    <div class="route-panel-header">
        <h3>🗺️ Route Overzicht</h3>
        <button class="route-panel-close" onclick="closeRouteMap()">×</button>
    </div>
    <div class="route-panel-body">
        <div id="routeMap"></div>
        <div class="route-legend">
            <div class="route-legend-item"><span class="legend-dot"></span> Bestemming</div>
            <div class="route-legend-item"><span class="legend-line"></span> Route</div>
        </div>
    </div>
</div>

<script>
// Map destinations data
var mapDestinations = <?php echo json_encode($map_destinations); ?>;
var routeMapInstance = null;
var tcBaseUrl = '<?php echo esc_js(rtrim($tc_base_url, '/')); ?>';

// ========================================
// ROUTE MAP FUNCTIONS
// ========================================
function openRouteMap() {
    document.getElementById('routeOverlay').style.display = 'block';
    setTimeout(function() {
        document.getElementById('routeOverlay').classList.add('active');
        document.getElementById('routePanel').classList.add('active');
        setTimeout(initRouteMap, 150);
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeRouteMap() {
    document.getElementById('routeOverlay').classList.remove('active');
    document.getElementById('routePanel').classList.remove('active');
    setTimeout(function() {
        document.getElementById('routeOverlay').style.display = 'none';
    }, 300);
    document.body.style.overflow = '';
}

// Mini map in sidebar
var miniMapInstance = null;
function initMiniMap() {
    var container = document.getElementById('miniRouteMap');
    if (!container || !mapDestinations || mapDestinations.length === 0) return;
    
    // Get bounds
    var bounds = [];
    mapDestinations.forEach(function(dest) {
        if (dest.lat && dest.lng) {
            bounds.push([dest.lat, dest.lng]);
        }
    });
    
    if (bounds.length === 0) return;
    
    // Create map (no zoom controls, not interactive)
    miniMapInstance = L.map('miniRouteMap', {
        zoomControl: false,
        dragging: false,
        scrollWheelZoom: false,
        doubleClickZoom: false,
        boxZoom: false,
        keyboard: false,
        tap: false,
        touchZoom: false
    });
    
    // Add tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(miniMapInstance);
    
    // Fit bounds
    miniMapInstance.fitBounds(bounds, { padding: [20, 20] });
    
    // Add route line
    if (bounds.length > 1) {
        L.polyline(bounds, {
            color: '<?php echo esc_js($primary_color); ?>',
            weight: 3,
            opacity: 0.8
        }).addTo(miniMapInstance);
    }
    
    // Add small markers
    mapDestinations.forEach(function(dest, index) {
        if (dest.lat && dest.lng) {
            var icon = L.divIcon({
                className: 'mini-marker',
                html: '<div style="background:<?php echo esc_js($primary_color); ?>;color:white;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:9px;border:2px solid white;box-shadow:0 1px 3px rgba(0,0,0,0.3);">' + (index + 1) + '</div>',
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            });
            L.marker([dest.lat, dest.lng], { icon: icon }).addTo(miniMapInstance);
        }
    });
}

// Initialize mini map on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(initMiniMap, 100);
});

function initRouteMap() {
    var container = document.getElementById('routeMap');
    if (!container || !mapDestinations || mapDestinations.length === 0) return;
    
    // Clean up existing
    if (routeMapInstance) {
        routeMapInstance.remove();
        routeMapInstance = null;
    }
    
    // Get bounds
    var bounds = [];
    mapDestinations.forEach(function(dest) {
        if (dest.lat && dest.lng) {
            bounds.push([dest.lat, dest.lng]);
        }
    });
    
    if (bounds.length === 0) return;
    
    // Create map
    routeMapInstance = L.map('routeMap');
    
    // Add tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap'
    }).addTo(routeMapInstance);
    
    // Fit bounds
    routeMapInstance.fitBounds(bounds, { padding: [50, 50] });
    
    // Add route line
    if (bounds.length > 1) {
        L.polyline(bounds, {
            color: '<?php echo esc_js($primary_color); ?>',
            weight: 4,
            opacity: 0.8,
            dashArray: '10, 10'
        }).addTo(routeMapInstance);
    }
    
    // Add markers
    mapDestinations.forEach(function(dest, index) {
        if (dest.lat && dest.lng) {
            var icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="background:<?php echo esc_js($primary_color); ?>;color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:12px;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + (index + 1) + '</div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            
            var marker = L.marker([dest.lat, dest.lng], { icon: icon }).addTo(routeMapInstance);
            
            var popup = '<strong>' + (index + 1) + '. ' + (dest.name || 'Bestemming') + '</strong>';
            if (dest.image) {
                popup += '<br><img src="' + dest.image + '" style="width:200px;height:120px;object-fit:cover;border-radius:6px;margin-top:10px;">';
            }
            marker.bindPopup(popup, { maxWidth: 250, minWidth: 200 });
        }
    });
    
    // Fix size
    setTimeout(function() {
        routeMapInstance.invalidateSize();
    }, 100);
}

// ========================================
// SHOW DETAIL FUNCTIONS (called from buttons)
// ========================================
function showDestinationDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-dest'));
    var html = generateDestinationDetail(data);
    openDetailPanel(data.name || 'Bestemming', html);
}

function showHotelDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-hotel'));
    var html = generateHotelDetail(data);
    openDetailPanel(data.name || 'Hotel', html);
}

function showCruiseDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-cruise'));
    var html = generateCruiseDetail(data);
    openDetailPanel((data.ship || data.shipName || 'Cruise'), html);
}

// Generate destination detail HTML
function generateDestinationDetail(data) {
    var html = '';
    
    // Gallery
    if (data.imageUrls && data.imageUrls.length > 0) {
        html += '<div class="detail-section"><div class="detail-gallery">';
        data.imageUrls.slice(0, 6).forEach(function(img) {
            html += '<img src="' + img + '" alt="' + (data.name || '') + '">';
        });
        html += '</div></div>';
    }
    
    // Description
    if (data.description) {
        html += '<div class="detail-section">';
        html += '<p>' + data.description + '</p>';
        html += '</div>';
    }
    
    // Info
    html += '<div class="detail-section">';
    if (data.country) html += '<p>🌍 ' + data.country + '</p>';
    if (data.fromDay && data.toDay) {
        var nights = data.toDay - data.fromDay;
        if (nights > 0) html += '<p>🌙 ' + nights + ' ' + (nights == 1 ? 'nacht' : 'nachten') + '</p>';
    }
    html += '</div>';
    
    // Map
    var lat = data.geolocation && data.geolocation.latitude;
    var lng = data.geolocation && data.geolocation.longitude;
    if (lat && lng) {
        html += '<div class="detail-section"><h3>📍 Locatie</h3>';
        html += '<div id="hotelMapContainer" class="detail-map"></div></div>';
        window.pendingHotelMap = { lat: lat, lng: lng, name: data.name || 'Bestemming' };
    }
    
    return html;
}

// ========================================
// DETAIL PANEL FUNCTIONS
// ========================================
function openDetailPanel(title, html) {
    document.getElementById('detailTitle').textContent = title;
    document.getElementById('detailBody').innerHTML = html;
    document.getElementById('detailOverlay').style.display = 'block';
    setTimeout(function() {
        document.getElementById('detailOverlay').classList.add('active');
        document.getElementById('detailPanel').classList.add('active');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    // Init hotel map if needed - check for container after DOM is updated
    setTimeout(function() {
        var mapContainer = document.getElementById('hotelMapContainer');
        if (mapContainer) {
            if (window.pendingHotelMap) {
                initHotelMap(window.pendingHotelMap.lat, window.pendingHotelMap.lng, window.pendingHotelMap.name);
                window.pendingHotelMap = null;
            } else {
                // Show placeholder if no coordinates
                mapContainer.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f5f5f5;color:#999;font-size:14px;border-radius:8px;">📍 Locatie wordt geladen...</div>';
            }
        }
    }, 400);
}

function closeDetailPanel() {
    document.getElementById('detailOverlay').classList.remove('active');
    document.getElementById('detailPanel').classList.remove('active');
    setTimeout(function() {
        document.getElementById('detailOverlay').style.display = 'none';
    }, 300);
    document.body.style.overflow = '';
}

// Hotel map in detail panel
var hotelMapInstance = null;
function initHotelMap(lat, lng, name) {
    var container = document.getElementById('hotelMapContainer');
    if (!container) {
        console.log('Hotel map container not found');
        return;
    }
    
    // If no coordinates, show placeholder
    if (!lat || !lng) {
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f5f5f5;color:#999;font-size:14px;">📍 Locatie niet beschikbaar</div>';
        return;
    }
    
    if (hotelMapInstance) {
        hotelMapInstance.remove();
        hotelMapInstance = null;
    }
    
    try {
        hotelMapInstance = L.map('hotelMapContainer').setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        }).addTo(hotelMapInstance);
        
        L.marker([lat, lng]).addTo(hotelMapInstance).bindPopup(name).openPopup();
        
        setTimeout(function() {
            if (hotelMapInstance) {
                hotelMapInstance.invalidateSize();
            }
        }, 100);
    } catch (e) {
        console.log('Error initializing hotel map:', e);
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f5f5f5;color:#999;font-size:14px;">📍 Kaart kon niet geladen worden</div>';
    }
}

// Photo Gallery - Global state
window.currentGalleryIndex = 0;
window.currentGalleryImages = [];

function initGallery(images) {
    window.currentGalleryImages = images;
    window.currentGalleryIndex = 0;
    updateGalleryImage();
}

function galleryNext() {
    window.currentGalleryIndex = (window.currentGalleryIndex + 1) % window.currentGalleryImages.length;
    updateGalleryImage();
    return false;
}

function galleryPrev() {
    window.currentGalleryIndex = (window.currentGalleryIndex - 1 + window.currentGalleryImages.length) % window.currentGalleryImages.length;
    updateGalleryImage();
    return false;
}

function galleryGoTo(index) {
    window.currentGalleryIndex = index;
    updateGalleryImage();
    return false;
}

function updateGalleryImage() {
    var img = document.getElementById('galleryMainImage');
    var counter = document.getElementById('galleryCounter');
    
    if (!img || window.currentGalleryImages.length === 0) return;
    
    if (window.currentGalleryImages[window.currentGalleryIndex]) {
        img.src = window.currentGalleryImages[window.currentGalleryIndex];
        if (counter) {
            counter.textContent = (window.currentGalleryIndex + 1) + ' / ' + window.currentGalleryImages.length;
        }
    }
    
    // Update thumbnails
    var thumbs = document.querySelectorAll('.gallery-thumbnail');
    thumbs.forEach(function(thumb, index) {
        thumb.classList.toggle('active', index === window.currentGalleryIndex);
    });
}

// Generate hotel detail HTML
function generateHotelDetail(data) {
    var html = '';
    var imageUrls = [];
    
    // Photo Gallery
    if (data.images && data.images.length > 0) {
        imageUrls = data.images.map(function(img) { return typeof img === 'string' ? img : img.url; });
        html += '<div class="photo-gallery">';
        html += '<img id="galleryMainImage" src="' + imageUrls[0] + '" alt="Hotel photo" class="gallery-main-image">';
        html += '<div class="gallery-nav">';
        html += '<button class="gallery-nav-btn" onclick="galleryPrev(); return false;">‹</button>';
        html += '<button class="gallery-nav-btn" onclick="galleryNext(); return false;">›</button>';
        html += '</div>';
        html += '<div class="gallery-counter" id="galleryCounter">1 / ' + imageUrls.length + '</div>';
        html += '</div>';
        html += '<div class="gallery-thumbnails">';
        for (var i = 0; i < Math.min(10, imageUrls.length); i++) {
            html += '<img src="' + imageUrls[i] + '" class="gallery-thumbnail ' + (i === 0 ? 'active' : '') + '" onclick="galleryGoTo(' + i + '); return false;" alt="Thumbnail">';
        }
        html += '</div>';
    }
    
    // Description
    if (data.description) {
        html += '<div class="detail-section"><h3>📝 Beschrijving</h3><div class="detail-description">' + data.description + '</div></div>';
    }
    
    // Hotel Info with Map instead of address
    html += '<div class="detail-section"><h3>ℹ️ Hotel Informatie</h3>';
    
    // Map first (instead of address text) - try multiple sources for coordinates
    var lat = null;
    var lng = null;
    
    // Try direct latitude/longitude
    if (data.latitude && data.longitude) {
        lat = data.latitude;
        lng = data.longitude;
    }
    // Try destination object
    else if (data.destination && data.destination.latitude && data.destination.longitude) {
        lat = data.destination.latitude;
        lng = data.destination.longitude;
    }
    // Try geolocation object
    else if (data.geolocation && data.geolocation.latitude && data.geolocation.longitude) {
        lat = data.geolocation.latitude;
        lng = data.geolocation.longitude;
    }
    // Try destination.geolocation
    else if (data.destination && data.destination.geolocation && data.destination.geolocation.latitude) {
        lat = data.destination.geolocation.latitude;
        lng = data.destination.geolocation.longitude;
    }
    
    // Always show map container, even if no coordinates (will show placeholder)
    html += '<div id="hotelMapContainer" class="detail-map" style="margin-bottom: 15px;"></div>';
    if (lat && lng) {
        window.pendingHotelMap = { lat: parseFloat(lat), lng: parseFloat(lng), name: data.name || 'Hotel' };
    } else {
        // No coordinates - show message in map container
        window.pendingHotelMap = null;
    }
    
    html += '<div class="info-items">';
    // Show city/location name only (not country)
    if (data.destination && data.destination.name) {
        // Extract just the city name (before comma if present)
        var locationName = data.destination.name;
        if (locationName.indexOf(',') > -1) {
            locationName = locationName.split(',')[0].trim();
        }
        html += '<div class="info-item-row"><span class="info-label">📍 Locatie:</span> ' + locationName + '</div>';
    }
    if (data.stars) {
        html += '<div class="info-item-row"><span class="info-label">⭐ Sterren:</span> ' + '⭐'.repeat(data.stars) + '</div>';
    }
    // Room type - check multiple sources
    var roomType = data.roomType || data.selectedRoom || data.room || 
                   (data.hotelData && (data.hotelData.roomType || data.hotelData.selectedRoom)) || '';
    if (roomType) {
        html += '<div class="info-item-row"><span class="info-label">🛏️ Kamertype:</span> ' + roomType + '</div>';
    }
    // Meal plan - check multiple sources
    var mealPlan = data.mealPlan || data.board || data.boardType || 
                   (data.hotelData && (data.hotelData.mealPlan || data.hotelData.board)) || '';
    if (mealPlan) {
        html += '<div class="info-item-row"><span class="info-label">🍽️ Maaltijden:</span> ' + mealPlan + '</div>';
    }
    if (data.nights) {
        html += '<div class="info-item-row"><span class="info-label">🌙 Nachten:</span> ' + data.nights + '</div>';
    }
    html += '</div></div>';
    
    // Facilities
    if (data.facilities && data.facilities.otherFacilities && data.facilities.otherFacilities.length > 0) {
        var facilities = data.facilities.otherFacilities.slice(0, 12);
        html += '<div class="detail-section"><h3>🏊 Faciliteiten</h3><div class="facilities-grid">';
        facilities.forEach(function(fac) {
            html += '<div class="facility-item">✓ ' + fac.description + '</div>';
        });
        html += '</div></div>';
    }
    
    // Initialize gallery after returning
    if (imageUrls.length > 0) {
        setTimeout(function() { initGallery(imageUrls); }, 100);
    }
    
    return html;
}

// Generate cruise detail HTML
function generateCruiseDetail(data) {
    var html = '';
    var imageUrls = [];
    
    // Photo Gallery for Cruise
    if (data.images && data.images.length > 0) {
        imageUrls = data.images.map(function(img) { return typeof img === 'string' ? img : img.url; });
        html += '<div class="photo-gallery">';
        html += '<img id="galleryMainImage" src="' + imageUrls[0] + '" alt="Cruise photo" class="gallery-main-image">';
        html += '<div class="gallery-nav">';
        html += '<button class="gallery-nav-btn" onclick="galleryPrev(); return false;">‹</button>';
        html += '<button class="gallery-nav-btn" onclick="galleryNext(); return false;">›</button>';
        html += '</div>';
        html += '<div class="gallery-counter" id="galleryCounter">1 / ' + imageUrls.length + '</div>';
        html += '</div>';
        html += '<div class="gallery-thumbnails">';
        for (var i = 0; i < Math.min(10, imageUrls.length); i++) {
            html += '<img src="' + imageUrls[i] + '" class="gallery-thumbnail ' + (i === 0 ? 'active' : '') + '" onclick="galleryGoTo(' + i + '); return false;" alt="Thumbnail">';
        }
        html += '</div>';
    }
    
    // Cruise Basic Info
    html += '<div class="detail-section"><h3>📝 Cruise Informatie</h3><div class="info-items">';
    if (data.cruiseLine) {
        html += '<div class="info-item-row"><span class="info-label">Cruise Lijn:</span> ' + data.cruiseLine + '</div>';
    }
    if (data.ship) {
        html += '<div class="info-item-row"><span class="info-label">Schip:</span> ' + data.ship + '</div>';
    }
    if (data.embarkDate) {
        html += '<div class="info-item-row"><span class="info-label">Vertrek:</span> ' + new Date(data.embarkDate).toLocaleDateString('nl-NL', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) + '</div>';
    }
    if (data.disembarkDate) {
        html += '<div class="info-item-row"><span class="info-label">Aankomst:</span> ' + new Date(data.disembarkDate).toLocaleDateString('nl-NL', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) + '</div>';
    }
    if (data.nights) {
        html += '<div class="info-item-row"><span class="info-label">Nachten:</span> ' + data.nights + ' nachten</div>';
    }
    if (data.embarkPort) {
        html += '<div class="info-item-row"><span class="info-label">Vertrek Haven:</span> ' + data.embarkPort + '</div>';
    }
    if (data.disembarkPort) {
        html += '<div class="info-item-row"><span class="info-label">Aankomst Haven:</span> ' + data.disembarkPort + '</div>';
    }
    html += '</div></div>';
    
    // Cabin Information
    if (data.cabin || data.group || data.category) {
        html += '<div class="detail-section"><h3>🛏️ Hut Informatie</h3><div class="info-items">';
        if (data.cabin) {
            html += '<div class="info-item-row"><span class="info-label">Hut Type:</span> ' + data.cabin + '</div>';
        }
        if (data.group) {
            html += '<div class="info-item-row"><span class="info-label">Categorie:</span> ' + data.group + '</div>';
        }
        if (data.category) {
            html += '<div class="info-item-row"><span class="info-label">Cabine:</span> ' + data.category + '</div>';
        }
        html += '</div></div>';
    }
    
    // Cruise Ports/Destinations
    if (data.destinations && data.destinations.length > 0) {
        html += '<div class="detail-section"><h3>⚓ Cruise Route & Havens</h3>';
        html += '<p style="margin-bottom: 15px; color: #666;">Deze cruise bezoekt ' + data.destinations.length + ' prachtige bestemmingen:</p>';
        
        // Add itinerary link if available
        if (data.itineraryUrl) {
            // Combine TC base URL with relative itinerary path
            var fullUrl = tcBaseUrl + '/' + data.itineraryUrl.replace(/^\//, '');
            html += '<div style="margin-bottom: 15px;">';
            html += '<a href="' + fullUrl + '" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--primary); color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">';
            html += '🗺️ Bekijk Routekaart';
            html += '</a>';
            html += '</div>';
        }
        
        html += '<div class="cruise-ports">';
        data.destinations.forEach(function(port, index) {
            var portName = typeof port === 'string' ? port : (port.name || 'Haven ' + (index + 1));
            html += '<div class="port-card">⚓ Stop ' + (index + 1) + ': <strong>' + portName + '</strong></div>';
        });
        html += '</div></div>';
    }
    
    // Description if available
    if (data.description) {
        html += '<div class="detail-section"><h3>📖 Beschrijving</h3><div class="detail-description">' + data.description + '</div></div>';
    }
    
    // Initialize gallery after returning
    if (imageUrls.length > 0) {
        setTimeout(function() { initGallery(imageUrls); }, 100);
    }
    
    return html;
}
</script>

<?php rbstravel_get_footer(); ?>
</body>
</html>
