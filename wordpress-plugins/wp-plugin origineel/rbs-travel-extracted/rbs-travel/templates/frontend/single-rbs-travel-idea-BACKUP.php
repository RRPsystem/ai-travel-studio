<?php
/**
 * Template Name: Sales-Focused Travel Detail
 * Description: Modern template with grouped days and sliding detail panels for maximum conversions
 */

if (!defined('ABSPATH')) exit;

global $travel_meta_fields;
$travel_meta_fields = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields();

// Get TC base URL from settings
$tc_base_url = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('tc_base_url', 'https://online.travelcompositor.com/');

// Get brand colors from settings
$primary_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('primary_color', '#6366f1');
$secondary_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('secondary_color', '#10b981');
$heading_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('heading_color', '#1f2937');
$text_color = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('text_color', '#4b5563');

rbstravel_get_header();
?>

<style>
/* INLINE SCRIPT IN HEAD - WordPress kan dit niet blokkeren */
</style>

<script>
// Travel Compositor base URL voor whitelabel support
var tcBaseUrl = '<?php echo esc_js(rtrim($tc_base_url, '/')); ?>';

// Globale functies - direct beschikbaar
function showDestinationDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-dest'));
    openDetailPanel('destination', data);
}

function showHotelDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-hotel'));
    openDetailPanel('hotel', data);
}

function showCruiseDetail(btn) {
    var data = JSON.parse(btn.getAttribute('data-cruise'));
    openDetailPanel('cruise', data);
}

function openDetailPanel(type, data) {
    var panel = document.getElementById('detailPanel');
    var overlay = document.getElementById('detailPanelOverlay');
    var title = document.getElementById('detailPanelTitle');
    var body = document.getElementById('detailPanelBody');
    
    var images = [];
    
    if (type === 'hotel') {
        title.textContent = '🏨 ' + data.name;
        body.innerHTML = generateHotelDetail(data);
        // Extract images for gallery
        if (data.images && data.images.length > 0) {
            images = data.images.map(function(img) { return img.url; });
        }
    } else if (type === 'destination') {
        title.textContent = '📍 ' + data.name;
        body.innerHTML = generateDestinationDetail(data);
        // Extract images for gallery
        if (data.imageUrls && data.imageUrls.length > 0) {
            images = data.imageUrls;
        }
    } else if (type === 'cruise') {
        title.textContent = '🚢 Cruise';
        body.innerHTML = generateCruiseDetail(data);
        // Extract images for gallery
        if (data.images && data.images.length > 0) {
            images = data.images;
        }
    }
    
    panel.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    
    // Initialize gallery AFTER HTML is added
    if (images.length > 0) {
        setTimeout(function() {
            initGallery(images);
        }, 100);
    }
    
    // Initialize hotel map if pending
    if (window.pendingHotelMap) {
        setTimeout(function() {
            initHotelMap(window.pendingHotelMap.lat, window.pendingHotelMap.lng, window.pendingHotelMap.name);
            window.pendingHotelMap = null;
        }, 300);
    }
}

