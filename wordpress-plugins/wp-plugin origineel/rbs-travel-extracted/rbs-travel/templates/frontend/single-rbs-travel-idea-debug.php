<?php
/**
 * DEBUG TEMPLATE - Shows what's wrong
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>DEBUG MODE</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .debug-box { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { border-left: 4px solid #10b981; }
        .error { border-left: 4px solid #ef4444; }
        .warning { border-left: 4px solid #f59e0b; }
        h2 { margin-top: 0; color: #1f2937; }
        pre { background: #f9fafb; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .status.ok { background: #d1fae5; color: #065f46; }
        .status.fail { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>🔍 RRP SYSTEM DEBUG MODE</h1>
    
    <?php
    // Test 1: Basic PHP
    echo '<div class="debug-box success">';
    echo '<h2>✅ Test 1: PHP werkt</h2>';
    echo '<p>PHP versie: ' . phpversion() . '</p>';
    echo '</div>';
    
    // Test 2: WordPress
    echo '<div class="debug-box ' . (function_exists('get_header') ? 'success' : 'error') . '">';
    echo '<h2>' . (function_exists('get_header') ? '✅' : '❌') . ' Test 2: WordPress</h2>';
    if (function_exists('get_header')) {
        echo '<p><span class="status ok">OK</span> WordPress geladen</p>';
    } else {
        echo '<p><span class="status fail">FAIL</span> WordPress NIET geladen!</p>';
    }
    echo '</div>';
    
    // Test 3: Post ID
    global $post;
    echo '<div class="debug-box ' . (isset($post) ? 'success' : 'error') . '">';
    echo '<h2>' . (isset($post) ? '✅' : '❌') . ' Test 3: Post object</h2>';
    if (isset($post)) {
        echo '<p><span class="status ok">OK</span> Post ID: ' . $post->ID . '</p>';
        echo '<p>Post type: ' . get_post_type($post->ID) . '</p>';
        echo '<p>Post title: ' . get_the_title($post->ID) . '</p>';
    } else {
        echo '<p><span class="status fail">FAIL</span> Geen post gevonden!</p>';
    }
    echo '</div>';
    
    // Test 4: Plugin defines
    echo '<div class="debug-box ' . (defined('RBS_TRAVEL') ? 'success' : 'error') . '">';
    echo '<h2>' . (defined('RBS_TRAVEL') ? '✅' : '❌') . ' Test 4: Plugin defines</h2>';
    if (defined('RBS_TRAVEL')) {
        echo '<p><span class="status ok">OK</span> RBS_TRAVEL constant bestaat</p>';
        echo '<p>Plugin path: ' . (defined('RBS_TRAVEL_PLUGIN_PATH') ? RBS_TRAVEL_PLUGIN_PATH : 'NOT DEFINED') . '</p>';
    } else {
        echo '<p><span class="status fail">FAIL</span> Plugin niet correct geladen!</p>';
    }
    echo '</div>';
    
    // Test 5: Settings class
    echo '<div class="debug-box ' . (class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings') ? 'success' : 'error') . '">';
    echo '<h2>' . (class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings') ? '✅' : '❌') . ' Test 5: Settings class</h2>';
    if (class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings')) {
        echo '<p><span class="status ok">OK</span> Settings class bestaat</p>';
        try {
            $test_setting = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings::GetSetting('primary_color', '#6366f1');
            echo '<p>Test setting (primary_color): ' . $test_setting . '</p>';
        } catch (Exception $e) {
            echo '<p><span class="status fail">ERROR</span> Settings ophalen: ' . $e->getMessage() . '</p>';
        }
    } else {
        echo '<p><span class="status fail">FAIL</span> Settings class niet gevonden!</p>';
    }
    echo '</div>';
    
    // Test 6: Helper functions
    echo '<div class="debug-box ' . (function_exists('RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel') ? 'success' : 'error') . '">';
    echo '<h2>' . (function_exists('RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel') ? '✅' : '❌') . ' Test 6: Helper functions</h2>';
    if (function_exists('RBS_TRAVEL\HELPERS\rbstravel_get_expert_for_travel')) {
        echo '<p><span class="status ok">OK</span> Expert helper functie bestaat</p>';
    } else {
        echo '<p><span class="status fail">FAIL</span> Expert helper functie niet gevonden!</p>';
        echo '<p>Dit betekent dat de helper file niet is geladen.</p>';
    }
    echo '</div>';
    
    // Test 7: Post meta
    if (isset($post)) {
        echo '<div class="debug-box warning">';
        echo '<h2>📊 Test 7: Post meta data</h2>';
        
        $destinations = get_post_meta($post->ID, 'travel_destinations', true);
        $hotels = get_post_meta($post->ID, 'travel_hotels', true);
        $transports = get_post_meta($post->ID, 'travel_transports', true);
        
        echo '<p>Destinations: ' . (is_array($destinations) ? count($destinations) . ' items' : 'NONE') . '</p>';
        echo '<p>Hotels: ' . (is_array($hotels) ? count($hotels) . ' items' : 'NONE') . '</p>';
        echo '<p>Transports: ' . (is_array($transports) ? count($transports) . ' items' : 'NONE') . '</p>';
        
        echo '<details>';
        echo '<summary>Bekijk alle post meta</summary>';
        echo '<pre>';
        $all_meta = get_post_meta($post->ID);
        foreach ($all_meta as $key => $value) {
            if (strpos($key, 'travel_') === 0 || strpos($key, 'rbs') === 0) {
                echo $key . ': ' . (is_array($value[0]) ? 'Array(' . count(maybe_unserialize($value[0])) . ')' : 'Scalar') . "\n";
            }
        }
        echo '</pre>';
        echo '</details>';
        echo '</div>';
    }
    
    // Test 8: Memory & Limits
    echo '<div class="debug-box warning">';
    echo '<h2>⚙️ Test 8: Server info</h2>';
    echo '<p>Memory limit: ' . ini_get('memory_limit') . '</p>';
    echo '<p>Memory usage: ' . round(memory_get_usage() / 1024 / 1024, 2) . ' MB</p>';
    echo '<p>Max execution time: ' . ini_get('max_execution_time') . 's</p>';
    echo '<p>PHP version: ' . phpversion() . '</p>';
    echo '</div>';
    
    // FINAL: Try to load actual template
    echo '<div class="debug-box warning">';
    echo '<h2>🚀 Test 9: Probeer echte template te laden</h2>';
    echo '<p>Als je hieronder een error ziet, dan is dat het probleem!</p>';
    echo '<hr>';
    
    // Capture errors
    ob_start();
    try {
        // Try to include the real template
        $real_template = dirname(__FILE__) . '/single-rbs-travel-idea.php';
        if (file_exists($real_template)) {
            echo '<p><span class="status ok">OK</span> Template bestand bestaat</p>';
            echo '<p><strong>⚠️ Nu gaan we de echte template laden. Als de pagina hier stopt, is er een error in de template zelf!</strong></p>';
            echo '<hr style="margin: 20px 0; border: 2px solid #ef4444;">';
            
            // Include it
            include $real_template;
        } else {
            echo '<p><span class="status fail">FAIL</span> Template bestand niet gevonden: ' . $real_template . '</p>';
        }
    } catch (Exception $e) {
        echo '<p><span class="status fail">EXCEPTION</span> ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
    
    $output = ob_get_clean();
    echo $output;
    
    echo '</div>';
    ?>
    
</body>
</html>
