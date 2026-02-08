<?php
/**
 * Template: Travel Detail Page - EXACT RBS COPY with TravelC Data
 * Version: 3.0.0
 */

if (!defined('ABSPATH')) exit;

// TravelC Data (passed from shortcode)
$primary_color = '#4a6cf7';
$secondary_color = '#6366f1';

// Extract data from $travel array
$title = $travel['title'] ?? '';
$description = $travel['description'] ?? '';
$nights = intval($travel['number_of_nights'] ?? 0);
$days = intval($travel['number_of_days'] ?? ($nights + 1));
$price = floatval($travel['price_per_person'] ?? 0);

// Components
$destinations = $travel['destinations'] ?? [];
$hotels = $travel['hotels'] ?? [];
$transports = $travel['transports'] ?? [];

// Collect all images for slideshow
$all_images = [];
foreach ($destinations as $dest) {
    if (!empty($dest['images'])) {
        $all_images = array_merge($all_images, $dest['images']);
    }
}
foreach ($hotels as $hotel) {
    if (!empty($hotel['images'])) {
        $all_images = array_merge($all_images, $hotel['images']);
    }
}
$all_images = array_slice(array_unique(array_filter($all_images)), 0, 8);

// Get main image
$main_image = !empty($all_images[0]) ? $all_images[0] : '';

// Get location
$location = '';
if (!empty($destinations)) {
    $dest_names = array_map(function($d) { return $d['name'] ?? ''; }, $destinations);
    $location = implode(', ', array_slice(array_filter($dest_names), 0, 3));
}

// Build map destinations
$map_destinations = [];
foreach ($destinations as $dest) {
    $lat = $dest['geolocation']['latitude'] ?? 0;
    $lng = $dest['geolocation']['longitude'] ?? 0;
    if ($lat != 0 && $lng != 0) {
        $map_destinations[] = [
            'name' => $dest['name'] ?? '',
            'lat' => floatval($lat),
            'lng' => floatval($lng),
            'image' => $dest['images'][0] ?? ''
        ];
    }
}

// Hero style - always slideshow for TravelC
$hero_style = count($all_images) >= 2 ? 'slideshow' : 'single';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        /* EXACT RBS CSS */
        :root {
            --primary: <?php echo esc_attr($primary_color); ?>;
            --secondary: <?php echo esc_attr($secondary_color); ?>;
            --text: #374151;
            --text-light: #6b7280;
            --bg: #f9fafb;
            --white: #ffffff;
            --border: #e5e7eb;
            --radius: 12px;
        }
        
        * { box-sizing: border-box; }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        #content,
        .site-content,
        .content-area,
        main,
        article {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        .travel-hero-single {
            width: 100%;
            height: 450px;
            background: url('<?php echo esc_url($main_image); ?>') center/cover;
            position: relative;
        }
        
        .travel-hero-slideshow {
            width: 100%;
            height: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .travel-hero-slideshow .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .travel-hero-slideshow .slide.active {
            opacity: 1;
        }
        
        .travel-hero-slideshow .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .travel-hero-slideshow .slide-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 10;
        }
        
        .travel-hero-slideshow .slide-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            border: none;
            padding: 0;
            transition: all 0.3s ease;
        }
        
        .travel-hero-slideshow .slide-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        .travel-title-bar {
            background: white;
            padding: 25px 0;
            border-bottom: 1px solid var(--border);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .travel-title-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .travel-title-left h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: var(--text);
        }
        
        .travel-title-location {
            color: var(--text-light);
            font-size: 14px;
        }
        
        .travel-title-right {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        
        .travel-stat {
            text-align: center;
        }
        
        .travel-stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .travel-stat-label {
            font-size: 13px;
            color: var(--text-light);
        }
        
        .travel-stat.days .travel-stat-value {
            color: var(--text);
        }
        
        .travel-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .travel-intro h2 {
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin: 0 0 15px 0;
        }
        
        .travel-intro-text {
            color: var(--text);
            line-height: 1.7;
        }
        
        @media (max-width: 768px) {
            .travel-hero-slideshow { height: 300px; }
        }
    </style>
</head>
<body>

<!-- HERO -->
<?php if ($hero_style === 'slideshow' && count($all_images) >= 2): ?>
    <div class="travel-hero-slideshow" id="heroSlideshow">
        <?php foreach ($all_images as $index => $img): ?>
            <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
        <?php endforeach; ?>
        <div class="slide-nav">
            <?php foreach ($all_images as $index => $img): ?>
                <button type="button" class="slide-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
<?php else: ?>
    <div class="travel-hero-single"></div>
<?php endif; ?>

<!-- TITLE BAR -->
<div class="travel-title-bar">
    <div class="travel-title-container">
        <div class="travel-title-left">
            <h1><?php echo esc_html($title); ?></h1>
            <?php if ($location): ?>
                <div class="travel-title-location">📍 <?php echo esc_html($location); ?></div>
            <?php endif; ?>
        </div>
        <div class="travel-title-right">
            <?php if ($price > 0): ?>
            <div class="travel-stat">
                <div class="travel-stat-value">€<?php echo number_format($price, 0, ',', '.'); ?></div>
                <div class="travel-stat-label">per persoon</div>
            </div>
            <?php endif; ?>
            <div class="travel-stat days">
                <div class="travel-stat-value"><?php echo $days; ?></div>
                <div class="travel-stat-label">dagen</div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="travel-container">
    <section class="travel-intro">
        <h2>Ontdek deze Reis</h2>
        <div class="travel-intro-text">
            <?php echo wp_kses_post($description); ?>
        </div>
    </section>
</div>

<script>
(function() {
    // Slideshow
    var slideshow = document.getElementById('heroSlideshow');
    if (slideshow) {
        var slides = slideshow.querySelectorAll('.slide');
        var dots = slideshow.querySelectorAll('.slide-dot');
        var current = 0;
        
        function showSlide(index) {
            slides.forEach(function(s, i) { s.classList.toggle('active', i === index); });
            dots.forEach(function(d, i) { d.classList.toggle('active', i === index); });
            current = index;
        }
        
        setInterval(function() { showSlide((current + 1) % slides.length); }, 4000);
        
        dots.forEach(function(dot) {
            dot.addEventListener('click', function() {
                showSlide(parseInt(this.dataset.index));
            });
        });
    }
})();
</script>

</body>
</html>
