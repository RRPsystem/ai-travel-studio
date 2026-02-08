<?php
/**
 * Template Name: Travel Listing (Met Filters & Elementor)
 * Description: Travel listing with search filters and Elementor support
 */

// Get WordPress REST API URLs
$api_url = rest_url('rbs-travel/v1/ideas');
$filters_url = rest_url('rbs-travel/v1/filters');
$detail_base_url = home_url('/reizen-detail/');

// Enqueue Leaflet
wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);

get_header(); 
?>

<style>
    /* Travel Listing Styles - Scoped */
    .rbs-travel-listing {
        background: #f5f7fa;
        padding: 3rem 0;
    }
    
    .rbs-travel-listing * {
        box-sizing: border-box;
    }
    
    /* Container */
    .rbs-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    /* Search & Filter Bar */
    .rbs-filter-bar {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 3rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .rbs-filter-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .rbs-search-box {
        margin-bottom: 1.5rem;
    }
    
    .rbs-search-input {
        width: 100%;
        padding: 1rem 1.5rem;
        font-size: 1.1rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .rbs-search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .rbs-filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .rbs-filter-group label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    
    .rbs-filter-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }
    
    .rbs-filter-select:focus {
        border-color: #667eea;
    }
    
    .rbs-price-range-group {
        grid-column: span 2;
    }
    
    @media (max-width: 768px) {
        .rbs-price-range-group {
            grid-column: span 1;
        }
    }
    
    .rbs-price-inputs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .rbs-price-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 1rem;
        outline: none;
    }
    
    .rbs-price-input:focus {
        border-color: #667eea;
    }
    
    .rbs-filter-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .rbs-btn {
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .rbs-btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .rbs-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .rbs-btn-secondary {
        background: white;
        color: #4a5568;
        border: 2px solid #e2e8f0;
    }
    
    .rbs-btn-secondary:hover {
        background: #f7fafc;
    }
    
    /* Sort Options */
    .rbs-sort-bar {
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
    
    .rbs-stats-info {
        font-size: 1.1rem;
        color: #4a5568;
    }
    
    .rbs-stats-info strong {
        color: #667eea;
        font-weight: 700;
    }
    
    .rbs-sort-group {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .rbs-sort-label {
        font-weight: 600;
        color: #4a5568;
    }
    
    .rbs-sort-select {
        padding: 0.5rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        outline: none;
        cursor: pointer;
        background: white;
    }
    
    /* Loading State */
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
    
    /* Grid */
    .rbs-travel-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }
    
    /* Travel Card */
    .rbs-travel-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }
    
    .rbs-travel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .rbs-card-image {
        position: relative;
        height: 250px;
        overflow: hidden;
        background: #e2e8f0;
    }
    
    .rbs-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .rbs-travel-card:hover .rbs-card-image img {
        transform: scale(1.05);
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
        transition: all 0.2s ease;
    }
    
    .rbs-card-favorite:hover {
        transform: scale(1.1);
        background: #ff6b6b;
        color: white;
    }
    
    .rbs-card-price-tag {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        background: rgba(102, 126, 234, 0.95);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
    }
    
    .rbs-card-content {
        padding: 1.5rem;
    }
    
    .rbs-card-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
        line-height: 1.3;
    }
    
    .rbs-card-destination {
        color: #667eea;
        font-size: 0.95rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .rbs-card-excerpt {
        color: #4a5568;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .rbs-card-info {
        display: flex;
        gap: 1.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .rbs-info-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #718096;
        font-size: 0.9rem;
    }
    
    .rbs-info-icon {
        color: #667eea;
    }
    
    .rbs-card-button {
        display: inline-block;
        margin-top: 1rem;
        padding: 0.75rem 1.5rem;
        background: #667eea;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        text-align: center;
    }
    
    .rbs-card-button:hover {
        background: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    /* No Results */
    .rbs-no-results {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    
    .rbs-no-results-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }
    
    .rbs-no-results-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4a5568;
        margin-bottom: 0.5rem;
    }
    
    .rbs-no-results-text {
        color: #718096;
    }
    
    /* Pagination */
    .rbs-pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 3rem;
        padding-bottom: 3rem;
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
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    .rbs-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .rbs-pagination button.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .rbs-travel-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .rbs-filter-actions {
            flex-direction: column;
        }
        
        .rbs-btn {
            width: 100%;
        }
    }
