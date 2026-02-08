<?php
/**
 * Itinerary Timeline Component
 * Modern card-based timeline with all travel components
 */

if (!defined('ABSPATH')) exit;

// Build timeline items array
$timeline_items = array();

// Add destinations
if (!empty($travel_meta_fields['travel_destinations'])) {
    foreach ($travel_meta_fields['travel_destinations'] as $destination) {
        $timeline_items[] = array(
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
        $timeline_items[] = array(
            'type' => 'cruise',
            'fromDay' => isset($cruise['fromDay']) ? $cruise['fromDay'] : 0,
            'toDay' => isset($cruise['toDay']) ? $cruise['toDay'] : 0,
            'data' => $cruise
        );
    }
}

// Add hotels (with calculated days)
if (!empty($travel_meta_fields['travel_hotels'])) {
    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
        $timeline_items[] = array(
            'type' => 'hotel',
            'fromDay' => isset($hotel['fromDay']) ? $hotel['fromDay'] : 0,
            'toDay' => isset($hotel['toDay']) ? $hotel['toDay'] : 0,
            'data' => $hotel
        );
    }
}

// Sort by fromDay
usort($timeline_items, function($a, $b) {
    return $a['fromDay'] - $b['fromDay'];
});
?>

<section class="timeline-section">
    <h2 class="section-title">📅 Dag-voor-dag programma</h2>
    
    <div class="timeline">
        <?php foreach ($timeline_items as $item): ?>
            
            <?php if ($item['type'] === 'destination'): 
                $destination = $item['data'];
            ?>
                <!-- DESTINATION CARD -->
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: var(--success);"></div>
                    
                    <div class="timeline-card">
                        <div class="timeline-card-header" style="background: linear-gradient(135deg, #4CAF50 0%, #388E3C 100%);">
                            <div class="timeline-day-range">
                                Dag <?php echo $destination['fromDay']; ?> 
                                <?php if ($destination['toDay'] != $destination['fromDay']): ?>
                                    - <?php echo $destination['toDay']; ?>
                                <?php endif; ?>
                            </div>
                            <h3 class="timeline-title">📍 <?php echo esc_html($destination['name']); ?></h3>
                        </div>
                        
                        <div class="timeline-card-content">
                            <?php if (!empty($destination['imageUrls'][0])): ?>
                                <img src="<?php echo esc_url($destination['imageUrls'][0]); ?>" 
                                     alt="<?php echo esc_attr($destination['name']); ?>" 
                                     class="timeline-image">
                            <?php endif; ?>
                            
                            <div class="timeline-text">
                                <?php if (!empty($destination['description'])): ?>
                                    <div class="timeline-description">
                                        <?php echo wp_kses_post(substr($destination['description'], 0, 200)); ?>...
                                    </div>
                                <?php endif; ?>
                                
                                <div class="timeline-details">
                                    <?php if (!empty($destination['country'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>🌍</span>
                                            <strong><?php echo esc_html($destination['country']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $nights = $destination['toDay'] - $destination['fromDay'];
                                    if ($nights > 0):
                                    ?>
                                        <div class="timeline-detail-item">
                                            <span>🌙</span>
                                            <strong><?php echo $nights; ?> <?php echo $nights == 1 ? 'nacht' : 'nachten'; ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($destination['imageUrls'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>📸</span>
                                            <strong><?php echo count($destination['imageUrls']); ?> foto's</strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php elseif ($item['type'] === 'cruise'): 
                $cruise = $item['data'];
            ?>
                <!-- CRUISE CARD -->
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: var(--primary);"></div>
                    
                    <div class="timeline-card">
                        <div class="timeline-card-header">
                            <div class="timeline-day-range">
                                Dag <?php echo $cruise['fromDay']; ?> - <?php echo $cruise['toDay']; ?>
                            </div>
                            <h3 class="timeline-title">🚢 <?php echo isset($cruise['name']) ? esc_html($cruise['name']) : 'Cruise'; ?></h3>
                        </div>
                        
                        <div class="timeline-card-content">
                            <?php 
                            $cruise_image = '';
                            if (!empty($cruise['images'][0])) {
                                $cruise_image = $cruise['images'][0];
                            }
                            if (!empty($cruise_image)): 
                            ?>
                                <img src="<?php echo esc_url($cruise_image); ?>" 
                                     alt="Cruise" 
                                     class="timeline-image">
                            <?php endif; ?>
                            
                            <div class="timeline-text">
                                <div class="timeline-description">
                                    Geniet van een prachtige cruise 
                                    <?php if (!empty($cruise['destinations'])): ?>
                                        naar <?php echo implode(', ', array_slice($cruise['destinations'], 0, 3)); ?>
                                    <?php endif; ?>.
                                </div>
                                
                                <div class="timeline-details">
                                    <div class="timeline-detail-item">
                                        <span>📅</span>
                                        <strong><?php echo date('d M', strtotime($cruise['embarkDate'])); ?></strong>
                                        →
                                        <strong><?php echo date('d M', strtotime($cruise['disembarkDate'])); ?></strong>
                                    </div>
                                    
                                    <?php if (!empty($cruise['nights'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>🌙</span>
                                            <strong><?php echo $cruise['nights']; ?> nachten</strong>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($cruise['cabin'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>🛏️</span>
                                            <strong><?php echo esc_html($cruise['group']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- CRUISE DESTINATIONS/PORTS -->
                                <?php if (!empty($cruise['destinations'])): ?>
                                    <div class="timeline-sub-items">
                                        <?php foreach ($cruise['destinations'] as $dest_name): ?>
                                            <div class="timeline-sub-item">
                                                <div class="timeline-sub-icon">⚓</div>
                                                <div class="timeline-sub-content">
                                                    <div class="timeline-sub-title"><?php echo esc_html($dest_name); ?></div>
                                                    <div class="timeline-sub-text">Cruise bestemming</div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php elseif ($item['type'] === 'hotel' && !empty($item['data']['hotelData'])): 
                $hotel = $item['data'];
                $hotel_data = $hotel['hotelData'];
            ?>
                <!-- HOTEL CARD -->
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: var(--accent);"></div>
                    
                    <div class="timeline-card">
                        <div class="timeline-card-header" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
                            <div class="timeline-day-range">
                                <?php if ($item['fromDay'] > 0): ?>
                                    Dag <?php echo $item['fromDay']; ?>
                                    <?php if ($item['toDay'] > $item['fromDay']): ?>
                                        - <?php echo $item['toDay']; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    Hotel
                                <?php endif; ?>
                            </div>
                            <h3 class="timeline-title">🏨 <?php echo esc_html($hotel_data['name']); ?></h3>
                        </div>
                        
                        <div class="timeline-card-content">
                            <?php if (!empty($hotel_data['imageUrls'][0])): ?>
                                <img src="<?php echo esc_url($hotel_data['imageUrls'][0]); ?>" 
                                     alt="<?php echo esc_attr($hotel_data['name']); ?>" 
                                     class="timeline-image">
                            <?php endif; ?>
                            
                            <div class="timeline-text">
                                <?php if (!empty($hotel_data['address'])): ?>
                                    <div class="timeline-description">
                                        📍 <?php echo esc_html($hotel_data['address']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="timeline-details">
                                    <?php if (!empty($hotel_data['city'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>🌆</span>
                                            <strong><?php echo esc_html($hotel_data['city']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $nights = $item['toDay'] - $item['fromDay'];
                                    if ($nights > 0):
                                    ?>
                                        <div class="timeline-detail-item">
                                            <span>🌙</span>
                                            <strong><?php echo $nights; ?> <?php echo $nights == 1 ? 'nacht' : 'nachten'; ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($hotel_data['stars'])): ?>
                                        <div class="timeline-detail-item">
                                            <span>⭐</span>
                                            <strong><?php echo str_repeat('★', $hotel_data['stars']); ?></strong>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php endif; ?>
            
        <?php endforeach; ?>
    </div>
</section>
