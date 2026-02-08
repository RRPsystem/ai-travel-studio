<?php
/**
 * STEP BY STEP DEBUG - Shows exactly where it crashes
 */

echo "<!-- STEP 1: PHP Started -->\n";
flush();

// Enable ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!-- STEP 2: Error reporting enabled -->\n";
flush();

try {
    // Try to use Settings class
    echo "<!-- STEP 3: About to import Settings class -->\n";
    flush();
    
    use RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Settings;
    
    echo "<!-- STEP 4: Settings class imported OK -->\n";
    flush();
    
} catch (Exception $e) {
    echo "<!-- ERROR in STEP 3-4: " . $e->getMessage() . " -->\n";
    die("Error importing Settings class: " . $e->getMessage());
}

echo "<!-- STEP 5: About to call get_header() -->\n";
flush();

try {
    get_header();
    echo "<!-- STEP 6: get_header() completed -->\n";
    flush();
} catch (Exception $e) {
    echo "<!-- ERROR in get_header(): " . $e->getMessage() . " -->\n";
    die("Error in get_header(): " . $e->getMessage());
}

echo "<!-- STEP 7: About to set cache headers -->\n";
flush();

header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');

echo "<!-- STEP 8: Cache headers set -->\n";
flush();

echo "<!-- STEP 9: About to get brand colors -->\n";
flush();

try {
    $primary_color = RBS_TRAVEL_Settings::GetSetting('primary_color', '#6366f1');
    echo "<!-- STEP 10: Got primary color: $primary_color -->\n";
    flush();
    
    $secondary_color = RBS_TRAVEL_Settings::GetSetting('secondary_color', '#10b981');
    echo "<!-- STEP 11: Got secondary color: $secondary_color -->\n";
    flush();
    
    $heading_color = RBS_TRAVEL_Settings::GetSetting('heading_color', '#1f2937');
    $text_color = RBS_TRAVEL_Settings::GetSetting('text_color', '#4b5563');
    
    echo "<!-- STEP 12: All colors retrieved OK -->\n";
    flush();
    
} catch (Exception $e) {
    echo "<!-- ERROR getting colors: " . $e->getMessage() . " -->\n";
    die("Error getting colors: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DEBUG - Step By Step</title>
    <style>
        body { 
            font-family: Arial; 
            padding: 20px; 
            background: #f5f5f5; 
            max-width: 1200px;
            margin: 0 auto;
        }
        .step { 
            background: #d1fae5; 
            padding: 15px; 
            margin: 10px 0; 
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }
        .error {
            background: #fee2e2;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
        }
        .info {
            background: #e0e7ff;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #6366f1;
        }
        h1 { color: #1f2937; }
        code { 
            background: #f9fafb; 
            padding: 2px 6px; 
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>

<h1>🔍 STEP BY STEP DEBUG</h1>

<div class="step">
    ✅ <strong>STEP 13:</strong> HTML output started successfully!
</div>

<?php
echo '<div class="step">✅ <strong>STEP 14:</strong> PHP echo inside HTML works!</div>';
flush();

global $post;
$post_id = isset($post) ? $post->ID : 0;

echo '<div class="info">';
echo '<strong>Post Info:</strong><br>';
echo 'Post ID: ' . $post_id . '<br>';
echo 'Post Type: ' . (isset($post) ? get_post_type($post->ID) : 'NONE') . '<br>';
echo 'Post Title: ' . (isset($post) ? get_the_title($post->ID) : 'NONE');
echo '</div>';

echo '<div class="step">✅ <strong>STEP 15:</strong> Post data retrieved OK</div>';
flush();

// Try to get post meta
try {
    $destinations = get_post_meta($post_id, 'travel_destinations', true);
    $dest_count = is_array($destinations) ? count($destinations) : 0;
    
    echo '<div class="step">✅ <strong>STEP 16:</strong> Post meta retrieved - ' . $dest_count . ' destinations</div>';
    flush();
    
} catch (Exception $e) {
    echo '<div class="error">❌ <strong>ERROR in STEP 16:</strong> ' . $e->getMessage() . '</div>';
}

// Now try to render expert widget
echo '<div class="info"><strong>STAP 17:</strong> Nu gaan we expert widget proberen...</div>';
flush();

try {
    // Check if function exists
    if (function_exists('RBS_TRAVEL\HELPERS\rbstravel_render_expert_widget')) {
        echo '<div class="step">✅ Helper function exists!</div>';
        flush();
        
        echo '<div class="info">Calling expert widget...</div>';
        flush();
        
        // Try to call it
        ob_start();
        \RBS_TRAVEL\HELPERS\rbstravel_render_expert_widget($post_id);
        $widget_output = ob_get_clean();
        
        echo '<div class="step">✅ <strong>STEP 18:</strong> Expert widget rendered OK!</div>';
        echo '<div style="background: white; padding: 20px; border: 2px solid #e5e7eb; border-radius: 8px; margin: 20px 0;">';
        echo '<h3>Expert Widget Output:</h3>';
        echo $widget_output;
        echo '</div>';
        flush();
        
    } else {
        echo '<div class="error">❌ Expert helper function NOT FOUND!</div>';
    }
    
} catch (Exception $e) {
    echo '<div class="error">❌ <strong>ERROR calling expert widget:</strong><br>';
    echo 'Message: ' . $e->getMessage() . '<br>';
    echo 'File: ' . $e->getFile() . '<br>';
    echo 'Line: ' . $e->getLine() . '<br>';
    echo '</div>';
}

echo '<div class="step">✅ <strong>STEP 19:</strong> All tests completed!</div>';

?>

<div class="info">
    <h3>🎯 Conclusie</h3>
    <p>Als je dit ziet, werkt de basis template!</p>
    <p>Het probleem zit waarschijnlijk in:</p>
    <ul>
        <li>Een specifiek onderdeel van de volledige template</li>
        <li>Een loop die te veel data verwerkt</li>
        <li>Een complexe rendering functie</li>
    </ul>
</div>

<div class="info">
    <h3>📸 Stuur screenshot</h3>
    <p>Stuur screenshot van deze pagina naar de developer!</p>
    <p>Let vooral op welke STEP als laatste gelukt is.</p>
</div>

<?php
// Check HTML source
echo "\n\n<!-- ==================== -->\n";
echo "<!-- If you see this in HTML source, PHP completed successfully! -->\n";
echo "<!-- Version: 4.15.0-STEPBYSTEP -->\n";
echo "<!-- ==================== -->\n\n";
?>

</body>
</html>

<?php
// Don't call get_footer yet - let's see if we even get here
echo "<!-- FINAL STEP: End of template reached! -->\n";
?>
