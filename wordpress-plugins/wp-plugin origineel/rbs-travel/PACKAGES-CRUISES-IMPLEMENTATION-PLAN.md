# 📦 Packages & Cruises - Complete Implementation Plan

## 🎯 OVERVIEW

**Goal:** Fully integrate Travel Compositor Packages & Cruises into WordPress plugin

**Systems:**
1. ✅ **Ideas** (rbsTravel) - Already implemented
2. 🆕 **Packages** - NEW! Complete bookable packages
3. 🚢 **Cruises** - NEW! Cruise-specific data

---

## 📊 PHASE 1: Database & Import (Week 1)

### **1.1 Extend Database Schema**

**Add Package Meta Fields:**
```php
// Add to rbstravel-import.class.php
$package_meta_keys = [
    'travel_package_id',              // External package ID
    'travel_package_external_ref',    // External reference
    'travel_package_type',            // Type of package
    'travel_package_counters',        // Component counters (JSON)
    'travel_package_origin',          // Departure location
    'travel_package_themes',          // Themes/tags (JSON)
    'travel_package_visible',         // Visibility flag
    'travel_package_calendar',        // Departure dates & prices (JSON)
    'travel_package_itinerary',       // Day-by-day itinerary (JSON)
    'travel_has_cruise',              // Boolean: contains cruise
    'travel_has_flights',             // Boolean: contains flights
    'travel_has_tours',               // Boolean: contains tours
    'travel_has_hotels',              // Boolean: contains hotels
];
```

**Add Cruise Meta Fields:**
```php
// Already partially done, extend:
$cruise_meta_keys = [
    'travel_cruises',                 // Cruise data array (existing)
    'travel_cruise_id',               // Primary cruise ID
    'travel_cruise_ship_id',          // Ship ID
    'travel_cruise_ship_name',        // Ship name
    'travel_cruise_line',             // Cruise line
    'travel_cruise_region',           // Region
    'travel_cruise_origin_port',      // Departure port
    'travel_cruise_nights',           // Duration
    'travel_cruise_departures',       // Departure dates with prices
    'travel_cruise_itinerary',        // Day-by-day route
    'travel_cruise_categories',       // Cabin categories
];
```

### **1.2 Create Package Import Class**

**New File:** `includes/rbstravel-package-import.class.php`

