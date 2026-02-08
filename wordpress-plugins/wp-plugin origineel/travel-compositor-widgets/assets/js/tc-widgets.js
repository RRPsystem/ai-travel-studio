/**
 * Travel Compositor Widgets - JavaScript
 * Version: 1.0.0
 */

(function($) {
    'use strict';
    
    // Configuration (passed from PHP via wp_localize_script)
    const config = window.tcWidgetsConfig || {
        microsite: 'rondreis-planner',
        baseUrl: 'https://rondreis-planner.nl',
        primaryColor: '#066168',
        language: 'NL',
        currency: 'EUR'
    };
    
    // State per widget instance
    const widgetStates = {};
    
    // Common airports for autocomplete
    const commonAirports = [
        { label: 'Amsterdam Schiphol (AMS)', value: 'Amsterdam Schiphol', code: 'AMS' },
        { label: 'Rotterdam The Hague (RTM)', value: 'Rotterdam', code: 'RTM' },
        { label: 'Eindhoven Airport (EIN)', value: 'Eindhoven', code: 'EIN' },
        { label: 'Brussel Zaventem (BRU)', value: 'Brussel', code: 'BRU' },
        { label: 'Düsseldorf (DUS)', value: 'Düsseldorf', code: 'DUS' },
        { label: 'Frankfurt (FRA)', value: 'Frankfurt', code: 'FRA' },
        { label: 'Parijs CDG (CDG)', value: 'Parijs', code: 'CDG' },
        { label: 'Londen Heathrow (LHR)', value: 'Londen', code: 'LHR' },
        { label: 'Barcelona (BCN)', value: 'Barcelona', code: 'BCN' },
        { label: 'Madrid (MAD)', value: 'Madrid', code: 'MAD' },
        { label: 'Rome Fiumicino (FCO)', value: 'Rome', code: 'FCO' },
        { label: 'Milaan Malpensa (MXP)', value: 'Milaan', code: 'MXP' }
    ];
    
    /**
     * Initialize all widgets on page
     */
    function init() {
        // Initialize each widget type
        $('.tc-widget').each(function() {
            const $widget = $(this);
            const widgetId = $widget.attr('id') || 'tc-widget-' + Math.random().toString(36).substr(2, 9);
            $widget.attr('id', widgetId);
            
            // Initialize state for this widget
            widgetStates[widgetId] = {
                adults: 2,
                children: 0,
                rooms: 1,
                childAges: []
            };
            
            initTabs($widget);
            initDatepickers($widget);
            initRoomSelector($widget, widgetId);
            initAutocomplete($widget);
            initSubmitButtons($widget, widgetId);
        });
    }
    
    /**
     * Tab switching
     */
    function initTabs($widget) {
        $widget.find('.tc-tab').on('click', function() {
            const $tab = $(this);
            const panelId = $tab.data('panel');
            
            // Update tabs
            $widget.find('.tc-tab').removeClass('active');
            $tab.addClass('active');
            
            // Update panels
            $widget.find('.tc-panel').removeClass('active');
            $widget.find('#panel-' + panelId).addClass('active');
        });
    }
    
    /**
     * Datepickers
     */
    function initDatepickers($widget) {
        const minDate = new Date();
        minDate.setDate(minDate.getDate() + 3);
        
        $widget.find('.tc-datepicker').each(function() {
            const $input = $(this);
            const isCheckout = $input.hasClass('tc-datepicker-checkout');
            
            $input.datepicker({
                dateFormat: 'dd-mm-yy',
                minDate: isCheckout ? new Date(minDate.getTime() + 86400000) : minDate,
                numberOfMonths: window.innerWidth > 768 ? 2 : 1,
                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        inst.dpDiv.css({ 'z-index': 9999 });
                    }, 0);
                },
                onSelect: function(dateText) {
                    // If this is a check-in field, update the checkout min date
                    if ($input.hasClass('tc-datepicker-checkin')) {
                        const $checkout = $widget.find('.tc-datepicker-checkout');
                        if ($checkout.length) {
                            const checkinDate = $input.datepicker('getDate');
                            const minCheckout = new Date(checkinDate);
                            minCheckout.setDate(minCheckout.getDate() + 1);
                            $checkout.datepicker('option', 'minDate', minCheckout);
                            
                            // Auto-open checkout picker
                            setTimeout(function() {
                                $checkout.datepicker('show');
                            }, 100);
                        }
                    }
                }
            });
        });
    }
    
    /**
     * Room selector
     */
    function initRoomSelector($widget, widgetId) {
        const $trigger = $widget.find('.tc-room-trigger');
        const $dropdown = $widget.find('.tc-room-dropdown');
        
        if (!$trigger.length || !$dropdown.length) return;
        
        // Toggle dropdown
        $trigger.on('click', function(e) {
            e.stopPropagation();
            $dropdown.toggleClass('open');
        });
        
        // Close on outside click
        $(document).on('click', function(e) {
            if (!$dropdown.is(e.target) && !$dropdown.has(e.target).length && !$trigger.is(e.target)) {
                $dropdown.removeClass('open');
            }
        });
        
        // Counter buttons
        $widget.find('.tc-counter-btn').on('click', function() {
            const $btn = $(this);
            const action = $btn.data('action');
            const target = $btn.data('target');
            const state = widgetStates[widgetId];
            
            if (action === 'increase') {
                if (target === 'adults' && state.adults < 9) state.adults++;
                if (target === 'children' && state.children < 6) state.children++;
                if (target === 'rooms' && state.rooms < 5) state.rooms++;
            } else {
                if (target === 'adults' && state.adults > 1) state.adults--;
                if (target === 'children' && state.children > 0) state.children--;
                if (target === 'rooms' && state.rooms > 1) state.rooms--;
            }
            
            updateRoomDisplay($widget, widgetId);
        });
        
        updateRoomDisplay($widget, widgetId);
    }
    
    function updateRoomDisplay($widget, widgetId) {
        const state = widgetStates[widgetId];
        
        $widget.find('.tc-adults-value').text(state.adults);
        $widget.find('.tc-children-value').text(state.children);
        $widget.find('.tc-rooms-value').text(state.rooms);
        
        let summary = state.adults + ' Volwassene' + (state.adults > 1 ? 'n' : '');
        if (state.children > 0) {
            summary += ', ' + state.children + ' Kind' + (state.children > 1 ? 'eren' : '');
        }
        summary += ', ' + state.rooms + ' Kamer' + (state.rooms > 1 ? 's' : '');
        
        $widget.find('.tc-room-summary').text(summary);
    }
    
    /**
     * Autocomplete
     */
    function initAutocomplete($widget) {
        // Origin (airports)
        $widget.find('.tc-autocomplete-origin').autocomplete({
            source: function(request, response) {
                const term = request.term.toLowerCase();
                const filtered = commonAirports.filter(function(a) {
                    return a.label.toLowerCase().indexOf(term) !== -1;
                });
                response(filtered);
            },
            minLength: 1,
            select: function(event, ui) {
                $(this).siblings('.tc-origin-code').val(ui.item.code);
            }
        });
        
        // Destination
        $widget.find('.tc-autocomplete-dest').autocomplete({
            source: function(request, response) {
                // Try API first, fallback to empty
                $.ajax({
                    url: config.baseUrl + '/api/autocomplete',
                    dataType: 'json',
                    data: { term: request.term, type: 'destination' },
                    success: function(data) {
                        response(data.slice(0, 10));
                    },
                    error: function() {
                        response([]);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $(this).siblings('.tc-dest-code').val(ui.item.code || ui.item.value);
            }
        });
        
        // Car rental locations
        $widget.find('.tc-autocomplete-car').autocomplete({
            source: function(request, response) {
                const term = request.term.toLowerCase();
                // Use airports as car rental locations too
                const filtered = commonAirports.filter(function(a) {
                    return a.label.toLowerCase().indexOf(term) !== -1;
                });
                response(filtered);
            },
            minLength: 1
        });
    }
    
    /**
     * Submit buttons
     */
    function initSubmitButtons($widget, widgetId) {
        // Flight + Hotel
        $widget.find('.tc-submit-flight-hotel').on('click', function() {
            submitFlightHotel($widget, widgetId);
        });
        
        // Hotel only
        $widget.find('.tc-submit-hotel').on('click', function() {
            submitHotel($widget, widgetId);
        });
        
        // Flight only
        $widget.find('.tc-submit-flight').on('click', function() {
            submitFlight($widget, widgetId);
        });
        
        // Car rental
        $widget.find('.tc-submit-car').on('click', function() {
            submitCar($widget, widgetId);
        });
        
        // Routing / Fly & Drive
        $widget.find('.tc-submit-routing').on('click', function() {
            submitRouting($widget, widgetId);
        });
        
        // Trip Planner
        $widget.find('.tc-submit-tripplanner').on('click', function() {
            submitTripPlanner($widget, widgetId);
        });
        
        // AI Planner
        $widget.find('.tc-submit-ai').on('click', function() {
            submitAI($widget, widgetId);
        });
    }
    
    /**
     * Format date for URL (dd-mm-yy to yyyy-mm-dd)
     */
    function formatDateForUrl(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return dateStr;
    }
    
    /**
     * Build distribution string
     */
    function buildDistribution(widgetId) {
        const state = widgetStates[widgetId];
        let dist = '';
        for (let i = 0; i < state.rooms; i++) {
            if (i > 0) dist += '|';
            dist += state.adults;
            if (state.children > 0 && state.childAges.length > 0) {
                dist += '-' + state.childAges.slice(0, state.children).join(',');
            }
        }
        return dist || state.adults.toString();
    }
    
    /**
     * Submit: Flight + Hotel
     */
    function submitFlightHotel($widget, widgetId) {
        const origin = $widget.find('.tc-fh-origin').val();
        const destination = $widget.find('.tc-fh-destination').val();
        const checkin = $widget.find('.tc-fh-checkin').val();
        const checkout = $widget.find('.tc-fh-checkout').val();
        
        if (!origin) { alert('Selecteer een vertrekplaats'); return; }
        if (!destination) { alert('Selecteer een bestemming'); return; }
        if (!checkin) { alert('Selecteer een check-in datum'); return; }
        if (!checkout) { alert('Selecteer een check-out datum'); return; }
        
        const params = new URLSearchParams({
            origin: $widget.find('.tc-fh-origin-code').val() || origin,
            destination: $widget.find('.tc-fh-dest-code').val() || destination,
            departureDate: formatDateForUrl(checkin),
            returnDate: formatDateForUrl(checkout),
            distribution: buildDistribution(widgetId),
            language: config.language,
            currency: config.currency
        });
        
        window.open(config.baseUrl + '/flight-hotel?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: Hotel only
     */
    function submitHotel($widget, widgetId) {
        const destination = $widget.find('.tc-hotel-destination').val();
        const checkin = $widget.find('.tc-hotel-checkin').val();
        const checkout = $widget.find('.tc-hotel-checkout').val();
        
        if (!destination) { alert('Selecteer een bestemming'); return; }
        if (!checkin) { alert('Selecteer een check-in datum'); return; }
        if (!checkout) { alert('Selecteer een check-out datum'); return; }
        
        const params = new URLSearchParams({
            destination: $widget.find('.tc-hotel-dest-code').val() || destination,
            departureDate: formatDateForUrl(checkin),
            returnDate: formatDateForUrl(checkout),
            distribution: buildDistribution(widgetId),
            language: config.language,
            currency: config.currency
        });
        
        window.open(config.baseUrl + '/hotel?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: Flight only
     */
    function submitFlight($widget, widgetId) {
        const origin = $widget.find('.tc-flight-origin').val();
        const destination = $widget.find('.tc-flight-destination').val();
        const departure = $widget.find('.tc-flight-departure').val();
        const returnDate = $widget.find('.tc-flight-return').val();
        
        if (!origin) { alert('Selecteer een vertrekplaats'); return; }
        if (!destination) { alert('Selecteer een bestemming'); return; }
        if (!departure) { alert('Selecteer een vertrekdatum'); return; }
        
        const state = widgetStates[widgetId];
        const params = new URLSearchParams({
            origin: $widget.find('.tc-flight-origin-code').val() || origin,
            destination: $widget.find('.tc-flight-dest-code').val() || destination,
            departureDate: formatDateForUrl(departure),
            adults: state.adults,
            children: state.children,
            language: config.language,
            currency: config.currency
        });
        
        if (returnDate) {
            params.append('returnDate', formatDateForUrl(returnDate));
        }
        
        window.open(config.baseUrl + '/flight?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: Car rental
     */
    function submitCar($widget, widgetId) {
        const pickup = $widget.find('.tc-car-pickup').val();
        const pickupDate = $widget.find('.tc-car-pickup-date').val();
        const returnDate = $widget.find('.tc-car-return-date').val();
        
        if (!pickup) { alert('Selecteer een ophaallocatie'); return; }
        if (!pickupDate) { alert('Selecteer een ophaaldatum'); return; }
        if (!returnDate) { alert('Selecteer een inleverdatum'); return; }
        
        const params = new URLSearchParams({
            pickupLocation: pickup,
            dropoffLocation: pickup,
            pickupDate: formatDateForUrl(pickupDate),
            dropoffDate: formatDateForUrl(returnDate),
            pickupTime: '10:00',
            dropoffTime: '10:00',
            language: config.language,
            currency: config.currency
        });
        
        window.open(config.baseUrl + '/car-rental?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: Routing / Fly & Drive
     */
    function submitRouting($widget, widgetId) {
        const origin = $widget.find('.tc-routing-origin').val();
        const destination = $widget.find('.tc-routing-destination').val();
        const departure = $widget.find('.tc-routing-departure').val();
        
        if (!origin) { alert('Selecteer een vertrekplaats'); return; }
        if (!destination) { alert('Selecteer een bestemming'); return; }
        if (!departure) { alert('Selecteer een vertrekdatum'); return; }
        
        const params = new URLSearchParams({
            origin: $widget.find('.tc-routing-origin-code').val() || origin,
            destination: $widget.find('.tc-routing-dest-code').val() || destination,
            departureDate: formatDateForUrl(departure),
            routingType: 'FLIGHT_PLUS_CAR',
            distribution: buildDistribution(widgetId),
            language: config.language,
            currency: config.currency
        });
        
        window.open(config.baseUrl + '/routing?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: Trip Planner
     */
    function submitTripPlanner($widget, widgetId) {
        const departure = $widget.find('.tc-tripplanner-departure').val();
        
        if (!departure) { alert('Selecteer een vertrekdatum'); return; }
        
        const params = new URLSearchParams({
            departureDate: formatDateForUrl(departure),
            distribution: buildDistribution(widgetId),
            language: config.language,
            currency: config.currency
        });
        
        window.open(config.baseUrl + '/tripplanner?' + params.toString(), '_blank');
    }
    
    /**
     * Submit: AI Planner
     */
    function submitAI($widget, widgetId) {
        const description = $widget.find('.tc-ai-description').val();
        
        if (!description) { alert('Beschrijf je droomreis'); return; }
        
        const params = new URLSearchParams({
            description: description,
            language: config.language
        });
        
        window.open(config.baseUrl + '/ai-trip?' + params.toString(), '_blank');
    }
    
    // Initialize on DOM ready
    $(document).ready(init);
    
    // Expose for external use
    window.TCWidgets = {
        init: init,
        getState: function(widgetId) {
            return widgetStates[widgetId];
        },
        setConfig: function(newConfig) {
            Object.assign(config, newConfig);
        }
    };
    
})(jQuery);
