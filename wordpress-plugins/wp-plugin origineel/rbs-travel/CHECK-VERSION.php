<?php
/**
 * VERSION CHECK SCRIPT
 * Upload dit bestand naar je WordPress root en bezoek het via browser
 * Bijvoorbeeld: https://jouwsite.nl/CHECK-VERSION.php
 */

// WordPress laden
require_once('wp-load.php');

echo "<h1>RBS Travel Plugin Version Check</h1>";

// Check if plugin is active
$plugin_file = 'rbs-travel/rbs-travel.php';
$active_plugins = get_option('active_plugins');

echo "<h2>Plugin Status</h2>";
if (in_array($plugin_file, $active_plugins)) {
    echo "<p style='color:green;'>✅ Plugin is ACTIVE</p>";
} else {
    echo "<p style='color:red;'>❌ Plugin is NOT ACTIVE</p>";
}

// Check template file modification time
$template_file = WP_PLUGIN_DIR . '/rbs-travel/templates/frontend/single-rbs-travel-idea.php';
if (file_exists($template_file)) {
    $mod_time = filemtime($template_file);
    echo "<h2>Template File Info</h2>";
    echo "<p><strong>Last Modified:</strong> " . date('Y-m-d H:i:s', $mod_time) . "</p>";
    
    // Check if file contains new cruise code
    $content = file_get_contents($template_file);
    
    if (strpos($content, 'cruise-main') !== false) {
        echo "<p style='color:green;'>✅ Template contains NEW cruise code (cruise-main class found)</p>";
    } else {
        echo "<p style='color:red;'>❌ Template does NOT contain new cruise code</p>";
        echo "<p><strong>Action needed:</strong> Upload rbs-travel-CRUISE-IMPROVED.zip</p>";
    }
    
    if (strpos($content, 'cruise-sub-item') !== false) {
        echo "<p style='color:green;'>✅ Template contains cruise port sub-items</p>";
    } else {
        echo "<p style='color:red;'>❌ Template missing cruise port sub-items</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Template file not found!</p>";
}

// Check for cruise data in posts
echo "<h2>Cruise Data Check</h2>";
$posts_with_cruises = get_posts(array(
    'post_type' => 'rbs-travel-idea',
    'numberposts' => 10,
    'meta_query' => array(
        array(
            'key' => 'travel_cruises',
            'compare' => 'EXISTS'
        )
    )
));

if ($posts_with_cruises) {
    echo "<p style='color:green;'>✅ Found " . count($posts_with_cruises) . " posts with cruise data:</p>";
    echo "<ul>";
    foreach ($posts_with_cruises as $post) {
        $cruises = get_post_meta($post->ID, 'travel_cruises', true);
        $cruise_count = is_array($cruises) ? count($cruises) : 0;
        echo "<li><a href='" . get_permalink($post->ID) . "' target='_blank'>" . $post->post_title . "</a> (ID: {$post->ID}) - {$cruise_count} cruise(s)</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange;'>⚠️ No posts with cruise data found</p>";
}

echo "<hr>";
echo "<h2>Debug Info</h2>";
echo "<p><strong>Plugin Dir:</strong> " . WP_PLUGIN_DIR . "/rbs-travel/</p>";
echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
