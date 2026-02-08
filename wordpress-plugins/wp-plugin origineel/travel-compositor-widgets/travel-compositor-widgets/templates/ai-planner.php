<?php
/**
 * Template: AI Trip Planner Widget
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-ai <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    <div class="tc-widget-content">
        
        <?php if ($atts['title']): ?>
        <h3 class="tc-widget-title">
            <i class="fa-light fa-microchip-ai"></i>
            <span><?php echo esc_html($atts['title']); ?></span>
        </h3>
        <?php endif; ?>
        
        <!-- AI Description Input -->
        <div class="tc-form-row">
            <div class="tc-form-group" style="grid-column: 1 / -1;">
                <label>Beschrijf je droomreis</label>
                <input type="text" class="tc-ai-input tc-ai-description" placeholder="<?php echo esc_attr($atts['placeholder']); ?>">
            </div>
        </div>
        
        <!-- Submit -->
        <div class="tc-form-row" style="justify-content: flex-end;">
            <button type="button" class="tc-submit tc-submit-inline tc-submit-ai">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>Plan met AI</span>
            </button>
        </div>
        
    </div>
</div>