```php
<?php
/**
 * RBS Travel - Package Import Class
 * Import packages from Travel Compositor
 */

class RBS_Travel_Package_Import {
    
    private $api_url;
    private $api_token;
    private $microsite_id;
    
    public function __construct() {
        $this->api_url = get_option('rbstravel_api_url');
        $this->api_token = get_option('rbstravel_api_token');
        $this->microsite_id = get_option('rbstravel_microsite_id');
    }
    
    /**
     * Fetch all packages from API
     */
    public function fetch_packages($filters = []) {
        $endpoint = "{$this->api_url}/package/{$this->microsite_id}";
        
        $params = array_merge([
            'lang' => 'NL',
            'currency' => 'EUR',
            'onlyVisible' => true,
            'first' => 0,
            'limit' => 100
        ], $filters);
        
        $url = add_query_arg($params, $endpoint);
        
        $response = wp_remote_get($url, [
            'headers' => [
                'auth-token' => $this->api_token
            ]
        ]);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['package'] ?? [];
    }
    
    /**
     * Import single package
     */
    public function import_package($package_id) {
        // Fetch package info
        $endpoint = "{$this->api_url}/package/{$this->microsite_id}/info/{$package_id}";
        
        $response = wp_remote_get($endpoint, [
            'headers' => [
                'auth-token' => $this->api_token,
            ],
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }
        
        $package = json_decode(wp_remote_retrieve_body($response), true);
        
        // Create or update post
        $post_id = $this->create_or_update_post($package);
        
        // Fetch and store full details
        $this->import_package_details($post_id, $package_id);
        
        // Fetch and store calendar
        $this->import_package_calendar($post_id, $package_id);
        
        return ['success' => true, 'post_id' => $post_id];
    }
    
    /**
     * Create or update WordPress post
     */
    private function create_or_update_post($package) {
        // Check if exists
        $existing = get_posts([
            'post_type' => 'rbs-travel-idea',
            'meta_key' => 'travel_package_id',
            'meta_value' => $package['id'],
            'posts_per_page' => 1
        ]);
        
        $post_data = [
            'post_type' => 'rbs-travel-idea',
            'post_title' => $package['title'],
            'post_content' => $package['description'],
            'post_status' => $package['active'] ? 'publish' : 'draft',
            'post_excerpt' => $package['largeTitle'],
        ];
        
        if (!empty($existing)) {
            $post_data['ID'] = $existing[0]->ID;
            $post_id = wp_update_post($post_data);
        } else {
            $post_id = wp_insert_post($post_data);
        }
        
        // Update meta
        update_post_meta($post_id, 'travel_package_id', $package['id']);
        update_post_meta($post_id, 'travel_price', $package['pricePerPerson']['amount']);
        update_post_meta($post_id, 'travel_package_counters', json_encode($package['counters']));
        update_post_meta($post_id, 'travel_package_themes', json_encode($package['themes']));
        update_post_meta($post_id, 'travel_package_visible', $package['visible'] ?? true);
        update_post_meta($post_id, 'travel_has_cruise', $package['counters']['cruises'] > 0);
        update_post_meta($post_id, 'travel_has_flights', $package['counters']['transports'] > 0);
        update_post_meta($post_id, 'travel_has_tours', $package['counters']['closedTours'] > 0);
        update_post_meta($post_id, 'travel_has_hotels', $package['counters']['hotels'] > 0);
        
        // Set featured image
        if (!empty($package['imageUrl'])) {
            $this->set_featured_image($post_id, $package['imageUrl']);
        }
        
        // Set taxonomies
        $this->set_taxonomies($post_id, $package);
        
        return $post_id;
    }
    
    /**
     * Import full package details
     */
    private function import_package_details($post_id, $package_id) {
        $endpoint = "{$this->api_url}/package/{$this->microsite_id}/{$package_id}";
        
        $response = wp_remote_get($endpoint, [
            'headers' => ['auth-token' => $this->api_token],
            'timeout' => 45
        ]);
        
        if (!is_wp_error($response)) {
            $details = json_decode(wp_remote_retrieve_body($response), true);
            
            // Store detailed data
            update_post_meta($post_id, 'travel_package_details', json_encode($details));
            
            // Extract cruises
            if (!empty($details['cruises'])) {
                update_post_meta($post_id, 'travel_cruises', json_encode($details['cruises']));
                
                // Import cruise-specific data
                foreach ($details['cruises'] as $cruise) {
                    $this->import_cruise_data($post_id, $cruise);
                }
            }
        }
    }
    
    /**
     * Import package calendar
     */
    private function import_package_calendar($post_id, $package_id) {
        $endpoint = "{$this->api_url}/package/calendar/{$this->microsite_id}/{$package_id}";
        
        $response = wp_remote_get($endpoint, [
            'headers' => ['auth-token' => $this->api_token]
        ]);
        
        if (!is_wp_error($response)) {
            $calendar = json_decode(wp_remote_retrieve_body($response), true);
            update_post_meta($post_id, 'travel_package_calendar', json_encode($calendar));
        }
    }
    
    /**
     * Import cruise-specific data
     */
    private function import_cruise_data($post_id, $cruise) {
        // Store cruise details
        update_post_meta($post_id, 'travel_cruise_id', $cruise['id']);
        update_post_meta($post_id, 'travel_cruise_ship_id', $cruise['shipId']);
        update_post_meta($post_id, 'travel_cruise_ship_name', $cruise['shipName']);
        update_post_meta($post_id, 'travel_cruise_line', $cruise['cruiseLine']);
        update_post_meta($post_id, 'travel_cruise_region', $cruise['region']);
        update_post_meta($post_id, 'travel_cruise_nights', $cruise['nights']);
        
        // Fetch cruise itinerary
        $this->import_cruise_itinerary($post_id, $cruise['id']);
        
        // Fetch ship details
        $this->import_ship_details($post_id, $cruise['shipId']);
    }
    
    /**
     * Import cruise itinerary
     */
    private function import_cruise_itinerary($post_id, $cruise_id) {
        $endpoint = "{$this->api_url}/cruise/{$cruise_id}/itinerary";
        
        $response = wp_remote_get($endpoint, [
            'headers' => ['auth-token' => $this->api_token]
        ]);
        
        if (!is_wp_error($response)) {
            $itinerary = json_decode(wp_remote_retrieve_body($response), true);
            update_post_meta($post_id, 'travel_cruise_itinerary', json_encode($itinerary));
        }
    }
    
    /**
     * Import ship details
     */
    private function import_ship_details($post_id, $ship_id) {
        $endpoint = "{$this->api_url}/cruise/{$this->microsite_id}/ship/{$ship_id}";
        
        $response = wp_remote_get($endpoint, [
            'headers' => ['auth-token' => $this->api_token]
        ]);
        
        if (!is_wp_error($response)) {
            $ship = json_decode(wp_remote_retrieve_body($response), true);
            update_post_meta($post_id, 'travel_cruise_ship_details', json_encode($ship));
        }
    }
    
    /**
     * Set taxonomies based on package data
     */
    private function set_taxonomies($post_id, $package) {
        // Set tour-type
        $types = ['Package'];
        if ($package['counters']['cruises'] > 0) {
            $types[] = 'Cruise';
        }
        wp_set_object_terms($post_id, $types, 'tour-type');
        
        // Set themes
        if (!empty($package['themes'])) {
            $theme_names = array_column($package['themes'], 'name');
            wp_set_object_terms($post_id, $theme_names, 'tour-theme');
        }
        
        // Set destinations
        if (!empty($package['destinations'])) {
            $dest_names = array_column($package['destinations'], 'name');
            wp_set_object_terms($post_id, $dest_names, 'location');
        }
    }
    
    /**
     * Set featured image from URL
     */
    private function set_featured_image($post_id, $image_url) {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        
        $attachment_id = media_sideload_image($image_url, $post_id, '', 'id');
        
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }
    }
}
```

