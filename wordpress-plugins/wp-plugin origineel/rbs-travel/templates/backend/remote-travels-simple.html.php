<?php
/**
 * Simple Travel Import Page
 * Import travel by ID only
 */

// Get API credential sets - handle multisite vs single site
if (is_multisite() && class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Network_Settings')) {
    // Multisite: Get credentials from network settings (with password for API calls)
    $api_credentials = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Network_Settings::get_site_api_credentials(true);
    $default_active_set = get_option('rbstravel_site_active_api', '');
    
    // If no active set selected, use first available
    if (empty($default_active_set) && !empty($api_credentials)) {
        $default_active_set = array_key_first($api_credentials);
    }
} else {
    // Single site: use local credentials
    $api_credentials = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('api_credentials', array());
    $default_active_set = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('active_api_set', '');
}

// Handle import
$import_message = '';
$import_status = '';

if (isset($_POST['import_travel_id']) && !empty($_POST['import_travel_id'])) {
    $travel_id = sanitize_text_field($_POST['import_travel_id']);
    $selected_api_set = isset($_POST['selected_api_set']) ? sanitize_text_field($_POST['selected_api_set']) : '';
    
    // Determine which API set to use
    if (!empty($selected_api_set) && isset($api_credentials[$selected_api_set])) {
        $use_api_set = $selected_api_set;
    } else {
        $use_api_set = $default_active_set;
    }
    
    // Get the credentials for the selected API set
    if (isset($api_credentials[$use_api_set])) {
        $credentials = $api_credentials[$use_api_set];
        $api_name = isset($credentials['name']) ? $credentials['name'] : 'Unknown';
        
        // Temporarily override the active API set for this import
        update_option('rbstravel_settings_temp_active_api_set', $use_api_set);
        
        try {
            // Create API instance (it will use the temporarily set active API)
            $RBS_TRAVEL_Api = new \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Api();
            
            // DEBUG: Log API credentials being used
            error_log('=== RRP IMPORT DEBUG ===');
            error_log('Using API Set: ' . $use_api_set);
            error_log('API Name: ' . $api_name);
            error_log('Travel ID: ' . $travel_id);
            
            // Get travel info and details from API
            $travel_info = $RBS_TRAVEL_Api->GetInfo($travel_id);
            $travel_details = $RBS_TRAVEL_Api->GetDetails($travel_id);
            
            // DEBUG: Log API response
            error_log('Travel Info Keys: ' . (is_array($travel_info) ? implode(', ', array_keys($travel_info)) : 'NOT AN ARRAY'));
            error_log('Travel Info Title: ' . (isset($travel_info['title']) ? $travel_info['title'] : 'NO TITLE'));
            error_log('Travel Details Keys: ' . (is_array($travel_details) ? implode(', ', array_keys($travel_details)) : 'NOT AN ARRAY'));
            
            // Check if we have valid data
            if (empty($travel_info) || !isset($travel_info['id'])) {
                $import_status = 'error';
                
                // Check if it's a 404 error
                if (isset($travel_info['status']) && $travel_info['status'] == 'NOT_FOUND') {
                    $import_message = '❌ <strong>Reis niet gevonden (HTTP 404)</strong>';
                    $import_message .= '<br><br>📋 <strong>Gegevens:</strong>';
                    $import_message .= '<br>• <strong>Reis ID:</strong> ' . esc_html($travel_id);
                    $import_message .= '<br>• <strong>API Set:</strong> ' . esc_html($api_name);
                    $import_message .= '<br>• <strong>Microsite:</strong> ' . esc_html($credentials['micrositeid']);
                    $import_message .= '<br><br>🔍 <strong>Mogelijke oorzaken:</strong>';
                    $import_message .= '<br>• Reis ID is van een <strong>ander microsite</strong>';
                    $import_message .= '<br>• Reis is een <strong>OFFER</strong> in plaats van een IDEA/PACKAGE';
                    $import_message .= '<br>• Reis is niet gepubliceerd of inactief';
                    $import_message .= '<br>• ID is verkeerd getypt';
                    $import_message .= '<br><br>💡 <strong>Tip:</strong> Controleer in Travel Compositor:';
                    $import_message .= '<br>1. Is dit een <strong>Travel Idea</strong> of <strong>Package</strong>?';
                    $import_message .= '<br>2. Is de reis gekoppeld aan microsite: <code>' . esc_html($credentials['micrositeid']) . '</code>?';
                    $import_message .= '<br>3. Probeer een ander reis ID uit hetzelfde microsite';
                } else {
                    $import_message = '❌ Fout: Geen data ontvangen van API. Controleer:';
                    $import_message .= '<br>• API credentials correct?';
                    $import_message .= '<br>• Reis ID bestaat?';
                    $import_message .= '<br>• API bereikbaar?';
                    $import_message .= '<br><small>Check error_log voor details</small>';
                }
            } else {
                // Create WordPress post from API data
                $result = \RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Import::CreatePost($travel_info, $travel_details);
                
                if (is_numeric($result) && $result > 0) {
                    $import_status = 'success';
                    $post_edit_link = get_edit_post_link($result);
                    $import_message = '✅ Reis succesvol geïmporteerd via <strong>' . esc_html($api_name) . '</strong>! ID: ' . $travel_id;
                    $import_message .= '<br>📝 Titel: ' . esc_html($travel_info['title']);
                    if ($post_edit_link) {
                        $import_message .= ' <a href="' . esc_url($post_edit_link) . '" class="button button-small">✏️ Bewerken</a>';
                    }
                } else {
                    $import_status = 'error';
                    $error_msg = is_wp_error($result) ? $result->get_error_message() : 'Onbekende fout';
                    $import_message = '❌ Fout bij importeren: ' . esc_html($error_msg);
                }
            }
        } catch (Exception $e) {
            $import_status = 'error';
            $import_message = '❌ Fout: ' . esc_html($e->getMessage()) . '<br>';
            $import_message .= '<small>File: ' . esc_html($e->getFile()) . ' (Line: ' . $e->getLine() . ')</small>';
        } catch (Error $e) {
            $import_status = 'error';
            $import_message = '❌ PHP Error: ' . esc_html($e->getMessage()) . '<br>';
            $import_message .= '<small>File: ' . esc_html($e->getFile()) . ' (Line: ' . $e->getLine() . ')</small>';
        } finally {
            // Always cleanup temporary option
            delete_option('rbstravel_settings_temp_active_api_set');
        }
    } else {
        $import_status = 'error';
        $import_message = '❌ Geselecteerde API credentials niet gevonden.';
    }
}
?>

