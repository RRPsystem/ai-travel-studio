<?php
/**
 * SAFE MODE TEMPLATE - Minimal version that should always work
 */

// Get WordPress header
get_header();

// Get post ID
global $post;
$post_id = $post->ID;

?>

<div style="max-width: 1200px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    
    <div style="background: #10b981; color: white; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h1 style="margin: 0; color: white;">⚠️ SAFE MODE ACTIEF</h1>
        <p style="margin: 10px 0 0 0;">De normale template crashed. Dit is een veilige fallback versie.</p>
    </div>
    
    <h2><?php the_title(); ?></h2>
    
    <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 20px 0;">
        <h3 style="margin-top: 0;">🔍 Debug Info</h3>
        <p><strong>Post ID:</strong> <?php echo $post_id; ?></p>
        <p><strong>Post Type:</strong> <?php echo get_post_type($post_id); ?></p>
        <p><strong>WordPress:</strong> <?php echo get_bloginfo('version'); ?></p>
        <p><strong>PHP:</strong> <?php echo phpversion(); ?></p>
    </div>
    
    <?php
    // Check if Settings class exists
    if (class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings')) {
        echo '<div style="background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0;">';
        echo '<p style="margin: 0;"><strong>✅ Settings class:</strong> OK</p>';
        echo '</div>';
    } else {
        echo '<div style="background: #fee2e2; padding: 20px; border-radius: 8px; margin: 20px 0;">';
        echo '<p style="margin: 0;"><strong>❌ Settings class:</strong> NIET GEVONDEN!</p>';
        echo '<p style="margin: 10px 0 0 0; font-size: 14px;">Dit is waarschijnlijk het probleem. De plugin is niet correct geladen.</p>';
        echo '</div>';
    }
    
    // Check if helper functions exist
    if (function_exists('RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel')) {
        echo '<div style="background: #d1fae5; padding: 20px; border-radius: 8px; margin: 20px 0;">';
        echo '<p style="margin: 0;"><strong>✅ Helper functions:</strong> OK</p>';
        echo '</div>';
    } else {
        echo '<div style="background: #fee2e2; padding: 20px; border-radius: 8px; margin: 20px 0;">';
        echo '<p style="margin: 0;"><strong>❌ Helper functions:</strong> NIET GEVONDEN!</p>';
        echo '<p style="margin: 10px 0 0 0; font-size: 14px;">Expert helper file niet geladen.</p>';
        echo '</div>';
    }
    
    // Show post meta
    echo '<div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">';
    echo '<h3>📊 Post Meta Data</h3>';
    
    $destinations = get_post_meta($post_id, 'travel_destinations', true);
    $hotels = get_post_meta($post_id, 'travel_hotels', true);
    $transports = get_post_meta($post_id, 'travel_transports', true);
    $cruises = get_post_meta($post_id, 'travel_cruises', true);
    
    echo '<p><strong>Destinations:</strong> ' . (is_array($destinations) ? count($destinations) : 0) . '</p>';
    echo '<p><strong>Hotels:</strong> ' . (is_array($hotels) ? count($hotels) : 0) . '</p>';
    echo '<p><strong>Transports:</strong> ' . (is_array($transports) ? count($transports) : 0) . '</p>';
    echo '<p><strong>Cruises:</strong> ' . (is_array($cruises) ? count($cruises) : 0) . '</p>';
    
    echo '</div>';
    
    // Show description
    $description = get_post_meta($post_id, 'travel_description', true);
    if ($description) {
        echo '<div style="background: white; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; margin: 20px 0;">';
        echo '<h3>📝 Beschrijving</h3>';
        echo '<p>' . nl2br(esc_html($description)) . '</p>';
        echo '</div>';
    }
    
    // Show destinations
    if (is_array($destinations) && !empty($destinations)) {
        echo '<div style="background: white; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; margin: 20px 0;">';
        echo '<h3>📍 Bestemmingen</h3>';
        foreach ($destinations as $dest) {
            if (isset($dest['name'])) {
                echo '<div style="padding: 10px; background: #f9fafb; margin: 10px 0; border-radius: 4px;">';
                echo '<strong>' . esc_html($dest['name']) . '</strong>';
                if (isset($dest['country'])) {
                    echo ' (' . esc_html($dest['country']) . ')';
                }
                echo '</div>';
            }
        }
        echo '</div>';
    }
    
    // Instructions to fix
    echo '<div style="background: #fef3c7; padding: 20px; border-radius: 8px; border-left: 4px solid #f59e0b; margin: 20px 0;">';
    echo '<h3 style="margin-top: 0;">🔧 Hoe te fixen</h3>';
    echo '<ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">';
    echo '<li>Kijk naar de rode errors hierboven</li>';
    echo '<li>Als Settings class of Helper functions ontbreken → plugin heractiveren</li>';
    echo '<li>Als die OK zijn → check PHP error log voor specifieke errors</li>';
    echo '<li>Contact developer met screenshot van deze pagina</li>';
    echo '</ol>';
    echo '</div>';
    ?>
    
    <div style="background: #e0e7ff; padding: 20px; border-radius: 8px; margin: 20px 0;">
        <h3 style="margin-top: 0;">🚀 Normale template activeren</h3>
        <p>Als alle checks hierboven ✅ zijn, dan kun je de normale template weer activeren door:</p>
        <ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
            <li>Hernoem <code>single-rbs-travel-idea-BACKUP.php</code> naar <code>single-rbs-travel-idea.php</code></li>
            <li>Of upload versie 4.12.0 opnieuw</li>
        </ol>
    </div>
    
</div>

<?php get_footer(); ?>
