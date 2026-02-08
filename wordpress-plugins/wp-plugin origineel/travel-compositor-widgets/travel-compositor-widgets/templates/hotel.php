<?php
/**
 * Template: Hotel Only Widget
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-hotel <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    <div class="tc-widget-content">
        
        <?php if ($atts['title']): ?>
        <h3 class="tc-widget-title">
            <i class="fa-light fa-bed-front"></i>
            <span><?php echo esc_html($atts['title']); ?></span>
        </h3>
        <?php endif; ?>
        
        <!-- Row 1: Destination -->
        <div class="tc-form-row">
            <div class="tc-form-group" style="grid-column: 1 / -1;">
                <label>Bestemming</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-map-marker-alt"></i>
                    <input type="text" class="tc-input tc-autocomplete-dest tc-hotel-destination" placeholder="Stad, regio of hotelnaam" autocomplete="off">
                    <input type="hidden" class="tc-hotel-dest-code tc-dest-code">
                </div>
            </div>
        </div>
        
        <!-- Row 2: Dates -->
        <div class="tc-form-row">
            <div class="tc-form-group">
                <label>Check-in</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-hotel-checkin" placeholder="Selecteer datum" readonly>
                </div>
            </div>
            <div class="tc-form-group">
                <label>Check-out</label>
                <div class="tc-input-wrap">
                    <i class="fa-light fa-calendar-alt"></i>
                    <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-hotel-checkout" placeholder="Selecteer datum" readonly>
                </div>
            </div>
        </div>
        
        <!-- Row 3: Travelers -->
        <div class="tc-form-row">
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
            <button type="button" class="tc-submit tc-submit-hotel">
                <i class="fa-solid fa-search"></i>
                <span>Zoek accommodatie</span>
            </button>
        </div>
        
    </div>
</div>
