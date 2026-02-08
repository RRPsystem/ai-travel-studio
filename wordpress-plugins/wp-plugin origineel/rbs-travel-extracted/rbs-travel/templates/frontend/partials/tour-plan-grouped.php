<?php
/**
 * Grouped Tour Plan Component
 * Groups destinations, hotels, and cruises by day ranges for clean presentation
 */

if (!defined('ABSPATH')) exit;

// Build all items with their day ranges
$all_items = array();

// Add destinations
if (!empty($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $destination) {
        if (!empty($destination['fromDay']) && $destination['fromDay'] > 0) {
            $all_items[] = array(
                'type' => 'destination',
                'fromDay' => $destination['fromDay'],
                'toDay' => $destination['toDay'],
                'data' => $destination
            );
        }
    }
}

// Add hotels
if (!empty($travel_meta_fields['travel_hotels'])) {
    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
        $fromDay = isset($hotel['fromDay']) ? $hotel['fromDay'] : (isset($hotel['day']) ? $hotel['day'] : 0);
        $toDay = isset($hotel['toDay']) ? $hotel['toDay'] : ($fromDay + (isset($hotel['nights']) ? $hotel['nights'] : 0));
        
        if ($fromDay > 0) {
            $all_items[] = array(
                'type' => 'hotel',
                'fromDay' => $fromDay,
                'toDay' => $toDay,
                'data' => $hotel
            );
        }
    }
}

// Add cruises
if (!empty($travel_meta_fields['travel_cruises'])) {
    foreach ($travel_meta_fields['travel_cruises'] as $cruise) {
        if (!empty($cruise['fromDay']) && $cruise['fromDay'] > 0) {
            $all_items[] = array(
                'type' => 'cruise',
                'fromDay' => $cruise['fromDay'],
                'toDay' => $cruise['toDay'],
                'data' => $cruise
            );
        }
    }
}

// Add transports (flights, transfers, etc.)
if (!empty($travel_meta_fields['travel_transports'])) {
    foreach ($travel_meta_fields['travel_transports'] as $transport) {
        $day = isset($transport['day']) ? intval($transport['day']) : 0;
        if ($day > 0) {
            $all_items[] = array(
                'type' => 'transport',
                'fromDay' => $day,
                'toDay' => $day,
                'data' => $transport
            );
        }
    }
}

// Add transfers
if (!empty($travel_meta_fields['travel_transfers'])) {
    foreach ($travel_meta_fields['travel_transfers'] as $transfer) {
        $day = isset($transfer['day']) ? intval($transfer['day']) : 0;
        if ($day > 0) {
            $all_items[] = array(
                'type' => 'transfer',
                'fromDay' => $day,
                'toDay' => $day,
                'data' => $transfer
            );
        }
    }
}

// Sort by fromDay, then by type priority (flights/transports first)
usort($all_items, function($a, $b) {
    // First sort by day
    if ($a['fromDay'] !== $b['fromDay']) {
        return $a['fromDay'] - $b['fromDay'];
    }
    // Within same day: transports/flights first, then transfers, then destinations, then hotels, then cruises
    $type_priority = array('transport' => 1, 'transfer' => 2, 'destination' => 3, 'hotel' => 4, 'cruise' => 5);
    $a_priority = isset($type_priority[$a['type']]) ? $type_priority[$a['type']] : 99;
    $b_priority = isset($type_priority[$b['type']]) ? $type_priority[$b['type']] : 99;
    return $a_priority - $b_priority;
});

// Group items by day range
$grouped_days = array();
foreach ($all_items as $item) {
    $key = $item['fromDay'] . '-' . $item['toDay'];
    if (!isset($grouped_days[$key])) {
        $grouped_days[$key] = array(
            'fromDay' => $item['fromDay'],
            'toDay' => $item['toDay'],
            'items' => array()
        );
    }
    $grouped_days[$key]['items'][] = $item;
}

// Helper function for ordinal numbers
if (!function_exists('get_day_ordinal')) {
    function get_day_ordinal($number) {
        $suffixes = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $suffixes[$number % 10];
        }
    }
}

// Helper function to get icon URL from settings
if (!function_exists('rbs_get_type_icon')) {
    function rbs_get_type_icon($type) {
        $icon_file = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('icon_' . $type, '');
        if (!empty($icon_file)) {
            return RBS_TRAVEL_PLUGIN_URL . 'assets/images/icons/' . $icon_file;
        }
        return '';
    }
}