function closeDetailPanel() {
    document.getElementById('detailPanel').classList.remove('active');
    document.getElementById('detailPanelOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Hotel Map initialization
var hotelMapInstance = null;
function initHotelMap(lat, lng, hotelName) {
    var container = document.getElementById('hotelMapContainer');
    if (!container) return;
    
    // Clean up existing map
    if (hotelMapInstance) {
        hotelMapInstance.remove();
        hotelMapInstance = null;
    }
    
    // Get Mapbox token from global or use default
    var mapboxToken = window.mapboxToken || 'pk.eyJ1Ijoicm9uZHJlaXNwbGFubmVyIiwiYSI6ImNtNGZtcXo0eTBkMnkya3B2OWJsMzM5ZHcifQ.LlxEnvSP7Z0ejLkWBBkHYA';
    
    hotelMapInstance = L.map('hotelMapContainer').setView([lat, lng], 15);
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxToken, {
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1
    }).addTo(hotelMapInstance);
    
    // Add marker
    L.marker([lat, lng]).addTo(hotelMapInstance)
        .bindPopup('<strong>' + hotelName + '</strong>')
        .openPopup();
}

// Photo Gallery - Global state
window.currentGalleryIndex = 0;
window.currentGalleryImages = [];

function initGallery(images) {
    console.log('🖼️ Init gallery with', images.length, 'images');
    window.currentGalleryImages = images;
    window.currentGalleryIndex = 0;
    updateGalleryImage();
}

function galleryNext() {
    console.log('➡️ Next');
    window.currentGalleryIndex = (window.currentGalleryIndex + 1) % window.currentGalleryImages.length;
    updateGalleryImage();
    return false;
}

function galleryPrev() {
    console.log('⬅️ Prev');
    window.currentGalleryIndex = (window.currentGalleryIndex - 1 + window.currentGalleryImages.length) % window.currentGalleryImages.length;
    updateGalleryImage();
    return false;
}

function galleryGoTo(index) {
    console.log('🎯 Go to', index);
    window.currentGalleryIndex = index;
    updateGalleryImage();
    return false;
}

function updateGalleryImage() {
    var img = document.getElementById('galleryMainImage');
    var counter = document.getElementById('galleryCounter');
    
    if (!img) {
        console.log('⚠️ No gallery image element found');
        return;
    }
    
    if (window.currentGalleryImages.length === 0) {
        console.log('⚠️ No images in array');
        return;
    }
    
    if (window.currentGalleryImages[window.currentGalleryIndex]) {
        var newSrc = window.currentGalleryImages[window.currentGalleryIndex];
        img.src = newSrc;
        console.log('✅ Updated to image', window.currentGalleryIndex + 1, 'of', window.currentGalleryImages.length);
        
        if (counter) {
            counter.textContent = (window.currentGalleryIndex + 1) + ' / ' + window.currentGalleryImages.length;
        }
    }
    
    // Update thumbnails
    var thumbs = document.querySelectorAll('.gallery-thumbnail');
    thumbs.forEach(function(thumb, index) {
        if (index === window.currentGalleryIndex) {
            thumb.classList.add('active');
        } else {
            thumb.classList.remove('active');
        }
    });
}

// Generate HTML functions
function generateHotelDetail(data) {
    var html = '';
    
    // Photo Gallery
    if (data.images && data.images.length > 0) {
        var imageUrls = data.images.map(function(img) { return img.url; });
        html += '<div class="photo-gallery">';
        html += '<img id="galleryMainImage" src="' + imageUrls[0] + '" alt="Hotel photo" class="gallery-main-image">';
        html += '<div class="gallery-nav">';
        html += '<button class="gallery-nav-btn" onclick="galleryPrev(); return false;">‹</button>';
        html += '<button class="gallery-nav-btn" onclick="galleryNext(); return false;">›</button>';
        html += '</div>';
        html += '<div class="gallery-counter" id="galleryCounter">1 / ' + imageUrls.length + '</div>';
        html += '</div>';
        html += '<div class="gallery-thumbnails">';
        for (var i = 0; i < Math.min(10, imageUrls.length); i++) {
            html += '<img src="' + imageUrls[i] + '" class="gallery-thumbnail ' + (i === 0 ? 'active' : '') + '" onclick="galleryGoTo(' + i + '); return false;" alt="Thumbnail ' + (i+1) + '">';
        }
        html += '</div>';
    }
    
    // Description
    if (data.description) {
        html += '<div class="detail-section"><h3>📝 Beschrijving</h3><div class="detail-description">' + data.description + '</div></div>';
    }
    
    // Hotel Location Map (instead of text address)
    var hasCoords = false;
    var mapLat = 0, mapLng = 0;
    if (data.latitude && data.longitude) {
        hasCoords = true;
        mapLat = data.latitude;
        mapLng = data.longitude;
    } else if (data.destination && data.destination.latitude && data.destination.longitude) {
        hasCoords = true;
        mapLat = data.destination.latitude;
        mapLng = data.destination.longitude;
    }
    
    if (hasCoords) {
        html += '<div class="detail-section"><h3>📍 Locatie</h3>';
        html += '<div id="hotelMapContainer" class="hotel-map-container" data-lat="' + mapLat + '" data-lng="' + mapLng + '" data-name="' + (data.name || 'Hotel').replace(/"/g, '&quot;') + '" style="width: 100%; height: 300px; border-radius: 8px; overflow: hidden; margin-bottom: 10px;"></div>';
        html += '</div>';
        // Store coords for later initialization
        window.pendingHotelMap = { lat: mapLat, lng: mapLng, name: data.name || 'Hotel' };
    }
    
    // Facilities
    if (data.facilities && data.facilities.otherFacilities && data.facilities.otherFacilities.length > 0) {
        var facilities = data.facilities.otherFacilities.slice(0, 12);
        html += '<div class="detail-section"><h3>🏊 Faciliteiten</h3><div class="facilities-grid">';
        facilities.forEach(function(fac) {
            html += '<div class="facility-item"><span class="facility-icon">✓</span><span>' + fac.description + '</span></div>';
        });
        html += '</div></div>';
    }
    
    return html;
}

function generateDestinationDetail(data) {
    var html = '';
    // Images
    if (data.imageUrls && data.imageUrls.length > 0) {
        html += '<div class="photo-gallery">';
        html += '<img id="galleryMainImage" src="' + data.imageUrls[0] + '" alt="Destination photo" class="gallery-main-image">';
        html += '<div class="gallery-nav">';
        html += '<button class="gallery-nav-btn" onclick="galleryPrev(); return false;">‹</button>';
        html += '<button class="gallery-nav-btn" onclick="galleryNext(); return false;">›</button>';
        html += '</div>';
        html += '<div class="gallery-counter" id="galleryCounter">1 / ' + data.imageUrls.length + '</div>';
        html += '</div>';
        html += '<div class="gallery-thumbnails">';
        for (var i = 0; i < Math.min(10, data.imageUrls.length); i++) {
            html += '<img src="' + data.imageUrls[i] + '" class="gallery-thumbnail ' + (i === 0 ? 'active' : '') + '" onclick="galleryGoTo(' + i + '); return false;" alt="Thumbnail ' + (i+1) + '">';
        }
        html += '</div>';
    }
    
    if (data.description) {
        html += '<div class="detail-section"><h3> Over ' + data.name + '</h3><div class="detail-description">' + data.description + '</div></div>';
    }
    
    return html;
}

function generateCruiseDetail(data) {
    var html = '';
    
    // Photo Gallery for Cruise
    if (data.images && data.images.length > 0) {
        html += '<div class="photo-gallery">';
        html += '<img id="galleryMainImage" src="' + data.images[0] + '" alt="Cruise photo" class="gallery-main-image">';
        html += '<div class="gallery-nav">';
        html += '<button class="gallery-nav-btn" onclick="galleryPrev(); return false;">‹</button>';
        html += '<button class="gallery-nav-btn" onclick="galleryNext(); return false;">›</button>';
        html += '</div>';
        html += '<div class="gallery-counter" id="galleryCounter">1 / ' + data.images.length + '</div>';
        html += '</div>';
        html += '<div class="gallery-thumbnails">';
        for (var i = 0; i < Math.min(10, data.images.length); i++) {
            html += '<img src="' + data.images[i] + '" class="gallery-thumbnail ' + (i === 0 ? 'active' : '') + '" onclick="galleryGoTo(' + i + '); return false;" alt="Thumbnail ' + (i+1) + '">';
        }
        html += '</div>';
    }
    
    // Cruise Basic Info
    html += '<div class="detail-section"><h3>📝 Cruise Informatie</h3><div class="info-items">';
    
    if (data.embarkDate) {
        html += '<div class="info-item-row"><div class="info-item-label">Vertrek:</div><div class="info-item-value">' + new Date(data.embarkDate).toLocaleDateString('nl-NL', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) + '</div></div>';
    }
    if (data.disembarkDate) {
        html += '<div class="info-item-row"><div class="info-item-label">Aankomst:</div><div class="info-item-value">' + new Date(data.disembarkDate).toLocaleDateString('nl-NL', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}) + '</div></div>';
    }
    if (data.nights) {
        html += '<div class="info-item-row"><div class="info-item-label">Aantal Nachten:</div><div class="info-item-value">' + data.nights + ' nachten</div></div>';
    }
    if (data.embarkPort) {
        html += '<div class="info-item-row"><div class="info-item-label">Vertrek Haven:</div><div class="info-item-value">' + data.embarkPort + '</div></div>';
    }
    if (data.disembarkPort) {
        html += '<div class="info-item-row"><div class="info-item-label">Aankomst Haven:</div><div class="info-item-value">' + data.disembarkPort + '</div></div>';
    }
    
    html += '</div></div>';
    
    // Cabin Information - use category (selectedCategory) for readable cabin type
    var cabinType = data.category || data.cabin || '';
    if (cabinType) {
        html += '<div class="detail-section"><h3>🛏️ Hut Informatie</h3><div class="info-items">';
        html += '<div class="info-item-row"><div class="info-item-label">Hut Type:</div><div class="info-item-value">' + cabinType + '</div></div>';
        html += '</div></div>';
    }
    
    // Ship Information - use shipName or ship for readable name
    var shipName = data.shipName || data.ship || '';
    var cruiseLine = data.cruiseLine || '';
    if (shipName || cruiseLine) {
        html += '<div class="detail-section"><h3>🚢 Schip Informatie</h3><div class="info-items">';
        if (cruiseLine) {
            html += '<div class="info-item-row"><div class="info-item-label">Cruise Lijn:</div><div class="info-item-value">' + cruiseLine + '</div></div>';
        }
        if (shipName) {
            html += '<div class="info-item-row"><div class="info-item-label">Schip:</div><div class="info-item-value">' + shipName + '</div></div>';
        }
        html += '</div></div>';
    }
    
    // Cruise Ports/Destinations - only show port names with icons, no descriptions
    if (data.destinations && data.destinations.length > 0) {
        html += '<div class="detail-section"><h3>⚓ Cruise Route & Havens</h3>';
        
        // Simple list of ports with icons
        html += '<div class="cruise-ports-list" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">';
        data.destinations.forEach(function(port, index) {
            var portName = typeof port === 'string' ? port : (port.name || 'Haven ' + (index + 1));
            html += '<span style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #f0f4f8; border-radius: 20px; font-size: 14px;">⚓ ' + portName + '</span>';
        });
        html += '</div>';
        
        // Add itinerary link if available - use secondary color from CSS variable
        if (data.itineraryUrl) {
            var fullUrl = tcBaseUrl + data.itineraryUrl;
            html += '<div style="margin-top: 15px;">';
            html += '<a href="' + fullUrl + '" target="_blank" rel="noopener" class="itinerary-link" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--secondary); color: white; text-decoration: none; border-radius: 5px; font-weight: 500; transition: background 0.3s;">';
            html += '🗺️ Zie Routekaart & Cruise Omschrijving';
            html += '</a>';
            html += '</div>';
        }
        
        html += '</div>';
    }
    
    // Description if available
    if (data.description) {
        html += '<div class="detail-section"><h3>📖 Beschrijving</h3><div class="detail-description">' + data.description + '</div></div>';
    }
    
    return html;
}
</script>

<style>
:root {
    --primary: <?php echo esc_attr($primary_color); ?>;
    --primary-dark: <?php echo esc_attr($primary_color); ?>;
    --secondary: <?php echo esc_attr($secondary_color); ?>;
    --text: <?php echo esc_attr($text_color); ?>;
    --text-light: #6c757d;
    --heading: <?php echo esc_attr($heading_color); ?>;
    --border: #dee2e6;
    --bg-light: #f8f9fa;
    --white: #ffffff;
    --shadow: 0 2px 8px rgba(0,0,0,0.1);
    --shadow-lg: 0 4px 20px rgba(0,0,0,0.15);
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

/* === PAGE WRAPPER WITH FIXED SIDEBAR === */
.page-content-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    gap: 30px;
}

.page-main-content {
    flex: 1;
    min-width: 0;
}

.page-sidebar {
    width: 350px;
    flex-shrink: 0;
}

.page-sidebar .sidebar-inner {
    position: sticky;
    top: 20px;
}

@media (max-width: 900px) {
    .page-content-wrapper {
        flex-direction: column;
    }
    .page-sidebar {
        width: 100%;
        order: -1;
    }
    .page-sidebar .sidebar-inner {
        position: relative;
        top: 0;
    }
}

/* === INTRO SECTION === */
.intro-section {
    margin-bottom: 5px;
}

.intro-section h2 {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--heading);
}

