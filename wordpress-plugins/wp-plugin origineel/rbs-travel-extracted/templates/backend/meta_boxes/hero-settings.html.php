<?php
/**
 * Hero Settings Metabox Template
 * Allows selection of hero style and main hero image
 */

if (!defined('ABSPATH')) exit;

// Get current values
$hero_style = get_post_meta($post->ID, 'travel_hero_style', true);
$hero_image = get_post_meta($post->ID, 'travel_hero_image', true);
$hero_youtube = get_post_meta($post->ID, 'travel_hero_youtube', true);
$hero_youtube_delay = get_post_meta($post->ID, 'travel_hero_youtube_delay', true);
if ($hero_youtube_delay === '') $hero_youtube_delay = '0'; // Default: start immediately

// Default to 'slideshow' if not set (best option)
if (empty($hero_style)) {
    $hero_style = 'slideshow';
}

// Get all available images from travel data
$travel_meta = get_post_meta($post->ID, 'rbstravel_travel_meta_fields', true);
$available_images = array();

// Collect images from travel_images
if (!empty($travel_meta['travel_images']) && is_array($travel_meta['travel_images'])) {
    foreach ($travel_meta['travel_images'] as $img) {
        if (!empty($img)) {
            $available_images[] = $img;
        }
    }
}

// Collect images from destinations
if (!empty($travel_meta['travel_destinations']) && is_array($travel_meta['travel_destinations'])) {
    foreach ($travel_meta['travel_destinations'] as $dest) {
        if (!empty($dest['imageUrls']) && is_array($dest['imageUrls'])) {
            foreach ($dest['imageUrls'] as $img) {
                if (!empty($img) && !in_array($img, $available_images)) {
                    $available_images[] = $img;
                }
            }
        }
    }
}

// Collect images from hotels
if (!empty($travel_meta['travel_hotels']) && is_array($travel_meta['travel_hotels'])) {
    foreach ($travel_meta['travel_hotels'] as $hotel) {
        if (!empty($hotel['imageUrls']) && is_array($hotel['imageUrls'])) {
            foreach ($hotel['imageUrls'] as $img) {
                if (!empty($img) && !in_array($img, $available_images)) {
                    $available_images[] = $img;
                }
            }
        }
        if (!empty($hotel['images']) && is_array($hotel['images'])) {
            foreach ($hotel['images'] as $img) {
                if (!empty($img) && !in_array($img, $available_images)) {
                    $available_images[] = $img;
                }
            }
        }
    }
}

// Get first 4 images for preview
$preview_images = array_slice($available_images, 0, 4);
// Limit to first 20 images for selection
$available_images = array_slice($available_images, 0, 20);

wp_nonce_field('hero_settings_nonce', 'hero_settings_nonce_field');
?>

<style>
.hero-settings-container {
    padding: 15px 0;
}

.hero-settings-section {
    margin-bottom: 30px;
}

.hero-settings-section h4 {
    margin: 0 0 15px 0;
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
}

/* Style Options with Real Previews */
.hero-style-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.hero-style-option {
    border: 3px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
}

.hero-style-option:hover {
    border-color: #2271b1;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.hero-style-option.selected {
    border-color: #2271b1;
    box-shadow: 0 4px 12px rgba(34, 113, 177, 0.3);
}

.hero-style-option input[type="radio"] {
    display: none;
}

/* Preview with real images */
.hero-style-preview {
    height: 120px;
    overflow: hidden;
    position: relative;
    background: #f0f0f0;
}

.hero-style-preview.single img,
.hero-style-preview.wide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-style-preview.wide {
    height: 80px;
}

.hero-style-preview.grid-preview {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2px;
    height: 100px;
}

.hero-style-preview.grid-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-style-preview.slideshow-preview {
    position: relative;
    height: 100px;
}

.hero-style-preview.slideshow-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-style-preview.slideshow-preview .play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.hero-style-info {
    padding: 12px 15px;
    background: #f9f9f9;
    border-top: 1px solid #eee;
}

.hero-style-name {
    font-size: 14px;
    font-weight: 600;
    color: #1d2327;
    margin-bottom: 3px;
}

.hero-style-desc {
    font-size: 12px;
    color: #666;
}

.hero-style-badge {
    display: inline-block;
    background: #2271b1;
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 8px;
    vertical-align: middle;
}

/* Image Selection */
.hero-image-selection {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
    margin-top: 12px;
}

.hero-image-option {
    position: relative;
    aspect-ratio: 16/10;
    border: 3px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
}

.hero-image-option:hover {
    border-color: #2271b1;
    transform: scale(1.03);
}

.hero-image-option.selected {
    border-color: #2271b1;
    box-shadow: 0 0 0 3px rgba(34, 113, 177, 0.3);
}

.hero-image-option img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.hero-image-option input[type="radio"] {
    display: none;
}

.hero-image-option .check-mark {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 24px;
    height: 24px;
    background: #2271b1;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: bold;
}

.hero-image-option.selected .check-mark {
    display: flex;
}

.no-images-message {
    padding: 30px;
    background: #f6f7f7;
    border-radius: 8px;
    text-align: center;
    color: #666;
}

