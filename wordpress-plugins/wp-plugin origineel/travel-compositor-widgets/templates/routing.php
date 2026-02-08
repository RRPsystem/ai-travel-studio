<?php
/**
 * Template: Fly & Drive / Routing Widget
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-routing <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    <div class="tc-widget-content">
        
        <?php if ($atts['title']): ?>
        <h3 class="tc-widget-title">
            <i class="fa-light fa-route"></i>
            <span><?php echo esc_html($atts['title']); ?></span>
        </h3>
        <?php endif; ?>
        
        <!-- Row 1: Origin & Destination -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Vertrek vanaf</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-plane-departure"></i>
                    <input type="text" class="tc-input tc-autocomplete-origin tc-routing-origin" placeholder="Luchthaven" autocomplete="off">
                    <input type="hidden" class="tc-routing-origin-code tc-origin-code">
                </div>
            </div>
            <div class="tc-form-group">
                <label>Bestemming</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-map-marker-alt"></i>
                    <input type="text" class="tc-input tc-autocomplete-dest tc-routing-destination" placeholder="Stad of regio" autocomplete="off">
                    <input type="hidden" class="tc-routing-dest-code tc-dest-code">
                </div>
            </div>
        </div>
        
        <!-- Row 2: Date & Travelers -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Vertrekdatum</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-routing-departure" placeholder="Selecteer datum" readonly>
                </div>
            </div>
            <div class="tc-form-group tc-room-selector">
                <label>Reizigers</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-users"></i>
                    <button type="button" class="tc-room-trigger">
                        <span class="tc-room-summary">2 Volwassenen, 1 Kamer</span>
                    </button>
                </div>
                <div class="tc-room-dropdown">
                    <div class="tc-room-row">
                        <div class="tc-room-label">
                            Volwassenen
                            <small>18 jaar en ouder</small>
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
                            <small>0-17 jaar</small>
                        </div>
                        <div class="tc-counter">
                            <button type="button" class="tc-counter-btn" data-action="decrease" data-target="children">−</button>
                            <span class="tc-counter-value tc-children-value">0</span>
                            <button type="button" class="tc-counter-btn" data-action="increase" data-target="children">+</button>
                        </div>
                    </div>
                    <div class="tc-room-row">
                        <div class="tc-room-label">Kamers</div>
                        <div class="tc-counter">
                            <button type="button" class="tc-counter-btn" data-action="decrease" data-target="rooms">−</button>
                            <span class="tc-counter-value tc-rooms-value">1</span>
                            <button type="button" class="tc-counter-btn" data-action="increase" data-target="rooms">+</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Submit -->
        <div class="tc-form-row">
            <button type="button" class="tc-submit tc-submit-routing">
                <i class="fa-solid fa-search"></i>
                <span>Zoek Fly & Drive</span>
            </button>
        </div>
        
    </div>
</div>
