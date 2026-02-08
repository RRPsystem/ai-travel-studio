<?php
/**
 * Single Travel Idea Template - Speelse Style
 * 
 * Features:
 * - Multi-destination route visualization with OpenStreetMap
 * - Interactive hotel cards with sliding panels
 * - Day-by-day itinerary with flights
 * - 3 info cards
 * - Highlights section
 * - Responsive design
 * 
 * @package RBS_Travel
 */

if (!defined('ABSPATH')) exit;

// Get all meta fields from Travel Compositor
global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

$destinations = $travel_meta_fields['travel_destinations'] ?? [];
$transports = $travel_meta_fields['travel_transports'] ?? [];
$hotels = $travel_meta_fields['travel_hotels'] ?? [];
$price_per_person = $travel_meta_fields['travel_price_per_person'] ?? '0.00';
$number_of_nights = $travel_meta_fields['travel_number_of_nights'] ?? 0;
$number_of_days = $number_of_nights + 1;

// Enqueue Leaflet CSS/JS for maps
wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);

get_header();
?>

<style>
    /* Speelse Style CSS */
    .rbs-travel-detail {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f8f9fa;
        color: #1f2937;
    }

    /* Hero Section */
    .rbs-hero-section {
        position: relative;
        height: 500px;
        background-size: cover;
        background-position: center;
        margin-bottom: 0;
    }

    .rbs-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 3rem 2rem;
        background: linear-gradient(transparent, rgba(0,0,0,0.85));
    }

    .rbs-hero-content {
        max-width: 1400px;
        margin: 0 auto;
        color: white;
    }

    .rbs-hero-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 20px rgba(0,0,0,0.5);
    }

    .rbs-hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
    }

    /* Container */
    .rbs-container {
        max-width: 1400px;
        margin: -80px auto 0;
        padding: 0 2rem 4rem;
        position: relative;
        z-index: 10;
    }

    .rbs-content-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 3rem;
    }

    /* Main Content */
    .rbs-main-content {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    /* Quick Info Cards */
    .rbs-quick-info-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .rbs-info-card {
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
    }

    .rbs-info-icon {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .rbs-info-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .rbs-info-value {
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* Section Title */
    .rbs-section-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 2rem;
        color: #1f2937;
    }

    /* Highlights */
    .rbs-highlights-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .rbs-highlight-item {
        border-radius: 16px;
        padding: 1.5rem;
        display: flex;
        gap: 1rem;
    }

    .rbs-highlight-icon {
        font-size: 2.5rem;
        flex-shrink: 0;
    }

    .rbs-highlight-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .rbs-highlight-desc {
        color: #4b5563;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Day by Day */
    .rbs-day-item {
        background: #f9fafb;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .rbs-day-item:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .rbs-day-header {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }

    .rbs-day-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        font-weight: 800;
        flex-shrink: 0;
    }

    .rbs-day-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2937;
    }

    .rbs-day-subtitle {
        font-size: 0.95rem;
        color: #667eea;
        font-weight: 600;
    }

    .rbs-day-content {
        font-size: 1rem;
        line-height: 1.8;
        color: #4b5563;
    }

    .rbs-day-image {
        width: 100%;
        height: 250px;
        background-size: cover;
        background-position: center;
        border-radius: 12px;
        margin: 1rem 0;
    }

    /* Flight Info */
    .rbs-flight-info {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .rbs-flight-route {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .rbs-flight-arrow {
        color: #667eea;
        margin: 0 0.5rem;
    }

    .rbs-flight-time {
        font-size: 0.9rem;
        color: #6b7280;
    }

    /* Hotel Card */
    .rbs-hotel-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .rbs-hotel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .rbs-hotel-image {
        width: 100%;
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .rbs-hotel-badge {
        position: absolute;
        top: 1rem;
        left: 1rem;
        background: rgba(255,255,255,0.95);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.85rem;
        color: #667eea;
    }

    .rbs-hotel-content {
        padding: 1.5rem;
    }

    .rbs-hotel-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .rbs-hotel-location {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .rbs-hotel-features {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .rbs-feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #4b5563;
    }

    .rbs-hotel-cta {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
        text-decoration: none;
    }

    /* Sidebar */
    .rbs-sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .rbs-booking-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .rbs-price-display {
        text-align: center;
        padding: 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        color: white;
        margin-bottom: 2rem;
    }

    .rbs-price-amount {
        font-size: 3rem;
        font-weight: 900;
    }

    .rbs-price-note {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    .rbs-booking-btn {
        width: 100%;
        padding: 1.25rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 16px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .rbs-booking-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .rbs-info-list {
        background: #f9fafb;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .rbs-info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .rbs-info-item:last-child {
        border-bottom: none;
    }

    .rbs-info-label {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .rbs-info-value {
        font-weight: 600;
        color: #1f2937;
    }

    /* Map Card in Sidebar */
    .rbs-map-preview-card {
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .rbs-map-preview-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }

    .rbs-sidebar-map {
        width: 100%;
        height: 200px;
    }

    .rbs-map-cta {
        padding: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
    }

    .rbs-map-cta-title {
        font-weight: 700;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .rbs-map-cta-subtitle {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    /* Sliding Panels */
    .rbs-panel-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .rbs-panel-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    .rbs-sliding-panel {
        position: fixed;
        right: -600px;
        top: 0;
        width: 600px;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.2);
        z-index: 1000;
        overflow-y: auto;
        transition: right 0.4s ease;
    }

    .rbs-sliding-panel.active {
        right: 0;
    }

    .rbs-panel-header {
        position: sticky;
        top: 0;
        background: white;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
    }

    .rbs-panel-title {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .rbs-panel-close {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f3f4f6;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .rbs-panel-close:hover {
        background: #e5e7eb;
        transform: rotate(90deg);
    }

    .rbs-panel-content {
        padding: 2rem;
    }
    
    /* Map panel - full height */
    #mapPanel .rbs-panel-content {
        height: calc(100vh - 80px);
        overflow: hidden;
    }

    .rbs-panel-gallery {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .rbs-gallery-image {
        width: 100%;
        height: 150px;
        background-size: cover;
        background-position: center;
        border-radius: 12px;
    }

    .rbs-panel-section {
        margin-bottom: 2rem;
    }

    .rbs-panel-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1f2937;
    }

    .rbs-facilities-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .rbs-facility-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #4b5563;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .rbs-content-layout {
            grid-template-columns: 1fr;
        }

        .rbs-sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .rbs-hero-title {
            font-size: 2rem;
        }

        .rbs-main-content {
            padding: 2rem;
        }

        .rbs-quick-info-cards {
            grid-template-columns: 1fr;
        }

        .rbs-highlights-grid {
            grid-template-columns: 1fr;
        }

        .rbs-flight-info {
            flex-direction: column;
            gap: 1rem;
        }

        .rbs-sliding-panel {
            width: 100%;
            right: -100%;
        }
    }
</style>

<div class="rbs-travel-detail">
    <!-- Hero Section -->
    <div class="rbs-hero-section" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), url('<?php echo !empty($destinations) ? esc_url($destinations[0]['imageUrls'][0] ?? '') : ''; ?>');">
        <div class="rbs-hero-overlay">
            <div class="rbs-hero-content">
                <h1 class="rbs-hero-title"><?php the_title(); ?></h1>
                <p class="rbs-hero-subtitle"><?php echo esc_html($travel_meta_fields['travel_large_title'] ?? ''); ?></p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="rbs-container">
        <div class="rbs-content-layout">
            <!-- Left Column -->
            <div class="rbs-main-content">
                
                <!-- Quick Info Cards -->
                <div class="rbs-quick-info-cards">
                    <div class="rbs-info-card" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                        <div class="rbs-info-icon">⏱️</div>
                        <div class="rbs-info-title">Directe vlucht</div>
                        <div class="rbs-info-value">
                            <?php 
                            if (!empty($transports[0])) {
                                $flight_duration = ''; // Calculate from departure and arrival times
                                echo esc_html($flight_duration ?: '3.5 uur');
                            }
                            ?>
                        </div>
                    </div>
                    <div class="rbs-info-card" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
                        <div class="rbs-info-icon">🌍</div>
                        <div class="rbs-info-title">2,400 km</div>
                        <div class="rbs-info-value">Zuid-Europa</div>
                    </div>
                    <div class="rbs-info-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                        <div class="rbs-info-icon">🌡️</div>
                        <div class="rbs-info-title">25-30°C</div>
                        <div class="rbs-info-value">Zomerklimaat</div>
                    </div>
                </div>

                <!-- Description -->
                <h2 class="rbs-section-title">Over deze reis</h2>
                <div class="rbs-day-content">
                    <?php the_content(); ?>
                </div>

                <!-- Highlights Section -->
                <?php if (!empty($destinations)): ?>
                <h2 class="rbs-section-title" style="margin-top: 3rem;">Hoogtepunten</h2>
                <div class="rbs-highlights-grid">
                    <?php 
                    $highlight_colors = [
                        'linear-gradient(135deg, #fef3c7 0%, #fde68a 100%)',
                        'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)',
                        'linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%)',
                        'linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%)'
                    ];
                    $highlight_icons = ['🌅', '🏛️', '🍷', '⛵'];
                    
                    for ($i = 0; $i < min(4, count($destinations)); $i++): 
                        $dest = $destinations[$i];
                    ?>
                    <div class="rbs-highlight-item" style="background: <?php echo $highlight_colors[$i]; ?>;">
                        <div class="rbs-highlight-icon"><?php echo $highlight_icons[$i]; ?></div>
                        <div>
                            <div class="rbs-highlight-title"><?php echo esc_html($dest['name']); ?></div>
                            <div class="rbs-highlight-desc"><?php echo esc_html(wp_trim_words($dest['description'] ?? '', 15)); ?></div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

                <!-- Day by Day Itinerary -->
                <div class="rbs-itinerary">
                    <h2 class="rbs-section-title">Dag tot Dag Programma</h2>

                    <!-- Day 1: Departure Flight -->
                    <?php if (!empty($transports)): 
                        $departure_flight = $transports[0];
                    ?>
                    <div class="rbs-day-item">
                        <div class="rbs-day-header">
                            <div class="rbs-day-number">1</div>
                            <div>
                                <h3 class="rbs-day-title">✈️ Heenvlucht</h3>
                                <div class="rbs-day-subtitle">
                                    <?php echo esc_html($departure_flight['originDestinationCode'] ?? 'AMS'); ?> → 
                                    <?php echo esc_html($departure_flight['targetDestinationCode'] ?? ''); ?>
                                </div>
                            </div>
                        </div>
                        <div class="rbs-flight-info">
                            <div>
                                <div class="rbs-flight-route">
                                    <?php echo esc_html($departure_flight['originDestinationCode'] ?? 'AMS'); ?>
                                    <span class="rbs-flight-arrow">✈️</span>
                                    <?php echo esc_html($departure_flight['targetDestinationCode'] ?? ''); ?>
                                </div>
                                <div class="rbs-flight-time">
                                    <?php echo esc_html(($departure_flight['company'] ?? '') . ' ' . ($departure_flight['transportNumber'] ?? '')); ?>
                                </div>
                            </div>
                            <div>
                                <div class="rbs-flight-time">Vertrek: <?php echo esc_html($departure_flight['departureTime'] ?? ''); ?></div>
                                <div class="rbs-flight-time">Aankomst: <?php echo esc_html($departure_flight['arrivalTime'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Destination Days with Hotels -->
                    <?php foreach ($destinations as $index => $destination): 
                        // Find hotel for this destination
                        $destination_hotel = null;
                        foreach ($hotels as $hotel) {
                            if (isset($hotel['fromDay']) && isset($destination['fromDay']) &&
                                $hotel['fromDay'] <= $destination['fromDay'] && 
                                $hotel['toDay'] >= $destination['fromDay']) {
                                $destination_hotel = $hotel;
                                break;
                            }
                        }
                        
                        $from_day = $destination['fromDay'] ?? $index + 2;
                        $to_day = $destination['toDay'] ?? $from_day + 1;
                    ?>
                    <div class="rbs-day-item">
                        <div class="rbs-day-header">
                            <div class="rbs-day-number"><?php echo esc_html($from_day); ?>-<?php echo esc_html($to_day); ?></div>
                            <div>
                                <h3 class="rbs-day-title"><?php echo esc_html($destination['name']); ?></h3>
                                <div class="rbs-day-subtitle">
                                    <?php echo esc_html(($to_day - $from_day + 1)); ?> dagen
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($destination['imageUrls'][0])): ?>
                        <div class="rbs-day-image" style="background-image: url('<?php echo esc_url($destination['imageUrls'][0]); ?>');"></div>
                        <?php endif; ?>
                        
                        <div class="rbs-day-content">
                            <?php echo wp_kses_post($destination['description'] ?? ''); ?>
                        </div>

                        <!-- Hotel Card (Clickable) -->
                        <?php if ($destination_hotel): ?>
                        <div class="rbs-hotel-card" onclick="openHotelPanel(<?php echo esc_js($index); ?>)">
                            <div class="rbs-hotel-image" style="background-image: url('<?php echo esc_url($destination_hotel['hotelData']['imageUrls'][0] ?? ''); ?>');">
                                <div class="rbs-hotel-badge">🏨 Hotel</div>
                            </div>
                            <div class="rbs-hotel-content">
                                <h4 class="rbs-hotel-name"><?php echo esc_html($destination_hotel['hotelData']['product'] ?? 'Hotel'); ?></h4>
                                <div class="rbs-hotel-location">
                                    📍 <?php echo esc_html($destination_hotel['hotelData']['location'] ?? ''); ?>
                                </div>
                                <div class="rbs-hotel-features">
                                    <div class="rbs-feature-item">⭐ <?php echo esc_html($destination_hotel['hotelData']['hotelCategory'] ?? '4'); ?> Sterren</div>
                                    <div class="rbs-feature-item">🛏️ <?php echo esc_html($destination_hotel['hotelData']['roomCode'] ?? 'Standaard'); ?></div>
                                    <div class="rbs-feature-item">🍽️ <?php echo esc_html($destination_hotel['hotelData']['boardTypeDescription'] ?? 'Ontbijt'); ?></div>
                                </div>
                                <span class="rbs-hotel-cta">📋 Bekijk Hotel Details →</span>
                            </div>
                        </div>
                        
                        <!-- Hidden hotel data for panel -->
                        <script type="application/json" id="hotel-data-<?php echo esc_attr($index); ?>">
                            <?php echo wp_json_encode($destination_hotel); ?>
                        </script>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <!-- Last Day: Return Flight -->
                    <?php if (!empty($transports) && count($transports) > 1): 
                        $return_flight = $transports[count($transports) - 1];
                    ?>
                    <div class="rbs-day-item">
                        <div class="rbs-day-header">
                            <div class="rbs-day-number"><?php echo esc_html($number_of_days); ?></div>
                            <div>
                                <h3 class="rbs-day-title">✈️ Terugvlucht</h3>
                                <div class="rbs-day-subtitle">
                                    <?php echo esc_html($return_flight['originDestinationCode'] ?? ''); ?> → 
                                    <?php echo esc_html($return_flight['targetDestinationCode'] ?? 'AMS'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="rbs-flight-info">
                            <div>
                                <div class="rbs-flight-route">
                                    <?php echo esc_html($return_flight['originDestinationCode'] ?? ''); ?>
                                    <span class="rbs-flight-arrow">✈️</span>
                                    <?php echo esc_html($return_flight['targetDestinationCode'] ?? 'AMS'); ?>
                                </div>
                                <div class="rbs-flight-time">
                                    <?php echo esc_html(($return_flight['company'] ?? '') . ' ' . ($return_flight['transportNumber'] ?? '')); ?>
                                </div>
                            </div>
                            <div>
                                <div class="rbs-flight-time">Vertrek: <?php echo esc_html($return_flight['departureTime'] ?? ''); ?></div>
                                <div class="rbs-flight-time">Aankomst: <?php echo esc_html($return_flight['arrivalTime'] ?? ''); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="rbs-sidebar">
                <div class="rbs-booking-card">
                    <div class="rbs-price-display">
                        <div style="font-size: 0.9rem; opacity: 0.9;">Vanaf</div>
                        <div class="rbs-price-amount">
                            €<?php echo esc_html(number_format((float)$price_per_person, 0, ',', '.')); ?>
                            <span style="font-size: 1.2rem;">p.p.</span>
                        </div>
                        <div class="rbs-price-note">Gebaseerd op 2 personen</div>
                    </div>
                    <button class="rbs-booking-btn">📅 Boek Nu</button>
                    
                    <!-- Map Preview Card -->
                    <div class="rbs-info-list rbs-map-preview-card" onclick="openMapPanel()">
                        <div class="rbs-sidebar-map" id="sidebarMap"></div>
                        <div class="rbs-map-cta">
                            <div class="rbs-map-cta-title">🗺️ Bekijk Route</div>
                            <div class="rbs-map-cta-subtitle">Klik voor grote kaart</div>
                        </div>
                    </div>
                    
                    <div class="rbs-info-list">
                        <div class="rbs-info-item">
                            <span class="rbs-info-label">Duur</span>
                            <span class="rbs-info-value"><?php echo esc_html($number_of_days); ?> dagen</span>
                        </div>
                        <div class="rbs-info-item">
                            <span class="rbs-info-label">Nachten</span>
                            <span class="rbs-info-value"><?php echo esc_html($number_of_nights); ?> nachten</span>
                        </div>
                        <div class="rbs-info-item">
                            <span class="rbs-info-label">Bestemmingen</span>
                            <span class="rbs-info-value"><?php echo esc_html(count($destinations)); ?> steden</span>
                        </div>
                        <div class="rbs-info-item">
                            <span class="rbs-info-label">Hotels</span>
                            <span class="rbs-info-value"><?php echo esc_html(count($hotels)); ?> hotels</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hotel Sliding Panel -->
<div class="rbs-panel-overlay" id="hotelPanelOverlay" onclick="closeHotelPanel()"></div>
<div class="rbs-sliding-panel" id="hotelPanel">
    <div class="rbs-panel-header">
        <h3 class="rbs-panel-title" id="panelHotelName">Hotel Details</h3>
        <button class="rbs-panel-close" onclick="closeHotelPanel()">×</button>
    </div>
    <div class="rbs-panel-content" id="hotelPanelContent"></div>
</div>

<!-- Map Sliding Panel -->
<div class="rbs-panel-overlay" id="mapPanelOverlay" onclick="closeMapPanel()" style="opacity: 0; pointer-events: none;"></div>
<div class="rbs-sliding-panel" id="mapPanel" style="right: -600px; width: 600px;">
    <div class="rbs-panel-header">
        <h3 class="rbs-panel-title">🗺️ Jouw Reis Route</h3>
        <button class="rbs-panel-close" onclick="closeMapPanel()">×</button>
    </div>
    <div class="rbs-panel-content" style="padding: 0;">
        <div id="fullRouteMap" style="width: 100%; height: calc(100vh - 80px);"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize sidebar preview map
    const sidebarMap = L.map('sidebarMap', {
        zoomControl: false,
        scrollWheelZoom: false,
        dragging: false,
        doubleClickZoom: false,
        touchZoom: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap',
        maxZoom: 19
    }).addTo(sidebarMap);

    // Define destinations from PHP
    const destinations = <?php echo wp_json_encode($destinations); ?>;
    let fullMap = null;

    // Custom icon function
    function createCustomIcon(emoji, color, isLarge = false) {
        const size = isLarge ? 45 : 40;
        return L.divIcon({
            html: `<div style="
                background: ${color};
                width: ${size}px;
                height: ${size}px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: ${isLarge ? '24px' : '20px'};
                box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                border: 3px solid white;
                transform: translate(-50%, -50%);
            ">${emoji}</div>`,
            className: '',
            iconSize: [size, size],
            iconAnchor: [size/2, size/2]
        });
    }

    // Setup map function
    function setupMap(map, interactive = true) {
        const bounds = [];
        const markers = [];

        // Add home marker
        const homeCoords = [52.3676, 4.9041]; // Amsterdam
        const homeMarker = L.marker(homeCoords, {
            icon: createCustomIcon('🏠', '#10b981', true)
        }).addTo(map);
        bounds.push(homeCoords);
        markers.push(homeMarker);

        // Add destination markers
        destinations.forEach((dest, index) => {
            if (dest.latitude && dest.longitude) {
                const coords = [parseFloat(dest.latitude), parseFloat(dest.longitude)];
                const marker = L.marker(coords, {
                    icon: createCustomIcon('📍', '#667eea')
                }).addTo(map);

                if (interactive) {
                    const popupContent = `
                        <div style="padding: 0.5rem;">
                            <div style="font-weight: 700; margin-bottom: 0.25rem;">${dest.name}</div>
                            <div style="font-size: 0.85rem; color: #667eea;">Dag ${dest.fromDay}-${dest.toDay}</div>
                        </div>
                    `;
                    marker.bindPopup(popupContent);
                }

                bounds.push(coords);
                markers.push(marker);
            }
        });

        return { bounds, markers };
    }

    // Setup sidebar map
    if (destinations.length > 0) {
        const sidebarMapData = setupMap(sidebarMap, false);
        if (sidebarMapData.bounds.length > 0) {
            sidebarMap.fitBounds(sidebarMapData.bounds, {
                padding: [20, 20],
                maxZoom: 6
            });
        }
    }

    // Hotel panel functions
    window.openHotelPanel = function(index) {
        const hotelDataEl = document.getElementById('hotel-data-' + index);
        if (!hotelDataEl) return;
        
        const hotelData = JSON.parse(hotelDataEl.textContent);
        const hotel = hotelData.hotelData;
        
        document.getElementById('panelHotelName').textContent = hotel.product;
        
        let content = '';
        
        // Gallery
        if (hotel.imageUrls && hotel.imageUrls.length > 0) {
            content += '<div class="rbs-panel-gallery">';
            hotel.imageUrls.slice(0, 4).forEach(url => {
                content += `<div class="rbs-gallery-image" style="background-image: url('${url}');"></div>`;
            });
            content += '</div>';
        }
        
        // Hotel Info
        content += '<div class="rbs-panel-section">';
        content += '<h4 class="rbs-panel-section-title">📍 Locatie</h4>';
        content += `<p>${hotel.location}</p>`;
        content += `<p style="color: #6b7280; margin-top: 0.5rem;">Check-in: ${hotelData.startDate} | Check-out: ${hotelData.endDate}</p>`;
        content += '</div>';
        
        // Room Details
        content += '<div class="rbs-panel-section">';
        content += '<h4 class="rbs-panel-section-title">🛏️ Kamer Details</h4>';
        content += '<div class="rbs-facilities-list">';
        content += `<div class="rbs-facility-item">⭐ ${hotel.hotelCategory || '4'} Sterren</div>`;
        content += `<div class="rbs-facility-item">🛏️ ${hotel.roomCode || 'Standaard Kamer'}</div>`;
        content += `<div class="rbs-facility-item">🍽️ ${hotel.boardTypeDescription || 'Ontbijt'}</div>`;
        content += `<div class="rbs-facility-item">👥 ${hotelData.numberOfPersons || '2'} Personen</div>`;
        content += '</div>';
        content += '</div>';
        
        // Facilities
        content += '<div class="rbs-panel-section">';
        content += '<h4 class="rbs-panel-section-title">✨ Faciliteiten</h4>';
        content += '<div class="rbs-facilities-list">';
        content += '<div class="rbs-facility-item">✓ WiFi</div>';
        content += '<div class="rbs-facility-item">✓ Zwembad</div>';
        content += '<div class="rbs-facility-item">✓ Restaurant</div>';
        content += '<div class="rbs-facility-item">✓ Bar</div>';
        content += '<div class="rbs-facility-item">✓ Airconditioning</div>';
        content += '<div class="rbs-facility-item">✓ Roomservice</div>';
        content += '</div>';
        content += '</div>';
        
        document.getElementById('hotelPanelContent').innerHTML = content;
        
        document.getElementById('hotelPanel').classList.add('active');
        document.getElementById('hotelPanelOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeHotelPanel = function() {
        document.getElementById('hotelPanel').classList.remove('active');
        document.getElementById('hotelPanelOverlay').classList.remove('active');
        document.body.style.overflow = '';
    };

    // Map panel functions
    window.openMapPanel = function() {
        if (!fullMap && destinations.length > 0) {
            fullMap = L.map('fullRouteMap', {
                zoomControl: true,
                scrollWheelZoom: true
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(fullMap);

            const fullMapData = setupMap(fullMap, true);
            if (fullMapData.bounds.length > 0) {
                fullMap.fitBounds(fullMapData.bounds, {
                    padding: [50, 50],
                    maxZoom: 7
                });
            }

            setTimeout(() => {
                if (fullMapData.markers.length > 1) {
                    fullMapData.markers[1].openPopup();
                }
            }, 500);
        }

        document.getElementById('mapPanel').style.right = '0';
        document.getElementById('mapPanelOverlay').style.opacity = '1';
        document.getElementById('mapPanelOverlay').style.pointerEvents = 'all';
        document.body.style.overflow = 'hidden';

        setTimeout(() => {
            if (fullMap) fullMap.invalidateSize();
        }, 400);
    };

    window.closeMapPanel = function() {
        document.getElementById('mapPanel').style.right = '-600px';
        document.getElementById('mapPanelOverlay').style.opacity = '0';
        document.getElementById('mapPanelOverlay').style.pointerEvents = 'none';
        document.body.style.overflow = '';
    };
});
</script>

<?php get_footer(); ?>