---

## 📡 PHASE 2: REST API Endpoints (Week 1-2)

### **2.1 Extend REST API**

**File:** `includes/api-rest-endpoints.php`

```php
// Add to existing RBS_TRAVEL_REST_API class

/**
 * Register package endpoints
 */
public function register_package_routes() {
    // List/search packages
    register_rest_route('rbs-travel/v1', '/packages', [
        'methods' => 'GET',
        'callback' => [$this, 'get_packages'],
        'permission_callback' => '__return_true'
    ]);
    
    // Get single package
    register_rest_route('rbs-travel/v1', '/packages/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => [$this, 'get_package'],
        'permission_callback' => '__return_true'
    ]);
    
    // Get package calendar
    register_rest_route('rbs-travel/v1', '/packages/(?P<id>\d+)/calendar', [
        'methods' => 'GET',
        'callback' => [$this, 'get_package_calendar'],
        'permission_callback' => '__return_true'
    ]);
}

/**
 * Get packages with filters
 */
public function get_packages($request) {
    $params = $request->get_params();
    
    $args = [
        'post_type' => 'rbs-travel-idea',
        'posts_per_page' => $params['limit'] ?? 20,
        'offset' => $params['offset'] ?? 0,
        'post_status' => 'publish'
    ];
    
    // Meta query for filters
    $meta_query = ['relation' => 'AND'];
    
    if (isset($params['has_cruise']) && $params['has_cruise']) {
        $meta_query[] = [
            'key' => 'travel_has_cruise',
            'value' => '1',
            'compare' => '='
        ];
    }
    
    if (isset($params['has_flights']) && $params['has_flights']) {
        $meta_query[] = [
            'key' => 'travel_has_flights',
            'value' => '1',
            'compare' => '='
        ];
    }
    
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    
    // Tax query for destination
    if (!empty($params['destination'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'location',
            'field' => 'slug',
            'terms' => sanitize_title($params['destination'])
        ]];
    }
    
    $query = new WP_Query($args);
    
    $packages = array_map(function($post) {
        return $this->format_package($post);
    }, $query->posts);
    
    return new WP_REST_Response([
        'packages' => $packages,
        'total' => $query->found_posts,
        'pages' => $query->max_num_pages
    ]);
}

/**
 * Format package for API response
 */
private function format_package($post) {
    $counters = json_decode(get_post_meta($post->ID, 'travel_package_counters', true), true);
    $themes = json_decode(get_post_meta($post->ID, 'travel_package_themes', true), true);
    
    return [
        'id' => $post->ID,
        'package_id' => get_post_meta($post->ID, 'travel_package_id', true),
        'title' => $post->post_title,
        'description' => $post->post_content,
        'excerpt' => $post->post_excerpt,
        'image' => get_the_post_thumbnail_url($post->ID, 'large'),
        'price' => floatval(get_post_meta($post->ID, 'travel_price', true)),
        'link' => get_permalink($post->ID),
        'counters' => $counters,
        'themes' => $themes,
        'has_cruise' => (bool) get_post_meta($post->ID, 'travel_has_cruise', true),
        'has_flights' => (bool) get_post_meta($post->ID, 'travel_has_flights', true),
        'has_tours' => (bool) get_post_meta($post->ID, 'travel_has_tours', true),
        'has_hotels' => (bool) get_post_meta($post->ID, 'travel_has_hotels', true),
        'cruises' => json_decode(get_post_meta($post->ID, 'travel_cruises', true), true),
        'cruise_itinerary' => json_decode(get_post_meta($post->ID, 'travel_cruise_itinerary', true), true)
    ];
}
```

