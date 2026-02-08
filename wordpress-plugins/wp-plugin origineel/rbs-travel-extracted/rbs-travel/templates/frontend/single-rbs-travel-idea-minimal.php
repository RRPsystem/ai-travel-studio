<?php
/**
 * MINIMAL WORKING VERSION - Guaranteed to work
 */

// NO use statements that might fail
// NO complex code
// JUST basic HTML output

get_header();

global $post;
$post_id = $post->ID;

?>

<div style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    
    <div style="background: #10b981; color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; text-align: center;">
        <h1 style="margin: 0; color: white; font-size: 32px;">
            ✅ TEMPLATE WERKT!
        </h1>
        <p style="margin: 15px 0 0 0; font-size: 18px;">
            Versie 4.16.0 - Minimal Safe Mode
        </p>
    </div>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-top: 0; color: #1f2937;">
            <?php the_title(); ?>
        </h2>
        
        <?php
        // Try to get description - with error handling
        $description = '';
        try {
            $description = get_post_meta($post_id, 'travel_description', true);
        } catch (Exception $e) {
            $description = 'Beschrijving kon niet worden geladen.';
        }
        ?>
        
        <?php if ($description): ?>
            <div style="background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="margin-top: 0;">📝 Beschrijving</h3>
                <p style="line-height: 1.8;"><?php echo nl2br(esc_html($description)); ?></p>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">📊 Reis Data</h3>
        
        <?php
        // Get all travel meta - safely
        $meta_keys = array(
            'travel_destinations' => '📍 Bestemmingen',
            'travel_hotels' => '🏨 Hotels',
            'travel_transports' => '✈️ Vervoer',
            'travel_cruises' => '🚢 Cruises',
            'travel_number_of_nights' => '🌙 Nachten',
            'travel_price_per_person' => '💰 Prijs per persoon'
        );
        
        foreach ($meta_keys as $key => $label) {
            $value = get_post_meta($post_id, $key, true);
            
            echo '<div style="padding: 10px; background: #f9fafb; margin: 10px 0; border-radius: 6px;">';
            echo '<strong>' . $label . ':</strong> ';
            
            if (is_array($value)) {
                echo count($value) . ' items';
            } elseif (!empty($value)) {
                echo esc_html($value);
            } else {
                echo 'Niet ingesteld';
            }
            
            echo '</div>';
        }
        ?>
    </div>
    
    <div style="background: #fef3c7; padding: 30px; border-radius: 12px; margin-top: 30px; border-left: 4px solid #f59e0b;">
        <h3 style="margin-top: 0; color: #92400e;">⚠️ Dit is een vereenvoudigde versie</h3>
        <p style="color: #92400e; line-height: 1.8;">
            De volledige template veroorzaakt een PHP fatal error. Deze minimale versie werkt gegarandeerd.<br><br>
            <strong>Volgende stap:</strong> WordPress debug log checken om exacte error te zien.
        </p>
    </div>
    
    <div style="background: #e0e7ff; padding: 30px; border-radius: 12px; margin-top: 30px; border-left: 4px solid #6366f1;">
        <h3 style="margin-top: 0; color: #3730a3;">🔍 Hoe te debuggen</h3>
        <ol style="color: #3730a3; line-height: 2;">
            <li>Open wp-config.php</li>
            <li>Voeg toe: <code>define('WP_DEBUG', true);</code></li>
            <li>Voeg toe: <code>define('WP_DEBUG_LOG', true);</code></li>
            <li>Bekijk wp-content/debug.log voor exact error</li>
        </ol>
    </div>

</div>

<?php get_footer(); ?>