<div class="wrap">
    <h1>📥 Importeren</h1>
    
    <div class="rbstravel-import-intro" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 20px; margin: 20px 0; max-width: 800px;">
        <h2 style="margin-top: 0;">Remote Travels</h2>
        <p style="font-size: 14px; line-height: 1.6;">
            Importeer reizen uit de <strong>Rondreis Planner</strong> en/of <strong>Travel Compositor</strong>.<br>
            Vul het ID nummer van de reis in en klik op importeer.
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #666;">
            <strong>Let op:</strong> Daarna kun je de reis aanpassen en publiceren bij <em>Travel Ideas</em>.
        </p>
    </div>

    <!-- Result Area (for AJAX responses) -->
    <div id="import-result-area" class="notice is-dismissible" style="max-width: 800px; display: none;">
        <p></p>
    </div>
    
    <?php if ($import_message): ?>
        <div class="notice notice-<?php echo $import_status; ?> is-dismissible" style="max-width: 800px;">
            <p><?php echo $import_message; ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Progress Overlay -->
    <div id="rrp-import-overlay" style="display: none;">
        <div class="rrp-import-modal">
            <div class="rrp-import-modal-content">
                <div class="rrp-import-spinner-large"></div>
                <h3>Reis importeren...</h3>
                <p id="rrp-progress-text">Verbinden met API...</p>
                <div class="rrp-progress-container">
                    <div id="rrp-progress-bar" class="rrp-progress-bar"></div>
                </div>
                <span id="rrp-progress-pct" class="rrp-progress-pct">0%</span>
            </div>
        </div>
    </div>

    <!-- Sync All Section -->
    <div class="rbstravel-sync-all" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 30px; margin: 20px 0; max-width: 800px;">
        <h2 style="margin-top: 0;">🔄 Synchroniseer Alle Reizen</h2>
        <p style="font-size: 14px; line-height: 1.6; color: #666; margin-bottom: 20px;">
            Importeer automatisch alle reizen van een API set. Nieuwe reizen worden toegevoegd, bestaande reizen worden bijgewerkt.
        </p>
        
        <?php foreach ($api_credentials as $set_id => $credentials): ?>
            <?php 
            $api_name = isset($credentials['name']) ? esc_html($credentials['name']) : 'Unnamed API';
            $microsite_id = isset($credentials['micrositeid']) ? esc_html($credentials['micrositeid']) : 'Unknown';
            ?>
            <div class="rrp-sync-api-item" style="border: 1px solid #dcdcde; border-radius: 4px; padding: 20px; margin-bottom: 15px; background: #f9f9f9;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0 0 5px 0; font-size: 16px;">🌐 <?php echo $api_name; ?></h3>
                        <p style="margin: 0; font-size: 13px; color: #666;">
                            Microsite ID: <code><?php echo $microsite_id; ?></code>
                        </p>
                    </div>
                    <button 
                        type="button" 
                        class="button button-primary rrp-sync-all-btn" 
                        data-api-set="<?php echo esc_attr($set_id); ?>"
                        data-api-name="<?php echo esc_attr($api_name); ?>"
                        style="font-size: 14px; padding: 8px 20px; height: auto;"
                    >
                        🔄 Synchroniseer Alle Reizen
                    </button>
                </div>
                <div class="rrp-sync-progress" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
                    <div class="rrp-sync-status" style="font-size: 14px; margin-bottom: 10px;"></div>
                    <div style="background: #e0e0e0; border-radius: 10px; height: 20px; overflow: hidden;">
                        <div class="rrp-sync-progress-bar" style="background: #2271b1; height: 100%; width: 0%; transition: width 0.3s;"></div>
                    </div>
                    <div class="rrp-sync-results" style="margin-top: 10px; font-size: 13px;"></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="rbstravel-import-form" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; padding: 30px; margin: 20px 0; max-width: 800px;">
        <h2 style="margin-top: 0;">📥 Importeer Enkele Reis</h2>
        <form method="post" action="" id="rbstravel-import-form">
            <table class="form-table" role="presentation">
                <tbody>
                    <?php if (!empty($api_credentials) && count($api_credentials) > 1): ?>
                    <tr>
                        <th scope="row" style="width: 200px; vertical-align: top; padding-top: 12px;">
                            <label style="font-weight: 600;">
                                🔌 <?php _e('Selecteer API', 'rbs-travel'); ?>
                            </label>
                        </th>
                        <td>
                            <div class="rrp-api-buttons">
                                <?php foreach ($api_credentials as $set_id => $credentials): ?>
                                    <?php 
                                    $is_active = ($set_id === $default_active_set);
                                    $api_name = isset($credentials['name']) ? esc_html($credentials['name']) : 'Unnamed API';
                                    ?>
                                    <button 
                                        type="button" 
                                        class="rrp-api-btn <?php echo $is_active ? 'active' : ''; ?>" 
                                        data-api-set="<?php echo esc_attr($set_id); ?>"
                                    >
                                        <span class="rrp-api-icon">🌐</span>
                                        <span class="rrp-api-name"><?php echo $api_name; ?></span>
                                        <span class="rrp-api-check">✓</span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" id="selected_api_set" name="selected_api_set" value="<?php echo esc_attr($default_active_set); ?>" />
                            <p class="description" style="margin-top: 12px;">
                                Kies van welke API je de reis wilt importeren.
                            </p>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr>
                        <th scope="row" style="width: 200px;">
                            <label for="import_travel_id" style="font-weight: 600;">
                                <?php _e('Reis ID Nummer', 'rbs-travel'); ?>
                            </label>
                        </th>
                        <td>
                            <input 
                                type="text" 
                                id="import_travel_id" 
                                name="import_travel_id" 
                                class="regular-text" 
                                placeholder="Bijv: 41023443"
                                style="font-size: 16px; padding: 8px 12px; width: 300px;"
                                required
                            />
                            <p class="description">
                                Vul het ID nummer in van de reis die je wilt importeren.
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>

            <p class="submit" style="padding-left: 200px;">
                <button type="submit" class="button button-primary button-large" style="font-size: 14px; padding: 8px 24px;">
                    📥 <?php _e('Importeer Reis', 'rbs-travel'); ?>
                </button>
            </p>

            <?php wp_nonce_field('rbstravel_import_travel', 'rbstravel_import_nonce'); ?>
        </form>
    </div>

    <div class="rbstravel-import-help" style="background: #f0f6fc; border-left: 4px solid #0073aa; padding: 15px 20px; margin: 20px 0; max-width: 800px;">
        <h3 style="margin-top: 0; color: #0073aa;">ℹ️ Waar vind ik het Reis ID?</h3>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Rondreis Planner:</strong> Het ID staat in de URL of in de reis details</li>
            <li><strong>Travel Compositor:</strong> Het ID vind je in de lijst met reizen</li>
            <li>Het ID is meestal een nummer van 6-8 cijfers (bijv: 41023443)</li>
        </ul>
    </div>

    <div class="rbstravel-import-next" style="background: #f0f0f1; border-radius: 4px; padding: 15px 20px; margin: 20px 0; max-width: 800px;">
        <h3 style="margin-top: 0;">📝 Na het importeren</h3>
        <ol style="margin: 10px 0; padding-left: 20px; line-height: 1.8;">
            <li>Ga naar <strong>Travel Ideas</strong> in het menu</li>
            <li>Zoek de zojuist geïmporteerde reis</li>
            <li>Pas de reis aan naar wens (tekst, foto's, prijzen)</li>
            <li>Publiceer de reis op je website</li>
        </ol>
    </div>

</div>

<style>
.rbstravel-import-intro h2 {
    color: #1d2327;
    font-size: 18px;
    margin-bottom: 15px;
}

.rbstravel-import-form input[type="text"]:focus {
    border-color: #2271b1;
    box-shadow: 0 0 0 1px #2271b1;
}

.button-primary.button-large {
    height: auto !important;
    line-height: 1.5 !important;
}

.rbstravel-import-help ul li,
.rbstravel-import-next ol li {
    margin-bottom: 8px;
    font-size: 14px;
}

/* API Selector Buttons */
.rrp-api-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-start;
}