---

## 🎨 PHASE 3: Frontend Templates (Week 2-3)

### **3.1 Package Search Template**

**New File:** `templates/page-package-search.php`

```php
<?php
/*
Template Name: Package Search
*/

get_header();
?>

<div class="package-search-page">
    <!-- Search Filters -->
    <div class="search-filters">
        <h1>Find Your Perfect Package</h1>
        
        <div class="filter-grid">
            <select id="filter-destination">
                <option value="">All Destinations</option>
                <!-- Loaded dynamically -->
            </select>
            
            <select id="filter-month">
                <option value="">Any Month</option>
                <option value="JUNE">June</option>
                <option value="JULY">July</option>
                <!-- ... -->
            </select>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" name="has_cruise" value="1">
                    Includes Cruise
                </label>
                <label>
                    <input type="checkbox" name="has_flights" value="1">
                    Flights Included
                </label>
            </div>
            
            <button id="search-btn">Search</button>
        </div>
    </div>
    
    <!-- Results -->
    <div id="package-results" class="package-grid">
        <!-- Loaded via AJAX -->
    </div>
    
    <!-- Pagination -->
    <div id="pagination"></div>
</div>

<script>
jQuery(document).ready(function($) {
    function searchPackages() {
        const filters = {
            destination: $('#filter-destination').val(),
            month: $('#filter-month').val(),
            has_cruise: $('[name="has_cruise"]').is(':checked') ? 1 : 0,
            has_flights: $('[name="has_flights"]').is(':checked') ? 1 : 0,
            limit: 20,
            offset: 0
        };
        
        $.get('/wp-json/rbs-travel/v1/packages', filters, function(data) {
            renderPackages(data.packages);
        });
    }
    
    function renderPackages(packages) {
        const html = packages.map(pkg => `
            <div class="package-card">
                <img src="${pkg.image}" alt="${pkg.title}">
                <h3>${pkg.title}</h3>
                <p>${pkg.description}</p>
                
                <div class="badges">
                    ${pkg.has_cruise ? '<span class="badge">Cruise</span>' : ''}
                    ${pkg.has_flights ? '<span class="badge">Flights</span>' : ''}
                </div>
                
                <div class="price">
                    <strong>€${pkg.price}</strong>
                    <span>per person</span>
                </div>
                
                <a href="${pkg.link}" class="btn">View Details</a>
            </div>
        `).join('');
        
        $('#package-results').html(html);
    }
    
    $('#search-btn').on('click', searchPackages);
    searchPackages(); // Initial load
});
</script>

<?php get_footer(); ?>
```