// Get all icon URLs
$icons = array(
    'flight' => rbs_get_type_icon('flight'),
    'destination' => rbs_get_type_icon('destination'),
    'hotel' => rbs_get_type_icon('hotel'),
    'cruise' => rbs_get_type_icon('cruise'),
    'transfer' => rbs_get_type_icon('transfer'),
    'transport' => rbs_get_type_icon('transport'),
    'activity' => rbs_get_type_icon('activity'),
);
?>

<style>
/* ========================================
   TOUR PLAN TIMELINE LAYOUT
   ======================================== */
.tour-plan {
    position: relative;
    padding-left: 0;
}

.tour-plan h2 {
    padding-left: 0;
    margin-bottom: 30px;
}

/* Timeline line */
.tour-plan::before {
    display: none;
}

/* Day Group Card with shadow */
.day-group-card {
    position: relative;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(16,24,40,0.08);
    margin-bottom: 28px;
    overflow: hidden;
    border: 1px solid #eef2f7;
    border-top: 3px solid var(--primary, #4a6cf7);
}

/* Timeline dot */
.day-group-card::before {
    display: none;
}

.day-group-header {
    background: #ffffff;
    color: #111827;
    padding: 16px 20px;
    font-weight: 700;
    font-size: 15px;
    border-bottom: 1px solid #eef2f7;
}

.day-group-content {
    padding: 16px;
}

/* Day Item Card */
.day-item {
    background: #ffffff;
    border-radius: 14px;
    margin-bottom: 15px;
    overflow: hidden;
    border: 1px solid #eef2f7;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.day-item:last-child {
    margin-bottom: 0;
}

.day-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(16,24,40,0.12);
}

.day-item-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.day-item-content {
    padding: 15px;
}

.day-item-title {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.day-item-snippet {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 12px;
}

.day-item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}

