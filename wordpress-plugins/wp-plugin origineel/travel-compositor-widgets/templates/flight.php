<?php
/**
 * Template: Flight Only Widget
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-flight <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    <div class="tc-widget-content">
        
        <?php if ($atts['title']): ?>
        <h3 class="tc-widget-title">
            <i class="fa-light fa-plane-departure"></i>
            <span><?php echo esc_html($atts['title']); ?></span>
        </h3>
        <?php endif; ?>
        
        <!-- Row 1: Origin & Destination -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Van</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-plane-departure"></i>
                    <input type="text" class="tc-input tc-autocomplete-origin tc-flight-origin" placeholder="Vertrek luchthaven" autocomplete="off">
                    <input type="hidden" class="tc-flight-origin-code tc-origin-code">
                </div>
            </div>
            <div class="tc-form-group">
                <label>Naar</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-plane-arrival"></i>
                    <input type="text" class="tc-input tc-autocomplete-origin tc-flight-destination" placeholder="Aankomst luchthaven" autocomplete="off">
                    <input type="hidden" class="tc-flight-dest-code tc-origin-code">
                </div>
            </div>
        </div>
        
        <!-- Row 2: Dates -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Heenreis</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-flight-departure" placeholder="Vertrekdatum" readonly>
                </div>
            </div>
            <div class="tc-form-group">
                <label>Terugreis (optioneel)</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-flight-return" placeholder="Retourdatum" readonly>
                </div>
            </div>
        </div>
        
        <!-- Row 3: Passengers -->
        <div class="tc-form-row">
            <div class="tc-form-group tc-room-selector">
                <label>Passagiers</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-users"></i>
                    <button type="button" class="tc-room-trigger">
                        <span class="tc-room-summary">2 Volwassenen</span>
                    </button>
                </div>
                <div class="tc-room-dropdown">
                    <div class="tc-room-row">
                        <div class="tc-room-label">
                            Volwassenen
                            <small>12 jaar en ouder</small>
                        </div>
                        <div class="tc-counter">
                            <button type="button" class="tc-counter-btn" data-action="decrease" data-target="adults">−</button>
                            <span class="tc-counter-value tc-adults-value">2</span>
                            <button type="button" class="tc-counter-btn" data-action="increase" data-target="adults">+</button>
                        </div>
                    </div>
                    <div class="tc-room-row">
                        <div class="tc-room-label">
                            Kinderen
                            <small>2-11 jaar</small>
                        </div>
                        <div class="tc-counter">
                            <button type="button" class="tc-counter-btn" data-action="decrease" data-target="children">−</button>
                            <span class="tc-counter-value tc-children-value">0</span>
                            <button type="button" class="tc-counter-btn" data-action="increase" data-target="children">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="tc-form-row">
            <button type="button" class="tc-submit tc-submit-flight">
                <i class="fa-solid fa-search"></i>
                <span>Zoek vluchten</span>
            </button>
        </div>
        
    </div>
</div>