.intro-text {
    color: var(--text);
    line-height: 1.6;
    margin-bottom: 0;
    max-width: 100%;
}

.intro-text p {
    margin: 0 0 10px 0;
}

.intro-text p:last-child {
    margin-bottom: 0;
}

.intro-text ul, .intro-text ol {
    margin: 5px 0;
    padding-left: 20px;
}

.intro-text li {
    margin-bottom: 3px;
}

/* === ROUTE MAP === */
#rbstravel-idea__fullmap-container {
    margin-bottom: 10px;
    margin-top: 5px;
}

#rbstravel-idea__fullmap {
    height: 280px;
    border-radius: 10px;
    overflow: hidden;
}

#rbstravel-idea__distance {
    margin-top: 5px;
    margin-bottom: 0;
    font-size: 13px;
    color: var(--text-light);
}

/* === TOUR PLAN === */
.tour-plan {
    margin-bottom: 15px;
    margin-top: 0;
}

.tour-plan h2 {
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 15px;
}

/* === DAY GROUP CARD === */
.day-group-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.day-group-card:hover {
    box-shadow: var(--shadow-lg);
}

.day-group-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 20px 24px;
    font-size: 18px;
    font-weight: 600;
}

.day-group-content {
    padding: 24px;
}

.day-item {
    display: flex;
    gap: 20px;
    padding: 16px 0;
    border-bottom: 1px solid var(--border);
}