.image-count-info {
    background: #e7f3ff;
    padding: 10px 15px;
    border-radius: 6px;
    margin-bottom: 12px;
    font-size: 13px;
    color: #1d2327;
}

.image-count-info strong {
    color: #2271b1;
}

/* YouTube Style Preview */
.hero-style-preview.youtube-preview {
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
}

.hero-style-preview.youtube-preview .youtube-icon {
    width: 60px;
    height: 42px;
    background: #ff0000;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

/* YouTube URL Input */
.youtube-url-section {
    margin-top: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.youtube-url-section label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #1d2327;
}

.youtube-url-section input[type="text"] {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.youtube-url-section input[type="text"]:focus {
    border-color: #2271b1;
    outline: none;
    box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.2);
}

.youtube-url-section .description {
    margin-top: 8px;
    font-size: 12px;
    color: #666;
}

.youtube-preview-container {
    margin-top: 15px;
    border-radius: 8px;
    overflow: hidden;
}

.youtube-preview-container iframe {
    width: 100%;
    height: 200px;
    border: none;
}
</style>

<div class="hero-settings-container">
    
    <!-- Hero Style Selection -->
    <div class="hero-settings-section">
        <h4>🎨 Kies een Hero Stijl</h4>
        <div class="hero-style-options">
            
            <!-- Slideshow Option (Recommended) -->
            <label class="hero-style-option <?php echo $hero_style === 'slideshow' ? 'selected' : ''; ?>">
                <input type="radio" name="travel_hero_style" value="slideshow" <?php checked($hero_style, 'slideshow'); ?>>
                <div class="hero-style-preview slideshow-preview">
                    <?php if (!empty($preview_images[0])): ?>
                        <img src="<?php echo esc_url($preview_images[0]); ?>" alt="Slideshow preview">
                        <span class="play-icon">▶</span>
                    <?php else: ?>
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#667eea;color:white;">▶ Slideshow</div>
                    <?php endif; ?>
                </div>
                <div class="hero-style-info">
                    <div class="hero-style-name">📸 Foto Slideshow <span class="hero-style-badge">Aanbevolen</span></div>
                    <div class="hero-style-desc">Automatische slideshow met alle bestemmingsfoto's</div>
                </div>
            </label>
            
            <!-- Grid Option -->
            <label class="hero-style-option <?php echo $hero_style === 'grid' ? 'selected' : ''; ?>">
                <input type="radio" name="travel_hero_style" value="grid" <?php checked($hero_style, 'grid'); ?>>
                <div class="hero-style-preview grid-preview">
                    <?php 
                    for ($i = 0; $i < 4; $i++): 
                        $img = isset($preview_images[$i]) ? $preview_images[$i] : '';
                    ?>
                        <?php if ($img): ?>
                            <img src="<?php echo esc_url($img); ?>" alt="Grid preview">
                        <?php else: ?>
                            <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="hero-style-info">
                    <div class="hero-style-name">🖼️ Foto Grid</div>
                    <div class="hero-style-desc">4 foto's naast elkaar</div>
                </div>
            </label>
            
            <!-- Single Photo Option -->
            <label class="hero-style-option <?php echo $hero_style === 'single' ? 'selected' : ''; ?>">
                <input type="radio" name="travel_hero_style" value="single" <?php checked($hero_style, 'single'); ?>>
                <div class="hero-style-preview single">
                    <?php if (!empty($preview_images[0])): ?>
                        <img src="<?php echo esc_url($preview_images[0]); ?>" alt="Single preview">
                    <?php else: ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                </div>
                <div class="hero-style-info">
                    <div class="hero-style-name">🏞️ Enkele Grote Foto</div>
                    <div class="hero-style-desc">Eén grote foto met titel eronder</div>
                </div>
            </label>
            
            <!-- YouTube Video Option -->
            <label class="hero-style-option <?php echo $hero_style === 'youtube' ? 'selected' : ''; ?>">
                <input type="radio" name="travel_hero_style" value="youtube" <?php checked($hero_style, 'youtube'); ?>>
                <div class="hero-style-preview youtube-preview">
                    <span class="youtube-icon">▶</span>
                </div>
                <div class="hero-style-info">
                    <div class="hero-style-name">🎬 YouTube Video</div>
                    <div class="hero-style-desc">Embedded YouTube video als hero</div>
                </div>
            </label>
            
            <!-- Wide Option -->
            <label class="hero-style-option <?php echo $hero_style === 'wide' ? 'selected' : ''; ?>">
                <input type="radio" name="travel_hero_style" value="wide" <?php checked($hero_style, 'wide'); ?>>
                <div class="hero-style-preview wide">
                    <?php if (!empty($preview_images[0])): ?>
                        <img src="<?php echo esc_url($preview_images[0]); ?>" alt="Wide preview">
                    <?php else: ?>
                        <div style="width:100%;height:100%;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                    <?php endif; ?>
                </div>
                <div class="hero-style-info">
                    <div class="hero-style-name">📐 Breed Formaat</div>
                    <div class="hero-style-desc">Brede foto, minder hoog</div>
                </div>
            </label>
            
        </div>
    </div>
    
    <!-- Hero Image Selection (only for single/wide) -->
    <div class="hero-settings-section" id="hero-image-section">
        <h4>📷 Hoofdafbeelding Selecteren <small style="color:#666;font-weight:normal;">(voor Enkele Foto / Breed)</small></h4>
        
        <?php if (!empty($available_images)): ?>
            <div class="image-count-info">
                ✅ <strong><?php echo count($available_images); ?> foto's</strong> beschikbaar uit bestemmingen en hotels
            </div>
            <div class="hero-image-selection">
                <?php foreach ($available_images as $index => $img_url): ?>
                    <label class="hero-image-option <?php echo $hero_image === $img_url ? 'selected' : ''; ?>">
                        <input type="radio" name="travel_hero_image" value="<?php echo esc_attr($img_url); ?>" <?php checked($hero_image, $img_url); ?>>
                        <img src="<?php echo esc_url($img_url); ?>" alt="Hero image option">
                        <span class="check-mark">✓</span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-images-message">
                <p>📷 Nog geen afbeeldingen beschikbaar.<br><small>Foto's worden automatisch geladen uit bestemmingen en hotels.</small></p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- YouTube URL Section (only for youtube style) -->
    <div class="hero-settings-section" id="hero-youtube-section" style="<?php echo $hero_style !== 'youtube' ? 'display:none;' : ''; ?>">
        <h4>🎬 YouTube Video URL</h4>
        <div class="youtube-url-section">
            <label for="travel_hero_youtube">YouTube URL of Video ID</label>
            <input type="text" id="travel_hero_youtube" name="travel_hero_youtube" value="<?php echo esc_attr($hero_youtube); ?>" placeholder="https://www.youtube.com/watch?v=VIDEO_ID of VIDEO_ID">
            <p class="description">
                Plak de volledige YouTube URL of alleen de video ID.<br>
                Voorbeelden: <code>https://www.youtube.com/watch?v=dQw4w9WgXcQ</code> of <code>dQw4w9WgXcQ</code>
            </p>
            
            <label for="travel_hero_youtube_delay" style="margin-top: 15px; display: block;">⏱️ Startvertraging (seconden)</label>
            <input type="number" id="travel_hero_youtube_delay" name="travel_hero_youtube_delay" value="<?php echo esc_attr($hero_youtube_delay); ?>" min="0" max="60" step="1" style="width: 80px;">
            <p class="description">
                Video start automatisch na dit aantal seconden. Zet op 0 voor direct starten.
            </p>
            <?php 
            // Show preview if YouTube URL is set
            if (!empty($hero_youtube)):
                $video_id = $hero_youtube;
                // Extract video ID from full URL if needed
                if (strpos($hero_youtube, 'youtube.com') !== false || strpos($hero_youtube, 'youtu.be') !== false) {
                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $hero_youtube, $matches);
                    if (!empty($matches[1])) {
                        $video_id = $matches[1];
                    }
                }
            ?>
            <div class="youtube-preview-container">
                <iframe src="https://www.youtube.com/embed/<?php echo esc_attr($video_id); ?>" allowfullscreen></iframe>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</div>

