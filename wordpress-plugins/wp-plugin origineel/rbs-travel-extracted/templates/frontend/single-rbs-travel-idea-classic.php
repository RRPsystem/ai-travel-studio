<?php
/**
 * Template Name: Classic Tour Detail Layout
 * Description: Tour detail page with photo header, sidebar booking form, and day-by-day accordion
 */

if (!defined('ABSPATH')) exit;

global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

rbstravel_get_header();
?>

<style>
:root {
    --primary: #28a745;
    --secondary: #6c757d;
    --text: #212529;
    --text-light: #6c757d;
    --border: #dee2e6;
    --bg-light: #f8f9fa;
    --white: #ffffff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    color: var(--text);
    line-height: 1.6;
}

/* === PHOTO HEADER === */
.photo-header {
    width: 100%;
    margin-bottom: 0;
}

.photo-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    max-height: 400px;
}

.photo-grid.single-photo {
    grid-template-columns: 1fr;
    max-height: 500px;
}

.photo-grid-item {
    position: relative;
    overflow: hidden;
    aspect-ratio: 4/3;
    cursor: pointer;
}

.photo-grid-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-grid-item:hover img {
    transform: scale(1.05);
}

.photo-count-overlay {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* === TITLE BAR === */
.title-bar {
    background: white;
    padding: 24px 0;
    border-bottom: 1px solid var(--border);
}

.title-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.title-left h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 8px;
}

.title-location {
    color: var(--text-light);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.title-right {
    display: flex;
    gap: 32px;
    align-items: flex-start;
}

.info-badge {
    text-align: center;
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e8f5e9;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-size: 20px;
}

.info-label {
    font-size: 12px;
    color: var(--text-light);
    margin-bottom: 4px;
}

.info-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
}

.info-value.price {
    color: var(--primary);
    font-size: 18px;
}

/* === MAIN CONTENT === */
.main-content {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 40px;
}

.content-left {
    min-width: 0;
}

/* === INTRO SECTION === */
.intro-section {
    margin-bottom: 40px;
}

.intro-section h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 16px;
}

.intro-text {
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 24px;
}

/* === TOUR AMENITIES === */
.tour-amenities {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 40px;
}

.tour-amenities h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
}

.amenities-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.amenity-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: var(--bg-light);
    border-radius: 6px;
}

.amenity-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.amenity-label {
    font-size: 14px;
    font-weight: 500;
}

/* === TOUR PLAN (ACCORDION) === */
.tour-plan {
    margin-bottom: 40px;
}

.tour-plan h2 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 24px;
}

.day-accordion {
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 16px;
}

.day-header {
    background: white;
    padding: 20px 24px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background 0.3s ease;
    border-bottom: 1px solid var(--border);
}

.day-header:hover {
    background: var(--bg-light);
}

.day-header.active {
    background: var(--primary);
    color: white;
}

.day-header.active .day-arrow {
    transform: rotate(180deg);
}

.day-title {
    font-size: 18px;
    font-weight: 600;
}

.day-arrow {
    font-size: 20px;
    transition: transform 0.3s ease;
}

.day-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s ease;
}

.day-content.active {
    max-height: 2000px;
}

.day-body {
    padding: 24px;
    background: white;
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 24px;
}

.day-image {
    width: 300px;
    height: 200px;
    object-fit: cover;
    border-radius: 8px;
}

.day-details h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
}

.day-description {
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 16px;
}

.day-info-items {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.day-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--text-light);
}

.day-info-item strong {
    color: var(--text);
}

/* === INCLUDED/EXCLUDED === */
.included-section {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 40px;
}

.included-section h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 16px;
}

.included-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.included-list {
    list-style: none;
}

.included-list li {
    padding: 8px 0;
    color: var(--text-light);
    font-size: 14px;
}

.included-list li::before {
    content: "✓ ";
    color: var(--primary);
    font-weight: 700;
    margin-right: 8px;
}

/* === SIDEBAR === */
.sidebar {
    position: sticky;
    top: 20px;
}

.booking-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.booking-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.form-group {
    margin-bottom: 16px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 4px;
    font-size: 14px;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
}

.btn-book {
    width: 100%;
    padding: 16px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.3s ease;
}

.btn-book:hover {
    background: #218838;
}

.btn-secondary {
    width: 100%;
    padding: 12px;
    background: white;
    color: var(--text);
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 12px;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: var(--bg-light);
}

/* === TOUR INFO === */
.tour-info-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tour-info-card h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item-icon {
    width: 36px;
    height: 36px;
    background: #e8f5e9;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.info-item-text {
    flex: 1;
}