.day-item:last-child {
    border-bottom: none;
}

.day-item-image {
    width: 150px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
}

.day-item-content {
    flex: 1;
    min-width: 0;
}

.day-item-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.day-item-snippet {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 12px;
}

.day-item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 13px;
    color: var(--text-light);
}

.day-item-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-view-details {
    margin-top: 12px;
    padding: 10px 20px;
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-view-details:hover {
    background: var(--primary);
    color: white;
}

/* === SLIDING DETAIL PANEL === */
.detail-panel-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.detail-panel-overlay.active {
    opacity: 1;
    visibility: visible;
}

.detail-panel {
    position: fixed;
    top: 0;
    right: -600px;
    width: 600px;
    max-width: 90vw;
    height: 100vh;
    background: white;
    z-index: 9999;
    overflow-y: auto;
    box-shadow: -4px 0 20px rgba(0,0,0,0.2);
    transition: right 0.3s ease;
}

.detail-panel.active {
    right: 0;
}

.detail-panel-header {
    position: sticky;
    top: 0;
    background: white;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 10;
}

.detail-panel-title {
    font-size: 20px;
    font-weight: 700;
    flex: 1;
}

.detail-panel-close {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--bg-light);
    border: none;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.detail-panel-close:hover {
    background: var(--border);
}

.detail-panel-body {
    padding: 24px;
}

/* === PHOTO GALLERY CAROUSEL === */
.photo-gallery {
    position: relative;
    margin-bottom: 24px;
    border-radius: 8px;
    overflow: hidden;
}

.gallery-main-image {
    width: 100%;
    height: 350px;
    object-fit: cover;
}

.gallery-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    padding: 0 12px;
    pointer-events: none;
}

