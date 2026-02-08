<?php
/**
 * Template Name: Travel Listing (Met Theme Header/Footer)
 * Description: Travel listing with theme header and footer integration
 */

// Get WordPress REST API URL
$api_url = rest_url('rbs-travel/v1/ideas');
$detail_base_url = home_url('/reizen-detail/');

// Enqueue Leaflet
wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);

get_header(); 
?>

<style>
    /* Travel Listing Styles - Scoped to avoid theme conflicts */
    .rbs-travel-listing {
        background: #f5f7fa;
        padding: 3rem 0;
    }
    
    .rbs-travel-listing * {
        box-sizing: border-box;
    }
    
    /* Loading State */
    .rbs-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
        font-size: 24px;
        color: #667eea;
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
    
    /* Header Banner */
    .rbs-page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 3rem;
    }
    
    .rbs-page-title {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .rbs-page-subtitle {
        font-size: 1.25rem;
        opacity: 0.95;
        max-width: 600px;
        margin: 0 auto;
    }
    
    /* Container */
    .rbs-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
    }
    
    /* Stats Bar */
    .rbs-stats-bar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem 2rem;
        margin-bottom: 3rem;
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
    
    /* Map Overlay */
    .rbs-map-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        display: none;
        z-index: 20;
    }
    
    .rbs-travel-card:hover .rbs-map-overlay {
        display: block;
    }
    
    .rbs-map-container {
        width: 100%;
        height: 100%;
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
        .rbs-page-title {
            font-size: 2rem;
        }
        
        .rbs-travel-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        .rbs-stats-bar {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<!-- Elementor Content Area -->
<?php the_content(); ?>

<!-- Page Header -->
<div class="rbs-page-header">
    <h1 class="rbs-page-title">✈️ Ontdek Jouw Droomreis</h1>
    <p class="rbs-page-subtitle">Gepersonaliseerde reizen samengesteld door experts</p>
</div>

<!-- Loading State -->
<div id="rbs-loading" class="rbs-loading">
    <div class="rbs-loading-spinner"></div>
</div>

<!-- Main Container -->
<div id="rbs-app" class="rbs-travel-listing" style="display: none;">
    <div class="rbs-container">
        
        <!-- Stats Bar -->
        <div class="rbs-stats-bar">
            <div class="rbs-stats-info">
                <strong id="rbs-total-ideas">0</strong> reizen gevonden
            </div>
            <div class="rbs-stats-info" style="opacity: 0.7;">
                📍 Wereldwijd • 🌟 Persoonlijk • 💎 Uniek
            </div>
        </div>
        
        <!-- Travel Grid -->
        <div id="rbs-travel-grid" class="rbs-travel-grid"></div>
        
        <!-- No Results -->
        <div id="rbs-no-results" class="rbs-no-results" style="display: none;">
            <div class="rbs-no-results-icon">🔍</div>
            <h2 class="rbs-no-results-title">Geen reizen gevonden</h2>
            <p class="rbs-no-results-text">Er zijn momenteel geen reizen beschikbaar.</p>
        </div>
        
        <!-- Pagination -->
        <div id="rbs-pagination" class="rbs-pagination"></div>
        
    </div>
</div>

<script>
(function() {
    const API_URL = '<?php echo esc_js($api_url); ?>';
    const DETAIL_BASE_URL = '<?php echo esc_js($detail_base_url); ?>';
    
    let currentPage = 1;
    let totalPages = 1;
    let mapInstances = new Map();
    
    // Fetch travel ideas
    async function fetchTravelIdeas(page = 1) {
        try {
            console.log('🔍 Fetching from:', `${API_URL}?per_page=12&page=${page}`);
            const response = await fetch(`${API_URL}?per_page=12&page=${page}`);
            console.log('📡 Response status:', response.status, response.statusText);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ API Error:', errorText);
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
        card.setAttribute('data-id', idea.id);
        
        // Format price
        const price = idea.price ? `€ ${Math.round(idea.price)}` : 'Prijs op aanvraag';
        
        // Format nights
        const nights = idea.nights ? `${idea.nights} nachten` : '';
        
        // Get destination names
        let destinationText = '';
        if (idea.start_destination && idea.end_destination) {
            destinationText = `${idea.start_destination.cityName || idea.start_destination.city} → ${idea.end_destination.cityName || idea.end_destination.city}`;
        } else if (idea.destinations && idea.destinations.length > 0) {
            destinationText = idea.destinations.map(d => d.cityName || d.city).join(' • ');
        }
        
        card.innerHTML = `
            <div class="rbs-card-image">
                ${idea.image ? `<img src="${idea.image}" alt="${idea.title}">` : ''}
                <div class="rbs-card-favorite" onclick="event.stopPropagation();">
                    ❤️
                </div>
                <div class="rbs-card-price-tag">${price}</div>
                <div class="rbs-map-overlay">
                    <div class="rbs-map-container" id="rbs-map-${idea.id}"></div>
                </div>
            </div>
            <div class="rbs-card-content">
                <h3 class="rbs-card-title">${idea.title}</h3>
                ${destinationText ? `
                    <div class="rbs-card-destination">
                        <span class="rbs-info-icon">📍</span>
                        ${destinationText}
                    </div>
                ` : ''}
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
            </div>
        `;
        
        // Add click handler
        card.addEventListener('click', () => {
            window.location.href = idea.url || `${DETAIL_BASE_URL}${idea.slug}`;
        });
        
        // Initialize map on hover
        card.addEventListener('mouseenter', () => {
            if (!mapInstances.has(idea.id) && window.L) {
                initializeMap(idea);
            }
        });
        
        return card;
    }
    
    // Initialize Leaflet map
    function initializeMap(idea) {
        const mapElement = document.getElementById(`rbs-map-${idea.id}`);
        if (!mapElement || mapInstances.has(idea.id) || !window.L) return;
        
        try {
            const map = L.map(mapElement, {
                zoomControl: false,
                attributionControl: false
            });
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            
            if (idea.destinations && idea.destinations.length > 0) {
                const bounds = [];
                
                idea.destinations.forEach((dest, index) => {
                    if (dest.lat && dest.lng) {
                        const marker = L.marker([dest.lat, dest.lng]).addTo(map);
                        marker.bindPopup(dest.cityName || dest.city);
                        bounds.push([dest.lat, dest.lng]);
                    }
                });
                
                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [20, 20] });
                }
            }
            
            mapInstances.set(idea.id, map);
        } catch (error) {
            console.error('Map initialization failed:', error);
        }
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
    
    // Initialize
    async function init() {
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