.rrp-api-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: #f6f7f7;
    border: 2px solid #dcdcde;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
    font-weight: 500;
    color: #2c3338;
}

.rrp-api-btn:hover {
    background: #fff;
    border-color: #2271b1;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.rrp-api-btn.active {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
}

.rrp-api-btn.active:hover {
    background: #135e96;
    border-color: #135e96;
}

.rrp-api-icon {
    font-size: 18px;
    line-height: 1;
}

.rrp-api-name {
    font-size: 14px;
    line-height: 1.4;
}

.rrp-api-check {
    opacity: 0;
    font-size: 16px;
    font-weight: 700;
    margin-left: 4px;
    transition: opacity 0.2s ease;
}

.rrp-api-btn.active .rrp-api-check {
    opacity: 1;
}

/* Spinner in button */
.rrp-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: rrp-spin 0.8s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

@keyframes rrp-spin {
    to { transform: rotate(360deg); }
}

/* Progress Overlay */
#rrp-import-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rrp-import-modal {
    background: #fff;
    border-radius: 12px;
    padding: 40px 50px;
    text-align: center;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    max-width: 400px;
    width: 90%;
}

.rrp-import-modal h3 {
    margin: 20px 0 10px;
    font-size: 20px;
    color: #1d2327;
}

.rrp-import-modal p {
    color: #666;
    margin: 0 0 20px;
    font-size: 14px;
}

