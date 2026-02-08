<?php
/**
 * Template Name: Travel Idea - Day-by-Day Timeline
 * Description: Shows a chronological timeline of the entire trip with transports, hotels, cruises, etc.
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

// Build timeline array
$timeline = array();

// Add transports
if (!empty($transports) && is_array($transports)) {
    foreach ($transports as $transport) {
        $day = isset($transport['day']) ? intval($transport['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'transport',
                'time' => isset($transport['departureTime']) ? $transport['departureTime'] : '00:00',
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
                'time' => '18:00', // Default check-in time
                'data' => $hotel
            );
        }
    }
}

// Add cruises
if (!empty($cruises) && is_array($cruises)) {
    foreach ($cruises as $cruise) {
        $from_day = isset($cruise['fromDay']) ? intval($cruise['fromDay']) : 0;
        if ($from_day > 0) {
            $timeline[] = array(
                'day' => $from_day,
                'type' => 'cruise',
                'time' => '08:00', // Default embark time
                'data' => $cruise
            );
        }
    }
}

// Add transfers
if (!empty($transfers) && is_array($transfers)) {
    foreach ($transfers as $transfer) {
        $day = isset($transfer['day']) ? intval($transfer['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'transfer',
                'time' => isset($transfer['pickupTime']) ? $transfer['pickupTime'] : '12:00',
                'data' => $transfer
            );
        }
    }
}

// Add trip spots
if (!empty($trip_spots) && is_array($trip_spots)) {
    foreach ($trip_spots as $spot) {
        $day = isset($spot['day']) ? intval($spot['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'tripspot',
                'time' => '10:00', // Default activity time
                'data' => $spot
            );
        }
    }
}

// Add closed tours
if (!empty($closed_tours) && is_array($closed_tours)) {
    foreach ($closed_tours as $tour) {
        $day_from = isset($tour['dayFrom']) ? intval($tour['dayFrom']) : 0;
        if ($day_from > 0) {
            $timeline[] = array(
                'day' => $day_from,
                'type' => 'closedtour',
                'time' => '09:00', // Default tour time
                'data' => $tour
            );
        }
    }
}

// Add tickets
if (!empty($tickets) && is_array($tickets)) {
    foreach ($tickets as $ticket) {
        $day = isset($ticket['day']) ? intval($ticket['day']) : 0;
        if ($day > 0) {
            $timeline[] = array(
                'day' => $day,
                'type' => 'ticket',
                'time' => '14:00', // Default ticket time
                'data' => $ticket
            );
        }
    }
}

// Add cars
if (!empty($cars) && is_array($cars)) {
    foreach ($cars as $car) {
        $day = isset($car['pickupDay']) ? intval($car['pickupDay']) : 0;
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
    return strcmp($a['time'], $b['time']);
});

// Get departure date for calculating actual dates
$departure_date_str = get_post_meta($post_id, 'travel_departure_date', true);
$departure_date = $departure_date_str ? new DateTime($departure_date_str) : null;

?>

<style>
.rbs-timeline {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.rbs-timeline-day {
    margin-bottom: 50px;
    position: relative;
}

.rbs-timeline-day-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 30px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.rbs-timeline-day-header h2 {
    margin: 0 0 5px 0;
    font-size: 28px;
    font-weight: 700;
}

.rbs-timeline-day-header .date {
    opacity: 0.9;
    font-size: 16px;
}

.rbs-timeline-items {
    padding-left: 40px;
    border-left: 3px solid #e0e0e0;
}

.rbs-timeline-item {
    position: relative;
    margin-bottom: 30px;
    background: white;
    border-radius: 10px;
    padding: 20px 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.rbs-timeline-item:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    transform: translateX(5px);
}

.rbs-timeline-item::before {
    content: '';
    position: absolute;
    left: -49px;
    top: 25px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: white;
    border: 4px solid;
    z-index: 2;
}

.rbs-timeline-item.type-transport::before { border-color: #3b82f6; }
.rbs-timeline-item.type-hotel::before { border-color: #8b5cf6; }
.rbs-timeline-item.type-cruise::before { border-color: #06b6d4; }
.rbs-timeline-item.type-transfer::before { border-color: #10b981; }
.rbs-timeline-item.type-tripspot::before { border-color: #f59e0b; }
.rbs-timeline-item.type-closedtour::before { border-color: #ef4444; }
.rbs-timeline-item.type-ticket::before { border-color: #ec4899; }
.rbs-timeline-item.type-car::before { border-color: #6366f1; }

.rbs-timeline-icon {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    border-radius: 8px;
    font-size: 20px;
    margin-right: 15px;
    vertical-align: middle;
}

.type-transport .rbs-timeline-icon { background: #dbeafe; }
.type-hotel .rbs-timeline-icon { background: #ede9fe; }
.type-cruise .rbs-timeline-icon { background: #cffafe; }
.type-transfer .rbs-timeline-icon { background: #d1fae5; }
.type-tripspot .rbs-timeline-icon { background: #fed7aa; }
.type-closedtour .rbs-timeline-icon { background: #fee2e2; }
.type-ticket .rbs-timeline-icon { background: #fce7f3; }
.type-car .rbs-timeline-icon { background: #e0e7ff; }

.rbs-timeline-time {
    color: #6b7280;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 8px;
}

.rbs-timeline-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 10px;
    color: #111827;
}

.rbs-timeline-details {
    color: #4b5563;
    line-height: 1.6;
}

.rbs-timeline-subtitle {
    color: #6b7280;
    font-size: 14px;
    margin-top: 5px;
}

.rbs-timeline-segments {
    margin-top: 15px;
    padding: 15px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #3b82f6;
}

.rbs-timeline-segment {
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.rbs-timeline-segment:last-child {
    border-bottom: none;
}

.cruise-itinerary {
    margin-top: 20px;
}

.cruise-itinerary-step {
    display: flex;
    padding: 15px;
    background: #f0f9ff;
    border-radius: 8px;
    margin-bottom: 10px;
    align-items: center;
}

.cruise-itinerary-day {
    font-weight: 700;
    color: #0369a1;
    margin-right: 20px;
    min-width: 80px;
}

.cruise-itinerary-port {
    flex: 1;
    font-weight: 600;
}

.cruise-itinerary-times {
    color: #64748b;
    font-size: 14px;
}
</style>

<div class="rbs-timeline">
    <header style="text-align: center; margin-bottom: 60px;">
        <h1 style="font-size: 42px; margin-bottom: 15px; color: #111827;"><?php the_title(); ?></h1>
        <p style="font-size: 18px; color: #6b7280;">Dag-tot-dag overzicht van je reis</p>
    </header>

    <?php
    if (empty($timeline)) {
        echo '<p style="text-align: center; padding: 60px 20px; color: #9ca3af;">Geen timeline data beschikbaar. Import eerst een reis vanuit Travel Compositor.</p>';
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
                    $actual_date = ' • ' . $day_date->format('d F Y');
                }
                
                echo '<div class="rbs-timeline-day">';
                echo '<div class="rbs-timeline-day-header">';
                echo '<h2>Dag ' . $day_count . $actual_date . '</h2>';
                echo '</div>';
                echo '<div class="rbs-timeline-items">';
            }
            
            $data = $item['data'];
            $type = $item['type'];
            
            echo '<div class="rbs-timeline-item type-' . esc_attr($type) . '">';
            echo '<div class="rbs-timeline-time">🕐 ' . esc_html($item['time']) . '</div>';
            
            // Render based on type
            switch ($type) {
                case 'transport':
                    render_transport_item($data);
                    break;
                case 'hotel':
                    render_hotel_item($data);
                    break;
                case 'cruise':
                    render_cruise_item($data);
                    break;
                case 'transfer':
                    render_transfer_item($data);
                    break;
                case 'tripspot':
                    render_tripspot_item($data);
                    break;
                case 'closedtour':
                    render_closedtour_item($data);
                    break;
                case 'ticket':
                    render_ticket_item($data);
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
function render_transport_item($transport) {
    $type = isset($transport['transportType']) ? $transport['transportType'] : 'FLIGHT';
    $icon = get_transport_icon($type);
    
    echo '<span class="rbs-timeline-icon">' . $icon . '</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    
    $type_label = array(
        'FLIGHT' => 'Vlucht',
        'BUS' => 'Bus',
        'TRAIN' => 'Trein',
        'FERRY' => 'Veerboot'
    );
    
    echo '<div class="rbs-timeline-title">';
    echo esc_html($type_label[$type] ?? $type);
    if (!empty($transport['company']) && !empty($transport['transportNumber'])) {
        echo ' ' . esc_html($transport['company']) . ' ' . esc_html($transport['transportNumber']);
    }
    echo '</div>';
    
    echo '<div class="rbs-timeline-details">';
    echo '<strong>' . esc_html($transport['originCode']) . '</strong> → <strong>' . esc_html($transport['targetCode']) . '</strong>';
    
    if (!empty($transport['departureTime']) && !empty($transport['arrivalTime'])) {
        echo '<div class="rbs-timeline-subtitle">';
        echo 'Vertrek: ' . esc_html($transport['departureTime']) . ' • Aankomst: ' . esc_html($transport['arrivalTime']);
        if (!empty($transport['duration'])) {
            echo ' • Duur: ' . esc_html($transport['duration']);
        }
        echo '</div>';
    }
    
    // Show segments for multi-stop flights
    if (!empty($transport['segments']) && count($transport['segments']) > 0) {
        echo '<div class="rbs-timeline-segments">';
        echo '<strong>✈️ Vluchtsegmenten:</strong><br>';
        foreach ($transport['segments'] as $idx => $segment) {
            echo '<div class="rbs-timeline-segment">';
            echo '<strong>Segment ' . ($idx + 1) . ':</strong> ';
            echo esc_html($segment['departureAirport']) . ' → ' . esc_html($segment['arrivalAirport']);
            if (!empty($segment['flightNumber'])) {
                echo ' • Vlucht ' . esc_html($segment['flightNumber']);
            }
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div></div>';
}

function render_hotel_item($hotel) {
    echo '<span class="rbs-timeline-icon">🏨</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Check-in Hotel</div>';
    echo '<div class="rbs-timeline-details">';
    
    $hotel_name = '';
    if (isset($hotel['hotelData']['name'])) {
        $hotel_name = $hotel['hotelData']['name'];
    } elseif (isset($hotel['name'])) {
        $hotel_name = $hotel['name'];
    }
    
    if ($hotel_name) {
        echo '<strong>' . esc_html($hotel_name) . '</strong>';
    }
    
    if (isset($hotel['nights'])) {
        echo '<div class="rbs-timeline-subtitle">' . esc_html($hotel['nights']) . ' nachten</div>';
    }
    
    echo '</div></div>';
}

function render_cruise_item($cruise) {
    echo '<span class="rbs-timeline-icon">🚢</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Cruise Embark</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($cruise['ship'])) {
        echo '<strong>' . esc_html($cruise['ship']) . '</strong>';
    }
    
    if (!empty($cruise['cruiseLine'])) {
        echo ' • ' . esc_html($cruise['cruiseLine']);
    }
    
    if (!empty($cruise['nights'])) {
        echo '<div class="rbs-timeline-subtitle">' . esc_html($cruise['nights']) . ' nachten cruise</div>';
    }
    
    // Show itinerary if available
    if (!empty($cruise['itinerary']) && is_array($cruise['itinerary'])) {
        echo '<div class="cruise-itinerary">';
        echo '<strong>📍 Cruise Route:</strong>';
        foreach ($cruise['itinerary'] as $step) {
            echo '<div class="cruise-itinerary-step">';
            echo '<div class="cruise-itinerary-day">Dag ' . esc_html($step['day']) . '</div>';
            echo '<div class="cruise-itinerary-port">' . esc_html($step['destination']) . '</div>';
            echo '<div class="cruise-itinerary-times">';
            if ($step['arrival'] != '-') echo 'Aan: ' . esc_html($step['arrival']) . ' ';
            if ($step['departure'] != '-') echo 'Vert: ' . esc_html($step['departure']);
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    echo '</div></div>';
}

function render_transfer_item($transfer) {
    echo '<span class="rbs-timeline-icon">🚐</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Transfer</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($transfer['origin']) && !empty($transfer['destination'])) {
        echo esc_html($transfer['origin']) . ' → ' . esc_html($transfer['destination']);
    }
    
    echo '</div></div>';
}

function render_tripspot_item($spot) {
    echo '<span class="rbs-timeline-icon">📍</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Bezienswaardheid</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($spot['name'])) {
        echo '<strong>' . esc_html($spot['name']) . '</strong>';
    }
    
    echo '</div></div>';
}

function render_closedtour_item($tour) {
    echo '<span class="rbs-timeline-icon">🗺️</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Rondleiding</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($tour['name'])) {
        echo '<strong>' . esc_html($tour['name']) . '</strong>';
    }
    
    echo '</div></div>';
}

function render_ticket_item($ticket) {
    echo '<span class="rbs-timeline-icon">🎫</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Ticket</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($ticket['name'])) {
        echo '<strong>' . esc_html($ticket['name']) . '</strong>';
    }
    
    echo '</div></div>';
}

function render_car_item($car) {
    echo '<span class="rbs-timeline-icon">🚗</span>';
    echo '<div style="display: inline-block; vertical-align: middle; width: calc(100% - 60px);">';
    echo '<div class="rbs-timeline-title">Huurauto Ophalen</div>';
    echo '<div class="rbs-timeline-details">';
    
    if (!empty($car['carModel'])) {
        echo '<strong>' . esc_html($car['carModel']) . '</strong>';
    }
    
    if (!empty($car['supplier'])) {
        echo ' • ' . esc_html($car['supplier']);
    }
    
    echo '</div></div>';
}

function get_transport_icon($type) {
    $icons = array(
        'FLIGHT' => '✈️',
        'BUS' => '🚌',
        'TRAIN' => '🚂',
        'FERRY' => '⛴️'
    );
    return $icons[$type] ?? '🚆';
}

get_footer();