.info-item-label {
    font-size: 12px;
    color: var(--text-light);
    margin-bottom: 2px;
}

.info-item-value {
    font-size: 14px;
    font-weight: 600;
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    .main-content {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        position: static;
    }
    
    .photo-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .title-container {
        flex-direction: column;
        gap: 20px;
    }
    
    .title-right {
        width: 100%;
        justify-content: space-between;
    }
    
    .amenities-grid {
        grid-template-columns: 1fr;
    }
    
    .day-body {
        grid-template-columns: 1fr;
    }
    
    .day-image {
        width: 100%;
        height: 200px;
    }
    
    .included-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="classic-layout">
    <!-- PHOTO HEADER -->
    <section class="photo-header">
        <?php
        // Collect photos for header
        $header_photos = array();
        
        if (!empty($travel_meta_fields['travel_destinations'])) {
            foreach ($travel_meta_fields['travel_destinations'] as $dest) {
                if (!empty($dest['imageUrls'])) {
                    $header_photos = array_merge($header_photos, array_slice($dest['imageUrls'], 0, 2));
                }
            }
        }
        
        if (!empty($travel_meta_fields['travel_hotels'])) {
            foreach ($travel_meta_fields['travel_hotels'] as $hotel) {
                if (!empty($hotel['hotelData']['imageUrls'])) {
                    $header_photos = array_merge($header_photos, array_slice($hotel['hotelData']['imageUrls'], 0, 1));
                }
            }
        }
        
        $header_photos = array_slice(array_unique($header_photos), 0, 4);
        $photo_count = count($header_photos);
        
        // Get total photo count
        $total_photos = 0;
        if (!empty($travel_meta_fields['travel_destinations'])) {
            foreach ($travel_meta_fields['travel_destinations'] as $dest) {
                if (!empty($dest['imageUrls'])) {
                    $total_photos += count($dest['imageUrls']);
                }
            }
        }
        ?>
        
        <div class="photo-grid <?php echo $photo_count == 1 ? 'single-photo' : ''; ?>">
            <?php foreach ($header_photos as $index => $photo): ?>
                <div class="photo-grid-item" onclick="openLightbox('<?php echo esc_url($photo); ?>')">
                    <img src="<?php echo esc_url($photo); ?>" alt="Travel photo <?php echo $index + 1; ?>">
                    <?php if ($index == 3 && $total_photos > 4): ?>
                        <div class="photo-count-overlay">
                            📷 +<?php echo $total_photos - 4; ?> foto's
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- TITLE BAR -->
    <section class="title-bar">
        <div class="title-container">
            <div class="title-left">
                <h1><?php the_title(); ?></h1>
                <div class="title-location">
                    📍 
                    <?php 
                    if (!empty($travel_meta_fields['travel_destinations'][0]['name'])) {
                        echo esc_html($travel_meta_fields['travel_destinations'][0]['name']);
                        if (!empty($travel_meta_fields['travel_destinations'][0]['country'])) {
                            echo ', ' . esc_html($travel_meta_fields['travel_destinations'][0]['country']);
                        }
                    }
                    ?>
                </div>
            </div>
            
            <div class="title-right">
                <div class="info-badge">
                    <div class="info-icon">💰</div>
                    <div class="info-label">From</div>
                    <div class="info-value price">€<?php echo number_format($travel_meta_fields['travel_price_per_person'], 0, ',', '.'); ?></div>
                </div>
                
                <div class="info-badge">
                    <div class="info-icon">⏱️</div>
                    <div class="info-label">Duration</div>
                    <div class="info-value"><?php echo $travel_meta_fields['travel_number_of_nights']; ?> days</div>
                </div>
                
                <div class="info-badge">
                    <div class="info-icon">🎯</div>
                    <div class="info-label">Tour Type</div>
                    <div class="info-value">
                        <?php 
                        // Try to get tour type from themes or set default
                        echo !empty($travel_meta_fields['travel_themes'][0]) ? esc_html($travel_meta_fields['travel_themes'][0]) : 'Adventure';
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="content-left">
            <!-- INTRO SECTION -->
            <section class="intro-section">
                <h2>Explore Tours</h2>
                <div class="intro-text">
                    <?php the_content(); ?>
                </div>
            </section>

            <!-- TOUR AMENITIES / REISELEMENTEN -->
            <section class="tour-amenities">
                <h3>Tour Amenities</h3>
                <div class="amenities-grid">
                    <?php if (!empty($travel_meta_fields['travel_transports'])): ?>
                        <div class="amenity-item">
                            <div class="amenity-icon">✈️</div>
                            <div class="amenity-label">Vliegtickets</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($travel_meta_fields['travel_transfers'])): ?>
                        <div class="amenity-item">
                            <div class="amenity-icon">🚐</div>
                            <div class="amenity-label">Transfers</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($travel_meta_fields['travel_cruises'])): ?>
                        <div class="amenity-item">
                            <div class="amenity-icon">🚢</div>
                            <div class="amenity-label">Cruise</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($travel_meta_fields['travel_hotels'])): ?>
                        <div class="amenity-item">
                            <div class="amenity-icon">🏨</div>
                            <div class="amenity-label">Hotels</div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($travel_meta_fields['travel_cars'])): ?>
                        <div class="amenity-item">
                            <div class="amenity-icon">🚗</div>
                            <div class="amenity-label">Huurauto</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="amenity-item">
                        <div class="amenity-icon">🍽️</div>
                        <div class="amenity-label">Restaurant</div>
                    </div>
                </div>
            </section>

            <!-- TOUR PLAN (DAY BY DAY) -->
            <?php include(RBS_TRAVEL_DIR . '/templates/frontend/partials/tour-plan-accordion.php'); ?>

            <!-- INCLUDED/EXCLUDED -->
            <section class="included-section">
                <h3>Included/Exclude</h3>
                <div class="included-grid">
                    <div>
                        <ul class="included-list">
                            <li>Pick and Drop Services</li>
                            <li>1 Meal Per Day</li>
                            <li>Cruise Dinner & Music Event</li>
                            <li>Visit 7 Best Places in the City With Group</li>
                        </ul>
                    </div>
                    <div>
                        <ul class="included-list">
                            <li>Additional Services</li>
                            <li>Insurance</li>
                            <li>Food & Drinks</li>
                            <li>Tickets</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <!-- BOOKING FORM -->
            <div class="booking-card">
                <h3>Booking Tour</h3>
                
                <form id="booking-form">
                    <div class="form-group">
                        <label class="form-label">From:</label>
                        <input type="date" class="form-input" value="<?php echo $travel_meta_fields['travel_departure_date']; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Time:</label>
                        <input type="time" class="form-input" value="10:00">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tickets:</label>
                        <select class="form-input">
                            <option>please, select date first</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn-book" onclick="window.location.href='<?php echo esc_url($travel_meta_fields['travel_idea_url']); ?>'">
                        🛒 Book Now
                    </button>
                    
                    <button type="button" class="btn-secondary">
                        📧 Info aanvragen
                    </button>
                    
                    <button type="button" class="btn-secondary">
                        ✏️ Reis aanpassen
                    </button>
                </form>
            </div>

            <!-- TOUR INFORMATION -->
            <div class="tour-info-card">
                <h3>Tour Information</h3>
                
                <div class="info-item">
                    <div class="info-item-icon">👥</div>
                    <div class="info-item-text">
                        <div class="info-item-label">Max Guests</div>
                        <div class="info-item-value"><?php echo $travel_meta_fields['travel_max_persons']; ?></div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-item-icon">👤</div>
                    <div class="info-item-text">
                        <div class="info-item-label">Min Guests</div>
                        <div class="info-item-value"><?php echo $travel_meta_fields['travel_min_persons']; ?></div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-item-icon">🎂</div>
                    <div class="info-item-text">
                        <div class="info-item-label">Min Age</div>
                        <div class="info-item-value">12+</div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-item-icon">✈️</div>
                    <div class="info-item-text">
                        <div class="info-item-label">Tour Location</div>
                        <div class="info-item-value">
                            <?php echo !empty($travel_meta_fields['travel_destinations'][0]['country']) 
                                ? esc_html($travel_meta_fields['travel_destinations'][0]['country']) 
                                : 'International'; ?>
                        </div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-item-icon">🌍</div>
                    <div class="info-item-text">
                        <div class="info-item-label">Languages Support</div>
                        <div class="info-item-value">Nederlands, English</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

<script>
// Day accordion toggle
document.querySelectorAll('.day-header').forEach(header => {
    header.addEventListener('click', function() {
        const content = this.nextElementSibling;
        const wasActive = this.classList.contains('active');
        
        // Close all
        document.querySelectorAll('.day-header').forEach(h => h.classList.remove('active'));
        document.querySelectorAll('.day-content').forEach(c => c.classList.remove('active'));
        
        // Open clicked if wasn't active
        if (!wasActive) {
            this.classList.add('active');
            content.classList.add('active');
        }
    });
});

// Lightbox
function openLightbox(imageUrl) {
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
    
    lightbox.addEventListener('click', () => document.body.removeChild(lightbox));
}
</script>

<?php rbstravel_get_footer(); ?>