### **3.2 Package Detail Template**

**File:** `templates/single-rbs-travel-idea.php` (extend existing)

```php
<?php
$package_id = get_post_meta(get_the_ID(), 'travel_package_id', true);
$has_cruise = get_post_meta(get_the_ID(), 'travel_has_cruise', true);
$counters = json_decode(get_post_meta(get_the_ID(), 'travel_package_counters', true), true);
$calendar = json_decode(get_post_meta(get_the_ID(), 'travel_package_calendar', true), true);
?>

<div class="package-detail">
    <!-- Hero Section -->
    <div class="package-hero">
        <?php the_post_thumbnail('full'); ?>
        <div class="hero-content">
            <h1><?php the_title(); ?></h1>
            <div class="price-tag">
                From €<?php echo get_post_meta(get_the_ID(), 'travel_price', true); ?>
            </div>
        </div>
    </div>
    
    <!-- What's Included -->
    <div class="includes-section">
        <h2>What's Included</h2>
        <div class="includes-grid">
            <?php if ($counters['cruises'] > 0): ?>
                <div class="include-item">
                    <span class="icon">🚢</span>
                    <strong><?php echo $counters['cruises']; ?> Cruise<?php echo $counters['cruises'] > 1 ? 's' : ''; ?></strong>
                </div>
            <?php endif; ?>
            
            <?php if ($counters['transports'] > 0): ?>
                <div class="include-item">
                    <span class="icon">✈️</span>
                    <strong><?php echo $counters['transports']; ?> Flight<?php echo $counters['transports'] > 1 ? 's' : ''; ?></strong>
                </div>
            <?php endif; ?>
            
            <?php if ($counters['hotelNights'] > 0): ?>
                <div class="include-item">
                    <span class="icon">🏨</span>
                    <strong><?php echo $counters['hotelNights']; ?> Hotel Nights</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Description -->
    <div class="package-description">
        <?php the_content(); ?>
    </div>
    
    <?php if ($has_cruise): ?>
        <!-- Cruise Itinerary -->
        <?php include 'partials/cruise-itinerary.php'; ?>
    <?php endif; ?>
    
    <!-- Calendar -->
    <div class="departure-calendar">
        <h2>Select Your Departure Date</h2>
        <div id="calendar-widget"></div>
    </div>
    
    <!-- Book Button -->
    <div class="booking-section">
        <button class="book-now-btn">Book Now</button>
    </div>
</div>
```

---

## 🧪 PHASE 4: Testing (Week 3)

### **4.1 Unit Tests**

```php
// Test package import
class Test_Package_Import extends WP_UnitTestCase {
    
    public function test_fetch_packages() {
        $importer = new RBS_Travel_Package_Import();
        $packages = $importer->fetch_packages();
        
        $this->assertIsArray($packages);
        $this->assertNotEmpty($packages);
    }
    
    public function test_import_package() {
        $importer = new RBS_Travel_Package_Import();
        $result = $importer->import_package(12345);
        
        $this->assertTrue($result['success']);
        $this->assertGreaterThan(0, $result['post_id']);
    }
}
```

### **4.2 Integration Tests**

**Test Checklist:**
- [ ] Import packages from Travel Compositor
- [ ] Verify all meta fields are saved
- [ ] Check REST API returns correct data
- [ ] Test search filters work
- [ ] Verify cruise data import
- [ ] Test calendar widget
- [ ] Check template rendering
- [ ] Verify images are imported
- [ ] Test taxonomies assignment
- [ ] Check permalink structure