.gallery-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.9);
    border: none;
    font-size: 18px;
    cursor: pointer;
    pointer-events: all;
    transition: all 0.3s ease;
}

.gallery-nav-btn:hover {
    background: white;
    transform: scale(1.1);
}

.gallery-counter {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
}

.gallery-thumbnails {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 12px 0;
}

.gallery-thumbnail {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0.6;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.gallery-thumbnail:hover,
.gallery-thumbnail.active {
    opacity: 1;
    transform: scale(1.05);
}

/* === DETAIL SECTIONS === */
.detail-section {
    margin-bottom: 32px;
}

.detail-section h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-description {
    color: var(--text-light);
    line-height: 1.8;
    margin-bottom: 16px;
}

/* === FACILITIES GRID === */
.facilities-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.facility-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: var(--bg-light);
    border-radius: 6px;
    font-size: 14px;
}

.facility-icon {
    font-size: 18px;
}

/* === INFO ITEMS === */
.info-items {
    display: grid;
    gap: 12px;
}

.info-item-row {
    display: flex;
    padding: 12px;
    background: var(--bg-light);
    border-radius: 6px;
}

.info-item-label {
    font-weight: 600;
    min-width: 120px;
    color: var(--text);
}

.info-item-value {
    color: var(--text-light);
}

