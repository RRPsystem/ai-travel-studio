<?php
/**
 * Single Travel Idea Template - Day-by-Day Timeline COMPLETE v2.4.0
 * This is the default single post template for rbs-travel-idea posts
 * Shows a chronological timeline with intro, destinations, and full details
 */

// Get WordPress header
get_header();

// Get post meta
$post_id = get_the_ID();
$transports = get_post_meta($post_id, 'travel_transports', true);
$hotels = get_post_meta($post_id, 'travel_hotels', true);
$cruises = get_post_meta($post_id, 'travel_cruises', true);
$transfers = get_post_meta($post_id, 'travel_transfers', true);
$destinations = get_post_meta($post_id, 'travel_destinations', true);
$trip_spots = get_post_meta($post_id, 'travel_trip_spots', true);
$closed_tours = get_post_meta($post_id, 'travel_closed_tours', true);
$tickets = get_post_meta($post_id, 'travel_tickets', true);
$cars = get_post_meta($post_id, 'travel_cars', true);

// Get travel info
$description = get_post_meta($post_id, 'travel_description', true);
$price_per_person = get_post_meta($post_id, 'travel_price_per_person', true);
$total_price = get_post_meta($post_id, 'travel_total_price', true);
$number_of_nights = get_post_meta($post_id, 'travel_number_of_nights', true);
$departure_date_str = get_post_meta($post_id, 'travel_departure_date', true);
$departure_date = $departure_date_str ? new DateTime($departure_date_str) : null;

// Build timeline array
$timeline = array();

// Add destinations as timeline items
if (!empty($destinations) && is_array($destinations)) {
    foreach ($destinations as $index => $destination) {
        $day = isset($destination['day']) ? intval($destination['day']) : ($index + 1);
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'destination',
                'time' => '00:00', // Destinations shown at top of day
                'data' => $destination
            );
        }
    }
}

// Add transports
if (!empty($transports) && is_array($transports)) {
    foreach ($transports as $index => $transport) {
        $day = isset($transport['day']) ? intval($transport['day']) : 0;
        if ($day > 0) {
            $time = isset($transport['departureTime']) ? $transport['departureTime'] : '00:00';
            $timeline[] = array(
                'day' => $day,
                'type' => 'transport',
                'time' => $time,
                'data' => $transport
            );
        }
    }
}

// Add hotels
if (!empty($hotels) && is_array($hotels)) {
    foreach ($hotels as $hotel) {
        $day = isset($hotel['day']) ? intval($hotel['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'hotel',
                'time' => '18:00',
                'data' => $hotel
            );
        }
    }
}

// Add cruises
if (!empty($cruises) && is_array($cruises)) {
    foreach ($cruises as $cruise) {
        $day = isset($cruise['fromDay']) ? intval($cruise['fromDay']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'cruise',
                'time' => '08:00',
                'data' => $cruise
            );
        }
    }
}

// Add cars
if (!empty($cars) && is_array($cars)) {
    foreach ($cars as $car) {
        $day = isset($car['day']) ? intval($car['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'car',
                'time' => isset($car['pickupTime']) ? $car['pickupTime'] : '10:00',
                'data' => $car
            );
        }
    }
}

// Sort timeline by day, then by time
usort($timeline, function($a, $b) {
    if ($a['day'] != $b['day']) {
        return $a['day'] - $b['day'];
    }
    // Destinations always first (time 00:00)
    if ($a['type'] == 'destination' && $b['type'] != 'destination') return -1;
    if ($a['type'] != 'destination' && $b['type'] == 'destination') return 1;
    return strcmp($a['time'], $b['time']);
});

?>

<style>
.rbs-travel-intro {
    max-width: 1200px;
    margin: 0 auto 60px;
    padding: 0 20px;
}

.rbs-travel-intro-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 40px;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
}

.rbs-travel-intro-header h1 {
    font-size: 48px;
    margin: 0 0 20px 0;
    font-weight: 800;
}

.rbs-travel-intro-header .description {
    font-size: 18px;
    line-height: 1.8;
    opacity: 0.95;
    max-width: 900px;
}

