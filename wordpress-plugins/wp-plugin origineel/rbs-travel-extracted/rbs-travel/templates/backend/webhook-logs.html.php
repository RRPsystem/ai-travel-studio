<?php
/**
 * Webhook Logs Admin Page
 * Shows recent webhook calls for debugging
 */

defined('RBS_TRAVEL') or die();

global $wpdb;

// In multisite, logs are stored on main site
if (is_multisite()) {
    $table_name = $wpdb->base_prefix . 'rbstravel_webhook_log';
} else {
    $table_name = $wpdb->prefix . 'rbstravel_webhook_log';
}

// Get recent webhook logs
$logs = $wpdb->get_results(
    "SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT 50",
    ARRAY_A
);

?>
<div class="wrap">
    <h1>🔗 Webhook Logs (Debug)</h1>
    
    <p class="description">
        Laatste 50 webhook calls van Travel Compositor. Gebruik dit om te debuggen waarom reizen niet automatisch importeren.
    </p>
    
    <?php if (empty($logs)): ?>
        <div class="notice notice-warning">
            <p><strong>Geen webhook logs gevonden.</strong></p>
            <p>Dit betekent dat Travel Compositor nog geen webhook calls heeft gestuurd, of dat de webhook endpoint niet bereikbaar is.</p>
        </div>
    <?php else: ?>
        <table class="wp-list-table widefat fixed striped" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 150px;">Datum/Tijd</th>
                    <th style="width: 100px;">API Set</th>
                    <th style="width: 80px;">Status</th>
                    <th>Payload</th>
                    <th>Response</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <?php
                    $payload = json_decode($log['payload'], true);
                    $response = json_decode($log['response'], true);
                    $idea_ids = isset($payload['ideaIds']) ? $payload['ideaIds'] : array();
                    $status_class = $log['status_code'] == 200 ? 'success' : 'error';
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html(date('d-m-Y H:i:s', strtotime($log['created_at']))); ?></strong>
                        </td>
                        <td>
                            <code><?php echo esc_html($log['api_set']); ?></code>
                        </td>
                        <td>
                            <span class="dashicons dashicons-<?php echo $status_class == 'success' ? 'yes-alt' : 'warning'; ?>" style="color: <?php echo $status_class == 'success' ? 'green' : 'red'; ?>;"></span>
                            <?php echo esc_html($log['status_code']); ?>
                        </td>
                        <td>
                            <?php if (!empty($idea_ids)): ?>
                                <strong><?php echo count($idea_ids); ?> Idea(s):</strong>
                                <ul style="margin: 5px 0; padding-left: 20px;">
                                    <?php foreach (array_slice($idea_ids, 0, 5) as $id): ?>
                                        <li><code><?php echo esc_html($id); ?></code></li>
                                    <?php endforeach; ?>
                                    <?php if (count($idea_ids) > 5): ?>
                                        <li><em>... en <?php echo count($idea_ids) - 5; ?> meer</em></li>
                                    <?php endif; ?>
                                </ul>
                            <?php else: ?>
                                <em style="color: #999;">Test request (geen ideaIds)</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($response): ?>
                                <?php if (isset($response['message'])): ?>
                                    <strong><?php echo esc_html($response['message']); ?></strong>
                                <?php endif; ?>
                                
                                <?php if (isset($response['results'])): ?>
                                    <details style="margin-top: 5px;">
                                        <summary style="cursor: pointer; color: #0073aa;">Details tonen</summary>
                                        <pre style="background: #f5f5f5; padding: 10px; margin-top: 5px; overflow-x: auto; font-size: 11px;"><?php echo esc_html(json_encode($response['results'], JSON_PRETTY_PRINT)); ?></pre>
                                    </details>
                                <?php endif; ?>
                            <?php else: ?>
                                <em style="color: #999;">Geen response</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <div class="card" style="max-width: 800px; margin-top: 30px; padding: 20px;">
        <h2>🔍 Troubleshooting</h2>
        
        <h3>1. Geen logs zichtbaar?</h3>
        <ul>
            <li>✅ Check of de webhook URL correct is ingesteld in Travel Compositor</li>
            <li>✅ Test de webhook in TC met de "Try Notification" knop</li>
            <li>✅ Check of de webhook enabled is in Network Admin → RRP System</li>
        </ul>
        
        <h3>2. Logs wel zichtbaar, maar reizen niet geïmporteerd?</h3>
        <ul>
            <li>✅ Check of "Automatisch reizen importeren via webhook" is aangevinkt</li>
            <li>✅ Check of de subsite toegang heeft tot de API set (Network Admin → Site Permissies)</li>
            <li>✅ Check de Response kolom voor error messages</li>
        </ul>
        
        <h3>3. Webhook URLs</h3>
        <p>Ga naar <strong>Network Admin → RRP System</strong> om de webhook URLs te zien en te kopiëren.</p>
    </div>
</div>
