<?php
/**
 * Photo Manager Meta Box
 * Based on builder code analysis
 */

if (!defined('ABSPATH')) exit;

$post_id = $post->ID;

// Get current data
$cruises = get_post_meta($post_id, 'travel_cruises', true);
$hotels = get_post_meta($post_id, 'travel_hotels', true);
$destinations = get_post_meta($post_id, 'travel_destinations', true);
$tripSpots = get_post_meta($post_id, 'travel_trip_spots', true);
$closedTours = get_post_meta($post_id, 'travel_closed_tours', true);
$tickets = get_post_meta($post_id, 'travel_tickets', true);
$transfers = get_post_meta($post_id, 'travel_transfers', true);
$cars = get_post_meta($post_id, 'travel_cars', true);

// Ensure arrays
$cruises = is_array($cruises) ? $cruises : array();
$hotels = is_array($hotels) ? $hotels : array();
$destinations = is_array($destinations) ? $destinations : array();
$tripSpots = is_array($tripSpots) ? $tripSpots : array();
$closedTours = is_array($closedTours) ? $closedTours : array();
$tickets = is_array($tickets) ? $tickets : array();
$transfers = is_array($transfers) ? $transfers : array();
$cars = is_array($cars) ? $cars : array();

// Helper function to extract images (wrapped to prevent redeclaration)
if (!function_exists('rbs_extract_images')) {
function rbs_extract_images($item_data, $type) {
    $images = array();
    
    if ($type === 'hotel') {
        // Hotels: hotelData.images array with .url property
        if (isset($item_data['hotelData']['images']) && is_array($item_data['hotelData']['images'])) {
            foreach ($item_data['hotelData']['images'] as $img) {
                if (is_array($img) && isset($img['url'])) {
                    $images[] = $img['url'];
                } elseif (is_string($img)) {
                    $images[] = $img;
                }
            }
        }
    } elseif ($type === 'destination') {
        // Destinations: imageUrls array
        if (isset($item_data['imageUrls']) && is_array($item_data['imageUrls'])) {
            $images = $item_data['imageUrls'];
        } elseif (isset($item_data['image'])) {
            $images[] = $item_data['image'];
        } elseif (isset($item_data['imageUrl'])) {
            $images[] = $item_data['imageUrl'];
        }
    } elseif ($type === 'cruise') {
        // Cruises: comprehensive image search
        // Try cruiseData first if it exists
        $cruise_data = isset($item_data['cruiseData']) ? $item_data['cruiseData'] : array();
        
        // 1. Check for images array in cruiseData
        if (isset($cruise_data['images']) && is_array($cruise_data['images'])) {
            foreach ($cruise_data['images'] as $img) {
                if (is_array($img) && isset($img['url'])) {
                    $images[] = $img['url'];
                } elseif (is_string($img) && !empty($img)) {
                    $images[] = $img;
                }
            }
        }
        
        // 2. Check for imageUrls array in cruiseData
        if (empty($images) && isset($cruise_data['imageUrls']) && is_array($cruise_data['imageUrls'])) {
            $images = array_filter($cruise_data['imageUrls']);
        }
        
        // 3. Check for single image fields in cruiseData
        if (empty($images)) {
            if (isset($cruise_data['image']) && !empty($cruise_data['image'])) {
                $images[] = $cruise_data['image'];
            } elseif (isset($cruise_data['imageUrl']) && !empty($cruise_data['imageUrl'])) {
                $images[] = $cruise_data['imageUrl'];
            } elseif (isset($cruise_data['mainImage']) && !empty($cruise_data['mainImage'])) {
                $images[] = $cruise_data['mainImage'];
            } elseif (isset($cruise_data['headerImage']) && !empty($cruise_data['headerImage'])) {
                $images[] = $cruise_data['headerImage'];
            } elseif (isset($cruise_data['shipImage']) && !empty($cruise_data['shipImage'])) {
                $images[] = $cruise_data['shipImage'];
            }
        }
        
        // 4. Check top-level images array
        if (empty($images) && isset($item_data['images']) && is_array($item_data['images'])) {
            foreach ($item_data['images'] as $img) {
                if (is_array($img) && isset($img['url'])) {
                    $images[] = $img['url'];
                } elseif (is_string($img) && !empty($img)) {
                    $images[] = $img;
                }
            }
        }
        
        // 5. Check top-level imageUrls
        if (empty($images) && isset($item_data['imageUrls']) && is_array($item_data['imageUrls'])) {
            $images = array_filter($item_data['imageUrls']);
        }
        
        // 6. Check top-level single image fields
        if (empty($images)) {
            if (isset($item_data['image']) && !empty($item_data['image'])) {
                $images[] = $item_data['image'];
            } elseif (isset($item_data['imageUrl']) && !empty($item_data['imageUrl'])) {
                $images[] = $item_data['imageUrl'];
            } elseif (isset($item_data['mainImage']) && !empty($item_data['mainImage'])) {
                $images[] = $item_data['mainImage'];
            } elseif (isset($item_data['headerImage']) && !empty($item_data['headerImage'])) {
                $images[] = $item_data['headerImage'];
            } elseif (isset($item_data['shipImage']) && !empty($item_data['shipImage'])) {
                $images[] = $item_data['shipImage'];
            }
        }
    } elseif ($type === 'tripspot') {
        // Trip Spots: image, secondImage, highResolutionImage
        if (isset($item_data['highResolutionImage']) && !empty($item_data['highResolutionImage'])) {
            $images[] = $item_data['highResolutionImage'];
        }
        if (isset($item_data['image']) && !empty($item_data['image'])) {
            $images[] = $item_data['image'];
        }
        if (isset($item_data['secondImage']) && !empty($item_data['secondImage'])) {
            $images[] = $item_data['secondImage'];
        }
    } elseif ($type === 'closedtour') {
        // Closed Tours: imageUrls array
        if (isset($item_data['imageUrls']) && is_array($item_data['imageUrls'])) {
            $images = $item_data['imageUrls'];
        }
    } elseif ($type === 'ticket') {
        // Tickets: imageUrls array
        if (isset($item_data['imageUrls']) && is_array($item_data['imageUrls'])) {
            $images = $item_data['imageUrls'];
        }
    } elseif ($type === 'transfer') {
        // Transfers: imageUrl (single)
        if (isset($item_data['imageUrl']) && !empty($item_data['imageUrl'])) {
            $images[] = $item_data['imageUrl'];
        }
    } elseif ($type === 'car') {
        // Cars: imageUrl (single)
        if (isset($item_data['imageUrl']) && !empty($item_data['imageUrl'])) {
            $images[] = $item_data['imageUrl'];
        }
    }
    
    return array_filter($images); // Remove empty values
}
} // end function_exists check
?>