<script>
jQuery(document).ready(function($) {
    // Hero style selection
    $('.hero-style-option').on('click', function() {
        $('.hero-style-option').removeClass('selected');
        $(this).addClass('selected');
        
        // Show/hide sections based on style
        var style = $(this).find('input').val();
        
        // Hide all optional sections first
        $('#hero-image-section').slideUp(200);
        $('#hero-youtube-section').slideUp(200);
        
        // Show relevant section
        if (style === 'single' || style === 'wide') {
            $('#hero-image-section').slideDown(200);
        } else if (style === 'youtube') {
            $('#hero-youtube-section').slideDown(200);
        }
    });
    
    // Initial state
    var currentStyle = $('input[name="travel_hero_style"]:checked').val();
    if (currentStyle === 'slideshow' || currentStyle === 'grid') {
        $('#hero-image-section').hide();
        $('#hero-youtube-section').hide();
    } else if (currentStyle === 'youtube') {
        $('#hero-image-section').hide();
    } else {
        $('#hero-youtube-section').hide();
    }
    
    // Hero image selection
    $('.hero-image-option').on('click', function() {
        $('.hero-image-option').removeClass('selected');
        $(this).addClass('selected');
    });
    
    // YouTube URL preview update
    $('#travel_hero_youtube').on('change blur', function() {
        var url = $(this).val();
        if (url) {
            var videoId = url;
            // Extract video ID from full URL
            var match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
            if (match && match[1]) {
                videoId = match[1];
            }
            // Update or create preview
            var previewHtml = '<div class="youtube-preview-container"><iframe src="https://www.youtube.com/embed/' + videoId + '" allowfullscreen></iframe></div>';
            if ($('.youtube-preview-container').length) {
                $('.youtube-preview-container').replaceWith(previewHtml);
            } else {
                $('.youtube-url-section').append(previewHtml);
            }
        }
    });
});
</script>
