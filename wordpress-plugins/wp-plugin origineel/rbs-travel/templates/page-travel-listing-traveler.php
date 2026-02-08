<?php
/**
 * Template Name: Travel Listing (Traveler Style)
 * Description: 3-column travel listing with destinations and detailed info
 * Version: 5.14.7 - Updated 2024-12-15
 */

// Get WordPress REST API URLs
$api_url = rest_url('rbs-travel/v1/ideas');
$filters_url = rest_url('rbs-travel/v1/filters');

// Get theme colors
$theme_colors = RBS_Travel_Theme_Colors::get_theme_colors();

// Enqueue Leaflet
wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);

get_header(); 

// Output theme color CSS variables
RBS_Travel_Theme_Colors::output_theme_color_css();
?>

<!-- RBS Travel Traveler Template v5.14.7 - 2024-12-15 -->
<style>
    /* Traveler Listing Styles */
    .rbs-traveler-listing {
        background: #f0f4f8;
        padding: 2rem 0 4rem;
        min-height: 100vh;
    }
    
    .rbs-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }
    
    /* Filter Bar */
    .rbs-filter-bar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    
    .rbs-filters-row {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .rbs-filter-group {
        flex: 1;
        min-width: 150px;
    }
    
    .rbs-filter-group label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.4rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .rbs-search-input,
    .rbs-filter-select {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    
    .rbs-search-input:focus,
    .rbs-filter-select:focus {
        border-color: var(--rbs-primary, #3182ce);
        background: white;
    }
    
    .rbs-btn-search {
        padding: 0.7rem 2rem;
        background: var(--rbs-primary, #3182ce);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .rbs-btn-search:hover {
        background: var(--rbs-secondary, #2c5282);
        transform: translateY(-1px);
    }
    
    /* Toolbar */
    .rbs-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 0 0.5rem;
    }
    
    .rbs-stats {
        font-size: 1rem;
        color: #4a5568;
    }
    
    .rbs-stats strong {
        color: var(--rbs-primary, #3182ce);
        font-weight: 700;
    }
    
    .rbs-sort-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.9rem;
        outline: none;
        cursor: pointer;
        background: white;
    }
    
    /* Travel Cards - Traveler Style */
    .rbs-travel-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .rbs-travel-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
        display: grid;
        grid-template-columns: 320px 1fr 220px;
        min-height: 220px;
    }
    
    .rbs-travel-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    /* Image Section - Simple, no hover effect */
    .rbs-card-image-wrapper {
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
    }
    
    .rbs-card-image {
        width: 100%;
        height: 100%;
    }
    
    .rbs-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .rbs-card-image-wrapper:hover img {
        transform: scale(1.05);
    }
    
    /* Favorite Button - Heart Icon */
    .rbs-traveler-listing .rbs-card-favorite {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        width: 40px !important;
        height: 40px !important;
        background: white !important;
        border: none !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15) !important;
        z-index: 10 !important;
        transition: all 0.2s ease !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .rbs-traveler-listing .rbs-card-favorite:hover {
        transform: scale(1.1) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
    }
    
    .rbs-traveler-listing .rbs-card-favorite.active {
        background: #e53e3e !important;
    }
    
    .rbs-traveler-listing .rbs-card-favorite svg {
        width: 20px !important;
        height: 20px !important;
        fill: none !important;
        stroke: #e53e3e !important;
        stroke-width: 2 !important;
        display: block !important;
    }
    
    .rbs-traveler-listing .rbs-card-favorite.active svg {
        fill: white !important;
        stroke: white !important;
    }
    
    /* Content Section */
    .rbs-card-content {
        padding: 1.5rem 2rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .rbs-card-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a365d;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    
    .rbs-card-title a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }
    
    .rbs-card-title a:hover {
        color: var(--rbs-primary, #3182ce);
        text-decoration: underline;
    }
    
    /* Destinations - subtle display */
    .rbs-card-destinations {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0.75rem;
        color: #999999;
        font-size: 8px;
        font-weight: 500;
    }
    
    .rbs-card-destinations svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
    
    /* Route map link */
    .rbs-map-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #e8f4f8;
        border-radius: 20px;
        font-size: 0.85rem;
        color: var(--rbs-primary, #3182ce);
        text-decoration: none;
        transition: all 0.2s ease;
        margin-left: auto;
    }
    
    .rbs-map-link:hover {
        background: var(--rbs-primary, #3182ce);
        color: white;
    }
    
    .rbs-map-link svg {
        width: 14px;
        height: 14px;
    }
    
    .rbs-card-excerpt {
        color: #4a5568;
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .rbs-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: auto;
    }
    
    .rbs-meta-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #edf2f7;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #4a5568;
    }
    
    .rbs-meta-tag svg {
        width: 14px;
        height: 14px;
    }
    
    /* Price Section */
    .rbs-card-price-section {
        background: var(--rbs-primary, #3182ce);
        color: white;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    
    .rbs-price-type {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }
    
    .rbs-price-amount {
        font-size: 2.2rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    
    .rbs-price-note {
        font-size: 0.8rem;
        opacity: 0.85;
        margin-bottom: 1.25rem;
    }
    
    .rbs-btn-info {
        display: inline-block;
        padding: 0.7rem 1.5rem;
        background: white;
        color: var(--rbs-primary, #3182ce);
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 2px solid white;
    }
    
    .rbs-btn-info:hover {
        background: transparent;
        color: white;
    }
    
    /* Favorite Button */
    .rbs-card-favorite {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 36px;
        height: 36px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 10;
        transition: all 0.2s ease;
        border: none;
    }
    
    .rbs-card-favorite:hover {
        transform: scale(1.1);
    }
    
    .rbs-card-favorite.active {
        background: #e53e3e;
        color: white;
    }
    
    .rbs-card-favorite svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: #e53e3e;
        stroke-width: 2;
    }
    
    .rbs-card-favorite.active svg {
        fill: white;
        stroke: white;
    }
    
    /* Loading */
    .rbs-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
    }
    
    .rbs-loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e2e8f0;
        border-top-color: var(--rbs-primary, #3182ce);
        border-radius: 50%;
        animation: rbs-spin 1s linear infinite;
    }
    
    @keyframes rbs-spin {
        to { transform: rotate(360deg); }
    }
    
    /* No Results */
    .rbs-no-results {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
    }
    
    /* Pagination */
    .rbs-pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin: 2.5rem 0;
    }
    
    .rbs-pagination button {
        padding: 0.6rem 1rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-weight: 600;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .rbs-pagination button:hover:not(:disabled) {
        background: var(--rbs-primary, #3182ce);
        color: white;
        border-color: var(--rbs-primary, #3182ce);
    }
    
    .rbs-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .rbs-pagination button.active {
        background: var(--rbs-primary, #3182ce);
        color: white;
        border-color: var(--rbs-primary, #3182ce);
    }
    
    /* Responsive */
    @media (max-width: 1024px) {
        .rbs-travel-card {
            grid-template-columns: 280px 1fr 180px;
        }
    }
    
    @media (max-width: 900px) {
        .rbs-travel-card {
            grid-template-columns: 1fr;
            grid-template-rows: 200px auto auto;
        }
        
        .rbs-card-price-section {
            flex-direction: row;
            justify-content: space-between;
            padding: 1rem 1.5rem;
        }
        
        .rbs-price-amount {
            font-size: 1.8rem;
        }
        
        .rbs-price-note {
            margin-bottom: 0;
        }
    }
    
    @media (max-width: 640px) {
        .rbs-filters-row {
            flex-direction: column;
        }
        
        .rbs-filter-group {
            width: 100%;
        }
        
        .rbs-card-price-section {
            flex-direction: column;
            text-align: center;
        }
        
        .rbs-price-note {
            margin-bottom: 1rem;
        }
    }
    
    /* === SLIDING PANEL FOR ROUTE MAP === */
    .rbs-panel-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9998;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .rbs-panel-overlay.active {
        opacity: 1;
        visibility: visible;
    }
    
    .rbs-route-panel {
        position: fixed;
        top: 0;
        right: -500px;
        width: 500px;
        max-width: 90vw;
        height: 100vh;
        background: white;
        z-index: 9999;
        box-shadow: -5px 0 30px rgba(0,0,0,0.2);
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .rbs-route-panel.active {
        right: 0;
    }
    
    .rbs-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    
    .rbs-panel-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a365d;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .rbs-panel-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 40px;
        height: 40px;
        border: none;
        background: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #333;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        transition: all 0.2s ease;
    }
    
    .rbs-panel-close:hover {
        background: #f0f0f0;
        transform: scale(1.1);
    }
    
    .rbs-route-map-container {
        width: 100%;
        height: 100%;
        background: #e2e8f0;
    }
    
    @media (max-width: 640px) {
        .rbs-route-panel {
            width: 100vw;
            right: -100vw;
        }
    }
</style>

<!-- Elementor Content Area -->
<?php the_content(); ?>

<!-- Loading State -->
<div id="rbs-loading" class="rbs-loading">
    <div class="rbs-loading-spinner"></div>
</div>

<!-- Sliding Panel for Route Map -->
<div id="rbs-panel-overlay" class="rbs-panel-overlay"></div>
<div id="rbs-route-panel" class="rbs-route-panel">
    <button class="rbs-panel-close" id="rbs-panel-close-btn">&times;</button>
    <div id="rbs-route-map-container" class="rbs-route-map-container"></div>
</div>

<!-- Main Container -->
<div id="rbs-app" class="rbs-traveler-listing" data-version="5.14.16" style="display: none;">
    <div class="rbs-container">
        
        <!-- Filter Bar -->
        <div class="rbs-filter-bar">
            <div class="rbs-filters-row">
                <!-- Search -->
                <div class="rbs-filter-group" style="flex: 2;">
                    <label>Zoeken</label>
                    <input 
                        type="text" 
                        id="rbs-search-input" 
                        class="rbs-search-input" 
                        placeholder="Bestemming, reistitel..."
                    >
                </div>
                
                <!-- Location Filter -->
                <div class="rbs-filter-group">
                    <label>Bestemming</label>
                    <select id="rbs-filter-location" class="rbs-filter-select">
                        <option value="">Alle</option>
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div class="rbs-filter-group">
                    <label>Type reis</label>
                    <select id="rbs-filter-type" class="rbs-filter-select">
                        <option value="">Alle</option>
                    </select>
                </div>
                
                <!-- Search Button -->
                <div class="rbs-filter-group" style="flex: 0;">
                    <label>&nbsp;</label>
                    <button id="rbs-apply-filters" class="rbs-btn-search">
                        Zoeken
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Toolbar -->
        <div class="rbs-toolbar">
            <div class="rbs-stats">
                <strong id="rbs-total-ideas">0</strong> reizen gevonden
            </div>
            <select id="rbs-sort-select" class="rbs-sort-select">
                <option value="date">Nieuwste</option>
                <option value="price-asc">Prijs laag → hoog</option>
                <option value="price-desc">Prijs hoog → laag</option>
                <option value="title">Naam A-Z</option>
            </select>
        </div>
        
        <!-- Travel List -->
        <div id="rbs-travel-container" class="rbs-travel-list"></div>
        
        <!-- No Results -->
        <div id="rbs-no-results" class="rbs-no-results" style="display: none;">
            <div style="font-size: 4rem; opacity: 0.3;">🔍</div>
            <h2>Geen reizen gevonden</h2>
            <p>Probeer andere filters of zoektermen.</p>
        </div>
        
        <!-- Pagination -->
        <div id="rbs-pagination" class="rbs-pagination"></div>
        
    </div>
</div>

<script>
(function() {
    const API_URL = '<?php echo esc_js($api_url); ?>';
    const FILTERS_URL = '<?php echo esc_js($filters_url); ?>';
    
    let currentPage = 1;
    let totalPages = 1;
    let filterOptions = {};
    let currentFilters = {};
    let favorites = JSON.parse(localStorage.getItem('rbs_favorites') || '[]');
    
    // Fetch filter options
    async function fetchFilterOptions() {
        try {
            const response = await fetch(FILTERS_URL);
            if (!response.ok) return;
            
            const data = await response.json();
            filterOptions = data;
            populateFilters();
        } catch (error) {
            console.error('Failed to fetch filters:', error);
        }
    }
    
    // Populate filter dropdowns
    function populateFilters() {
        if (filterOptions.locations) {
            const locationSelect = document.getElementById('rbs-filter-location');
            filterOptions.locations.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                locationSelect.appendChild(option);
            });
        }
        
        if (filterOptions.tour_types) {
            const typeSelect = document.getElementById('rbs-filter-type');
            filterOptions.tour_types.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                typeSelect.appendChild(option);
            });
        }
    }
    
    // Build API URL
    function buildApiUrl(page = 1) {
        let url = `${API_URL}?per_page=10&page=${page}`;
        
        if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
        if (currentFilters.location) url += `&location=${encodeURIComponent(currentFilters.location)}`;
        if (currentFilters.tour_type) url += `&tour_type=${encodeURIComponent(currentFilters.tour_type)}`;
        
        if (currentFilters.orderby) {
            url += `&orderby=${currentFilters.orderby}`;
            if (currentFilters.orderby === 'price') {
                url += `&order=${currentFilters.order || 'ASC'}`;
            }
        }
        
        return url;
    }
    
    // Fetch travel ideas
    async function fetchTravelIdeas(page = 1) {
        try {
            const url = buildApiUrl(page);
            const response = await fetch(url);
            if (!response.ok) throw new Error(`API error: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Failed to fetch:', error);
            return null;
        }
    }
    
    // Toggle favorite
    function toggleFavorite(id, button) {
        const index = favorites.indexOf(id);
        if (index > -1) {
            favorites.splice(index, 1);
            button.classList.remove('active');
        } else {
            favorites.push(id);
            button.classList.add('active');
        }
        localStorage.setItem('rbs_favorites', JSON.stringify(favorites));
    }
    
    // Get travel type label
    function getTravelTypeLabel(idea) {
        if (idea.tour_types && idea.tour_types.length > 0) {
            return idea.tour_types[0];
        }
        if (idea.transports && idea.transports.length > 0) {
            const hasFlights = idea.transports.some(t => t.type === 'Flight' || t.type === 'Vlucht');
            const hasCruise = idea.transports.some(t => t.type === 'Cruise');
            if (hasCruise) return 'Cruise reis';
            if (hasFlights) return 'Vliegvakantie';
        }
        return 'Rondreis';
    }
    
    // Get destinations text
    function getDestinationsText(idea) {
        let destinationsList = [];
        
        // 1. PRIORITEIT: destinations array (TC import data) - heeft alle steden
        if (idea.destinations && Array.isArray(idea.destinations) && idea.destinations.length > 0) {
            idea.destinations.forEach(dest => {
                const name = dest.name || dest.cityName || dest.city || dest.title;
                if (name && !destinationsList.includes(name)) {
                    destinationsList.push(name);
                }
            });
            if (destinationsList.length > 0) {
                return destinationsList.join(' • ');
            }
        }
        
        // 2. From start/end destination
        if (idea.start_destination) {
            const city = idea.start_destination.cityName || idea.start_destination.city || idea.start_destination.name;
            if (city) destinationsList.push(city);
        }
        if (idea.end_destination) {
            const city = idea.end_destination.cityName || idea.end_destination.city || idea.end_destination.name;
            if (city && !destinationsList.includes(city)) destinationsList.push(city);
        }
        if (destinationsList.length > 0) {
            return destinationsList.join(' → ');
        }
        
        // 3. Fallback: locations taxonomy
        if (idea.locations && idea.locations.length > 0) {
            return idea.locations.join(' • ');
        }
        
        return '';
    }
    
    // Count items
    function countItems(idea, type) {
        if (!idea.tour_plan) return 0;
        
        let count = 0;
        idea.tour_plan.forEach(day => {
            if (day.items) {
                day.items.forEach(item => {
                    if (type === 'hotels' && (item.type === 'Hotel' || item.type === 'Accommodation')) count++;
                    if (type === 'excursions' && (item.type === 'Excursion' || item.type === 'Activity')) count++;
                    if (type === 'cruises' && item.type === 'Cruise') count++;
                });
            }
        });
        return count;
    }
    
    // Render cards
    function renderTravelCards(ideas) {
        const container = document.getElementById('rbs-travel-container');
        container.innerHTML = '';
        
        if (!ideas || ideas.length === 0) {
            document.getElementById('rbs-no-results').style.display = 'block';
            return;
        }
        
        document.getElementById('rbs-no-results').style.display = 'none';
        
        ideas.forEach(idea => {
            container.appendChild(createTravelCard(idea));
        });
    }
    
    // Create card
    function createTravelCard(idea) {
        const card = document.createElement('div');
        card.className = 'rbs-travel-card';
        
        const price = idea.price ? `€${Math.round(idea.price).toLocaleString('nl-NL')}` : 'Op aanvraag';
        const nights = idea.nights || 0;
        const days = nights + 1;
        const travelType = getTravelTypeLabel(idea);
        const destinations = getDestinationsText(idea);
        const isFavorite = favorites.includes(idea.id);
        
        // Count various items
        const flightCount = idea.transports ? idea.transports.filter(t => t.type === 'Flight' || t.type === 'Vlucht').length : 0;
        const hotelCount = countItems(idea, 'hotels');
        const excursionCount = countItems(idea, 'excursions');
        const cruiseCount = countItems(idea, 'cruises');
        
        // Get route map image (if available)
        const routeMapUrl = idea.route_map_url || idea.map_image || '';
        const mainImage = idea.image || '';
        
        // Build meta tags
        let metaTags = '';
        if (nights > 0) {
            metaTags += `<span class="rbs-meta-tag">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" fill="none"/><path d="M12 6v6l4 2" stroke="currentColor" fill="none"/></svg>
                ${nights} nachten
            </span>`;
        }
        if (idea.persons) {
            metaTags += `<span class="rbs-meta-tag">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" fill="none"/><circle cx="9" cy="7" r="4" stroke="currentColor" fill="none"/></svg>
                ${idea.persons} Personen
            </span>`;
        }
        // Destinations now shown prominently above, not in meta tags
        if (hotelCount > 0) {
            metaTags += `<span class="rbs-meta-tag">
                <svg viewBox="0 0 24 24"><path d="M3 21h18M3 7v14M21 7v14M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3M9 21v-4h6v4" stroke="currentColor" fill="none"/></svg>
                ${hotelCount} Hotel${hotelCount > 1 ? 's' : ''}
            </span>`;
        }
        if (excursionCount > 0) {
            metaTags += `<span class="rbs-meta-tag">
                <svg viewBox="0 0 24 24"><path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z" stroke="currentColor" fill="none"/><path d="M20.5 10H19V8.5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5-.67 1.5-1.5 1.5z" stroke="currentColor" fill="none"/></svg>
                ${excursionCount} Excursie${excursionCount > 1 ? 's' : ''}
            </span>`;
        }
        if (cruiseCount > 0) {
            metaTags += `<span class="rbs-meta-tag">
                <svg viewBox="0 0 24 24"><path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" stroke="currentColor" fill="none"/><path d="M19.38 20A11.6 11.6 0 0 0 21 14l-9-4-9 4c0 2.9.94 5.34 2.81 7.76" stroke="currentColor" fill="none"/></svg>
                ${cruiseCount} Cruise${cruiseCount > 1 ? 's' : ''}
            </span>`;
        }
        
        card.innerHTML = `
            <div class="rbs-card-image-wrapper">
                <div class="rbs-card-image">
                    ${mainImage ? `<img src="${mainImage}" alt="${idea.title}" loading="lazy">` : ''}
                </div>
                <button class="rbs-card-favorite ${isFavorite ? 'active' : ''}" data-id="${idea.id}" title="Toevoegen aan favorieten">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
            </div>
            <div class="rbs-card-content">
                <h3 class="rbs-card-title">
                    <a href="${idea.url}">${idea.title}</a>
                </h3>
                ${destinations ? `
                    <div class="rbs-card-destinations">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>${destinations}</span>
                        <button class="rbs-map-link" data-idea-id="${idea.id}" title="Bekijk routekaart">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
                            Routekaart
                        </button>
                    </div>
                ` : ''}
                ${idea.excerpt ? `<p class="rbs-card-excerpt">${idea.excerpt}</p>` : ''}
                <div class="rbs-card-meta">
                    ${metaTags}
                </div>
            </div>
            <div class="rbs-card-price-section">
                <div class="rbs-price-type">${travelType}</div>
                <div class="rbs-price-amount">${price}</div>
                <div class="rbs-price-note">/ totaalprijs indicatieprijs</div>
                <a href="${idea.url}" class="rbs-btn-info">Informatie</a>
            </div>
        `;
        
        // Favorite button click
        const favBtn = card.querySelector('.rbs-card-favorite');
        favBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleFavorite(idea.id, favBtn);
        });
        
        // Route map button click
        const mapBtn = card.querySelector('.rbs-map-link');
        if (mapBtn) {
            mapBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openRoutePanel(idea);
            });
        }
        
        return card;
    }
    
    // Render pagination
    function renderPagination(current, total) {
        const pagination = document.getElementById('rbs-pagination');
        pagination.innerHTML = '';
        
        if (total <= 1) return;
        
        const prevBtn = document.createElement('button');
        prevBtn.textContent = '← Vorige';
        prevBtn.disabled = current === 1;
        prevBtn.onclick = () => loadPage(current - 1);
        pagination.appendChild(prevBtn);
        
        for (let i = Math.max(1, current - 2); i <= Math.min(total, current + 2); i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.className = i === current ? 'active' : '';
            pageBtn.onclick = () => loadPage(i);
            pagination.appendChild(pageBtn);
        }
        
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Volgende →';
        nextBtn.disabled = current === total;
        nextBtn.onclick = () => loadPage(current + 1);
        pagination.appendChild(nextBtn);
    }
    
    // Load page
    async function loadPage(page) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        
        document.getElementById('rbs-loading').style.display = 'flex';
        document.getElementById('rbs-app').style.display = 'none';
        
        const data = await fetchTravelIdeas(page);
        
        if (data) {
            currentPage = page;
            totalPages = data.pages;
            
            document.getElementById('rbs-total-ideas').textContent = data.total;
            renderTravelCards(data.ideas);
            renderPagination(currentPage, totalPages);
        }
        
        document.getElementById('rbs-loading').style.display = 'none';
        document.getElementById('rbs-app').style.display = 'block';
    }
    
    // Apply filters
    function applyFilters() {
        currentFilters = {
            search: document.getElementById('rbs-search-input').value,
            location: document.getElementById('rbs-filter-location').value,
            tour_type: document.getElementById('rbs-filter-type').value
        };
        
        const sortValue = document.getElementById('rbs-sort-select').value;
        if (sortValue === 'price-asc') {
            currentFilters.orderby = 'price';
            currentFilters.order = 'ASC';
        } else if (sortValue === 'price-desc') {
            currentFilters.orderby = 'price';
            currentFilters.order = 'DESC';
        } else if (sortValue === 'title') {
            currentFilters.orderby = 'title';
        } else {
            currentFilters.orderby = 'date';
            currentFilters.order = 'DESC';
        }
        
        loadPage(1);
    }
    
    // === ROUTE PANEL FUNCTIONS ===
    
    // Route map instance
    let routeMapInstance = null;
    
    function openRoutePanel(idea) {
        const panel = document.getElementById('rbs-route-panel');
        const overlay = document.getElementById('rbs-panel-overlay');
        const mapContainer = document.getElementById('rbs-route-map-container');
        
        // Get destinations
        let destinations = [];
        if (idea.destinations && Array.isArray(idea.destinations) && idea.destinations.length > 0) {
            destinations = idea.destinations;
        }
        
        // Show panel first
        panel.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Initialize Leaflet map after panel is visible
        setTimeout(() => {
            initRouteMap(destinations, mapContainer);
        }, 100);
    }
    
    function initRouteMap(destinations, container) {
        // Clean up existing map
        if (routeMapInstance) {
            routeMapInstance.remove();
            routeMapInstance = null;
        }
        
        // Check if Leaflet is available
        if (!window.L) {
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f1f5f9;color:#64748b;font-size:14px;">Kaart niet beschikbaar</div>';
            return;
        }
        
        // Get destinations with coordinates
        const mapDests = destinations.filter(d => {
            const lat = d.latitude || (d.geolocation && d.geolocation.latitude);
            const lng = d.longitude || (d.geolocation && d.geolocation.longitude);
            return lat && lng;
        });
        
        if (mapDests.length === 0) {
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:white;font-size:14px;flex-direction:column;"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:8px;"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/></svg>Route: ' + destinations.length + ' bestemmingen</div>';
            return;
        }
        
        // Create map
        routeMapInstance = L.map(container, { zoomControl: true });
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(routeMapInstance);
        
        // Add markers and collect bounds
        const bounds = [];
        const routeCoords = [];
        
        mapDests.forEach((dest, index) => {
            const lat = dest.latitude || (dest.geolocation && dest.geolocation.latitude);
            const lng = dest.longitude || (dest.geolocation && dest.geolocation.longitude);
            
            if (lat && lng) {
                bounds.push([lat, lng]);
                routeCoords.push([lat, lng]);
                
                // Create numbered marker
                const icon = L.divIcon({
                    className: 'rbs-map-marker',
                    html: '<div style="background:var(--rbs-primary, #3182ce);color:white;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:11px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + (index + 1) + '</div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const marker = L.marker([lat, lng], { icon: icon }).addTo(routeMapInstance);
                const name = dest.name || dest.cityName || dest.city || 'Bestemming';
                marker.bindPopup('<strong>' + (index + 1) + '. ' + name + '</strong>');
            }
        });
        
        // Draw route line
        if (routeCoords.length > 1) {
            L.polyline(routeCoords, {
                color: 'var(--rbs-primary, #3182ce)',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(routeMapInstance);
        }
        
        // Fit bounds
        if (bounds.length > 0) {
            routeMapInstance.fitBounds(bounds, { padding: [30, 30] });
        }
        
        // Fix map size
        setTimeout(() => {
            routeMapInstance.invalidateSize();
        }, 200);
    }
    
    function closeRoutePanel() {
        const panel = document.getElementById('rbs-route-panel');
        const overlay = document.getElementById('rbs-panel-overlay');
        
        panel.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
        
        // Clean up map
        if (routeMapInstance) {
            routeMapInstance.remove();
            routeMapInstance = null;
        }
    }
    
    // Event listeners
    document.getElementById('rbs-apply-filters').addEventListener('click', applyFilters);
    document.getElementById('rbs-sort-select').addEventListener('change', applyFilters);
    document.getElementById('rbs-search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') applyFilters();
    });
    
    // Panel close events
    document.getElementById('rbs-panel-overlay').addEventListener('click', closeRoutePanel);
    document.getElementById('rbs-panel-close-btn').addEventListener('click', closeRoutePanel);
    
    // Initialize
    async function init() {
        await fetchFilterOptions();
        await loadPage(1);
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>

<?php get_footer(); ?>
