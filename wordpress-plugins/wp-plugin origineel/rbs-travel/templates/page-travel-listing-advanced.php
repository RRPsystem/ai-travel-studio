<?php
/**
 * Template Name: Travel Listing (Standaard)
 * Description: Standaard travel listing met filters en layout opties
 */

// Get WordPress REST API URLs
$api_url = rest_url('rbs-travel/v1/ideas');
$filters_url = rest_url('rbs-travel/v1/filters');
$detail_base_url = home_url('/reizen-detail/');

// Get theme colors
$theme_colors = RBS_Travel_Theme_Colors::get_theme_colors();

// Enqueue Leaflet
wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);

get_header(); 

// Output theme color CSS variables
RBS_Travel_Theme_Colors::output_theme_color_css();
?>

<style>
    /* Travel Listing Styles */
    .rbs-travel-listing {
        background: #f5f7fa;
        padding: 3rem 0;
    }
    
    /* Sliding Panel for Route Map */
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
    }
    
    .rbs-route-panel.active {
        right: 0;
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
    
    .rbs-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    /* Filter Bar - Compact */
    .rbs-filter-bar {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .rbs-filter-title {
        font-size: 1.3rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
    }
    
    .rbs-filters-row {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
        gap: 1rem;
        align-items: end;
    }
    
    @media (max-width: 1024px) {
        .rbs-filters-row {
            grid-template-columns: 1fr 1fr;
        }
    }
    
    @media (max-width: 640px) {
        .rbs-filters-row {
            grid-template-columns: 1fr;
        }
    }
    
    .rbs-filter-group label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }
    
    .rbs-search-input,
    .rbs-filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .rbs-search-input:focus,
    .rbs-filter-select:focus {
        border-color: var(--rbs-primary, #667eea);
    }
    
    .rbs-btn-search {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--rbs-primary, #667eea) 0%, var(--rbs-secondary, #764ba2) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .rbs-btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        filter: brightness(1.1);
    }
    
    /* Toolbar */
    .rbs-toolbar {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .rbs-stats {
        font-size: 1.1rem;
        color: #4a5568;
    }
    
    .rbs-stats strong {
        color: var(--rbs-primary, #667eea);
        font-weight: 700;
    }
    
    .rbs-toolbar-right {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    
    .rbs-view-switcher {
        display: flex;
        gap: 0.5rem;
        background: #f7fafc;
        padding: 0.25rem;
        border-radius: 8px;
    }
    
    .rbs-view-btn {
        padding: 0.5rem 1rem;
        background: transparent;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1.2rem;
        transition: all 0.2s ease;
        color: #718096;
    }
    
    .rbs-view-btn:hover {
        color: var(--rbs-primary, #667eea);
    }
    
    .rbs-view-btn.active {
        background: white;
        color: var(--rbs-primary, #667eea);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .rbs-sort-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        outline: none;
        cursor: pointer;
    }
    
    /* GRID VIEW - Vertical Cards */
    .rbs-travel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    .rbs-travel-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
    }
    
    .rbs-travel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .rbs-card-image-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
        background: #e2e8f0;
    }
    
    .rbs-card-image,
    .rbs-card-map {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transition: opacity 0.4s ease;
    }
    
    .rbs-card-image img,
    .rbs-card-map img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Map overlay only shows if map exists */
    .rbs-card-map {
        opacity: 0;
        background: #e8f4f8;
        display: none; /* Hidden by default, shown via JS when map exists */
    }
    
    .rbs-card-image-wrapper.has-map .rbs-card-map {
        display: block;
    }
    
    .rbs-card-map img {
        object-fit: contain;
        padding: 15px;
    }
    
    .rbs-card-image-wrapper.has-map:hover .rbs-card-image {
        opacity: 0;
    }
    
    .rbs-card-image-wrapper.has-map:hover .rbs-card-map {
        opacity: 1;
    }
    
    .rbs-map-hint {
        position: absolute;
        bottom: 8px;
        left: 8px;
        background: rgba(0,0,0,0.6);
        color: white;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        opacity: 0.8;
        transition: opacity 0.3s;
    }
    
    .rbs-card-image-wrapper.has-map:hover .rbs-map-hint {
        opacity: 0;
    }
    
    .rbs-card-favorite {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        z-index: 10;
        border: none;
        transition: all 0.2s ease;
    }
    
    .rbs-card-favorite:hover {
        transform: scale(1.1);
    }
    
    .rbs-card-favorite.active {
        background: #e53e3e;
    }
    
    .rbs-card-favorite svg {
        width: 20px;
        height: 20px;
        fill: none !important;
        stroke: #e53e3e !important;
        stroke-width: 2 !important;
    }
    
    .rbs-card-favorite.active svg {
        fill: white;
        stroke: white;
    }
    
    .rbs-card-price-tag {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: var(--rbs-primary, rgba(102, 126, 234, 0.95));
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
        opacity: 0.95;
    }
    
    .rbs-card-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .rbs-card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    
    .rbs-card-destination {
        color: var(--rbs-primary, #667eea);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }
    
    .rbs-card-excerpt {
        color: #718096;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        flex: 1;
    }
    
    .rbs-card-info {
        display: flex;
        gap: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
        align-items: center;
    }
    
    .rbs-info-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: #718096;
        font-size: 0.85rem;
    }
    
    .rbs-info-item svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
    }
    
    .rbs-map-btn {
        margin-left: auto;
        width: 32px;
        height: 32px;
        border: none;
        background: #f0f4f8;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--rbs-primary, #667eea);
        transition: all 0.2s ease;
    }
    
    .rbs-map-btn svg {
        width: 18px;
        height: 18px;
        stroke: var(--rbs-primary, #667eea);
        fill: none;
        stroke-width: 2;
    }
    
    .rbs-map-btn:hover {
        background: var(--rbs-primary, #667eea);
        color: white;
        transform: scale(1.1);
    }
    
    .rbs-map-btn:hover svg {
        stroke: white;
    }
    
    /* View Button */
    .rbs-btn-view {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.6rem 1.2rem;
        background: var(--rbs-primary, #667eea);
        color: white;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .rbs-btn-view:hover {
        background: var(--rbs-secondary, #764ba2);
        transform: translateY(-1px);
    }
    
    /* LIST VIEW - Horizontal Cards */
    .rbs-travel-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .rbs-travel-list .rbs-travel-card {
        flex-direction: row;
        border-radius: 16px;
    }
    
    .rbs-travel-list .rbs-card-image-wrapper {
        width: 300px;
        height: auto;
        min-height: 200px;
        flex-shrink: 0;
    }
    
    .rbs-travel-list .rbs-card-content {
        flex: 1;
        padding: 2rem;
    }
    
    .rbs-travel-list .rbs-card-title {
        font-size: 1.75rem;
    }
    
    .rbs-travel-list .rbs-card-excerpt {
        font-size: 1rem;
    }
    
    .rbs-travel-list .rbs-card-info {
        flex-wrap: wrap;
    }
    
    @media (max-width: 768px) {
        .rbs-travel-list .rbs-travel-card {
            flex-direction: column;
        }
        
        .rbs-travel-list .rbs-card-image-wrapper {
            width: 100%;
            height: 200px;
        }
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
        border-top-color: #667eea;
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
        margin: 3rem 0;
    }
    
    .rbs-pagination button {
        padding: 0.75rem 1.25rem;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 600;
        color: #4a5568;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .rbs-pagination button:hover:not(:disabled) {
        background: var(--rbs-primary, #667eea);
        color: white;
        border-color: var(--rbs-primary, #667eea);
    }
    
    .rbs-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .rbs-pagination button.active {
        background: var(--rbs-primary, #667eea);
        color: white;
        border-color: var(--rbs-primary, #667eea);
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
<div id="rbs-app" class="rbs-travel-listing" style="display: none;">
    <div class="rbs-container">
        
        <!-- Filter Bar -->
        <div class="rbs-filter-bar">
            <h2 class="rbs-filter-title">🔍 Zoek & Boek Jouw Droomreis</h2>
            
            <div class="rbs-filters-row">
                <!-- Search -->
                <div class="rbs-filter-group">
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
                    <label>Type</label>
                    <select id="rbs-filter-type" class="rbs-filter-select">
                        <option value="">Alle</option>
                    </select>
                </div>
                
                <!-- Theme Filter -->
                <div class="rbs-filter-group">
                    <label>Thema</label>
                    <select id="rbs-filter-theme" class="rbs-filter-select">
                        <option value="">Alle</option>
                    </select>
                </div>
                
                <!-- Price -->
                <div class="rbs-filter-group">
                    <label>Max Prijs</label>
                    <input 
                        type="number" 
                        id="rbs-price-max" 
                        class="rbs-search-input" 
                        placeholder="€"
                    >
                </div>
                
                <!-- Search Button -->
                <div class="rbs-filter-group">
                    <button id="rbs-apply-filters" class="rbs-btn-search">
                        Zoek
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Toolbar -->
        <div class="rbs-toolbar">
            <div class="rbs-stats">
                <strong id="rbs-total-ideas">0</strong> reizen gevonden
            </div>
            <div class="rbs-toolbar-right">
                <!-- View Switcher -->
                <div class="rbs-view-switcher">
                    <button class="rbs-view-btn active" data-view="grid" title="Grid weergave">
                        ⊞
                    </button>
                    <button class="rbs-view-btn" data-view="list" title="Lijst weergave">
                        ☰
                    </button>
                </div>
                
                <!-- Sort -->
                <select id="rbs-sort-select" class="rbs-sort-select">
                    <option value="date">Nieuwste</option>
                    <option value="price-asc">Prijs ↑</option>
                    <option value="price-desc">Prijs ↓</option>
                    <option value="title">Naam A-Z</option>
                </select>
            </div>
        </div>
        
        <!-- Travel Grid/List -->
        <div id="rbs-travel-container" class="rbs-travel-grid"></div>
        
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
    const DETAIL_BASE_URL = '<?php echo esc_js($detail_base_url); ?>';
    
    let currentPage = 1;
    let totalPages = 1;
    let filterOptions = {};
    let currentFilters = {};
    let currentView = 'grid'; // 'grid' or 'list'
    let favorites = JSON.parse(localStorage.getItem('rbs_favorites') || '[]');
    
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
    
    // Count items in tour plan
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
        
        if (filterOptions.tour_themes) {
            const themeSelect = document.getElementById('rbs-filter-theme');
            filterOptions.tour_themes.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                themeSelect.appendChild(option);
            });
        }
    }
    
    // Build API URL
    function buildApiUrl(page = 1) {
        let url = `${API_URL}?per_page=12&page=${page}`;
        
        if (currentFilters.search) url += `&search=${encodeURIComponent(currentFilters.search)}`;
        if (currentFilters.location) url += `&location=${encodeURIComponent(currentFilters.location)}`;
        if (currentFilters.tour_type) url += `&tour_type=${encodeURIComponent(currentFilters.tour_type)}`;
        if (currentFilters.tour_theme) url += `&tour_theme=${encodeURIComponent(currentFilters.tour_theme)}`;
        if (currentFilters.max_price) url += `&max_price=${currentFilters.max_price}`;
        
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
    
    // Render cards
    function renderTravelCards(ideas) {
        const container = document.getElementById('rbs-travel-container');
        container.innerHTML = '';
        container.className = currentView === 'grid' ? 'rbs-travel-grid' : 'rbs-travel-list';
        
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
        
        const price = idea.price ? `€ ${Math.round(idea.price).toLocaleString('nl-NL')}` : 'Op aanvraag';
        const nights = idea.nights || 0;
        const isFavorite = favorites.includes(idea.id);
        
        // Get destination text
        let destinationText = '';
        if (idea.locations && idea.locations.length > 0) {
            destinationText = idea.locations.slice(0, 3).join(' • ');
        } else if (idea.start_destination && idea.end_destination) {
            const startCity = idea.start_destination.cityName || idea.start_destination.city || '';
            const endCity = idea.end_destination.cityName || idea.end_destination.city || '';
            if (startCity && endCity) {
                destinationText = startCity === endCity ? startCity : `${startCity} → ${endCity}`;
            }
        }
        
        const excerpt = idea.excerpt || '';
        const routeMapUrl = idea.route_map_url || idea.map_image || '';
        
        // Count items
        const flightCount = idea.transports ? idea.transports.filter(t => t.type === 'Flight' || t.type === 'Vlucht').length : 0;
        const hotelCount = countItems(idea, 'hotels');
        const excursionCount = countItems(idea, 'excursions');
        const cruiseCount = countItems(idea, 'cruises');
        
        // Build info items
        let infoItems = '';
        if (nights > 0) {
            infoItems += `<div class="rbs-info-item"><svg viewBox="0 0 24 24"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linecap="round" stroke-linejoin="round"/></svg>${nights} nachten</div>`;
        }
        if (flightCount > 0) {
            infoItems += `<div class="rbs-info-item"><svg viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke-linecap="round" stroke-linejoin="round"/></svg>${flightCount} vlucht${flightCount > 1 ? 'en' : ''}</div>`;
        }
        if (hotelCount > 0) {
            infoItems += `<div class="rbs-info-item"><svg viewBox="0 0 24 24"><path d="M3 21h18M3 7v14M21 7v14M6 7V4a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3" stroke-linecap="round" stroke-linejoin="round"/></svg>${hotelCount} hotel${hotelCount > 1 ? 's' : ''}</div>`;
        }
        if (excursionCount > 0) {
            infoItems += `<div class="rbs-info-item"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01" stroke-linecap="round" stroke-linejoin="round"/></svg>${excursionCount} excursie${excursionCount > 1 ? 's' : ''}</div>`;
        }
        if (cruiseCount > 0) {
            infoItems += `<div class="rbs-info-item"><svg viewBox="0 0 24 24"><path d="M2 21c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1 .6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1" stroke-linecap="round" stroke-linejoin="round"/></svg>${cruiseCount} cruise${cruiseCount > 1 ? 's' : ''}</div>`;
        }
        
        card.innerHTML = `
            <div class="rbs-card-image-wrapper">
                <div class="rbs-card-image">
                    ${idea.image ? `<img src="${idea.image}" alt="${idea.title}" loading="lazy">` : ''}
                </div>
                <button class="rbs-card-favorite ${isFavorite ? 'active' : ''}" data-id="${idea.id}" title="Favoriet">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </button>
                <div class="rbs-card-price-tag">${price}</div>
            </div>
            <div class="rbs-card-content">
                <h3 class="rbs-card-title">${idea.title}</h3>
                ${excerpt ? `<div class="rbs-card-excerpt">${excerpt}</div>` : ''}
                <div class="rbs-card-info">
                    ${infoItems}
                    <button class="rbs-map-btn" data-idea-id="${idea.id}" title="Bekijk routekaart">
                        <svg viewBox="0 0 24 24" width="16" height="16"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6" fill="none" stroke="currentColor" stroke-width="2"/><line x1="8" y1="2" x2="8" y2="18" stroke="currentColor" stroke-width="2"/><line x1="16" y1="6" x2="16" y2="22" stroke="currentColor" stroke-width="2"/></svg>
                    </button>
                </div>
                <a href="${idea.url}" class="rbs-btn-view" onclick="event.stopPropagation();">Bekijk de reis</a>
            </div>
        `;
        
        // Favorite button click
        const favBtn = card.querySelector('.rbs-card-favorite');
        favBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            toggleFavorite(idea.id, favBtn);
        });
        
        // Map button click
        const mapBtn = card.querySelector('.rbs-map-btn');
        if (mapBtn) {
            mapBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                openRoutePanel(idea);
            });
        }
        
        card.addEventListener('click', () => {
            window.location.href = idea.url || `${DETAIL_BASE_URL}${idea.slug}`;
        });
        
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
            tour_type: document.getElementById('rbs-filter-type').value,
            tour_theme: document.getElementById('rbs-filter-theme').value,
            max_price: document.getElementById('rbs-price-max').value
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
        
        // Initialize Leaflet map
        setTimeout(() => {
            initRouteMap(destinations, mapContainer);
        }, 100);
    }
    
    function initRouteMap(destinations, container) {
        if (routeMapInstance) {
            routeMapInstance.remove();
            routeMapInstance = null;
        }
        
        if (!window.L) {
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:#f1f5f9;color:#64748b;">Kaart niet beschikbaar</div>';
            return;
        }
        
        const mapDests = destinations.filter(d => {
            const lat = d.latitude || (d.geolocation && d.geolocation.latitude);
            const lng = d.longitude || (d.geolocation && d.geolocation.longitude);
            return lat && lng;
        });
        
        if (mapDests.length === 0) {
            container.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:white;flex-direction:column;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom:12px;"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/></svg>Route: ' + destinations.length + ' bestemmingen</div>';
            return;
        }
        
        routeMapInstance = L.map(container, { zoomControl: true });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap'
        }).addTo(routeMapInstance);
        
        const bounds = [];
        const routeCoords = [];
        
        mapDests.forEach((dest, index) => {
            const lat = dest.latitude || (dest.geolocation && dest.geolocation.latitude);
            const lng = dest.longitude || (dest.geolocation && dest.geolocation.longitude);
            
            if (lat && lng) {
                bounds.push([lat, lng]);
                routeCoords.push([lat, lng]);
                
                const icon = L.divIcon({
                    className: 'rbs-map-marker',
                    html: '<div style="background:var(--rbs-primary, #667eea);color:white;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:11px;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">' + (index + 1) + '</div>',
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });
                
                const marker = L.marker([lat, lng], { icon: icon }).addTo(routeMapInstance);
                const name = dest.name || dest.cityName || dest.city || 'Bestemming';
                marker.bindPopup('<strong>' + (index + 1) + '. ' + name + '</strong>');
            }
        });
        
        if (routeCoords.length > 1) {
            L.polyline(routeCoords, {
                color: 'var(--rbs-primary, #667eea)',
                weight: 3,
                opacity: 0.7,
                dashArray: '10, 10'
            }).addTo(routeMapInstance);
        }
        
        if (bounds.length > 0) {
            routeMapInstance.fitBounds(bounds, { padding: [30, 30] });
        }
        
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
        
        if (routeMapInstance) {
            routeMapInstance.remove();
            routeMapInstance = null;
        }
    }
    
    // View switcher
    document.querySelectorAll('.rbs-view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.rbs-view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentView = btn.dataset.view;
            
            // Re-render current results with new view
            const container = document.getElementById('rbs-travel-container');
            container.className = currentView === 'grid' ? 'rbs-travel-grid' : 'rbs-travel-list';
        });
    });
    
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
