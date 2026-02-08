<?php
/**
 * Template: Complete Searchbox with Tabs
 */
if (!defined('ABSPATH')) exit;
?>
<div class="tc-widget tc-widget-searchbox <?php echo esc_attr($atts['class']); ?>" <?php echo $style; ?>>
    
    <!-- Tabs -->
    <div class="tc-tabs">
        <?php if (in_array('tripplanner', $tabs)): ?>
        <button class="tc-tab active" data-panel="tripplanner">
            <i class="fa-light fa-map-location-dot"></i>
            <span>Trip Planner</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('ai', $tabs)): ?>
        <button class="tc-tab <?php echo !in_array('tripplanner', $tabs) ? 'active' : ''; ?>" data-panel="ai">
            <i class="fa-light fa-microchip-ai"></i>
            <span>AI Planner</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('flighthotel', $tabs)): ?>
        <button class="tc-tab" data-panel="flighthotel">
            <i class="fa-light fa-plane-departure"></i>
            <span>Vlucht + Hotel</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('hotel', $tabs)): ?>
        <button class="tc-tab" data-panel="hotel">
            <i class="fa-light fa-bed-front"></i>
            <span>Accommodatie</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('routing', $tabs)): ?>
        <button class="tc-tab" data-panel="routing">
            <i class="fa-light fa-route"></i>
            <span>Fly & Drive</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('car', $tabs)): ?>
        <button class="tc-tab" data-panel="car">
            <i class="fa-light fa-car"></i>
            <span>Huurauto</span>
        </button>
        <?php endif; ?>
        
        <?php if (in_array('flight', $tabs)): ?>
        <button class="tc-tab" data-panel="flight">
            <i class="fa-light fa-plane"></i>
            <span>Vluchten</span>
        </button>
        <?php endif; ?>
    </div>
    
    <!-- Content -->
    <div class="tc-widget-content">
        
        <!-- Trip Planner Panel -->
        <?php if (in_array('tripplanner', $tabs)): ?>
        <div class="tc-panel active" id="panel-tripplanner">
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Vertrekdatum</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-tripplanner-departure" placeholder="Selecteer datum" readonly>
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
                            <div class="tc-room-label">Volwassenen<small>18+</small></div>
                            <div class="tc-counter">
                                <button type="button" class="tc-counter-btn" data-action="decrease" data-target="adults">−</button>
                                <span class="tc-counter-value tc-adults-value">2</span>
                                <button type="button" class="tc-counter-btn" data-action="increase" data-target="adults">+</button>
                            </div>
                        </div>
                        <div class="tc-room-row">
                            <div class="tc-room-label">Kinderen<small>0-17</small></div>
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
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-tripplanner">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- AI Planner Panel -->
        <?php if (in_array('ai', $tabs)): ?>
        <div class="tc-panel <?php echo !in_array('tripplanner', $tabs) ? 'active' : ''; ?>" id="panel-ai">
            <div class="tc-form-row">
                <div class="tc-form-group" style="grid-column: 1 / -1;">
                    <input type="text" class="tc-ai-input tc-ai-description" placeholder="Beschrijf je droomreis... (bijv. 'Een romantische week in Italië')">
                </div>
            </div>
            <div class="tc-form-row" style="justify-content: flex-end;">
                <button type="button" class="tc-submit tc-submit-inline tc-submit-ai">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Plan met AI
                </button>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Flight + Hotel Panel -->
        <?php if (in_array('flighthotel', $tabs)): ?>
        <div class="tc-panel" id="panel-flighthotel">
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Vertrek vanaf</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-plane-departure"></i>
                        <input type="text" class="tc-input tc-autocomplete-origin tc-fh-origin" placeholder="Luchthaven" autocomplete="off">
                        <input type="hidden" class="tc-fh-origin-code tc-origin-code">
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Bestemming</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-map-marker-alt"></i>
                        <input type="text" class="tc-input tc-autocomplete-dest tc-fh-destination" placeholder="Stad of hotel" autocomplete="off">
                        <input type="hidden" class="tc-fh-dest-code tc-dest-code">
                    </div>
                </div>
            </div>
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Check-in</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-fh-checkin" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Check-out</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-fh-checkout" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-flight-hotel">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Hotel Panel -->
        <?php if (in_array('hotel', $tabs)): ?>
        <div class="tc-panel" id="panel-hotel">
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Bestemming</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-map-marker-alt"></i>
                        <input type="text" class="tc-input tc-autocomplete-dest tc-hotel-destination" placeholder="Stad of hotel" autocomplete="off">
                        <input type="hidden" class="tc-hotel-dest-code tc-dest-code">
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Check-in</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-hotel-checkin" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Check-out</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-hotel-checkout" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-hotel">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Routing Panel -->
        <?php if (in_array('routing', $tabs)): ?>
        <div class="tc-panel" id="panel-routing">
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
                <div class="tc-form-group">
                    <label>Vertrekdatum</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-routing-departure" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-routing">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Car Panel -->
        <?php if (in_array('car', $tabs)): ?>
        <div class="tc-panel" id="panel-car">
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Ophaallocatie</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-map-marker-alt"></i>
                        <input type="text" class="tc-input tc-autocomplete-car tc-car-pickup" placeholder="Luchthaven of stad" autocomplete="off">
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Ophaaldatum</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-car-pickup-date" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Inleverdatum</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkout tc-car-return-date" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-car">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Flight Panel -->
        <?php if (in_array('flight', $tabs)): ?>
        <div class="tc-panel" id="panel-flight">
            <div class="tc-form-row">
                <div class="tc-form-group">
                    <label>Van</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-plane-departure"></i>
                        <input type="text" class="tc-input tc-autocomplete-origin tc-flight-origin" placeholder="Luchthaven" autocomplete="off">
                        <input type="hidden" class="tc-flight-origin-code tc-origin-code">
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Naar</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-plane-arrival"></i>
                        <input type="text" class="tc-input tc-autocomplete-origin tc-flight-destination" placeholder="Luchthaven" autocomplete="off">
                        <input type="hidden" class="tc-flight-dest-code tc-origin-code">
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>Heenreis</label>
                    <div class="tc-input-wrap">
                        <i class="fa-light fa-calendar-alt"></i>
                        <input type="text" class="tc-input tc-datepicker tc-datepicker-checkin tc-flight-departure" placeholder="Datum" readonly>
                    </div>
                </div>
                <div class="tc-form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="tc-submit tc-submit-flight">
                        <i class="fa-solid fa-search"></i> Zoeken
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>
