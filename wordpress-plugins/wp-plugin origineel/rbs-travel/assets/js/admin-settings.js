jQuery(document).ready(function($) {
    console.log('RRP System Settings Script Loaded!');
    
    // Initialize WordPress color picker
    $('.color-picker').wpColorPicker({
        change: function(event, ui) {
            // Optional: Live preview in future
        }
    });
    
    // API Credentials Accordion
    var setCounter = parseInt($('#rrp-set-counter').val()) || 1;
    
    // Toggle accordion
    $(document).on('click', '.rrp-api-set-header', function(e) {
        if ($(e.target).closest('.rrp-remove-set').length) {
            return; // Don't toggle if clicking remove button
        }
        
        var $header = $(this);
        var $content = $header.next('.rrp-api-set-content');
        var $icon = $header.find('.rrp-toggle-icon');
        
        // Toggle this accordion
        $content.slideToggle(200);
        $icon.toggleClass('active');
        $header.toggleClass('active');
    });
    
    // Update display name when name field changes
    $(document).on('input', '.rrp-set-name', function() {
        var newName = $(this).val() || 'Unnamed API Set';
        $(this).closest('.rrp-api-set').find('.rrp-set-name-display').text(newName);
        
        // Update dropdown option
        var setId = $(this).closest('.rrp-api-set').data('set-id');
        $('#active_api_set option[value="' + setId + '"]').text(newName);
    });
    
    // Add new API set
    $('#rrp-add-api-set').on('click', function(e) {
        e.preventDefault();
        console.log('Add API button clicked!');
        
        setCounter++;
        var newSetId = 'set_' + setCounter;
        var newSetName = 'API Set ' + setCounter;
        
        var newSetHtml = '<div class="rrp-api-set" data-set-id="' + newSetId + '">' +
            '<div class="rrp-api-set-header">' +
                '<span class="rrp-toggle-icon dashicons dashicons-arrow-down-alt2"></span>' +
                '<strong class="rrp-set-name-display">' + newSetName + '</strong>' +
                '<button type="button" class="button button-small rrp-remove-set" style="float: right; margin-top: -3px;">' +
                    '<span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>' +
                '</button>' +
            '</div>' +
            '<div class="rrp-api-set-content" style="display: block;">' +
                '<table class="form-table">' +
                    '<tr>' +
                        '<th style="width: 200px;"><label>Naam</label></th>' +
                        '<td>' +
                            '<input type="text" class="regular-text rrp-set-name" name="rbstravel_settings[api_credentials][' + newSetId + '][name]" value="' + newSetName + '" placeholder="Bijv: Robas Main" />' +
                            '<p class="description">Geef deze API credential set een herkenbare naam</p>' +
                        '</td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label>API Gebruikersnaam</label></th>' +
                        '<td><input type="text" class="regular-text" name="rbstravel_settings[api_credentials][' + newSetId + '][username]" value="" autocomplete="off" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label>API Wachtwoord</label></th>' +
                        '<td><input type="password" class="regular-text" name="rbstravel_settings[api_credentials][' + newSetId + '][password]" value="" autocomplete="off" /></td>' +
                    '</tr>' +
                    '<tr>' +
                        '<th><label>API MicrositeID</label></th>' +
                        '<td><input type="text" class="regular-text" name="rbstravel_settings[api_credentials][' + newSetId + '][micrositeid]" value="" autocomplete="off" /></td>' +
                    '</tr>' +
                '</table>' +
            '</div>' +
        '</div>';
        
        $('#rrp-api-credentials-container').append(newSetHtml);
        
        // Add to dropdown
        $('#active_api_set').append('<option value="' + newSetId + '">' + newSetName + '</option>');
        
        // Update counter
        $('#rrp-set-counter').val(setCounter);
        
        // Show success message
        $('<div class="notice notice-success is-dismissible"><p>✅ Nieuwe API credential set toegevoegd. Vergeet niet op te slaan!</p></div>')
            .insertAfter('h1')
            .delay(3000)
            .fadeOut();
        
        console.log('New API set added: ' + newSetId);
    });
    
    // Remove API set
    $(document).on('click', '.rrp-remove-set', function(e) {
        e.stopPropagation();
        
        var $apiSet = $(this).closest('.rrp-api-set');
        var setId = $apiSet.data('set-id');
        var setName = $apiSet.find('.rrp-set-name-display').text();
        
        // Don't allow removing if it's the only one
        if ($('.rrp-api-set').length === 1) {
            alert('Je moet minimaal één API credential set hebben!');
            return;
        }
        
        if (confirm('Weet je zeker dat je "' + setName + '" wilt verwijderen?')) {
            $apiSet.fadeOut(300, function() {
                $(this).remove();
                
                // Remove from dropdown
                $('#active_api_set option[value="' + setId + '"]').remove();
                
                // If this was the active set, select the first available
                if ($('#active_api_set').val() === setId) {
                    $('#active_api_set option:first').prop('selected', true);
                }
            });
        }
    });
});
