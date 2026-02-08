<?php
/**
 * Template: Travel Idea Detail Page - Clean Rebuild
 * Version: 5.0.0
 */

if (!defined('ABSPATH')) exit;

// Get travel data
$travel_meta_fields = get_post_meta(get_the_ID(), 'rbstravel_travel_meta_fields', true);
if (!is_array($travel_meta_fields)) $travel_meta_fields = array();

// Defaults
$defaults = array(
    'travel_title' => get_the_title(),
    'travel_destinations' => array(),
    'travel_hotels' => array(),
    'travel_cruises' => array(),
    'travel_flights' => array(),
    'travel_transfers' => array(),
    'travel_number_of_nights' => 0,
    'travel_price_per_person' => 0,
    'travel_departure_date' => '',
    'travel_idea_url' => '',
    'travel_themes' => array(),
    'travel_main_image' => '',
    'travel_images' => array()
);
$travel_meta_fields = wp_parse_args($travel_meta_fields, $defaults);

// Get images
$main_image = '';
if (!empty($travel_meta_fields['travel_main_image'])) {
    $main_image = $travel_meta_fields['travel_main_image'];
} elseif (!empty($travel_meta_fields['travel_images'][0])) {
    $main_image = $travel_meta_fields['travel_images'][0];
}

// Get location
$location = '';
if (!empty($travel_meta_fields['travel_destinations'][0]['name'])) {
    $location = $travel_meta_fields['travel_destinations'][0]['name'];
}

// Build destinations for map
$map_destinations = array();
if (!empty($travel_meta_fields['travel_destinations'])) {
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

// Get brand colors from settings
$primary_color = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('brand_primary_color', '#4a6cf7');
$secondary_color = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('brand_secondary_color', '#6366f1');

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
        
        /* ========================================
           HERO SECTION
           ======================================== */
        .travel-hero {
            position: relative;
            height: 400px;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), 
                        url('<?php echo esc_url($main_image); ?>') center/cover;
            display: flex;
            align-items: flex-end;
            padding: 40px;
        }
        
        .travel-hero-content {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            color: white;
        }
        
        .travel-hero h1 {
            font-size: 42px;
            font-weight: 700;
            margin: 0 0 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .travel-hero-location {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .travel-hero-stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }
        
        .travel-hero-stat {
            text-align: center;
        }
        
        .travel-hero-stat-value {
            font-size: 24px;
            font-weight: 700;
        }
        
        .travel-hero-stat-label {
            font-size: 14px;
            opacity: 0.8;
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
        }
        
        #routeMap {
            width: 100%;
            height: 100%;
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
    </style>
</head>
<body>

<!-- HERO SECTION -->
<section class="travel-hero">
    <div class="travel-hero-content">
        <h1><?php echo esc_html($travel_meta_fields['travel_title']); ?></h1>
        <?php if ($location): ?>
            <div class="travel-hero-location">📍 <?php echo esc_html($location); ?></div>
        <?php endif; ?>
        <div class="travel-hero-stats">
            <div class="travel-hero-stat">
                <div class="travel-hero-stat-value">€<?php echo number_format($travel_meta_fields['travel_price_per_person'], 0, ',', '.'); ?></div>
                <div class="travel-hero-stat-label">per persoon</div>
            </div>
            <div class="travel-hero-stat">
                <div class="travel-hero-stat-value"><?php echo intval($travel_meta_fields['travel_number_of_nights']); ?></div>
                <div class="travel-hero-stat-label">dagen</div>
            </div>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="travel-container">
    <!-- LEFT: Main Content -->
    <div class="travel-main">
        <!-- Intro -->
        <section class="travel-intro">
            <h2>Ontdek deze Reis</h2>
            <div class="travel-intro-text">
                <?php the_content(); ?>
            </div>
        </section>
        
        <!-- Day-by-day Program -->
        <?php include(dirname(__FILE__) . '/partials/tour-plan-grouped.php'); ?>
    </div>
    
    <!-- RIGHT: Sidebar -->
    <aside class="travel-sidebar">
        <div class="travel-sidebar-inner">
            <!-- Booking Card -->
            <div class="booking-card">
                <h3>Boek deze Reis</h3>
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
                    <button type="button" class="btn-primary" onclick="window.location.href='<?php echo esc_url($travel_meta_fields['travel_idea_url']); ?>'">
                        Boek Nu
                    </button>
                    <button type="button" class="btn-secondary">Info Aanvragen</button>
                    <button type="button" class="btn-secondary">Reis Aanpassen</button>
                </form>
            </div>
            
            <!-- Route Map Button -->
            <?php if (count($map_destinations) >= 2): ?>
            <div class="booking-card" style="text-align: center;">
                <button type="button" class="btn-primary" onclick="openRouteMap()" style="margin-bottom: 0;">
                    🗺️ Bekijk de Route
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Travel Expert -->
            <?php 
            if (function_exists('rbstravel_render_expert_widget')) {
                rbstravel_render_expert_widget(get_the_ID());
            }
            ?>
        </div>
    </aside>
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
                popup += '<br><img src="' + dest.image + '" style="width:150px;height:80px;object-fit:cover;border-radius:4px;margin-top:8px;">';
            }
            marker.bindPopup(popup);
        }
    });
    
    // Fix size
    setTimeout(function() {
        routeMapInstance.invalidateSize();
    }, 100);
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
    
    // Init hotel map if needed
    if (window.pendingHotelMap) {
        setTimeout(function() {
            initHotelMap(window.pendingHotelMap.lat, window.pendingHotelMap.lng, window.pendingHotelMap.name);
            window.pendingHotelMap = null;
        }, 200);
    }
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
    if (!container) return;
    
    if (hotelMapInstance) {
        hotelMapInstance.remove();
    }
    
    hotelMapInstance = L.map('hotelMapContainer').setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18
    }).addTo(hotelMapInstance);
    
    L.marker([lat, lng]).addTo(hotelMapInstance).bindPopup(name).openPopup();
    
    setTimeout(function() {
        hotelMapInstance.invalidateSize();
    }, 100);
}

