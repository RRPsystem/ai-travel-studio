<?php
/**
 * Template: Car Rental Widget
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-car <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    <div class="tc-widget-content">
        
        <?php if ($atts['title']): ?>
        <h3 class="tc-widget-title">
            <i class="fa-light fa-car"></i>
            <span><?php echo esc_html($atts['title']); ?></span>
        </h3>
        <?php endif; ?>
        
        <!-- Row 1: Pickup Location -->
        <div class="tc-form-row">
            <div class="tc-form-group" style="grid-column: 1 / -1;">
                <label>Ophaallocatie</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-map-marker-alt"></i>
                    <input type="text" class="tc-input tc-autocomplete-car tc-car-pickup" placeholder="Luchthaven, stad of adres" autocomplete="off">
                </div>
            </div>
        </div>
        
        <!-- Row 2: Dates -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Ophaaldatum</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-car-pickup-date" placeholder="Selecteer datum" readonly>
                </div>
            </div>
            <div class="tc-form-group">
                <label>Inleverdatum</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-car-return-date" placeholder="Selecteer datum" readonly>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="tc-form-row">
            <button type="button" class="tc-submit tc-submit-car">
                <i class="fa-solid fa-search"></i>
                <span>Zoek huurauto's</span>
            </button>
        </div>
        
    </div>
</div>