/* === CRUISE PORTS === */
.cruise-ports {
    display: grid;
    gap: 16px;
}

.port-card {
    background: var(--bg-light);
    padding: 16px;
    border-radius: 8px;
}

.port-card h4 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.port-description {
    color: var(--text-light);
    font-size: 14px;
    line-height: 1.6;
}

/* === Itinerary Link === */
.itinerary-link:hover {
    background: #044d53 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(6, 97, 104, 0.3);
}

/* === CTAs === */
.cta-buttons {
    position: sticky;
    bottom: 0;
    background: white;
    padding: 20px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 12px;
}

.btn-cta-primary {
    flex: 1;
    padding: 16px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cta-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-cta-secondary {
    flex: 1;
    padding: 16px;
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-cta-secondary:hover {
    background: var(--bg-light);
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
    box-shadow: var(--shadow);
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
    transition: all 0.3s ease;
}

.btn-book:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
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

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    .main-content {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        position: static;
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
    
    .photo-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .day-item {
        flex-direction: column;
    }
    
    .day-item-image {
        width: 100%;
        height: 200px;
    }
    
    .detail-panel {
        width: 100vw;
    }
}
</style>

<div class="sales-layout">
    <!-- PHOTO HEADER -->
    <section class="photo-header">
        <?php
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
                } elseif (!empty($hotel['hotelData']['images'])) {
                    $hotel_images = array_map(function($img) {
                        return $img['url'];
                    }, array_slice($hotel['hotelData']['images'], 0, 1));
                    $header_photos = array_merge($header_photos, $hotel_images);
                }
            }
        }
        
        $header_photos = array_slice(array_unique($header_photos), 0, 4);
        $photo_count = count($header_photos);
        
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
                <div class="photo-grid-item">
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
                        <?php echo !empty($travel_meta_fields['travel_themes'][0]) ? esc_html($travel_meta_fields['travel_themes'][0]) : 'Adventure'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PAGE CONTENT WITH SIDEBAR -->
    <div class="page-content-wrapper">
        <!-- MAIN CONTENT -->
        <div class="page-main-content">
            <!-- INTRO -->
            <section class="intro-section">
                <h2>Ontdek deze Reis</h2>
                <div class="intro-text">
                    <?php the_content(); ?>
                </div>
            </section>

            <!-- ROUTE MAP -->
            <?php include(dirname(__FILE__) . '/single-idea/fullmap.html.php'); ?>

            <!-- TOUR PLAN WITH GROUPED DAYS -->
            <?php include(dirname(__FILE__) . '/partials/tour-plan-grouped.php'); ?>
        </div>

        <!-- SIDEBAR -->
        <aside class="page-sidebar">
            <div class="sidebar-inner">
                <div class="booking-card">
                    <h3>Boek deze Reis</h3>
                    
                    <form>
                        <div class="form-group">
                            <label class="form-label">Vertrekdatum:</label>
                            <input type="date" class="form-input" value="<?php echo $travel_meta_fields['travel_departure_date']; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Aantal personen:</label>
                            <select class="form-input">
                                <option>1 persoon</option>
                                <option>2 personen</option>
                                <option>3 personen</option>
                                <option>4 personen</option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-book" onclick="window.location.href='<?php echo esc_url($travel_meta_fields['travel_idea_url']); ?>'">
                            Boek Nu
                        </button>
                        
                        <button type="button" class="btn-secondary">
                            Info Aanvragen
                        </button>
                        
                        <button type="button" class="btn-secondary">
                            Reis Aanpassen
                        </button>
                    </form>
                </div>
                
                <!-- ROUTE MAP BUTTON -->
                <div class="booking-card" style="text-align: center;">
                    <button type="button" class="btn-book" onclick="openRouteMap()" style="width: 100%;">
                        Bekijk de Route
                    </button>
                </div>
                
                <!-- TRAVEL EXPERT -->
                <?php 
                if (function_exists('rbstravel_render_expert_widget')) {
                    rbstravel_render_expert_widget(get_the_ID());
                }
                ?>
            </div>
        </aside>
    </div>

