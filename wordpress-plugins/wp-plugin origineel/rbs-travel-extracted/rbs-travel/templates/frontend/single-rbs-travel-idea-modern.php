<?php
/**
 * Template Name: Modern Card-based Layout (Visual)
 * Description: Photo-heavy, modern card design with timeline itinerary
 */

if (!defined('ABSPATH')) exit;

global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

rbstravel_get_header();
?>

<style>
:root {
    --primary: #2196F3;
    --success: #4CAF50;
    --accent: #FF9800;
    --text: #333333;
    --text-light: #666666;
    --bg: #F8F9FA;
    --white: #FFFFFF;
    --shadow: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-hover: 0 4px 16px rgba(0,0,0,0.12);
    --radius: 12px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    color: var(--text);
    background: var(--bg);
    line-height: 1.6;
}

.modern-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

/* === HERO SECTION === */
.hero-section {
    position: relative;
    height: 70vh;
    min-height: 500px;
    max-height: 700px;
    background: var(--text);
    overflow: hidden;
    margin-bottom: 40px;
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.9;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.5) 100%);
    display: flex;
    align-items: flex-end;
    padding: 60px 20px;
}

.hero-content {
    color: white;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
}

.hero-title {
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 12px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: 24px;
    font-weight: 400;
    opacity: 0.95;
    margin-bottom: 24px;
}

.hero-info-card {
    display: inline-flex;
    gap: 32px;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    padding: 20px 32px;
    border-radius: var(--radius);
    color: var(--text);
    box-shadow: var(--shadow-hover);
}

.hero-info-item {
    text-align: center;
}

.hero-info-label {
    font-size: 12px;
    text-transform: uppercase;
    color: var(--text-light);
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.hero-info-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
}

/* === PHOTO GALLERY === */
.photo-gallery {
    margin: 60px 0;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 32px;
    text-align: center;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 40px;
}

.gallery-item {
    position: relative;
    aspect-ratio: 4/3;
    border-radius: var(--radius);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: var(--shadow);
}

.gallery-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* === QUICK FACTS === */
.quick-facts {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin: 60px 0;
}

.fact-card {
    background: var(--white);
    padding: 32px;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.fact-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
}

.fact-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.fact-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 8px;
}

.fact-label {
    font-size: 14px;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* === TIMELINE ITINERARY === */
.timeline-section {
    margin: 80px 0;
}

.timeline {
    position: relative;
    padding-left: 60px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, var(--primary), var(--accent));
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 40px;
}

.timeline-dot {
    position: absolute;
    left: -48px;
    top: 12px;
    width: 24px;
    height: 24px;
    background: var(--primary);
    border: 4px solid var(--white);
    border-radius: 50%;
    box-shadow: 0 0 0 4px var(--bg);
    z-index: 2;
}