</style>

<!-- Elementor Content Area (Voor custom hero/banner) -->
<?php the_content(); ?>

<!-- Loading State -->
<div id="rbs-loading" class="rbs-loading">
    <div class="rbs-loading-spinner"></div>
</div>

<!-- Main Container -->
<div id="rbs-app" class="rbs-travel-listing" style="display: none;">
    <div class="rbs-container">
        
        <!-- Search & Filter Bar -->
        <div class="rbs-filter-bar">
            <h2 class="rbs-filter-title">
                <span>🔍</span> Zoek & Boek Jouw Droomreis
            </h2>
            
            <!-- Search Box -->
            <div class="rbs-search-box">
                <input 
                    type="text" 
                    id="rbs-search-input" 
                    class="rbs-search-input" 
                    placeholder="Zoek op bestemming, reistitel..."
                >
            </div>
            
            <!-- Filters Grid -->
            <div class="rbs-filters-grid">
                <!-- Location Filter -->
                <div class="rbs-filter-group">
                    <label for="rbs-filter-location">📍 Bestemming</label>
                    <select id="rbs-filter-location" class="rbs-filter-select">
                        <option value="">Alle bestemmingen</option>
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div class="rbs-filter-group">
                    <label for="rbs-filter-type">🎨 Type Reis</label>
                    <select id="rbs-filter-type" class="rbs-filter-select">
                        <option value="">Alle types</option>
                    </select>
                </div>
                
                <!-- Theme Filter -->
                <div class="rbs-filter-group">
                    <label for="rbs-filter-theme">🌟 Thema</label>
                    <select id="rbs-filter-theme" class="rbs-filter-select">
                        <option value="">Alle thema's</option>
                    </select>
                </div>
                
                <!-- Service Filter -->
                <div class="rbs-filter-group">
                    <label for="rbs-filter-service">🛎️ Service</label>
                    <select id="rbs-filter-service" class="rbs-filter-select">
                        <option value="">Alle services</option>
                    </select>
                </div>
                
                <!-- Price Range -->
                <div class="rbs-filter-group rbs-price-range-group">
                    <label>💰 Prijsrange</label>
                    <div class="rbs-price-inputs">
                        <input 
                            type="number" 
                            id="rbs-price-min" 
                            class="rbs-price-input" 
                            placeholder="Min €"
                        >
                        <input 
                            type="number" 
                            id="rbs-price-max" 
                            class="rbs-price-input" 
                            placeholder="Max €"
                        >
                    </div>
                </div>
            </div>
            
            <!-- Filter Actions -->
            <div class="rbs-filter-actions">
                <button id="rbs-apply-filters" class="rbs-btn rbs-btn-primary">
                    🔍 Zoek Reizen
                </button>
                <button id="rbs-reset-filters" class="rbs-btn rbs-btn-secondary">
                    ↺ Reset Filters
                </button>
            </div>
        </div>
        
        <!-- Sort & Stats Bar -->
        <div class="rbs-sort-bar">
            <div class="rbs-stats-info">
                <strong id="rbs-total-ideas">0</strong> reizen gevonden
            </div>
            <div class="rbs-sort-group">
                <span class="rbs-sort-label">Sorteer op:</span>
                <select id="rbs-sort-select" class="rbs-sort-select">
                    <option value="date">Nieuwste</option>
                    <option value="price-asc">Prijs: Laag - Hoog</option>
                    <option value="price-desc">Prijs: Hoog - Laag</option>
                    <option value="title">Naam (A-Z)</option>
                </select>
            </div>
        </div>
        
        <!-- Travel Grid -->
        <div id="rbs-travel-grid" class="rbs-travel-grid"></div>
        
        <!-- No Results -->
        <div id="rbs-no-results" class="rbs-no-results" style="display: none;">
            <div class="rbs-no-results-icon">🔍</div>
            <h2 class="rbs-no-results-title">Geen reizen gevonden</h2>
            <p class="rbs-no-results-text">Probeer andere filters of zoektermen.</p>
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
    
    // Fetch filter options
    async function fetchFilterOptions() {
        try {
            const response = await fetch(FILTERS_URL);
            if (!response.ok) return;
            
            const data = await response.json();
            filterOptions = data;
            
            // Populate filter dropdowns
            populateFilters();
        } catch (error) {
            console.error('Failed to fetch filters:', error);
        }
    }
    
    // Populate filter dropdowns
    function populateFilters() {
        // Locations
        const locationSelect = document.getElementById('rbs-filter-location');
        if (filterOptions.locations) {
            filterOptions.locations.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                locationSelect.appendChild(option);
            });
        }
        
        // Tour Types
        const typeSelect = document.getElementById('rbs-filter-type');
        if (filterOptions.tour_types) {
            filterOptions.tour_types.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                typeSelect.appendChild(option);
            });
        }
        
        // Themes
        const themeSelect = document.getElementById('rbs-filter-theme');
        if (filterOptions.tour_themes) {
            filterOptions.tour_themes.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                themeSelect.appendChild(option);
            });
        }
        
        // Services
        const serviceSelect = document.getElementById('rbs-filter-service');
        if (filterOptions.tour_services) {
            filterOptions.tour_services.forEach(item => {
                const option = document.createElement('option');
                option.value = item.slug;
                option.textContent = `${item.name} (${item.count})`;
                serviceSelect.appendChild(option);
            });
        }
        
        // Set price placeholders
        if (filterOptions.price_range) {
            document.getElementById('rbs-price-min').placeholder = `Min € ${filterOptions.price_range.min}`;
            document.getElementById('rbs-price-max').placeholder = `Max € ${filterOptions.price_range.max}`;
        }
    }
    
    // Build API URL with filters
    function buildApiUrl(page = 1) {
        let url = `${API_URL}?per_page=12&page=${page}`;
        
        if (currentFilters.search) {
            url += `&search=${encodeURIComponent(currentFilters.search)}`;
        }
        
        if (currentFilters.location) {
            url += `&location=${encodeURIComponent(currentFilters.location)}`;
        }
        
        if (currentFilters.tour_type) {
            url += `&tour_type=${encodeURIComponent(currentFilters.tour_type)}`;
        }
        
        if (currentFilters.tour_theme) {
            url += `&tour_theme=${encodeURIComponent(currentFilters.tour_theme)}`;
        }
        
        if (currentFilters.tour_service) {
            url += `&tour_service=${encodeURIComponent(currentFilters.tour_service)}`;
        }
        
        if (currentFilters.min_price) {
            url += `&min_price=${currentFilters.min_price}`;
        }
        
        if (currentFilters.max_price) {
            url += `&max_price=${currentFilters.max_price}`;
        }
        
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
            console.log('🔍 Fetching from:', url);
            
            const response = await fetch(url);
            console.log('📡 Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`API error: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('✅ API Data:', data);
            return data;
        } catch (error) {
            console.error('❌ Failed to fetch travel ideas:', error);
            return null;
        }
    }
    
    // Render travel cards
    function renderTravelCards(ideas) {
        const grid = document.getElementById('rbs-travel-grid');
        grid.innerHTML = '';
        
        if (!ideas || ideas.length === 0) {
            document.getElementById('rbs-no-results').style.display = 'block';
            return;
        }
        
        document.getElementById('rbs-no-results').style.display = 'none';
        
        ideas.forEach(idea => {
            const card = createTravelCard(idea);
            grid.appendChild(card);
        });
    }
    
    // Create travel card element
    function createTravelCard(idea) {
        const card = document.createElement('div');
        card.className = 'rbs-travel-card';
        
        const price = idea.price ? `€ ${Math.round(idea.price)}` : 'Prijs op aanvraag';
        const nights = idea.nights ? `${idea.nights} nachten` : '';
        
        let destinationText = '';
        // Try locations taxonomy first (most reliable)
        if (idea.locations && idea.locations.length > 0) {
            destinationText = idea.locations.slice(0, 3).join(' • ');
        }
        // Fallback to start/end destinations
        else if (idea.start_destination || idea.end_destination) {
            const startCity = idea.start_destination ? (idea.start_destination.cityName || idea.start_destination.city || '') : '';
            const endCity = idea.end_destination ? (idea.end_destination.cityName || idea.end_destination.city || '') : '';
            if (startCity && endCity && startCity !== endCity) {
                destinationText = `${startCity} → ${endCity}`;
            } else if (startCity) {
                destinationText = startCity;
            } else if (endCity) {
                destinationText = endCity;
            }
        }
        
        // Get excerpt
        const excerpt = idea.excerpt ? idea.excerpt.substring(0, 120) + (idea.excerpt.length > 120 ? '...' : '') : '';
        
        card.innerHTML = `
            <div class="rbs-card-image">
                ${idea.image ? `<img src="${idea.image}" alt="${idea.title}">` : ''}
                <div class="rbs-card-favorite" onclick="event.stopPropagation();">
                    ❤️
                </div>
                <div class="rbs-card-price-tag">${price}</div>
            </div>
            <div class="rbs-card-content">
                <h3 class="rbs-card-title">${idea.title}</h3>
                ${destinationText ? `
                    <div class="rbs-card-destination">
                        <span class="rbs-info-icon">📍</span>
                        ${destinationText}
                    </div>
                ` : ''}
                ${excerpt ? `<p class="rbs-card-excerpt">${excerpt}</p>` : ''}
                <div class="rbs-card-info">
                    ${nights ? `
                        <div class="rbs-info-item">
                            <span class="rbs-info-icon">🌙</span>
                            ${nights}
                        </div>
                    ` : ''}
                    ${idea.transports && idea.transports.length > 0 ? `
                        <div class="rbs-info-item">
                            <span class="rbs-info-icon">✈️</span>
                            ${idea.transports.length} vlucht${idea.transports.length > 1 ? 'en' : ''}
                        </div>
                    ` : ''}
                </div>
                <a href="${idea.url}" class="rbs-card-button">Bekijk reis →</a>
            </div>
        `;
        
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
        
        const startPage = Math.max(1, current - 2);
        const endPage = Math.min(total, current + 2);
        
        for (let i = startPage; i <= endPage; i++) {
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
    
    // Load specific page
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
            tour_service: document.getElementById('rbs-filter-service').value,
            min_price: document.getElementById('rbs-price-min').value,
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
            currentFilters.order = 'ASC';
        } else {
            currentFilters.orderby = 'date';
            currentFilters.order = 'DESC';
        }
        
        loadPage(1);
    }
    
    // Reset filters
    function resetFilters() {
        document.getElementById('rbs-search-input').value = '';
        document.getElementById('rbs-filter-location').value = '';
        document.getElementById('rbs-filter-type').value = '';
        document.getElementById('rbs-filter-theme').value = '';
        document.getElementById('rbs-filter-service').value = '';
        document.getElementById('rbs-price-min').value = '';
        document.getElementById('rbs-price-max').value = '';
        document.getElementById('rbs-sort-select').value = 'date';
        
        currentFilters = {};
        loadPage(1);
    }
    
    // Event listeners
    document.getElementById('rbs-apply-filters').addEventListener('click', applyFilters);
    document.getElementById('rbs-reset-filters').addEventListener('click', resetFilters);
    document.getElementById('rbs-sort-select').addEventListener('change', applyFilters);
    
    // Search on Enter key
    document.getElementById('rbs-search-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });
    
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