---

## 📋 PHASE 5: Admin Interface (Week 4)

### **5.1 Add Admin Menu**

```php
add_action('admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=rbs-travel-idea',
        'Import Packages',
        'Import Packages',
        'manage_options',
        'rbstravel-import-packages',
        'rbstravel_import_packages_page'
    );
});

function rbstravel_import_packages_page() {
    ?>
    <div class="wrap">
        <h1>Import Packages from Travel Compositor</h1>
        
        <div class="import-options">
            <button id="import-all-packages" class="button button-primary">
                Import All Packages
            </button>
            
            <button id="import-cruise-packages" class="button">
                Import Only Cruise Packages
            </button>
        </div>
        
        <div id="import-progress"></div>
        <div id="import-results"></div>
    </div>
    
    <script>
    jQuery('#import-all-packages').on('click', function() {
        jQuery.post(ajaxurl, {
            action: 'rbstravel_import_packages'
        }, function(response) {
            jQuery('#import-results').html(response.message);
        });
    });
    </script>
    <?php
}
```

---

## ✅ IMPLEMENTATION CHECKLIST

### **Backend (WordPress Plugin)**
- [ ] Create package import class
- [ ] Add database meta fields
- [ ] Implement REST API endpoints
- [ ] Add cruise data import
- [ ] Create ship details cache
- [ ] Add port information storage
- [ ] Implement calendar endpoint
- [ ] Add admin import interface

### **Frontend (Templates)**
- [ ] Package search page
- [ ] Package detail template
- [ ] Cruise itinerary component
- [ ] Calendar widget
- [ ] Ship information display
- [ ] Port information pages
- [ ] Map integration (Leaflet.js)
- [ ] Price comparison charts

### **Testing**
- [ ] Unit tests for import
- [ ] API endpoint tests
- [ ] Template rendering tests
- [ ] Search filter tests
- [ ] Performance tests
- [ ] Cross-browser testing

### **Documentation**
- [ ] Update CHANGELOG
- [ ] Create user guide
- [ ] API documentation
- [ ] Developer documentation

---

## 🚀 QUICK START GUIDE

### **Step 1: Configure API**
1. Go to WordPress Admin → RBS Travel → Settings
2. Enter Travel Compositor API credentials
3. Test connection

### **Step 2: Import Packages**
1. Go to RBS Travel → Import Packages
2. Click "Import All Packages"
3. Wait for import to complete

### **Step 3: Create Pages**
1. Create new page
2. Select "Package Search" template
3. Publish page

### **Step 4: Test**
1. Visit package search page
2. Try filters
3. View package detail
4. Check cruise data

---

## 📊 TIMELINE

| Week | Task | Status |
|------|------|--------|
| 1 | Database schema & import class | ⏳ |
| 2 | REST API endpoints | ⏳ |
| 3 | Frontend templates | ⏳ |
| 4 | Admin interface | ⏳ |
| 5 | Testing & bug fixes | ⏳ |
| 6 | Documentation & launch | ⏳ |

---

## 💡 FUTURE ENHANCEMENTS

1. **Booking Integration**
   - Direct booking from WordPress
   - Payment gateway integration
   - Booking confirmation emails

2. **Advanced Features**
   - Package comparison tool
   - Favorite packages
   - Price alerts
   - Email notifications

3. **Marketing**
   - Featured packages
   - Best deals section
   - Seasonal promotions
   - Social sharing

---

## 🎉 READY TO START!

**All API documentation complete:**
- ✅ 8 Cruise endpoints documented
- ✅ 4 Package endpoints documented  
- ✅ Data structures mapped
- ✅ Implementation plan created
- ✅ Code examples provided

**Next steps:**
1. Review this plan
2. Set up development environment
3. Start with Phase 1 (Database & Import)
4. Test each phase before moving forward

**Questions?** Ask anytime! 😊
