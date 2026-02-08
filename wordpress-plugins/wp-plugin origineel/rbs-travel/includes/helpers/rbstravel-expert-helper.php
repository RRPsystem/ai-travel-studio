<?php
namespace RBS_TRAVEL\HELPERS;
use RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings;

defined('RBS_TRAVEL') or die();

/**
 * Get expert for a specific travel post
 * SIMPLE: First check manual assignment, then fallback to settings
 * 
 * @param int $post_id Travel post ID
 * @return array|false Expert data array or false if none found
 */
function rbstravel_get_expert_for_travel($post_id) {
    // PRIORITY 1: Check if expert is manually assigned to this travel
    $assigned_expert_id = get_post_meta($post_id, 'rbs_assigned_expert', true);
    
    if (!empty($assigned_expert_id)) {
        $expert = get_post($assigned_expert_id);
        
        // Verify expert exists and is published
        if ($expert && $expert->post_type === 'rbs-expert' && $expert->post_status === 'publish') {
            return rbstravel_format_expert_data($expert);
        }
    }
    
    // PRIORITY 2: No manual assignment, use fallback from settings
    return rbstravel_get_fallback_expert();
}

/**
 * Get fallback expert from settings
 * 
 * @return array|false Expert data array or false if none configured
 */
function rbstravel_get_fallback_expert() {
    $name = RBS_TRAVEL_Settings::GetSetting('fallback_expert_name', 'Reisexpert');
    $title = RBS_TRAVEL_Settings::GetSetting('fallback_expert_title', 'Reisspecialist');
    $text = RBS_TRAVEL_Settings::GetSetting('fallback_expert_text', 'Onze reisexperts staan voor u klaar.');
    $url = RBS_TRAVEL_Settings::GetSetting('fallback_expert_url', '/contact');
    $image_id = RBS_TRAVEL_Settings::GetSetting('fallback_expert_image', '');
    
    // ALWAYS return expert data, even if not configured
    // This prevents crashes on frontend
    
    $image_url = '';
    if ($image_id) {
        $image = wp_get_attachment_image_src($image_id, 'medium');
        if ($image) {
            $image_url = $image[0];
        }
    }
    
    return array(
        'name' => $name,
        'title' => $title,
        'text' => $text,
        'url' => $url,
        'image_url' => $image_url,
        'is_fallback' => true
    );
}

/**
 * Format expert post data into standard array
 * 
 * @param WP_Post $expert Expert post object
 * @return array Expert data array
 */
function rbstravel_format_expert_data($expert) {
    $expert_id = $expert->ID;
    
    // Get expert meta
    $email = get_post_meta($expert_id, '_expert_email', true);
    $phone = get_post_meta($expert_id, '_expert_phone', true);
    $specialisation = get_post_meta($expert_id, '_expert_specialisation', true);
    $years = get_post_meta($expert_id, '_expert_years', true);
    $expert_url = get_post_meta($expert_id, '_expert_url', true);
    $countries = get_post_meta($expert_id, '_expert_countries', true);
    
    // Get expert thumbnail
    $image_url = '';
    if (has_post_thumbnail($expert_id)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($expert_id), 'medium');
        if ($image) {
            $image_url = $image[0];
        }
    }
    
    // Get expert bio from post content
    $bio = wp_trim_words(get_the_content(null, false, $expert), 30);
    
    return array(
        'id' => $expert_id,
        'name' => get_the_title($expert),
        'title' => $specialisation,
        'text' => $bio,
        'email' => $email,
        'phone' => $phone,
        'years' => $years,
        'url' => !empty($expert_url) ? $expert_url : get_permalink($expert),
        'image_url' => $image_url,
        'countries' => $countries,
        'is_fallback' => false
    );
}

/**
 * Render expert widget for sidebar
 * SUPER SAFE VERSION - NEVER CRASHES
 * 
 * @param int $post_id Travel Idea post ID
 */
function rbstravel_render_expert_widget($post_id) {
    try {
        $expert = rbstravel_get_expert_for_travel($post_id);
        
        if (!$expert || !is_array($expert)) {
            // No expert configured - show generic fallback
            ?>
            <div class="rbs-sidebar-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 25px; color: white;">
                <h4 style="font-size: 18px; font-weight: 700; color: white; margin: 0 0 15px 0;">👤 Uw reisexpert</h4>
                <p style="color: rgba(255,255,255,0.9); font-size: 14px; line-height: 1.6; margin: 0;">
                    Onze reisexperts staan voor u klaar om deze reis volledig op maat te maken.
                </p>
                <a href="/contact" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 15px; border: 1px solid rgba(255,255,255,0.3);">
                    Neem contact op
                </a>
            </div>
            <?php
            return;
        }
    } catch (Exception $e) {
        // Log error but don't crash
        error_log('RBS Expert Widget Error: ' . $e->getMessage());
        ?>
        <div class="rbs-sidebar-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 25px; color: white;">
            <h4 style="font-size: 18px; font-weight: 700; color: white; margin: 0 0 15px 0;">👤 Uw reisexpert</h4>
            <p style="color: rgba(255,255,255,0.9); font-size: 14px; line-height: 1.6; margin: 0;">
                Onze reisexperts staan voor u klaar om deze reis volledig op maat te maken.
            </p>
            <a href="/contact" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 15px; border: 1px solid rgba(255,255,255,0.3);">
                Neem contact op
            </a>
        </div>
        <?php
        return;
    }
    
    ?>
    <div class="rbs-sidebar-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 25px; margin-bottom: 25px; color: white;">
        <h4 style="font-size: 18px; font-weight: 700; color: white; margin: 0 0 15px 0;">👤 Uw reisexpert</h4>
        
        <?php if (!empty($expert['image_url'])): ?>
            <div style="text-align: center; margin-bottom: 15px;">
                <img src="<?php echo esc_url($expert['image_url']); ?>" alt="<?php echo esc_attr($expert['name']); ?>" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(255,255,255,0.3);">
            </div>
        <?php endif; ?>
        
        <div style="text-align: center;">
            <div style="font-size: 20px; font-weight: 700; color: white; margin-bottom: 5px;">
                <?php echo esc_html($expert['name']); ?>
            </div>
            
            <?php if (!empty($expert['title'])): ?>
                <div style="font-size: 14px; color: rgba(255,255,255,0.8); margin-bottom: 15px;">
                    <?php echo esc_html($expert['title']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($expert['text'])): ?>
                <p style="color: rgba(255,255,255,0.9); font-size: 14px; line-height: 1.6; margin: 0 0 15px 0;">
                    <?php echo esc_html($expert['text']); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($expert['years'])): ?>
                <div style="font-size: 13px; color: rgba(255,255,255,0.7); margin-bottom: 15px;">
                    ✨ <?php echo esc_html($expert['years']); ?> jaar ervaring
                </div>
            <?php endif; ?>
            
            <a href="<?php echo esc_url($expert['url']); ?>" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid rgba(255,255,255,0.3); transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                Ontmoet <?php echo esc_html(explode(' ', $expert['name'])[0]); ?>
            </a>
        </div>
    </div>
    <?php
}