<!-- DETAIL PANEL OVERLAY -->
<div class="detail-panel-overlay" id="detailPanelOverlay" onclick="closeDetailPanel()"></div>

<!-- DETAIL PANEL -->
<div class="detail-panel" id="detailPanel">
    <div class="detail-panel-header">
        <div class="detail-panel-title" id="detailPanelTitle"></div>
        <button class="detail-panel-close" onclick="closeDetailPanel()">×</button>
    </div>
    <div class="detail-panel-body" id="detailPanelBody"></div>
</div>

<!-- ROUTE MAP PANEL - Dynamic Leaflet Map -->
<style>
.route-map-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.6);
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.route-map-overlay.active {
    opacity: 1;
}
.route-map-panel {
    position: fixed;
    top: 0;
    right: -100%;
    width: 80vw;
    max-width: 900px;
    height: 100vh;
    background: white;
    z-index: 9999;
    box-shadow: -10px 0 40px rgba(0,0,0,0.3);
    transition: right 0.4s ease;
    display: flex;
    flex-direction: column;
}
.route-map-panel.active {
    right: 0;
}
.route-map-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    background: var(--primary, #4a6cf7);
    flex-shrink: 0;
}
.route-map-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: white;
}
.route-map-close {
    background: rgba(255,255,255,0.2);
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: white;
    line-height: 1;
    padding: 5px 12px;
    border-radius: 4px;
    transition: background 0.2s;
}
.route-map-close:hover {
    background: rgba(255,255,255,0.3);
}
.route-map-body {
    flex: 1;
    padding: 0;
    overflow: hidden;
    position: relative;
}
#routeMapDynamic {
    width: 100%;
    height: 100%;
}
.route-map-legend {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    z-index: 1000;
    font-size: 13px;
}
.route-map-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}
.route-map-legend-item:last-child {
    margin-bottom: 0;
}
.legend-marker {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: var(--primary, #4a6cf7);
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
.legend-line {
    width: 20px;
    height: 3px;
    background: var(--primary, #4a6cf7);
    border-radius: 2px;
}
</style>
<div class="route-map-overlay" id="routeMapOverlay" onclick="closeRouteMap()"></div>
<div class="route-map-panel" id="routeMapPanel">
    <div class="route-map-header">
        <h3>🗺️ Route Overzicht</h3>
        <button class="route-map-close" onclick="closeRouteMap()">&times;</button>
    </div>
    <div class="route-map-body">
        <div id="routeMapDynamic"></div>
        <div class="route-map-legend">
            <div class="route-map-legend-item"><span class="legend-marker"></span> Bestemming</div>
            <div class="route-map-legend-item"><span class="legend-line"></span> Route</div>
        </div>
    </div>
</div>
<script>
var routeMapInstance = null;

function openRouteMap() {
    document.getElementById('routeMapOverlay').style.display = 'block';
    setTimeout(function() {
        document.getElementById('routeMapOverlay').classList.add('active');
        document.getElementById('routeMapPanel').classList.add('active');
        // Initialize map after panel is visible
        setTimeout(initRouteMap, 100);
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeRouteMap() {
    document.getElementById('routeMapOverlay').classList.remove('active');
    document.getElementById('routeMapPanel').classList.remove('active');
    setTimeout(function() {
        document.getElementById('routeMapOverlay').style.display = 'none';
    }, 300);
    document.body.style.overflow = '';
}

function initRouteMap() {
    var container = document.getElementById('routeMapDynamic');
    if (!container) return;
    
    // Clean up existing map
    if (routeMapInstance) {
        routeMapInstance.remove();
        routeMapInstance = null;
    }
    
    // Check if we have destinations
    if (typeof travelDestinations === 'undefined' || !travelDestinations || travelDestinations.length === 0) {
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#666;">Geen route data beschikbaar</div>';
        return;
    }
    
    // Get Mapbox token
    var mapboxToken = window.mapboxToken || 'pk.eyJ1Ijoicm9uZHJlaXNwbGFubmVyIiwiYSI6ImNtNGZtcXo0eTBkMnkya3B2OWJsMzM5ZHcifQ.LlxEnvSP7Z0ejLkWBBkHYA';
    
    // Calculate bounds
    var bounds = [];
    travelDestinations.forEach(function(dest) {
        if (dest.lat && dest.lng) {
            bounds.push([dest.lat, dest.lng]);
        }
    });
    
    if (bounds.length === 0) {
        container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#666;">Geen locatie data beschikbaar</div>';
        return;
    }
    
    // Initialize map
    routeMapInstance = L.map('routeMapDynamic');
    
    // Add Mapbox tiles
    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxToken, {
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1
    }).addTo(routeMapInstance);
    
    // Fit to bounds with padding
    routeMapInstance.fitBounds(bounds, { padding: [50, 50] });
    
    // Add route line
    if (bounds.length > 1) {
        var routeLine = L.polyline(bounds, {
            color: getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#4a6cf7',
            weight: 4,
            opacity: 0.8,
            dashArray: '10, 10'
        }).addTo(routeMapInstance);
    }
    
    // Add markers with popups
    travelDestinations.forEach(function(dest, index) {
        if (dest.lat && dest.lng) {
            var markerColor = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim() || '#4a6cf7';
            
            // Create custom icon
            var customIcon = L.divIcon({
                className: 'custom-route-marker',
                html: '<div style="background:' + markerColor + ';color:white;width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:12px;border:3px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + (index + 1) + '</div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            
            var marker = L.marker([dest.lat, dest.lng], { icon: customIcon }).addTo(routeMapInstance);
            
            // Create popup content
            var popupContent = '<div style="min-width:150px;">';
            popupContent += '<strong style="font-size:14px;">' + (index + 1) + '. ' + (dest.name || 'Bestemming') + '</strong>';
            if (dest.image) {
                popupContent += '<br><img src="' + dest.image + '" style="width:100%;max-width:200px;height:80px;object-fit:cover;border-radius:4px;margin-top:8px;">';
            }
            popupContent += '</div>';
            
            marker.bindPopup(popupContent);
        }
    });
    
    // Invalidate size after a short delay to ensure proper rendering
    setTimeout(function() {
        routeMapInstance.invalidateSize();
    }, 200);
}
</script>

<?php rbstravel_get_footer(); ?>
