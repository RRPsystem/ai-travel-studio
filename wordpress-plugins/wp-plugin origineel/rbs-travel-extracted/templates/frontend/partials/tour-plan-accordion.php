<?php
/**
 * Tour Plan Accordion Component
 * Day-by-day accordion with destinations, cruises, and hotels
 */

if (!defined('ABSPATH')) exit;

// Build day-by-day items
$day_items = array();

// Add destinations
if (!empty($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $destination) {
        $day_items[] = array(
            'type' => 'destination',
            'fromDay' => isset($destination['fromDay']) ? $destination['fromDay'] : 0,
            'toDay' => isset($destination['toDay']) ? $destination['toDay'] : 0,
            'data' => $destination
        );
    }
}

// Add cruises
if (!empty($travel_meta_fields['travel_cruises'])) {
    foreach ($travel_meta_fields['travel_cruises'] as $cruise) {
        $day_items[] = array(
            'type' => 'cruise',
            'fromDay' => isset($cruise['fromDay']) ? $cruise['fromDay'] : 0,
            'toDay' => isset($cruise['toDay']) ? $cruise['toDay'] : 0,
            'data' => $cruise
        );
    }
}

// Add hotels
if (!empty($travel_meta_fields['travel_hotels'])) {
    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
        $day_items[] = array(
            'type' => 'hotel',
            'fromDay' => isset($hotel['fromDay']) ? $hotel['fromDay'] : 0,
            'toDay' => isset($hotel['toDay']) ? $hotel['toDay'] : 0,
            'data' => $hotel
        );
    }
}

// Sort by fromDay
usort($day_items, function($a, $b) {
    return $a['fromDay'] - $b['fromDay'];
});

// Get ordinal suffix
function get_ordinal($number) {
    $suffixes = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $suffixes[$number % 10];
    }
}
?>

<section class="tour-plan">
    <h2>Tour Plan</h2>
    
    <?php foreach ($day_items as $index => $item): ?>
        <?php
        $day_label = 'Day ' . get_ordinal($item['fromDay']);
        if ($item['toDay'] > $item['fromDay']) {
            $day_label .= ' - ' . get_ordinal($item['toDay']);
        }
        ?>
        
        <div class="day-accordion">
            <div class="day-header">
                <div class="day-title"><?php echo $day_label; ?></div>
                <div class="day-arrow">▼</div>
            </div>
            
            <div class="day-content">
                <?php if ($item['type'] === 'destination'): 
                    $destination = $item['data'];
                ?>
                    <div class="day-body">
                        <?php if (!empty($destination['imageUrls'][0])): ?>
                            <img src="<?php echo esc_url($destination['imageUrls'][0]); ?>" 
                                 alt="<?php echo esc_attr($destination['name']); ?>" 
                                 class="day-image">
                        <?php endif; ?>
                        
                        <div class="day-details">
                            <h4><?php echo esc_html($destination['name']); ?></h4>
                            
                            <?php if (!empty($destination['description'])): ?>
                                <div class="day-description">
                                    <?php echo wp_kses_post(substr($destination['description'], 0, 300)); ?>...
                                </div>
                            <?php endif; ?>
                            
                            <div class="day-info-items">
                                <?php if (!empty($destination['country'])): ?>
                                    <div class="day-info-item">
                                        <span>🌍</span>
                                        <strong><?php echo esc_html($destination['country']); ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php 
                                $nights = $destination['toDay'] - $destination['fromDay'];
                                if ($nights > 0):
                                ?>
                                    <div class="day-info-item">
                                        <span>🌙</span>
                                        <strong><?php echo $nights; ?> <?php echo $nights == 1 ? 'nacht' : 'nachten'; ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                
                <?php elseif ($item['type'] === 'cruise'): 
                    $cruise = $item['data'];
                ?>
                    <div class="day-body">
                        <?php 
                        $cruise_image = '';
                        if (!empty($cruise['images'][0])) {
                            $cruise_image = $cruise['images'][0];
                        }
                        if (!empty($cruise_image)): 
                        ?>
                            <img src="<?php echo esc_url($cruise_image); ?>" 
                                 alt="Cruise" 
                                 class="day-image">
                        <?php endif; ?>
                        
                        <div class="day-details">
                            <h4>🚢 <?php echo isset($cruise['name']) ? esc_html($cruise['name']) : 'Cruise'; ?></h4>
                            
                            <div class="day-description">
                                Geniet van een prachtige cruise 
                                <?php if (!empty($cruise['destinations'])): ?>
                                    naar <?php echo implode(', ', $cruise['destinations']); ?>
                                <?php endif; ?>.
                                <?php if (!empty($cruise['cabin'])): ?>
                                    In een <?php echo esc_html($cruise['group']); ?> cabin.
                                <?php endif; ?>
                            </div>
                            
                            <div class="day-info-items">
                                <?php if (!empty($cruise['embarkDate'])): ?>
                                    <div class="day-info-item">
                                        <span>📅</span>
                                        <strong><?php echo date('d M', strtotime($cruise['embarkDate'])); ?></strong>
                                        →
                                        <strong><?php echo date('d M', strtotime($cruise['disembarkDate'])); ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($cruise['nights'])): ?>
                                    <div class="day-info-item">
                                        <span>🌙</span>
                                        <strong><?php echo $cruise['nights']; ?> nachten</strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($cruise['destinations'])): ?>
                                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
                                    <strong style="display: block; margin-bottom: 8px;">Cruise Bestemmingen:</strong>
                                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                        <?php foreach ($cruise['destinations'] as $dest_name): ?>
                                            <span style="background: #e8f5e9; padding: 6px 12px; border-radius: 20px; font-size: 13px;">
                                                ⚓ <?php echo esc_html($dest_name); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                
                <?php elseif ($item['type'] === 'hotel' && !empty($item['data']['hotelData'])): 
                    $hotel = $item['data'];
                    $hotel_data = $hotel['hotelData'];
                ?>
                    <div class="day-body">
                        <?php if (!empty($hotel_data['imageUrls'][0])): ?>
                            <img src="<?php echo esc_url($hotel_data['imageUrls'][0]); ?>" 
                                 alt="<?php echo esc_attr($hotel_data['name']); ?>" 
                                 class="day-image">
                        <?php endif; ?>
                        
                        <div class="day-details">
                            <h4>🏨 <?php echo esc_html($hotel_data['name']); ?></h4>
                            
                            <?php if (!empty($hotel_data['description'])): ?>
                                <div class="day-description">
                                    <?php echo wp_kses_post(substr($hotel_data['description'], 0, 200)); ?>...
                                </div>
                            <?php endif; ?>
                            
                            <div class="day-info-items">
                                <?php if (!empty($hotel_data['city'])): ?>
                                    <div class="day-info-item">
                                        <span>🌆</span>
                                        <strong><?php echo esc_html($hotel_data['city']); ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php 
                                $nights = $item['toDay'] - $item['fromDay'];
                                if ($nights > 0):
                                ?>
                                    <div class="day-info-item">
                                        <span>🌙</span>
                                        <strong><?php echo $nights; ?> <?php echo $nights == 1 ? 'nacht' : 'nachten'; ?></strong>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($hotel_data['stars'])): ?>
                                    <div class="day-info-item">
                                        <span>⭐</span>
                                        <strong><?php echo str_repeat('★', $hotel_data['stars']); ?></strong>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
