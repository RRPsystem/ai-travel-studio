<?php
/**
 * Archive Travel Ideas - Listing Page Template - Speelse Style
 * 
 * Features:
 * - 3D cards with shadow effects
 * - Hover to reveal interactive route map
 * - OpenStreetMap integration
 * - Filter sidebar
 * - Responsive grid layout
 * 
 * @package RBS_Travel
 */

if (!defined('ABSPATH')) exit;

// Enqueue Leaflet CSS/JS
wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);

get_header();
?>

<style>
    /* Speelse Listing Style CSS */
    .rbs-listing-page {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: #f5f7fa;
        padding: 2rem 1rem;
        min-height: 100vh;
    }

    .rbs-listing-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header Section */
    .rbs-page-header {
        text-align: center;
        background: white;
        border-radius: 24px;
        padding: 3rem 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .rbs-page-title {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 1rem;
    }

    .rbs-page-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Travel Cards Grid */
    .rbs-travel-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 2.5rem;
        margin-bottom: 3rem;
    }

    .rbs-travel-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        transform-style: preserve-3d;
    }

    .rbs-travel-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0,0,0,0.2), 0 8px 16px rgba(0,0,0,0.12);
    }

    .rbs-travel-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .rbs-travel-card:hover::after {
        opacity: 1;
    }

    .rbs-card-image {
        position: relative;
        height: 300px;
        overflow: hidden;
    }

    .rbs-card-image-photo {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        transition: opacity 0.4s ease;
    }

    .rbs-card-route-map {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }

    .rbs-travel-card:hover .rbs-card-route-map {
        opacity: 0.97;
    }

    .rbs-card-map-container {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .rbs-card-map {
        width: 100%;
        height: 100%;
    }

    .rbs-card-map-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.85));
        padding: 1.5rem;
        color: white;
        z-index: 1000;
    }

    .rbs-route-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .rbs-route-path {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1rem;
        font-weight: 600;
    }

    .rbs-route-arrow {
        font-size: 1.5rem;
        color: #10b981;
    }

    .rbs-route-stops {
        margin-top: 0.5rem;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .rbs-card-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
        color: #667eea;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .rbs-card-favorite {
        position: absolute;
        top: 1rem;
        left: 1rem;
        width: 45px;
        height: 45px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        z-index: 10;
    }

    .rbs-card-favorite:hover {
        transform: scale(1.1);
        background: #ff6b6b;
    }

    .rbs-card-favorite:hover::before {
        content: '❤️';
    }

    .rbs-card-favorite::before {
        content: '🤍';
        font-size: 1.3rem;
    }

    .rbs-card-content {
        padding: 1.75rem;
    }

    .rbs-card-destination {
        font-size: 0.875rem;
        color: #667eea;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.5rem;
    }

    .rbs-card-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 1rem;
        line-height: 1.3;
    }

    .rbs-card-description {
        font-size: 0.95rem;
        color: #6b7280;
        line-height: 1.7;
        margin-bottom: 1.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .rbs-card-meta {
        display: flex;
        gap: 1.5rem;
        padding: 1rem 0;
        border-top: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }

    .rbs-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #4b5563;
    }

    .rbs-meta-icon {
        font-size: 1.1rem;
    }

    .rbs-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .rbs-card-price {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .rbs-price-label {
        font-size: 0.75rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .rbs-price-amount {
        font-size: 1.75rem;
        font-weight: 900;
        color: #667eea;
    }

    .rbs-price-currency {
        font-size: 0.875rem;
        font-weight: 600;
    }

    .rbs-card-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .rbs-card-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }

    /* Pagination */
    .rbs-pagination {
        display: flex;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 3rem;
    }

    .rbs-page-btn {
        padding: 0.75rem 1.25rem;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 12px;
        font-weight: 600;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .rbs-page-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
    }

    .rbs-page-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .rbs-travel-card {
        animation: fadeInUp 0.6s ease forwards;
    }

    .rbs-travel-card:nth-child(1) { animation-delay: 0.1s; }
    .rbs-travel-card:nth-child(2) { animation-delay: 0.2s; }
    .rbs-travel-card:nth-child(3) { animation-delay: 0.3s; }
    .rbs-travel-card:nth-child(4) { animation-delay: 0.4s; }
    .rbs-travel-card:nth-child(5) { animation-delay: 0.5s; }
    .rbs-travel-card:nth-child(6) { animation-delay: 0.6s; }

    /* Responsive */
    @media (max-width: 1024px) {
        .rbs-travel-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .rbs-travel-grid {
            grid-template-columns: 1fr;
        }

        .rbs-page-title {
            font-size: 2rem;
        }

        .rbs-card-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .rbs-card-btn {
            text-align: center;
        }
    }
</style>

<div class="rbs-listing-page">
    <div class="rbs-listing-container">
        <!-- Page Header -->
        <div class="rbs-page-header">
            <h1 class="rbs-page-title">Ontdek Jouw Droomreis</h1>
            <p class="rbs-page-subtitle">
                Van avontuurlijke rondreizen tot ontspannen strandvakanties - vind jouw perfecte bestemming
            </p>
        </div>

        <!-- Elementor Content (if using page builder) -->
        <?php the_content(); ?>

        <!-- Travel Cards Grid -->
        <div class="rbs-travel-grid">
            <?php
            if (have_posts()) :
                while (have_posts()) : the_post();
                    // Get meta fields
                    $meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();
                    $destinations = $meta_fields['travel_destinations'] ?? [];
                    $transports = $meta_fields['travel_transports'] ?? [];
                    $price_per_person = $meta_fields['travel_price_per_person'] ?? '0.00';
                    $number_of_nights = $meta_fields['travel_number_of_nights'] ?? 0;
                    
                    // Get first destination for card
                    $first_dest = !empty($destinations) ? $destinations[0] : null;
                    $card_image = $first_dest['imageUrls'][0] ?? get_the_post_thumbnail_url(get_the_ID(), 'large');
                    
                    // Flight info
                    $departure_flight = !empty($transports) ? $transports[0] : null;
                    $origin = $departure_flight['originDestinationCode'] ?? 'AMS';
                    $target = $departure_flight['targetDestinationCode'] ?? ($first_dest['name'] ?? '');
                    
                    // Generate unique map ID
                    $map_id = 'map-' . get_the_ID();
            ?>
            
            <div class="rbs-travel-card" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                <div class="rbs-card-image">
                    <div class="rbs-card-image-photo" style="background-image: url('<?php echo esc_url($card_image); ?>');"></div>
                    
                    <!-- Route Map Overlay -->
                    <?php if ($first_dest && isset($first_dest['latitude']) && isset($first_dest['longitude'])): ?>
                    <div class="rbs-card-route-map" 
                         data-start="Amsterdam" 
                         data-end="<?php echo esc_attr($first_dest['name']); ?>"
                         data-start-coords="52.3676,4.9041" 
                         data-end-coords="<?php echo esc_attr($first_dest['latitude'] . ',' . $first_dest['longitude']); ?>">
                        <div class="rbs-card-map-container">
                            <div class="rbs-card-map" id="<?php echo esc_attr($map_id); ?>"></div>
                            <div class="rbs-card-map-overlay">
                                <div class="rbs-route-title">🗺️ Jouw Reis Route</div>
                                <div class="rbs-route-path">
                                    <span><?php echo esc_html($origin); ?></span>
                                    <span class="rbs-route-arrow">✈️</span>
                                    <span><?php echo esc_html($target); ?></span>
                                </div>
                                <div class="rbs-route-stops">
                                    <?php 
                                    if ($departure_flight) {
                                        echo esc_html($departure_flight['stops'] > 0 ? $departure_flight['stops'] . ' tussenstop' : 'Direct vlucht');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="rbs-card-favorite" onclick="event.stopPropagation(); toggleFavorite(this);"></div>
                    <div class="rbs-card-badge">✨ Nieuw</div>
                </div>
                
                <div class="rbs-card-content">
                    <div class="rbs-card-destination">
                        🌍 <?php echo $first_dest ? esc_html($first_dest['country'] ?? $first_dest['name']) : 'Bestemming'; ?>
                    </div>
                    <h3 class="rbs-card-title"><?php the_title(); ?></h3>
                    <p class="rbs-card-description">
                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                    </p>
                    
                    <div class="rbs-card-meta">
                        <div class="rbs-meta-item">
                            <span class="rbs-meta-icon">⏱️</span>
                            <span><?php echo esc_html($number_of_nights + 1); ?> dagen</span>
                        </div>
                        <div class="rbs-meta-item">
                            <span class="rbs-meta-icon">🌙</span>
                            <span><?php echo esc_html($number_of_nights); ?> nachten</span>
                        </div>
                        <div class="rbs-meta-item">
                            <span class="rbs-meta-icon">✈️</span>
                            <span>Vliegreis</span>
                        </div>
                    </div>
                    
                    <div class="rbs-card-footer">
                        <div class="rbs-card-price">
                            <span class="rbs-price-label">Vanaf</span>
                            <span class="rbs-price-amount">
                                €<?php echo esc_html(number_format((float)$price_per_person, 0, ',', '.')); ?> 
                                <span class="rbs-price-currency">p.p.</span>
                            </span>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="rbs-card-btn" onclick="event.stopPropagation();">Bekijk reis →</a>
                    </div>
                </div>
            </div>
            
            <?php
                endwhile;
            else :
            ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
                    <h3>Geen reizen gevonden</h3>
                    <p>Er zijn momenteel geen reizen beschikbaar.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php
        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
        $total_pages = $wp_query->max_num_pages;
        
        if ($total_pages > 1) :
        ?>
        <div class="rbs-pagination">
            <?php if ($paged > 1) : ?>
            <a href="<?php echo get_pagenum_link($paged - 1); ?>" class="rbs-page-btn">← Vorige</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
            <a href="<?php echo get_pagenum_link($i); ?>" class="rbs-page-btn <?php echo ($i == $paged) ? 'active' : ''; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($paged < $total_pages) : ?>
            <a href="<?php echo get_pagenum_link($paged + 1); ?>" class="rbs-page-btn">Volgende →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Store initialized maps
    const cardMaps = {};

    // Function to initialize a card map
    function initializeCardMap(mapId, startCoords, endCoords) {
        if (cardMaps[mapId]) return cardMaps[mapId];

        const mapElement = document.getElementById(mapId);
        if (!mapElement) return null;

        // Create map
        const map = L.map(mapId, {
            zoomControl: false,
            scrollWheelZoom: false,
            dragging: false,
            doubleClickZoom: false,
            touchZoom: false
        });

        // Add tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        }).addTo(map);

        // Parse coordinates
        const start = startCoords.split(',').map(Number);
        const end = endCoords.split(',').map(Number);

        // Add markers
        const startIcon = L.divIcon({
            html: '<div style="background: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            className: '',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        const endIcon = L.divIcon({
            html: '<div style="background: #667eea; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
            className: '',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        L.marker(start, { icon: startIcon }).addTo(map);
        L.marker(end, { icon: endIcon }).addTo(map);

        // Draw curved route line
        const offsetX = (end[1] - start[1]) * 0.5;
        const offsetY = (end[0] - start[0]) * 0.5;
        const controlPoint = [
            start[0] + offsetY * 0.3,
            start[1] + offsetX
        ];

        const curve = [];
        for (let i = 0; i <= 30; i++) {
            const t = i / 30;
            const lat = Math.pow(1 - t, 2) * start[0] + 
                       2 * (1 - t) * t * controlPoint[0] + 
                       Math.pow(t, 2) * end[0];
            const lng = Math.pow(1 - t, 2) * start[1] + 
                       2 * (1 - t) * t * controlPoint[1] + 
                       Math.pow(t, 2) * end[1];
            curve.push([lat, lng]);
        }

        L.polyline(curve, {
            color: '#f59e0b',
            weight: 3,
            opacity: 0.7,
            dashArray: '8, 8'
        }).addTo(map);

        // Fit bounds
        map.fitBounds([start, end], { padding: [40, 40] });

        cardMaps[mapId] = map;
        return map;
    }

    // Initialize maps on hover
    $('.rbs-travel-card').each(function() {
        const card = $(this);
        const routeMap = card.find('.rbs-card-route-map');
        if (!routeMap.length) return;

        const mapDiv = routeMap.find('.rbs-card-map');
        if (!mapDiv.length) return;

        const mapId = mapDiv.attr('id');
        const startCoords = routeMap.data('start-coords');
        const endCoords = routeMap.data('end-coords');

        let initialized = false;

        card.on('mouseenter', function() {
            if (!initialized) {
                setTimeout(function() {
                    const map = initializeCardMap(mapId, startCoords, endCoords);
                    if (map) {
                        setTimeout(function() {
                            map.invalidateSize();
                        }, 100);
                    }
                }, 100);
                initialized = true;
            } else if (cardMaps[mapId]) {
                setTimeout(function() {
                    cardMaps[mapId].invalidateSize();
                }, 100);
            }
        });
    });

    // Card click to navigate
    $('.rbs-travel-card').on('click', function() {
        const link = $(this).find('.rbs-card-btn').attr('href');
        if (link) {
            window.location.href = link;
        }
    });

    // Favorite toggle
    window.toggleFavorite = function(element) {
        $(element).toggleClass('active');
    };
});
</script>

<?php get_footer(); ?>