.rbs-travel-intro-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.rbs-travel-stat {
    background: white;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.rbs-travel-stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 8px;
}

.rbs-travel-stat-label {
    font-size: 14px;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
}

.rbs-timeline {
    max-width: 1200px;
    margin: 0 auto 60px;
    padding: 0 20px;
}

.rbs-timeline-day {
    margin-bottom: 60px;
}

.rbs-timeline-day-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 35px;
    border-radius: 15px;
    margin-bottom: 25px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.25);
}

.rbs-timeline-day-header h2 {
    margin: 0;
    font-size: 32px;
    font-weight: 800;
}

.rbs-timeline-items {
    padding-left: 50px;
    border-left: 4px solid #e5e7eb;
    position: relative;
}

.rbs-timeline-item {
    position: relative;
    margin-bottom: 35px;
    background: white;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.rbs-timeline-item:hover {
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    transform: translateX(8px);
}

.rbs-timeline-item::before {
    content: '';
    position: absolute;
    left: -60px;
    top: 30px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    border: 5px solid;
    z-index: 2;
}

.rbs-timeline-item.type-destination::before { border-color: #f59e0b; }
.rbs-timeline-item.type-transport::before { border-color: #3b82f6; }
.rbs-timeline-item.type-hotel::before { border-color: #8b5cf6; }
.rbs-timeline-item.type-cruise::before { border-color: #06b6d4; }
.rbs-timeline-item.type-car::before { border-color: #10b981; }

.rbs-timeline-time {
    font-size: 14px;
    color: #9ca3af;
    font-weight: 600;
    margin-bottom: 10px;
}

.rbs-timeline-icon {
    font-size: 32px;
    margin-right: 15px;
    vertical-align: middle;
}

.rbs-timeline-title {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 8px;
}

.rbs-timeline-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-top: 8px;
}

.rbs-timeline-details {
    font-size: 15px;
    line-height: 1.6;
    color: #374151;
}

.rbs-destination-preview {
    display: grid;
    grid-template-columns: 200px 1fr auto;
    gap: 20px;
    align-items: center;
}

.rbs-destination-image {
    width: 200px;
    height: 140px;
    object-fit: cover;
    border-radius: 10px;
}

.rbs-destination-text {
    flex: 1;
}

.rbs-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 28px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
}

.rbs-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.cruise-itinerary {
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid #06b6d4;
}

.cruise-itinerary-step {
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.cruise-itinerary-step:last-child {
    border-bottom: none;
}

.cruise-itinerary-day {
    font-weight: 700;
    color: #06b6d4;
    display: inline-block;
    width: 80px;
}

.cruise-itinerary-port {
    font-weight: 600;
    display: inline-block;
    width: 200px;
}

.cruise-itinerary-times {
    color: #6b7280;
    font-size: 14px;
    display: inline-block;
}

.hotel-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 12px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
}

.hotel-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.hotel-detail-item span:first-child {
    font-size: 20px;
}

.car-details-grid {
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid #10b981;
}

.car-detail-row {
    display: flex;
    gap: 30px;
    margin-bottom: 10px;
}

.car-detail-row:last-child {
    margin-bottom: 0;
}

.car-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.car-detail-item strong {
    color: #10b981;
}
</style>

<div class="rbs-travel-intro">
    <div class="rbs-travel-intro-header">
        <h1><?php the_title(); ?></h1>
        <?php if ($description): ?>
            <div class="description"><?php echo wp_kses_post($description); ?></div>
        <?php endif; ?>
    </div>
    
    <div class="rbs-travel-intro-stats">
        <?php if ($number_of_nights): ?>
            <div class="rbs-travel-stat">
                <div class="rbs-travel-stat-value"><?php echo esc_html($number_of_nights); ?></div>
                <div class="rbs-travel-stat-label">Nachten</div>
            </div>
        <?php endif; ?>
        
        <?php if ($departure_date): ?>
            <div class="rbs-travel-stat">
                <div class="rbs-travel-stat-value"><?php echo $departure_date->format('d M'); ?></div>
                <div class="rbs-travel-stat-label">Vertrek</div>
            </div>
        <?php endif; ?>
        
        <?php if ($price_per_person): ?>
            <div class="rbs-travel-stat">
                <div class="rbs-travel-stat-value">€<?php echo number_format($price_per_person, 0, ',', '.'); ?></div>
                <div class="rbs-travel-stat-label">Per persoon</div>
            </div>
        <?php endif; ?>
        
        <?php if ($total_price): ?>
            <div class="rbs-travel-stat">
                <div class="rbs-travel-stat-value">€<?php echo number_format($total_price, 0, ',', '.'); ?></div>
                <div class="rbs-travel-stat-label">Totaalprijs</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="rbs-timeline">
    <?php
    if (empty($timeline)) {
        echo '<p style="text-align: center; padding: 80px 20px; color: #9ca3af; font-size: 18px;">Geen timeline data beschikbaar. Import eerst een reis vanuit Travel Compositor.</p>';
    } else {
        $current_day = 0;
        $day_count = 0;
        
        foreach ($timeline as $item) {
            // New day header
            if ($item['day'] != $current_day) {
                if ($current_day > 0) {
                    echo '</div></div>'; // Close previous day
                }
                
                $current_day = $item['day'];
                $day_count++;
                
                // Calculate actual date
                $actual_date = '';
                if ($departure_date) {
                    $day_date = clone $departure_date;
                    $day_date->modify('+' . ($current_day - 1) . ' days');
                    $actual_date = ' • ' . strftime('%A %d %B %Y', $day_date->getTimestamp());
                    setlocale(LC_TIME, 'nl_NL.UTF-8', 'nl_NL', 'nld_nld', 'dutch');
                }
                
                echo '<div class="rbs-timeline-day">';
                echo '<div class="rbs-timeline-day-header">';
                echo '<h2>📅 Dag ' . $day_count . $actual_date . '</h2>';
                echo '</div>';
                echo '<div class="rbs-timeline-items">';
            }
            
            $data = $item['data'];
            $type = $item['type'];
            
            echo '<div class="rbs-timeline-item type-' . esc_attr($type) . '">';
            if ($item['time'] != '00:00') {
                echo '<div class="rbs-timeline-time">🕐 ' . esc_html($item['time']) . '</div>';
            }
            
            // Render based on type
            switch ($type) {
                case 'destination':
                    render_destination_item($data);
                    break;
                case 'transport':
                    render_transport_item($data);
                    break;
                case 'hotel':
                    render_hotel_item($data);
                    break;
                case 'cruise':
                    render_cruise_item($data);
                    break;
                case 'car':
                    render_car_item($data);
                    break;
            }
            
            echo '</div>';
        }
        
        if ($current_day > 0) {
            echo '</div></div>'; // Close last day
        }
    }
    ?>
</div>

<?php
// Render functions

function render_destination_item($destination) {
    echo '<span class="rbs-timeline-icon">📍</span>';
    echo '<div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">';
    
    $name = isset($destination['name']) ? $destination['name'] : 'Bestemming';
    $description = isset($destination['description']) ? $destination['description'] : '';
    $images = isset($destination['imageUrls']) && is_array($destination['imageUrls']) ? $destination['imageUrls'] : array();
    $image = !empty($images) ? $images[0] : '';
    
    echo '<div class="rbs-destination-preview">';
    
    if ($image) {
        echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($name) . '" class="rbs-destination-image">';
    }
    
    echo '<div class="rbs-destination-text">';
    echo '<div class="rbs-timeline-title">' . esc_html($name) . '</div>';
    if ($description) {
        $short_desc = wp_trim_words(strip_tags($description), 20);
        echo '<div class="rbs-timeline-details">' . esc_html($short_desc) . '</div>';
    }
    echo '</div>';
    
    echo '<a href="#" class="rbs-button" onclick="alert(\'Meer info over ' . esc_js($name) . '\'); return false;">Lees verder</a>';
    
    echo '</div>';
    echo '</div>';
}

function render_transport_item($transport) {
    $type = isset($transport['transportType']) ? $transport['transportType'] : 'FLIGHT';
    $icon = get_transport_icon($type);
    
    echo '<span class="rbs-timeline-icon">' . $icon . '</span>';
    echo '<div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">';
    
    $type_label = array(
        'FLIGHT' => 'Vlucht',
        'BUS' => 'Bus',
        'TRAIN' => 'Trein',
        'FERRY' => 'Veerboot'
    );
    
    $origin = isset($transport['originCode']) ? $transport['originCode'] : '';
    $destination = isset($transport['targetCode']) ? $transport['targetCode'] : '';
    
    echo '<div class="rbs-timeline-title">';
    echo esc_html($type_label[$type] ?? $type);
    if ($origin && $destination) {
        echo ' ' . esc_html($origin) . ' → ' . esc_html($destination);
    }
    echo '</div>';
    
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($transport['company']) && !empty($transport['transportNumber'])) {
        echo '<div style="color: #6366f1; font-weight: 600; margin-bottom: 8px;">';
        echo esc_html($transport['company']) . ' ' . esc_html($transport['transportNumber']);
        echo '</div>';
    }
    
    if (!empty($transport['departureTime']) && !empty($transport['arrivalTime'])) {
        echo '<div style="margin-bottom: 5px;">';
        echo '🛫 Vertrek: ' . esc_html($transport['departureTime']) . ' • ';
        echo '🛬 Aankomst: ' . esc_html($transport['arrivalTime']);
        if (!empty($transport['duration'])) {
            echo ' • ⏱️ Duur: ' . esc_html($transport['duration']);
        }
        echo '</div>';
    }
    
    echo '</div></div>';
}

function render_hotel_item($hotel) {
    echo '<span class="rbs-timeline-icon">🏨</span>';
    echo '<div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Check-in Hotel</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($hotel['name'])) {
        echo '<div style="font-weight: 600; font-size: 18px; color: #8b5cf6; margin-bottom: 10px;">';
        echo esc_html($hotel['name']);
        echo '</div>';
    }
    
    // Hotel details grid
    echo '<div class="hotel-details-grid">';
    
    if (isset($hotel['nights'])) {
        echo '<div class="hotel-detail-item">';
        echo '<span>🌙</span>';
        echo '<span>' . esc_html($hotel['nights']) . ' nachten</span>';
        echo '</div>';
    }
    
    // Room type
    $room_type = isset($hotel['roomType']) ? $hotel['roomType'] : (isset($hotel['selectedRoom']) ? $hotel['selectedRoom'] : null);
    if ($room_type) {
        echo '<div class="hotel-detail-item">';
        echo '<span>🛏️</span>';
        echo '<span>' . esc_html($room_type) . '</span>';
        echo '</div>';
    }
    
    // Breakfast
    $board = isset($hotel['selectedBoard']) ? $hotel['selectedBoard'] : '';
    $breakfast_included = (stripos($board, 'breakfast') !== false || stripos($board, 'ontbijt') !== false);
    echo '<div class="hotel-detail-item">';
    echo '<span>☕</span>';
    echo '<span>' . ($breakfast_included ? 'Met ontbijt' : 'Zonder ontbijt') . '</span>';
    echo '</div>';
    
    echo '</div>';
    
    // More info button
    echo '<div style="margin-top: 15px;">';
    echo '<a href="#" class="rbs-button" onclick="alert(\'Meer info over hotel\'); return false;">Meer info</a>';
    echo '</div>';
    
    echo '</div></div>';
}

function render_cruise_item($cruise) {
    echo '<span class="rbs-timeline-icon">🚢</span>';
    echo '<div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">';
    
    // Ship name as title
    $ship_name = '';
    if (!empty($cruise['ship'])) {
        $ship_name = $cruise['ship'];
    } elseif (!empty($cruise['shipName'])) {
        $ship_name = $cruise['shipName'];
    }
    
    if ($ship_name) {
        echo '<div class="rbs-timeline-title">' . esc_html($ship_name) . '</div>';
    } else {
        echo '<div class="rbs-timeline-title">Cruise Embark</div>';
    }
    
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($cruise['cruiseLine'])) {
        $cruise_line_labels = array(
            'ROYAL_CARIBBEAN' => 'Royal Caribbean International',
            'RCC' => 'Royal Caribbean International',
            'CARNIVAL' => 'Carnival Cruise Line',
            'MSC' => 'MSC Cruises',
            'NORWEGIAN' => 'Norwegian Cruise Line'
        );
        $cruise_line = isset($cruise_line_labels[$cruise['cruiseLine']]) ? $cruise_line_labels[$cruise['cruiseLine']] : $cruise['cruiseLine'];
        echo '<div style="color: #06b6d4; font-weight: 700; font-size: 16px; margin-bottom: 8px;">' . esc_html($cruise_line) . '</div>';
    }
    
    if (!empty($cruise['nights'])) {
        echo '<div class="rbs-timeline-subtitle">' . esc_html($cruise['nights']) . ' nachten cruise</div>';
    }
    
    // Show itinerary
    if (!empty($cruise['itinerary']) && is_array($cruise['itinerary'])) {
        echo '<div class="cruise-itinerary">';
        echo '<strong style="color: #06b6d4;">📍 Cruise Route:</strong><br><br>';
        foreach ($cruise['itinerary'] as $step) {
            echo '<div class="cruise-itinerary-step">';
            echo '<span class="cruise-itinerary-day">Dag ' . esc_html($step['day']) . '</span>';
            echo '<span class="cruise-itinerary-port">' . esc_html($step['destination']) . '</span>';
            echo '<span class="cruise-itinerary-times">';
            if ($step['arrival'] != '-') echo 'Aan: ' . esc_html($step['arrival']) . ' ';
            if ($step['departure'] != '-') echo 'Vert: ' . esc_html($step['departure']);
            echo '</span>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div></div>';
}

function render_car_item($car) {
    echo '<span class="rbs-timeline-icon">🚗</span>';
    echo '<div style="display: inline-block; vertical-align: top; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Huurauto Ophalen</div>';
    echo '<div class="rbs-timeline-details">';
    
    echo '<div class="car-details-grid">';
    
    // Car type
    if (!empty($car['selectedCarCategory'])) {
        echo '<div class="car-detail-row">';
        echo '<div class="car-detail-item">';
        echo '<span>🚗</span>';
        echo '<strong>Type:</strong> ' . esc_html($car['selectedCarCategory']);
        echo '</div>';
        echo '</div>';
    }
    
    // Pickup location
    $pickup_loc = isset($car['pickupOffice']) ? $car['pickupOffice'] : (isset($car['pickupLocation']) ? $car['pickupLocation'] : '');
    if ($pickup_loc) {
        echo '<div class="car-detail-row">';
        echo '<div class="car-detail-item">';
        echo '<span>📍</span>';
        echo '<strong>Ophalen:</strong> ' . esc_html($pickup_loc);
        echo '</div>';
        echo '</div>';
    }
    
    // Pickup time
    if (!empty($car['pickupTime'])) {
        echo '<div class="car-detail-row">';
        echo '<div class="car-detail-item">';
        echo '<span>🕐</span>';
        echo '<strong>Ophaaltijd:</strong> ' . esc_html($car['pickupTime']);
        echo '</div>';
        echo '</div>';
    }
    
    // Duration
    if (!empty($car['nights']) || !empty($car['duration'])) {
        $days = isset($car['nights']) ? $car['nights'] : $car['duration'];
        echo '<div class="car-detail-row">';
        echo '<div class="car-detail-item">';
        echo '<span>📅</span>';
        echo '<strong>Duur:</strong> ' . esc_html($days) . ' dagen';
        echo '</div>';
        echo '</div>';
    }
    
    // Company
    if (!empty($car['company'])) {
        echo '<div class="car-detail-row">';
        echo '<div class="car-detail-item">';
        echo '<span>🏢</span>';
        echo '<strong>Bedrijf:</strong> ' . esc_html($car['company']);
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div></div>';
}

function get_transport_icon($type) {
    $icons = array(
        'FLIGHT' => '✈️',
        'BUS' => '🚌',
        'TRAIN' => '🚂',
        'FERRY' => '⛴️'
    );
    return isset($icons[$type]) ? $icons[$type] : '🚗';
}
?>

<?php get_footer(); ?>