/* Large spinner */
.rrp-import-spinner-large {
    width: 60px;
    height: 60px;
    border: 4px solid #e5e7eb;
    border-radius: 50%;
    border-top-color: #2271b1;
    animation: rrp-spin 1s linear infinite;
    margin: 0 auto;
}

/* Progress bar */
.rrp-progress-container {
    background: #e5e7eb;
    border-radius: 10px;
    height: 12px;
    overflow: hidden;
    margin: 15px 0;
}

.rrp-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #2271b1, #135e96);
    border-radius: 10px;
    width: 0%;
    transition: width 0.4s ease;
}

.rrp-progress-pct {
    font-size: 13px;
    color: #666;
    font-weight: 600;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // API button click handler
    $('.rrp-api-btn').on('click', function() {
        $('.rrp-api-btn').removeClass('active');
        $(this).addClass('active');
        var apiSet = $(this).data('api-set');
        $('#selected_api_set').val(apiSet);
    });
    
    // AJAX Import Handler
    $('#rbstravel-import-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        var $resultArea = $('#import-result-area');
        var travelId = $('#import_travel_id').val();
        var apiSet = $('#selected_api_set').val();
        
        if (!travelId) {
            alert('Vul een Reis ID in');
            return;
        }
        
        // Show loading state
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="rrp-spinner"></span> Importeren...');
        
        // Show progress overlay
        $('#rrp-import-overlay').fadeIn(200);
        updateProgress(0, 'Verbinden met API...');
        
        // Start import via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rbstravel_import_with_progress',
                travel_id: travelId,
                api_set: apiSet,
                nonce: '<?php echo wp_create_nonce('rbstravel_ajax_import'); ?>'
            },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                return xhr;
            },
            success: function(response) {
                $('#rrp-import-overlay').fadeOut(200);
                $submitBtn.prop('disabled', false);
                $submitBtn.html('📥 <?php _e('Importeer Reis', 'rbs-travel'); ?>');
                
                if (response.success) {
                    showResult('success', response.data.message);
                    $('#import_travel_id').val('');
                } else {
                    showResult('error', response.data.message || 'Onbekende fout bij importeren');
                }
            },
            error: function(xhr, status, error) {
                $('#rrp-import-overlay').fadeOut(200);
                $submitBtn.prop('disabled', false);
                $submitBtn.html('📥 <?php _e('Importeer Reis', 'rbs-travel'); ?>');
                showResult('error', 'Verbindingsfout: ' + error);
            }
        });
        
        // Simulate progress updates
        var progress = 0;
        var progressSteps = [
            { pct: 15, msg: 'API token ophalen...' },
            { pct: 30, msg: 'Reis informatie ophalen...' },
            { pct: 50, msg: 'Reis details ophalen...' },
            { pct: 70, msg: 'WordPress post aanmaken...' },
            { pct: 85, msg: 'Afbeeldingen verwerken...' },
            { pct: 95, msg: 'Afronden...' }
        ];
        var stepIndex = 0;
        
        var progressInterval = setInterval(function() {
            if (stepIndex < progressSteps.length) {
                updateProgress(progressSteps[stepIndex].pct, progressSteps[stepIndex].msg);
                stepIndex++;
            } else {
                clearInterval(progressInterval);
            }
        }, 800);
    });
    
    function updateProgress(pct, msg) {
        $('#rrp-progress-bar').css('width', pct + '%');
        $('#rrp-progress-text').text(msg);
        $('#rrp-progress-pct').text(pct + '%');
    }
    
    function showResult(type, message) {
        var $result = $('#import-result-area');
        $result.removeClass('notice-success notice-error').addClass('notice-' + type);
        $result.find('p').html(message);
        $result.slideDown(300);
        
        // Scroll to result
        $('html, body').animate({
            scrollTop: $result.offset().top - 50
        }, 300);
    }
    
    // Sync All Travels Handler
    $('.rrp-sync-all-btn').on('click', function() {
        var $btn = $(this);
        var $container = $btn.closest('.rrp-sync-api-item');
        var $progress = $container.find('.rrp-sync-progress');
        var $status = $container.find('.rrp-sync-status');
        var $progressBar = $container.find('.rrp-sync-progress-bar');
        var $results = $container.find('.rrp-sync-results');
        
        var apiSet = $btn.data('api-set');
        var apiName = $btn.data('api-name');
        
        if (!confirm('Weet je zeker dat je alle reizen van "' + apiName + '" wilt synchroniseren?\n\nDit kan enkele minuten duren afhankelijk van het aantal reizen.')) {
            return;
        }
        
        // Show progress
        $btn.prop('disabled', true).html('<span class="rrp-spinner"></span> Synchroniseren...');
        $progress.slideDown(300);
        $status.html('🔄 Reizen ophalen van ' + apiName + '...');
        $progressBar.css('width', '10%');
        $results.html('');
        
        // Start sync via AJAX
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'rbstravel_sync_all_travels',
                api_set: apiSet,
                nonce: '<?php echo wp_create_nonce('rbstravel_sync_all'); ?>'
            },
            success: function(response) {
                $btn.prop('disabled', false).html('🔄 Synchroniseer Alle Reizen');
                
                if (response.success) {
                    $progressBar.css('width', '100%');
                    $status.html('✅ Synchronisatie voltooid!');
                    
                    var data = response.data;
                    var resultsHtml = '<strong>Resultaten:</strong><br>';
                    resultsHtml += '📊 Totaal in API: ' + (data.total_in_api || data.total) + ' reizen<br>';
                    resultsHtml += '📥 Opgehaald: ' + data.total + ' reizen<br>';
                    resultsHtml += '✅ Succesvol: ' + data.imported + ' reizen<br>';
                    if (data.updated > 0) {
                        resultsHtml += '🔄 Bijgewerkt: ' + data.updated + ' reizen<br>';
                    }
                    if (data.failed > 0) {
                        resultsHtml += '❌ Mislukt: ' + data.failed + ' reizen<br>';
                    }
                    resultsHtml += '⏱️ Duur: ' + data.duration + ' seconden';
                    
                    if (data.timeout_reached) {
                        resultsHtml += '<br><br>⚠️ <strong>Timeout bereikt:</strong> Sync gestopt na ' + data.processed + ' reizen. ';
                        if (data.remaining > 0) {
                            resultsHtml += 'Er zijn nog ' + data.remaining + ' reizen over. Klik opnieuw op sync om door te gaan.';
                        }
                    } else if (data.total_in_api && data.total < data.total_in_api) {
                        resultsHtml += '<br><br>⚠️ <strong>Let op:</strong> Er zijn meer reizen in de API (' + data.total_in_api + ') dan opgehaald (' + data.total + '). Mogelijk is er een API limiet.';
                    }
                    
                    $results.html(resultsHtml);
                    
                    // Show success message
                    showResult('success', '✅ Synchronisatie voltooid! ' + data.imported + ' reizen geïmporteerd van ' + apiName);
                } else {
                    $status.html('❌ Fout bij synchroniseren');
                    $results.html('Fout: ' + (response.data.message || 'Onbekende fout'));
                    showResult('error', response.data.message || 'Fout bij synchroniseren');
                }
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html('🔄 Synchroniseer Alle Reizen');
                $status.html('❌ Verbindingsfout');
                $results.html('Fout: ' + error);
                showResult('error', 'Verbindingsfout: ' + error);
            },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                
                // Progress updates (if server sends them)
                xhr.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        var percentComplete = (e.loaded / e.total) * 100;
                        $progressBar.css('width', percentComplete + '%');
                    }
                });
                
                return xhr;
            }
        });
    });
});
</script>
