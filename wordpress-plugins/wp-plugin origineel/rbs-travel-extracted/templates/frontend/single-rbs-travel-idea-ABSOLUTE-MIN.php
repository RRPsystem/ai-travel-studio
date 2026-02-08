<?php
/**
 * ABSOLUTE MINIMUM SAFE TEMPLATE
 * This will ALWAYS work - no fancy features, just basic content
 */

get_header();

// Get the post
global $post;
setup_postdata($post);

?>
<div style="max-width: 1200px; margin: 40px auto; padding: 20px; font-family: Arial, sans-serif;">
    <h1 style="font-size: 32px; margin-bottom: 20px;"><?php echo esc_html(get_the_title()); ?></h1>
    
    <?php if (has_post_thumbnail()): ?>
        <div style="margin-bottom: 30px;">
            <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; border-radius: 8px;')); ?>
        </div>
    <?php endif; ?>
    
    <div style="font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
        <?php 
        $description = get_post_meta(get_the_ID(), 'travel_description', true);
        if ($description) {
            echo wp_kses_post($description);
        } else {
            the_content();
        }
        ?>
    </div>
    
    <div style="background: #f0f9ff; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <h3 style="margin-top: 0;">Prijs</h3>
        <?php 
        $price_pp = get_post_meta(get_the_ID(), 'travel_price_per_person', true);
        $price_total = get_post_meta(get_the_ID(), 'travel_price_total', true);
        $nights = get_post_meta(get_the_ID(), 'travel_number_of_nights', true);
        
        if ($price_pp) echo '<p>Per persoon: €' . number_format($price_pp, 2, ',', '.') . '</p>';
        if ($price_total) echo '<p>Totaal: €' . number_format($price_total, 2, ',', '.') . '</p>';
        if ($nights) echo '<p>Aantal nachten: ' . esc_html($nights) . '</p>';
        ?>
    </div>
    
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px; border-radius: 12px;">
        <h3 style="margin-top: 0; color: white;">Contact opnemen</h3>
        <p style="margin-bottom: 15px;">Interesse in deze reis? Neem contact met ons op!</p>
        <a href="/contact" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; border: 1px solid rgba(255,255,255,0.3);">
            Neem contact op
        </a>
    </div>
</div>
<?php

get_footer();