<style>
.rbs-photo-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin: 15px 0; }
.rbs-photo-item { position: relative; aspect-ratio: 1; background: #f5f5f5; border: 2px solid #ddd; border-radius: 4px; overflow: hidden; cursor: move; }
.rbs-photo-item img { width: 100%; height: 100%; object-fit: cover; }
.rbs-photo-item:hover .rbs-photo-remove { opacity: 1; }
.rbs-photo-remove { position: absolute; top: 5px; right: 5px; background: rgba(255,0,0,0.8); color: white; border: none; border-radius: 3px; width: 24px; height: 24px; cursor: pointer; opacity: 0; transition: opacity 0.2s; font-size: 16px; line-height: 1; }
.rbs-photo-remove:hover { background: rgba(200,0,0,1); }
.rbs-section { background: white; padding: 15px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
.rbs-section h4 { margin: 0 0 10px 0; padding: 0; font-size: 14px; color: #333; }
.rbs-item-name { font-weight: bold; color: #0073aa; margin-bottom: 8px; }
.rbs-add-photo { display: inline-block; padding: 8px 16px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px; margin-top: 10px; cursor: pointer; border: none; }
.rbs-add-photo:hover { background: #005a87; color: white; }
.rbs-photo-item.ui-sortable-helper { opacity: 0.8; transform: scale(1.05); }
</style>

<div style="padding: 15px; background: #f9f9f9;">
    <h3 style="margin-top: 0;">📸 Foto Overzicht</h3>
    <p style="color: #666; margin-bottom: 20px;">Alle foto's van geïmporteerde Travel Compositor items.</p>
    
    <?php if (!empty($hotels)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🏨 Hotels (<?php echo count($hotels); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($hotels as $idx => $hotel): 
                    $images = rbs_extract_images($hotel, 'hotel');
                    $name = $hotel['name'] ?? $hotel['hotelData']['name'] ?? 'Hotel ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="hotel" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="hotel" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($destinations)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🌍 Bestemmingen (<?php echo count($destinations); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($destinations as $idx => $dest): 
                    $images = rbs_extract_images($dest, 'destination');
                    $name = $dest['name'] ?? 'Bestemming ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="destination" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="destination" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($cruises)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🚢 Cruises (<?php echo count($cruises); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($cruises as $idx => $cruise): 
                    $images = rbs_extract_images($cruise, 'cruise');
                    $name = $cruise['name'] ?? 'Cruise ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="cruise" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="cruise" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                        <?php if (empty($images)): ?>
                            <details style="margin: 10px 0; background: #fff3cd; padding: 10px; border: 1px solid #ffc107; border-radius: 4px;">
                                <summary style="cursor: pointer; font-size: 12px; color: #856404;">
                                    ⚠️ Travel Compositor stuurt geen cruise foto's - Klik om te uploaden!
                                </summary>
                                <p style="font-size: 12px; margin: 10px 0 0 0; color: #856404;">
                                    Gebruik de "+ Foto Toevoegen" knop hierboven om cruise foto's handmatig toe te voegen.
                                </p>
                            </details>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($tripSpots)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                📍 Trip Spots (<?php echo count($tripSpots); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($tripSpots as $idx => $spot): 
                    $images = rbs_extract_images($spot, 'tripspot');
                    $name = $spot['name'] ?? 'Trip Spot ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="tripspot" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="tripspot" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($closedTours)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🗺️ Closed Tours (<?php echo count($closedTours); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($closedTours as $idx => $tour): 
                    $images = rbs_extract_images($tour, 'closedtour');
                    $name = $tour['name'] ?? 'Tour ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="closedtour" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="closedtour" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($tickets)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🎫 Tickets (<?php echo count($tickets); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($tickets as $idx => $ticket): 
                    $images = rbs_extract_images($ticket, 'ticket');
                    $name = $ticket['name'] ?? 'Ticket ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="ticket" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="ticket" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($transfers)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🚐 Transfers (<?php echo count($transfers); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($transfers as $idx => $transfer): 
                    $images = rbs_extract_images($transfer, 'transfer');
                    $name = ($transfer['from']['name'] ?? 'Van') . ' → ' . ($transfer['to']['name'] ?? 'Naar');
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="transfer" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="transfer" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (!empty($cars)): ?>
        <details class="rbs-section">
            <summary style="cursor: pointer; font-weight: bold; padding: 10px; background: white; border-radius: 4px; user-select: none;">
                🚗 Car Rentals (<?php echo count($cars); ?>) - Klik om te tonen/verbergen
            </summary>
            <div style="padding-top: 15px;">
                <?php foreach ($cars as $idx => $car): 
                    $images = rbs_extract_images($car, 'car');
                    $name = $car['product'] ?? 'Car ' . ($idx + 1);
                ?>
                    <div style="margin-bottom: 20px;">
                        <div class="rbs-item-name"><?php echo esc_html($name); ?></div>
                        <div class="rbs-photo-grid" data-type="car" data-index="<?php echo $idx; ?>">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img_idx => $img_url): ?>
                                    <div class="rbs-photo-item" data-image-index="<?php echo $img_idx; ?>">
                                        <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy" />
                                        <button class="rbs-photo-remove" title="Verwijder foto">×</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <small style="color: #666;"><?php echo count($images); ?> foto's</small>
                        <button class="rbs-add-photo" data-type="car" data-index="<?php echo $idx; ?>">+ Foto Toevoegen</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </details>
    <?php endif; ?>
    
    <?php if (empty($hotels) && empty($destinations) && empty($cruises) && empty($tripSpots) && empty($closedTours) && empty($tickets) && empty($transfers) && empty($cars)): ?>
        <div style="text-align: center; padding: 40px; color: #999;">
            <p style="font-size: 48px; margin: 0;">📷</p>
            <p>Nog geen items geïmporteerd</p>
        </div>
    <?php endif; ?>
</div>

<script>
jQuery(document).ready(function($) {
    var currentGrid;
    var currentType;
    var currentIndex;
    
    // WordPress Media Uploader voor foto toevoegen
    $('.rbs-add-photo').on('click', function(e) {
        e.preventDefault();
        
        // Store current context
        currentGrid = $(this).siblings('.rbs-photo-grid');
        currentType = $(this).data('type');
        currentIndex = $(this).data('index');
        
        console.log('Opening media uploader for:', currentType, currentIndex);
        
        // Always create a new media uploader instance to ensure correct context
        var mediaUploader = wp.media({
            title: 'Selecteer foto',
            button: { text: 'Foto Toevoegen' },
            multiple: true
        });
        
        mediaUploader.on('select', function() {
            var attachments = mediaUploader.state().get('selection').toJSON();
            console.log('Selected attachments:', attachments.length);
            
            attachments.forEach(function(attachment) {
                var imgHtml = '<div class="rbs-photo-item" data-image-index="new">' +
                    '<img src="' + attachment.url + '" alt="" loading="lazy" />' +
                    '<button class="rbs-photo-remove" title="Verwijder foto">×</button>' +
                    '</div>';
                currentGrid.append(imgHtml);
            });
            
            savePhotos(currentGrid, currentType, currentIndex);
        });
        
        mediaUploader.open();
    });
    
    // Foto verwijderen
    $(document).on('click', '.rbs-photo-remove', function(e) {
        e.preventDefault();
        if (confirm('Weet je zeker dat je deze foto wilt verwijderen?')) {
            var grid = $(this).closest('.rbs-photo-grid');
            var type = grid.data('type');
            var index = grid.data('index');
            $(this).closest('.rbs-photo-item').fadeOut(300, function() {
                $(this).remove();
                savePhotos(grid, type, index);
            });
        }
    });
    
    // Drag & Drop sortering
    $('.rbs-photo-grid').sortable({
        items: '.rbs-photo-item',
        cursor: 'move',
        opacity: 0.8,
        update: function(event, ui) {
            var grid = $(this);
            var type = grid.data('type');
            var index = grid.data('index');
            savePhotos(grid, type, index);
        }
    });
    
    // Opslaan functie
    function savePhotos(grid, type, index) {
        var photos = [];
        grid.find('.rbs-photo-item img').each(function() {
            photos.push($(this).attr('src'));
        });
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rbs_save_photos',
                post_id: <?php echo $post_id; ?>,
                type: type,
                index: index,
                photos: photos,
                nonce: '<?php echo wp_create_nonce('rbs_photo_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Update teller
                    grid.next('small').text(photos.length + ' foto\'s');
                    console.log('Foto\'s opgeslagen!');
                }
            },
            error: function() {
                alert('Fout bij opslaan van foto\'s');
            }
        });
    }
});
</script>