.timeline-card {
    background: var(--white);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.timeline-card:hover {
    transform: translateX(8px);
    box-shadow: var(--shadow-hover);
}

.timeline-card-header {
    padding: 24px;
    background: linear-gradient(135deg, var(--primary) 0%, #1976D2 100%);
    color: white;
}

.timeline-day-range {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.timeline-title {
    font-size: 24px;
    font-weight: 700;
}

.timeline-card-content {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 24px;
}

.timeline-image {
    width: 200px;
    height: 150px;
    object-fit: cover;
}

.timeline-text {
    padding: 24px;
    padding-left: 0;
}

.timeline-description {
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 16px;
}

.timeline-details {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.timeline-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--text-light);
}

.timeline-detail-item strong {
    color: var(--text);
}

/* === SUB ITEMS (Cruise ports, etc) === */
.timeline-sub-items {
    margin-left: 40px;
    margin-top: 16px;
    margin-bottom: 16px;
}

.timeline-sub-item {
    background: var(--bg);
    border-left: 3px solid var(--accent);
    padding: 16px;
    margin-bottom: 12px;
    border-radius: 0 8px 8px 0;
    display: flex;
    gap: 16px;
    align-items: center;
}

.timeline-sub-icon {
    font-size: 24px;
}

.timeline-sub-content {
    flex: 1;
}

.timeline-sub-title {
    font-weight: 600;
    margin-bottom: 4px;
}

.timeline-sub-text {
    font-size: 14px;
    color: var(--text-light);
}

/* === ACCORDION SECTIONS === */
.accordion-section {
    margin: 60px 0;
}

.accordion-item {
    background: var(--white);
    border-radius: var(--radius);
    margin-bottom: 16px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.accordion-header {
    padding: 24px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
}

.accordion-header:hover {
    background: var(--bg);
}

.accordion-title {
    font-size: 24px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 12px;
}

.accordion-icon {
    font-size: 32px;
}

.accordion-arrow {
    font-size: 24px;
    transition: transform 0.3s ease;
}

.accordion-item.active .accordion-arrow {
    transform: rotate(180deg);
}

.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.accordion-item.active .accordion-content {
    max-height: 5000px;
}

.accordion-body {
    padding: 0 24px 24px;
}

/* === HOTEL CARDS === */
.hotel-grid {
    display: grid;
    gap: 24px;
}

.hotel-card {
    background: var(--bg);
    border-radius: var(--radius);
    overflow: hidden;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
}

.hotel-image {
    width: 300px;
    height: 200px;
    object-fit: cover;
}

.hotel-info {
    padding: 24px;
    padding-left: 0;
}

.hotel-name {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
}

.hotel-location {
    color: var(--text-light);
    margin-bottom: 16px;
}

.hotel-amenities {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.amenity-tag {
    background: var(--white);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: var(--text-light);
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .hero-title {
        font-size: 32px;
    }
    
    .hero-subtitle {
        font-size: 18px;
    }
    
    .hero-info-card {
        flex-direction: column;
        gap: 16px;
    }
    
    .timeline {
        padding-left: 40px;
    }
    
    .timeline-card-content {
        grid-template-columns: 1fr;
    }
    
    .timeline-image {
        width: 100%;
        height: 200px;
    }
    
    .hotel-card {
        grid-template-columns: 1fr;
    }
    
    .hotel-image {
        width: 100%;
    }
}

/* === UTILITY === */
.btn-primary {
    display: inline-block;
    background: var(--primary);
    color: white;
    padding: 16px 32px;
    border-radius: var(--radius);
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: var(--shadow);
}

.btn-primary:hover {
    background: #1976D2;
    transform: translateY(-2px);
    box-shadow: var(--shadow-hover);
}

.text-center {
    text-align: center;
}

.mt-4 { margin-top: 32px; }
.mb-4 { margin-bottom: 32px; }
</style>

<div class="modern-layout">
    <!-- HERO SECTION -->
    <section class="hero-section">
        <?php 
        $hero_image = '';
        if (!empty($travel_meta_fields['travel_destinations'][0]['imageUrls'][0])) {
            $hero_image = $travel_meta_fields['travel_destinations'][0]['imageUrls'][0];
        } elseif (!empty($travel_meta_fields['travel_image_url'])) {
            $hero_image = $travel_meta_fields['travel_image_url'];
        }
        ?>
        <img src="<?php echo esc_url($hero_image); ?>" alt="<?php the_title(); ?>" class="hero-image">
        
        <div class="hero-overlay">
            <div class="hero-content">
                <h1 class="hero-title"><?php the_title(); ?></h1>
                <p class="hero-subtitle"><?php echo esc_html($travel_meta_fields['travel_large_title']); ?></p>
                
                <div class="hero-info-card">
                    <div class="hero-info-item">
                        <div class="hero-info-label">Vanaf</div>
                        <div class="hero-info-value">€<?php echo number_format($travel_meta_fields['travel_price_per_person'], 0, ',', '.'); ?></div>
                    </div>
                    <div class="hero-info-item">
                        <div class="hero-info-label">Nachten</div>
                        <div class="hero-info-value"><?php echo $travel_meta_fields['travel_number_of_nights']; ?></div>
                    </div>
                    <div class="hero-info-item">
                        <div class="hero-info-label">Vertrek</div>
                        <div class="hero-info-value"><?php echo date('d M', strtotime($travel_meta_fields['travel_departure_date'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modern-container">
        <!-- PHOTO GALLERY -->
        <section class="photo-gallery">
            <h2 class="section-title">✨ Ontdek je reis in beeld</h2>
            
            <div class="gallery-grid">
                <?php
                // Collect all images
                $all_images = array();
                
                // From destinations
                if (!empty($travel_meta_fields['travel_destinations'])) {
                    foreach ($travel_meta_fields['travel_destinations'] as $dest) {
                        if (!empty($dest['imageUrls'])) {
                            $all_images = array_merge($all_images, array_slice($dest['imageUrls'], 0, 3));
                        }
                    }
                }
                
                // From hotels
                if (!empty($travel_meta_fields['travel_hotels'])) {
                    foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
                        if (!empty($hotel['hotelData']['imageUrls'])) {
                            $all_images = array_merge($all_images, array_slice($hotel['hotelData']['imageUrls'], 0, 2));
                        }
                    }
                }
                
                // Limit to 12 images
                $all_images = array_slice(array_unique($all_images), 0, 12);
                
                foreach ($all_images as $image_url):
                ?>
                    <div class="gallery-item" onclick="openLightbox('<?php echo esc_url($image_url); ?>')">
                        <img src="<?php echo esc_url($image_url); ?>" alt="Travel photo" loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- QUICK FACTS -->
        <section class="quick-facts">
            <div class="fact-card">
                <div class="fact-icon">🌍</div>
                <div class="fact-value"><?php echo count($travel_meta_fields['travel_destinations']); ?></div>
                <div class="fact-label">Bestemmingen</div>
            </div>
            
            <div class="fact-card">
                <div class="fact-icon">🏨</div>
                <div class="fact-value"><?php echo count($travel_meta_fields['travel_hotels']); ?></div>
                <div class="fact-label">Hotels</div>
            </div>
            
            <?php if (!empty($travel_meta_fields['travel_cruises'])): ?>
            <div class="fact-card">
                <div class="fact-icon">🚢</div>
                <div class="fact-value"><?php echo $travel_meta_fields['travel_cruises'][0]['nights']; ?></div>
                <div class="fact-label">Cruise Nachten</div>
            </div>
            <?php endif; ?>
            
            <div class="fact-card">
                <div class="fact-icon">✈️</div>
                <div class="fact-value"><?php echo count($travel_meta_fields['travel_transports']); ?></div>
                <div class="fact-label">Vluchten</div>
            </div>
            
            <?php if (!empty($travel_meta_fields['travel_cars'])): ?>
            <div class="fact-card">
                <div class="fact-icon">🚗</div>
                <div class="fact-value"><?php echo count($travel_meta_fields['travel_cars']); ?></div>
                <div class="fact-label">Huurauto</div>
            </div>
            <?php endif; ?>
        </section>

        <!-- DESCRIPTION -->
        <section class="description-section mt-4 mb-4">
            <div class="timeline-card">
                <div class="timeline-card-content" style="grid-template-columns: 1fr;">
                    <div class="timeline-text" style="padding: 32px;">
                        <h2 class="section-title" style="text-align: left; margin-bottom: 16px;">📖 Over deze reis</h2>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- ITINERARY TIMELINE -->
        <?php include(RBS_TRAVEL_DIR . '/templates/frontend/partials/itinerary-timeline.php'); ?>

        <!-- ACCORDION DETAILS -->
        <section class="accordion-section">
            <h2 class="section-title">🔍 Meer informatie</h2>
            
            <!-- HOTELS ACCORDION -->
            <?php if (!empty($travel_meta_fields['travel_hotels'])): ?>
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="accordion-icon">🏨</span>
                        Hotels & Accommodatie (<?php echo count($travel_meta_fields['travel_hotels']); ?>)
                    </div>
                    <span class="accordion-arrow">▼</span>
                </div>
                <div class="accordion-content">
                    <div class="accordion-body">
                        <div class="hotel-grid">
                            <?php foreach ($travel_meta_fields['travel_hotels'] as $hotel): 
                                if (empty($hotel['hotelData'])) continue;
                                $hotel_data = $hotel['hotelData'];
                            ?>
                                <div class="hotel-card">
                                    <?php if (!empty($hotel_data['imageUrls'][0])): ?>
                                        <img src="<?php echo esc_url($hotel_data['imageUrls'][0]); ?>" 
                                             alt="<?php echo esc_attr($hotel_data['name']); ?>" 
                                             class="hotel-image">
                                    <?php endif; ?>
                                    
                                    <div class="hotel-info">
                                        <h3 class="hotel-name"><?php echo esc_html($hotel_data['name']); ?></h3>
                                        
                                        <?php if (!empty($hotel_data['address'])): ?>
                                            <div class="hotel-location">
                                                📍 <?php echo esc_html($hotel_data['address']); ?>
                                                <?php if (!empty($hotel_data['city'])): ?>
                                                    , <?php echo esc_html($hotel_data['city']); ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($hotel_data['stars'])): ?>
                                            <div style="margin-bottom: 12px;">
                                                <span style="color: var(--accent); font-size: 18px;">
                                                    <?php echo str_repeat('★', $hotel_data['stars']); ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($hotel_data['description'])): ?>
                                            <div style="color: var(--text-light); margin-bottom: 16px; font-size: 14px;">
                                                <?php echo wp_kses_post(substr($hotel_data['description'], 0, 150)); ?>...
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($hotel_data['facilities']) || !empty($hotel_data['otherServices'])): ?>
                                            <div class="hotel-amenities">
                                                <?php 
                                                $amenities = array_merge(
                                                    !empty($hotel_data['facilities']) ? $hotel_data['facilities'] : array(),
                                                    !empty($hotel_data['otherServices']) ? $hotel_data['otherServices'] : array()
                                                );
                                                foreach (array_slice($amenities, 0, 8) as $amenity):
                                                ?>
                                                    <span class="amenity-tag">✓ <?php echo esc_html($amenity); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- DESTINATIONS ACCORDION -->
            <?php if (!empty($travel_meta_fields['travel_destinations'])): ?>
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="accordion-icon">🌍</span>
                        Bestemmingen (<?php echo count($travel_meta_fields['travel_destinations']); ?>)
                    </div>
                    <span class="accordion-arrow">▼</span>
                </div>
                <div class="accordion-content">
                    <div class="accordion-body">
                        <?php foreach ($travel_meta_fields['travel_destinations'] as $idx => $destination): ?>
                            <div style="background: var(--white); border-radius: var(--radius); padding: 24px; margin-bottom: 16px; box-shadow: var(--shadow);">
                                <h3 style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 32px;">📍</span>
                                    <?php echo esc_html($destination['name']); ?>
                                    <?php if (!empty($destination['country'])): ?>
                                        <span style="color: var(--text-light); font-size: 14px; font-weight: normal;">
                                            (<?php echo esc_html($destination['country']); ?>)
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                
                                <?php if (!empty($destination['description'])): ?>
                                    <div style="color: var(--text-light); margin-bottom: 16px; line-height: 1.8;">
                                        <?php echo wp_kses_post($destination['description']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($destination['imageUrls'])): ?>
                                    <div class="gallery-grid" style="margin-top: 16px;">
                                        <?php foreach (array_slice($destination['imageUrls'], 0, 4) as $image_url): ?>
                                            <div class="gallery-item" onclick="openLightbox('<?php echo esc_url($image_url); ?>')">
                                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($destination['name']); ?>" loading="lazy">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- TRANSPORTS ACCORDION -->
            <?php if (!empty($travel_meta_fields['travel_transports'])): ?>
            <div class="accordion-item">
                <div class="accordion-header">
                    <div class="accordion-title">
                        <span class="accordion-icon">✈️</span>
                        Vluchten (<?php echo count($travel_meta_fields['travel_transports']); ?>)
                    </div>
                    <span class="accordion-arrow">▼</span>
                </div>
                <div class="accordion-content">
                    <div class="accordion-body">
                        <?php foreach ($travel_meta_fields['travel_transports'] as $transport): ?>
                            <div style="background: var(--bg); border-radius: var(--radius); padding: 20px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-weight: 700; font-size: 18px; margin-bottom: 8px;">
                                        <?php echo esc_html($transport['from']); ?> 
                                        <span style="color: var(--primary);">→</span> 
                                        <?php echo esc_html($transport['to']); ?>
                                    </div>
                                    <?php if (!empty($transport['date'])): ?>
                                        <div style="color: var(--text-light); font-size: 14px;">
                                            📅 <?php echo date('d M Y', strtotime($transport['date'])); ?>
                                            <?php if (!empty($transport['time'])): ?>
                                                om <?php echo date('H:i', strtotime($transport['time'])); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($transport['airline'])): ?>
                                        <div style="color: var(--text-light); font-size: 14px; margin-top: 4px;">
                                            ✈️ <?php echo esc_html($transport['airline']); ?>
                                            <?php if (!empty($transport['flightNumber'])): ?>
                                                - Vlucht <?php echo esc_html($transport['flightNumber']); ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </section>

        <!-- CTA SECTION -->
        <section class="text-center mt-4 mb-4">
            <div class="timeline-card" style="padding: 48px; background: linear-gradient(135deg, var(--primary) 0%, #1976D2 100%); color: white;">
                <h2 style="font-size: 36px; margin-bottom: 16px;">Klaar voor vertrek?</h2>
                <p style="font-size: 18px; margin-bottom: 32px; opacity: 0.95;">
                    Boek nu deze fantastische reis vanaf €<?php echo number_format($travel_meta_fields['travel_price_per_person'], 0, ',', '.'); ?> p.p.
                </p>
                <a href="<?php echo esc_url($travel_meta_fields['travel_idea_url']); ?>" 
                   class="btn-primary" 
                   style="background: white; color: var(--primary); font-size: 18px; padding: 20px 40px;">
                    📞 Vraag vrijblijvend aan
                </a>
            </div>
        </section>
        
    </div>
</div>

<script>
// Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const accordionHeaders = document.querySelectorAll('.accordion-header');
    
    accordionHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const item = this.parentElement;
            const wasActive = item.classList.contains('active');
            
            // Close all accordions
            document.querySelectorAll('.accordion-item').forEach(i => {
                i.classList.remove('active');
            });
            
            // Open clicked one if it wasn't active
            if (!wasActive) {
                item.classList.add('active');
            }
        });
    });
});

// Simple lightbox
function openLightbox(imageUrl) {
    // Create lightbox overlay
    const lightbox = document.createElement('div');
    lightbox.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        cursor: pointer;
    `;
    
    const img = document.createElement('img');
    img.src = imageUrl;
    img.style.cssText = 'max-width: 90%; max-height: 90%; object-fit: contain;';
    
    lightbox.appendChild(img);
    document.body.appendChild(lightbox);
    
    lightbox.addEventListener('click', function() {
        document.body.removeChild(lightbox);
    });
}
</script>

<?php rbstravel_get_footer(); ?>
