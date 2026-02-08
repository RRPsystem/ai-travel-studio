<?php
/**
 * Single Travel Idea Template - Multi-Destination Rondreis
 * Uses Travel Compositor API data structure
 * 
 * Features:
 * - Multi-destination route visualization
 * - Day-by-day itinerary with flights
 * - Clickable hotels with sliding panel
 * - Responsive design
 */

if (!defined('ABSPATH')) exit;

// Get all meta fields from Travel Compositor
global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

$destinations = $travel_meta_fields['travel_destinations'] ?? [];
$transports = $travel_meta_fields['travel_transports'] ?? [];
$hotels = $travel_meta_fields['travel_hotels'] ?? [];
$cars = $travel_meta_fields['travel_cars'] ?? [];

rbstravel_get_header();
?>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f8f9fa;
        color: #1f2937;
    }

    /* Hero Section */
    .hero-section {
        position: relative;
        height: 500px;
        background-size: cover;
        background-position: center;
        background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.5)), 
                          url('<?php echo !empty($destinations) ? $destinations[0]['imageUrls'][0] : ''; ?>');
    }

    .hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 3rem 2rem;
        background: linear-gradient(transparent, rgba(0,0,0,0.85));
    }

    .hero-content {
        max-width: 1400px;
        margin: 0 auto;
        color: white;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 20px rgba(0,0,0,0.5);
    }

    .hero-subtitle {
        font-size: 1.3rem;
        opacity: 0.95;
    }

    /* Container */
    .container {
        max-width: 1400px;
        margin: -80px auto 0;
        padding: 0 2rem 4rem;
        position: relative;
        z-index: 10;
    }

    .content-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 3rem;
    }

    /* Main Content */
    .main-content {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    /* Multi-Destination Route Map */
    .route-map-section {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 20px;
        padding: 3rem;
        margin-bottom: 3rem;
    }

    .map-title {
        font-size: 2rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 2rem;
        color: #1f2937;
    }

    .route-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        margin: 2rem 0;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .route-point {
        flex: 1;
        min-width: 120px;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .point-icon {
        width: 70px;
        height: 70px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin: 0 auto 0.75rem;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        border: 3px solid #667eea;
    }

    .point-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .point-days {
        font-size: 0.85rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .route-connector {
        position: absolute;
        top: 35px;
        left: 10%;
        right: 10%;
        height: 3px;
        background: linear-gradient(90deg, #667eea 0%, #10b981 50%, #f59e0b 100%);
        z-index: 1;
    }

    /* Day by Day Itinerary */
    .itinerary {
        margin-bottom: 3rem;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 2rem;
        color: #1f2937;
    }

    .day-item {
        background: #f9fafb;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .day-item:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .day-header {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        align-items: center;
    }

    .day-number {
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

    .day-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1f2937;
    }

    .day-subtitle {
        font-size: 0.95rem;
        color: #667eea;
        font-weight: 600;
    }

    .day-content {
        font-size: 1rem;
        line-height: 1.8;
        color: #4b5563;
    }

    /* Flight Info */
    .flight-info {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .flight-route {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .flight-arrow {
        color: #667eea;
        margin: 0 0.5rem;
    }

    .flight-time {
        font-size: 0.9rem;
        color: #6b7280;
    }

    /* Hotel Card */
    .hotel-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .hotel-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    }

    .hotel-image {
        width: 100%;
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .hotel-badge {
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

    .hotel-content {
        padding: 1.5rem;
    }

    .hotel-name {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }

    .hotel-location {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .hotel-features {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #4b5563;
    }

    .hotel-cta {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
        text-decoration: none;
    }

    /* Sliding Hotel Panel */
    .hotel-panel {
        position: fixed;
        right: -500px;
        top: 0;
        width: 500px;
        height: 100vh;
        background: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.2);
        z-index: 1000;
        overflow-y: auto;
        transition: right 0.4s ease;
    }

    .hotel-panel.active {
        right: 0;
    }

    .panel-overlay {
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

    .panel-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    .panel-header {
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

    .panel-close {
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

    .panel-close:hover {
        background: #e5e7eb;
        transform: rotate(90deg);
    }

    .panel-content {
        padding: 2rem;
    }

    .panel-gallery {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .gallery-image {
        width: 100%;
        height: 150px;
        background-size: cover;
        background-position: center;
        border-radius: 12px;
    }

    .panel-section {
        margin-bottom: 2rem;
    }

    .panel-section-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #1f2937;
    }

    .facilities-list {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .facility-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #4b5563;
    }

    /* Sidebar */
    .sidebar {
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .booking-card {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .price-display {
        text-align: center;
        padding: 2rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 16px;
        color: white;
        margin-bottom: 2rem;
    }

    .price-amount {
        font-size: 3rem;
        font-weight: 900;
    }

    .price-note {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-top: 0.5rem;
    }

    .booking-btn {
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

    .booking-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }

    .info-card {
        background: #f9fafb;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .info-value {
        font-weight: 600;
        color: #1f2937;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .content-layout {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 2rem;
        }

        .main-content {
            padding: 2rem;
        }

        .route-line {
            flex-direction: column;
        }

        .route-connector {
            display: none;
        }

        .hotel-panel {
            width: 100%;
            right: -100%;
        }
    }
</style>

<div id="rbstravel-content" class="rbstravel-wrapper">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo get_the_title(); ?></h1>
                <p class="hero-subtitle"><?php echo $travel_meta_fields['travel_large_title']; ?></p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="content-layout">
            <!-- Left Column -->
            <div class="main-content">
                
                <!-- Multi-Destination Route Map -->
                <?php if (!empty($destinations) && count($destinations) > 1): ?>
                <div class="route-map-section">
                    <h2 class="map-title">🗺️ Jouw Reis Route</h2>
                    <div class="route-line">
                        <div class="route-connector"></div>
                        
                        <!-- Start: Home -->
                        <div class="route-point">
                            <div class="point-icon">🏠</div>
                            <div class="point-name">Amsterdam</div>
                            <div class="point-days">Dag 1</div>
                        </div>
                        
                        <!-- All Destinations -->
                        <?php foreach ($destinations as $dest): ?>
                        <div class="route-point">
                            <div class="point-icon">📍</div>
                            <div class="point-name"><?php echo $dest['name']; ?></div>
                            <div class="point-days">
                                Dag <?php echo $dest['fromDay']; ?>-<?php echo $dest['toDay']; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- End: Home -->
                        <div class="route-point">
                            <div class="point-icon">🏠</div>
                            <div class="point-name">Amsterdam</div>
                            <div class="point-days">Dag <?php echo ($travel_meta_fields['travel_number_of_nights'] + 1); ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <h2 class="section-title">Over deze reis</h2>
                <div class="day-content">
                    <?php the_content(); ?>
                </div>

                <!-- Day by Day Itinerary -->
                <div class="itinerary">
                    <h2 class="section-title">Dag tot Dag Programma</h2>

                    <!-- Day 1: Departure Flight -->
                    <?php if (!empty($transports)): 
                        $departure_flight = $transports[0];
                    ?>
                    <div class="day-item">
                        <div class="day-header">
                            <div class="day-number">1</div>
                            <div>
                                <h3 class="day-title">✈️ Heenvlucht</h3>
                                <div class="day-subtitle">
                                    <?php echo $departure_flight['originDestinationCode']; ?> → 
                                    <?php echo $departure_flight['targetDestinationCode']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flight-info">
                            <div>
                                <div class="flight-route">
                                    <?php echo $departure_flight['originDestinationCode']; ?>
                                    <span class="flight-arrow">✈️</span>
                                    <?php echo $departure_flight['targetDestinationCode']; ?>
                                </div>
                                <div class="flight-time">
                                    <?php echo $departure_flight['company']; ?> 
                                    <?php echo $departure_flight['transportNumber']; ?>
                                </div>
                            </div>
                            <div>
                                <div class="flight-time">Vertrek: <?php echo $departure_flight['departureTime']; ?></div>
                                <div class="flight-time">Aankomst: <?php echo $departure_flight['arrivalTime']; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Destination Days with Hotels -->
                    <?php foreach ($destinations as $index => $destination): 
                        // Find hotel for this destination
                        $destination_hotel = null;
                        foreach ($hotels as $hotel) {
                            if ($hotel['fromDay'] <= $destination['fromDay'] && 
                                $hotel['toDay'] >= $destination['fromDay']) {
                                $destination_hotel = $hotel;
                                break;
                            }
                        }
                    ?>
                    <div class="day-item">
                        <div class="day-header">
                            <div class="day-number"><?php echo $destination['fromDay']; ?>-<?php echo $destination['toDay']; ?></div>
                            <div>
                                <h3 class="day-title"><?php echo $destination['name']; ?></h3>
                                <div class="day-subtitle">
                                    <?php echo ($destination['toDay'] - $destination['fromDay'] + 1); ?> dagen
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($destination['imageUrls'][0])): ?>
                        <div style="width: 100%; height: 250px; background-image: url('<?php echo $destination['imageUrls'][0]; ?>'); background-size: cover; background-position: center; border-radius: 12px; margin: 1rem 0;"></div>
                        <?php endif; ?>
                        
                        <div class="day-content">
                            <?php echo $destination['description']; ?>
                        </div>

                        <!-- Hotel Card (Clickable) -->
                        <?php if ($destination_hotel): ?>
                        <div class="hotel-card" onclick="openHotelPanel(<?php echo $index; ?>)">
                            <div class="hotel-image" style="background-image: url('<?php echo $destination_hotel['hotelData']['imageUrls'][0] ?? ''; ?>');">
                                <div class="hotel-badge">🏨 Hotel</div>
                            </div>
                            <div class="hotel-content">
                                <h4 class="hotel-name"><?php echo $destination_hotel['hotelData']['product']; ?></h4>
                                <div class="hotel-location">
                                    📍 <?php echo $destination_hotel['hotelData']['location']; ?>
                                </div>
                                <div class="hotel-features">
                                    <div class="feature-item">⭐ <?php echo $destination_hotel['hotelData']['hotelCategory'] ?? '4'; ?> Sterren</div>
                                    <div class="feature-item">🛏️ <?php echo $destination_hotel['hotelData']['roomCode'] ?? 'Standaard'; ?></div>
                                    <div class="feature-item">🍽️ <?php echo $destination_hotel['hotelData']['boardTypeDescription'] ?? 'Ontbijt'; ?></div>
                                </div>
                                <span class="hotel-cta">📋 Bekijk Hotel Details →</span>
                            </div>
                        </div>
                        
                        <!-- Hidden hotel data for panel -->
                        <div id="hotel-data-<?php echo $index; ?>" style="display: none;">
                            <?php echo json_encode($destination_hotel); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <!-- Last Day: Return Flight -->
                    <?php if (!empty($transports) && count($transports) > 1): 
                        $return_flight = $transports[count($transports) - 1];
                        $last_day = $travel_meta_fields['travel_number_of_nights'] + 1;
                    ?>
                    <div class="day-item">
                        <div class="day-header">
                            <div class="day-number"><?php echo $last_day; ?></div>
                            <div>
                                <h3 class="day-title">✈️ Terugvlucht</h3>
                                <div class="day-subtitle">
                                    <?php echo $return_flight['originDestinationCode']; ?> → 
                                    <?php echo $return_flight['targetDestinationCode']; ?>
                                </div>
                            </div>
                        </div>
                        <div class="flight-info">
                            <div>
                                <div class="flight-route">
                                    <?php echo $return_flight['originDestinationCode']; ?>
                                    <span class="flight-arrow">✈️</span>
                                    <?php echo $return_flight['targetDestinationCode']; ?>
                                </div>
                                <div class="flight-time">
                                    <?php echo $return_flight['company']; ?> 
                                    <?php echo $return_flight['transportNumber']; ?>
                                </div>
                            </div>
                            <div>
                                <div class="flight-time">Vertrek: <?php echo $return_flight['departureTime']; ?></div>
                                <div class="flight-time">Aankomst: <?php echo $return_flight['arrivalTime']; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="sidebar">
                <div class="booking-card">
                    <div class="price-display">
                        <div style="font-size: 0.9rem; opacity: 0.9;">Vanaf</div>
                        <div class="price-amount">
                            €<?php echo $travel_meta_fields['travel_price_per_person']; ?>
                            <span style="font-size: 1.2rem;">p.p.</span>
                        </div>
                        <div class="price-note">Gebaseerd op 2 personen</div>
                    </div>
                    <button class="booking-btn">📅 Boek Nu</button>
                    
                    <div class="info-card">
                        <div class="info-item">
                            <span class="info-label">Duur</span>
                            <span class="info-value"><?php echo ($travel_meta_fields['travel_number_of_nights'] + 1); ?> dagen</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nachten</span>
                            <span class="info-value"><?php echo $travel_meta_fields['travel_number_of_nights']; ?> nachten</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bestemmingen</span>
                            <span class="info-value"><?php echo count($destinations); ?> steden</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Hotels</span>
                            <span class="info-value"><?php echo count($hotels); ?> hotels</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hotel Sliding Panel -->
<div class="panel-overlay" id="panelOverlay" onclick="closeHotelPanel()"></div>
<div class="hotel-panel" id="hotelPanel">
    <div class="panel-header">
        <h3 id="panelHotelName" style="font-size: 1.5rem; font-weight: 700;">Hotel Details</h3>
        <button class="panel-close" onclick="closeHotelPanel()">×</button>
    </div>
    <div class="panel-content" id="panelContent">
        <!-- Content loaded dynamically -->
    </div>
</div>

<script>
function openHotelPanel(index) {
    const hotelDataEl = document.getElementById('hotel-data-' + index);
    if (!hotelDataEl) return;
    
    const hotelData = JSON.parse(hotelDataEl.textContent);
    const hotel = hotelData.hotelData;
    
    // Update panel title
    document.getElementById('panelHotelName').textContent = hotel.product;
    
    // Build panel content
    let content = '';
    
    // Gallery
    if (hotel.imageUrls && hotel.imageUrls.length > 0) {
        content += '<div class="panel-gallery">';
        hotel.imageUrls.slice(0, 4).forEach(url => {
            content += `<div class="gallery-image" style="background-image: url('${url}');"></div>`;
        });
        content += '</div>';
    }
    
    // Hotel Info
    content += '<div class="panel-section">';
    content += '<h4 class="panel-section-title">📍 Locatie</h4>';
    content += `<p>${hotel.location}</p>`;
    content += `<p style="color: #6b7280; margin-top: 0.5rem;">Check-in: ${hotelData.startDate} | Check-out: ${hotelData.endDate}</p>`;
    content += '</div>';
    
    // Room Details
    content += '<div class="panel-section">';
    content += '<h4 class="panel-section-title">🛏️ Kamer Details</h4>';
    content += '<div class="facilities-list">';
    content += `<div class="facility-item">⭐ ${hotel.hotelCategory || '4'} Sterren</div>`;
    content += `<div class="facility-item">🛏️ ${hotel.roomCode || 'Standaard Kamer'}</div>`;
    content += `<div class="facility-item">🍽️ ${hotel.boardTypeDescription || 'Ontbijt'}</div>`;
    content += `<div class="facility-item">👥 ${hotelData.numberOfPersons || '2'} Personen</div>`;
    content += '</div>';
    content += '</div>';
    
    // Facilities
    content += '<div class="panel-section">';
    content += '<h4 class="panel-section-title">✨ Faciliteiten</h4>';
    content += '<div class="facilities-list">';
    content += '<div class="facility-item">✓ WiFi</div>';
    content += '<div class="facility-item">✓ Zwembad</div>';
    content += '<div class="facility-item">✓ Restaurant</div>';
    content += '<div class="facility-item">✓ Bar</div>';
    content += '<div class="facility-item">✓ Airconditioning</div>';
    content += '<div class="facility-item">✓ Roomservice</div>';
    content += '</div>';
    content += '</div>';
    
    document.getElementById('panelContent').innerHTML = content;
    
    // Show panel
    document.getElementById('hotelPanel').classList.add('active');
    document.getElementById('panelOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeHotelPanel() {
    document.getElementById('hotelPanel').classList.remove('active');
    document.getElementById('panelOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
</script>

<?php rbstravel_get_footer(); ?>