// Generate hotel detail HTML
function generateHotelDetail(data) {
    var html = '';
    
    // Gallery
    if (data.images && data.images.length > 0) {
        html += '<div class="detail-section"><div class="detail-gallery">';
        data.images.slice(0, 6).forEach(function(img) {
            html += '<img src="' + img + '" alt="Hotel">';
        });
        html += '</div></div>';
    }
    
    // Info
    html += '<div class="detail-section">';
    if (data.stars) html += '<p>⭐ ' + data.stars + ' sterren</p>';
    if (data.roomType) html += '<p>🛏️ ' + data.roomType + '</p>';
    if (data.mealPlan) html += '<p>🍽️ ' + data.mealPlan + '</p>';
    html += '</div>';
    
    // Map
    var lat = data.latitude || (data.destination && data.destination.latitude);
    var lng = data.longitude || (data.destination && data.destination.longitude);
    if (lat && lng) {
        html += '<div class="detail-section"><h3>📍 Locatie</h3>';
        html += '<div id="hotelMapContainer" class="detail-map"></div></div>';
        window.pendingHotelMap = { lat: lat, lng: lng, name: data.name || 'Hotel' };
    }
    
    return html;
}

// Generate cruise detail HTML
function generateCruiseDetail(data) {
    var html = '';
    
    // Ship info
    html += '<div class="detail-section">';
    if (data.ship || data.shipName) html += '<p>🚢 ' + (data.ship || data.shipName) + '</p>';
    if (data.cruiseLine) html += '<p>🏢 ' + data.cruiseLine + '</p>';
    if (data.category) html += '<p>🛏️ Cabine: ' + data.category + '</p>';
    html += '</div>';
    
    // Ports
    if (data.ports && data.ports.length > 0) {
        html += '<div class="detail-section"><h3>🚢 Havens</h3><ul style="margin:0;padding-left:20px;">';
        data.ports.forEach(function(port) {
            var portName = typeof port === 'string' ? port : (port.name || port.port || '');
            if (portName) html += '<li>' + portName + '</li>';
        });
        html += '</ul></div>';
    }
    
    // Link
    if (data.itineraryUrl) {
        html += '<div class="detail-section">';
        html += '<a href="' + data.itineraryUrl + '" target="_blank" style="display:inline-block;padding:12px 20px;background:var(--secondary);color:white;text-decoration:none;border-radius:8px;font-weight:600;">Bekijk Cruise Details</a>';
        html += '</div>';
    }
    
    return html;
}
</script>

<?php rbstravel_get_footer(); ?>
</body>
</html>