.btn-view-details {
    display: inline-block;
    padding: 10px 20px;
    background: var(--primary, #4a6cf7);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-view-details:hover {
    opacity: 0.9;
}

/* Professional Type Badges with Icons */
.item-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.item-type-badge img {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

.item-type-badge.destination { }
.item-type-badge.hotel { }
.item-type-badge.cruise { }
.item-type-badge.flight { }
.item-type-badge.transfer { }
.item-type-badge.transport { }

.day-item-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 12px;
    color: #374151;
}

.day-item-meta-item::before {
    content: '';
    width: 4px;
    height: 4px;
    background: #999;
    border-radius: 50%;
}

.day-item-meta-item:first-child::before {
    display: none;
}

/* Responsive */
@media (max-width: 768px) {
    .tour-plan {
        padding-left: 0;
    }
    .tour-plan::before {
        display: none;
    }
    .day-group-card::before {
        display: none;
    }
    .day-item-image {
        height: 150px;
    }
}
</style>

<section class="tour-plan">
    <h2>Dag-voor-dag Programma</h2>
    
    <?php foreach ($grouped_days as $group): ?>
        <?php
        $day_label = 'Day ' . get_day_ordinal($group['fromDay']);
        if ($group['toDay'] > $group['fromDay']) {
            $day_label .= ' - ' . get_day_ordinal($group['toDay']);
        }
        ?>
        
        <div class="day-group-card">
            <div class="day-group-header"><?php echo $day_label; ?></div>
            <div class="day-group-content">
                
                <?php foreach ($group['items'] as $item): ?>
                    
                    <?php if ($item['type'] === 'destination'): 
                        $dest = $item['data'];
                        $dest_image = !empty($dest['imageUrls'][0]) ? $dest['imageUrls'][0] : '';
                    ?>
                        <div class="day-item">
                            <?php if ($dest_image): ?>
                                <img src="<?php echo esc_url($dest_image); ?>" alt="<?php echo esc_attr($dest['name']); ?>" class="day-item-image">
                            <?php endif; ?>
                            
                            <div class="day-item-content">
                                <div class="day-item-title">
                                    <span class="item-type-badge destination">
                                        <?php if (!empty($icons['destination'])): ?><img src="<?php echo esc_url($icons['destination']); ?>" alt=""><?php endif; ?>
                                        Bestemming
                                    </span>
                                    <?php echo esc_html($dest['name']); ?>
                                </div>
                                
                                <?php if (!empty($dest['description'])): ?>
                                    <div class="day-item-snippet">
                                        <?php echo wp_kses_post(substr($dest['description'], 0, 150)); ?>...
                                    </div>
                                <?php endif; ?>
                                
                                <div class="day-item-meta">
                                    <?php if (!empty($dest['country'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo esc_html($dest['country']); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $nights = $dest['toDay'] - $dest['fromDay'];
                                    if ($nights > 0):
                                    ?>
                                        <div class="day-item-meta-item">
                                            <?php echo $nights; ?> <?php echo $nights == 1 ? 'nacht' : 'nachten'; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($dest['imageUrls'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo count($dest['imageUrls']); ?> foto's
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="btn-view-details" 
                                        onclick="showDestinationDetail(this); return false;"
                                        data-dest='<?php echo htmlspecialchars(json_encode($dest), ENT_QUOTES, 'UTF-8'); ?>'>
                                    Bekijk Details →
                                </button>
                            </div>
                        </div>
                    
                    <?php elseif ($item['type'] === 'hotel'): 
                        $hotel = $item['data'];
                        $hotel_data = !empty($hotel['hotelData']) ? $hotel['hotelData'] : $hotel;
                        
                        // Get first image
                        $hotel_image = '';
                        if (!empty($hotel_data['imageUrls'][0])) {
                            $hotel_image = $hotel_data['imageUrls'][0];
                        } elseif (!empty($hotel_data['images'][0]['url'])) {
                            $hotel_image = $hotel_data['images'][0]['url'];
                        }
                        
                        // Get stars
                        $stars = 0;
                        if (!empty($hotel_data['stars'])) {
                            $stars = $hotel_data['stars'];
                        } elseif (!empty($hotel_data['category'])) {
                            $stars = intval(preg_replace('/[^0-9]/', '', $hotel_data['category']));
                        }
                        
                        // Get city
                        $city = '';
                        if (!empty($hotel_data['city'])) {
                            $city = $hotel_data['city'];
                        } elseif (!empty($hotel_data['destination']['name'])) {
                            $city = $hotel_data['destination']['name'];
                        }
                    ?>
                        <div class="day-item">
                            <?php if ($hotel_image): ?>
                                <img src="<?php echo esc_url($hotel_image); ?>" alt="<?php echo esc_attr($hotel_data['name']); ?>" class="day-item-image">
                            <?php endif; ?>
                            
                            <div class="day-item-content">
                                <div class="day-item-title">
                                    <span class="item-type-badge hotel">
                                        <?php if (!empty($icons['hotel'])): ?><img src="<?php echo esc_url($icons['hotel']); ?>" alt=""><?php endif; ?>
                                        Hotel
                                    </span>
                                    <?php echo esc_html($hotel_data['name']); ?>
                                </div>
                                
                                <?php if (!empty($hotel_data['description'])): ?>
                                    <div class="day-item-snippet">
                                        <?php echo wp_kses_post(substr($hotel_data['description'], 0, 150)); ?>...
                                    </div>
                                <?php endif; ?>
                                
                                <div class="day-item-meta">
                                    <?php if ($stars > 0): ?>
                                        <div class="day-item-meta-item">
                                            ⭐ <?php echo $stars; ?> sterren
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Get room type from multiple possible locations
                                    $room_type = '';
                                    if (!empty($hotel_data['roomType'])) {
                                        $room_type = $hotel_data['roomType'];
                                    } elseif (!empty($hotel['roomType'])) {
                                        $room_type = $hotel['roomType'];
                                    } elseif (!empty($hotel_data['selectedRoom'])) {
                                        $room_type = $hotel_data['selectedRoom'];
                                    } elseif (!empty($hotel['selectedRoom'])) {
                                        $room_type = $hotel['selectedRoom'];
                                    } elseif (!empty($hotel_data['room'])) {
                                        $room_type = $hotel_data['room'];
                                    } elseif (!empty($hotel_data['hotelData']['roomType'])) {
                                        $room_type = $hotel_data['hotelData']['roomType'];
                                    } elseif (!empty($hotel_data['hotelData']['selectedRoom'])) {
                                        $room_type = $hotel_data['hotelData']['selectedRoom'];
                                    }
                                    // Add roomType to hotel_data for JavaScript
                                    if ($room_type) {
                                        $hotel_data['roomType'] = $room_type;
                                    }
                                    if ($room_type): ?>
                                        <div class="day-item-meta-item">
                                            🛏️ <?php echo esc_html($room_type); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Get meal plan from multiple possible locations
                                    $meal_plan = '';
                                    if (!empty($hotel_data['mealPlan'])) {
                                        $meal_plan = $hotel_data['mealPlan'];
                                    } elseif (!empty($hotel['mealPlan'])) {
                                        $meal_plan = $hotel['mealPlan'];
                                    } elseif (!empty($hotel_data['board'])) {
                                        $meal_plan = $hotel_data['board'];
                                    } elseif (!empty($hotel_data['boardType'])) {
                                        $meal_plan = $hotel_data['boardType'];
                                    } elseif (!empty($hotel_data['hotelData']['mealPlan'])) {
                                        $meal_plan = $hotel_data['hotelData']['mealPlan'];
                                    } elseif (!empty($hotel_data['hotelData']['board'])) {
                                        $meal_plan = $hotel_data['hotelData']['board'];
                                    }
                                    // Add mealPlan to hotel_data for JavaScript
                                    if ($meal_plan) {
                                        $hotel_data['mealPlan'] = $meal_plan;
                                    }
                                    if ($meal_plan): ?>
                                        <div class="day-item-meta-item">
                                            🍽️ <?php echo esc_html($meal_plan); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="btn-view-details" 
                                        onclick="showHotelDetail(this); return false;"
                                        data-hotel='<?php echo htmlspecialchars(json_encode($hotel_data), ENT_QUOTES, 'UTF-8'); ?>'>
                                    Bekijk Hotel Details →
                                </button>
                            </div>
                        </div>
                    
                    <?php elseif ($item['type'] === 'cruise'): 
                        $cruise = $item['data'];
                        // Safely get cruise image - handle different data structures
                        $cruise_image = '';
                        if (!empty($cruise['images']) && is_array($cruise['images'])) {
                            $first_image = reset($cruise['images']);
                            if (is_string($first_image)) {
                                $cruise_image = $first_image;
                            } elseif (is_array($first_image) && !empty($first_image['url'])) {
                                $cruise_image = $first_image['url'];
                            }
                        } elseif (!empty($cruise['imageUrls']) && is_array($cruise['imageUrls'])) {
                            $cruise_image = reset($cruise['imageUrls']);
                        }
                    ?>
                        <div class="day-item">
                            <?php if ($cruise_image): ?>
                                <img src="<?php echo esc_url($cruise_image); ?>" alt="Cruise" class="day-item-image">
                            <?php endif; ?>
                            
                            <div class="day-item-content">
                                <div class="day-item-title">
                                    <span class="item-type-badge cruise">
                                        <?php if (!empty($icons['cruise'])): ?><img src="<?php echo esc_url($icons['cruise']); ?>" alt=""><?php endif; ?>
                                        Cruise
                                    </span>
                                    <?php echo isset($cruise['name']) ? esc_html($cruise['name']) : 'Cruise'; ?>
                                </div>
                                
                                <div class="day-item-snippet">
                                    Geniet van een prachtige cruise 
                                    <?php if (!empty($cruise['destinations'])): ?>
                                        naar <?php 
                                        $dest_names = array_map(function($d) { 
                                            return is_array($d) ? $d['name'] : $d; 
                                        }, array_slice($cruise['destinations'], 0, 3));
                                        echo implode(', ', $dest_names); 
                                        ?>
                                        <?php if (count($cruise['destinations']) > 3): ?>
                                            en meer
                                        <?php endif; ?>
                                    <?php endif; ?>.
                                </div>
                                
                                <div class="day-item-meta">
                                    <?php if (!empty($cruise['embarkDate'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo date('d M', strtotime($cruise['embarkDate'])); ?> - <?php echo date('d M', strtotime($cruise['disembarkDate'])); ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($cruise['nights'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo $cruise['nights']; ?> nachten
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($cruise['destinations'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo count($cruise['destinations']); ?> havens
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($cruise['cabin'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo esc_html($cruise['cabin']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php 
                                // Create a safe subset of cruise data for JSON
                                // Debug: log available cruise fields to see what's in the data
                                error_log('RBS Cruise Data Keys: ' . implode(', ', array_keys($cruise)));
                                error_log('RBS Cruise ship: ' . (isset($cruise['ship']) ? $cruise['ship'] : 'NOT SET'));
                                error_log('RBS Cruise shipName: ' . (isset($cruise['shipName']) ? $cruise['shipName'] : 'NOT SET'));
                                error_log('RBS Cruise cruiseLine: ' . (isset($cruise['cruiseLine']) ? $cruise['cruiseLine'] : 'NOT SET'));
                                error_log('RBS Cruise category: ' . (isset($cruise['category']) ? $cruise['category'] : 'NOT SET'));
                                error_log('RBS Cruise cabin: ' . (isset($cruise['cabin']) ? $cruise['cabin'] : 'NOT SET'));
                                
                                // Get disembark port with multiple fallbacks
                                $disembark_port = '';
                                if (!empty($cruise['disembarkPort'])) {
                                    $disembark_port = $cruise['disembarkPort'];
                                } elseif (!empty($cruise['destinationPort'])) {
                                    $disembark_port = $cruise['destinationPort'];
                                } elseif (!empty($cruise['toPort'])) {
                                    $disembark_port = $cruise['toPort'];
                                }
                                
                                $cruise_safe = array(
                                    'name' => isset($cruise['name']) ? $cruise['name'] : 'Cruise',
                                    'images' => array(),
                                    'destinations' => isset($cruise['destinations']) ? $cruise['destinations'] : array(),
                                    'embarkDate' => isset($cruise['embarkDate']) ? $cruise['embarkDate'] : '',
                                    'disembarkDate' => isset($cruise['disembarkDate']) ? $cruise['disembarkDate'] : '',
                                    'nights' => isset($cruise['nights']) ? $cruise['nights'] : '',
                                    'cabin' => isset($cruise['cabin']) ? $cruise['cabin'] : '',
                                    'category' => isset($cruise['category']) ? $cruise['category'] : (isset($cruise['selectedCategory']) ? $cruise['selectedCategory'] : ''),
                                    'id' => isset($cruise['id']) ? $cruise['id'] : '',
                                    'itineraryUrl' => isset($cruise['itineraryUrl']) ? $cruise['itineraryUrl'] : '',
                                    'embarkPort' => isset($cruise['embarkPort']) ? $cruise['embarkPort'] : (isset($cruise['originPort']) ? $cruise['originPort'] : ''),
                                    'disembarkPort' => $disembark_port,
                                    'cruiseLine' => isset($cruise['cruiseLine']) ? $cruise['cruiseLine'] : '',
                                    'ship' => isset($cruise['ship']) ? $cruise['ship'] : (isset($cruise['shipName']) ? $cruise['shipName'] : ''),
                                    'shipName' => isset($cruise['shipName']) ? $cruise['shipName'] : (isset($cruise['ship']) ? $cruise['ship'] : ''),
                                );
                                // Add images safely
                                if (!empty($cruise['images']) && is_array($cruise['images'])) {
                                    foreach ($cruise['images'] as $img) {
                                        if (is_string($img)) {
                                            $cruise_safe['images'][] = $img;
                                        } elseif (is_array($img) && !empty($img['url'])) {
                                            $cruise_safe['images'][] = $img['url'];
                                        }
                                    }
                                }
                                if (!empty($cruise['imageUrls']) && is_array($cruise['imageUrls'])) {
                                    foreach ($cruise['imageUrls'] as $img) {
                                        if (is_string($img)) {
                                            $cruise_safe['images'][] = $img;
                                        }
                                    }
                                }
                                ?>
                                <button class="btn-view-details" 
                                        onclick="showCruiseDetail(this); return false;"
                                        data-cruise='<?php echo htmlspecialchars(json_encode($cruise_safe), ENT_QUOTES, 'UTF-8'); ?>'>
                                    Bekijk Cruise Details →
                                </button>
                            </div>
                        </div>
                    
                    <?php elseif ($item['type'] === 'transport'): 
                        $transport = $item['data'];
                        $transport_type = isset($transport['type']) ? strtolower($transport['type']) : 'flight';
                        $type_label = 'Vlucht';
                        if ($transport_type === 'train') $type_label = 'Trein';
                        elseif ($transport_type === 'bus') $type_label = 'Bus';
                        elseif ($transport_type === 'ferry') $type_label = 'Ferry';
                    ?>
                        <div class="day-item transport-item" style="background: #ffffff; border-left: 4px solid var(--primary, #4a6cf7);">
                            <div class="day-item-content" style="padding: 20px;">
                                <div class="day-item-title">
                                    <span class="item-type-badge transport">
                                        <?php if (!empty($icons['flight'])): ?><img src="<?php echo esc_url($icons['flight']); ?>" alt=""><?php endif; ?>
                                        <?php echo $type_label; ?>
                                    </span>
                                    <?php echo esc_html($transport['company'] ?? ''); ?> <?php echo esc_html($transport['transportNumber'] ?? ''); ?>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 20px; margin: 15px 0; font-size: 16px;">
                                    <div style="text-align: center;">
                                        <div style="font-weight: 700; font-size: 18px;"><?php echo esc_html($transport['originDestinationCode'] ?? ''); ?></div>
                                        <div style="color: #666; font-size: 14px;"><?php echo esc_html($transport['departureTime'] ?? ''); ?></div>
                                    </div>
                                    <div style="flex: 1; text-align: center;">
                                        <div style="border-top: 2px solid var(--primary, #4a6cf7); position: relative; margin: 10px 0;">
                                            <span style="position: absolute; top: -8px; left: 50%; transform: translateX(-50%); background: #ffffff; padding: 0 10px; font-size: 12px; color: var(--primary, #4a6cf7);">➔</span>
                                        </div>
                                    </div>
                                    <div style="text-align: center;">
                                        <div style="font-weight: 700; font-size: 18px;"><?php echo esc_html($transport['targetDestinationCode'] ?? ''); ?></div>
                                        <div style="color: #666; font-size: 14px;"><?php echo esc_html($transport['arrivalTime'] ?? ''); ?></div>
                                    </div>
                                </div>
                                
                                <div class="day-item-meta">
                                    <?php if (!empty($transport['departureDate'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo date('d M Y', strtotime($transport['departureDate'])); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($transport['class'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo esc_html($transport['class']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($transport['baggageIncluded'])): ?>
                                        <div class="day-item-meta-item">
                                            Bagage inbegrepen
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    
                    <?php elseif ($item['type'] === 'transfer'): 
                        $transfer = $item['data'];
                    ?>
                        <div class="day-item transfer-item" style="background: #ffffff; border-left: 4px solid var(--primary, #4a6cf7);">
                            <div class="day-item-content" style="padding: 20px;">
                                <div class="day-item-title">
                                    <span class="item-type-badge transfer">
                                        <?php if (!empty($icons['transfer'])): ?><img src="<?php echo esc_url($icons['transfer']); ?>" alt=""><?php endif; ?>
                                        Transfer
                                    </span>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 20px; margin: 15px 0; font-size: 16px;">
                                    <div style="text-align: center;">
                                        <div style="font-weight: 700;"><?php echo esc_html($transfer['from'] ?? $transfer['origin'] ?? 'Vertrek'); ?></div>
                                    </div>
                                    <div style="flex: 1; text-align: center;">
                                        <div style="border-top: 2px solid var(--primary, #4a6cf7); position: relative; margin: 10px 0;">
                                            <span style="position: absolute; top: -8px; left: 50%; transform: translateX(-50%); background: #ffffff; padding: 0 10px; font-size: 12px; color: var(--primary, #4a6cf7);">➔</span>
                                        </div>
                                    </div>
                                    <div style="text-align: center;">
                                        <div style="font-weight: 700;"><?php echo esc_html($transfer['to'] ?? $transfer['destination'] ?? 'Aankomst'); ?></div>
                                    </div>
                                </div>
                                
                                <div class="day-item-meta">
                                    <?php if (!empty($transfer['type'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo esc_html($transfer['type']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($transfer['duration'])): ?>
                                        <div class="day-item-meta-item">
                                            <?php echo esc_html($transfer['duration']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    
                    <?php endif; ?>
                    
                <?php endforeach; ?>
                
            </div>
        </div>
        
    <?php endforeach; ?>
</section>
