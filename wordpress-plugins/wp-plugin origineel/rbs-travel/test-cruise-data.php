<?php
/**
 * Quick test: Check what's in travel_details for a post
 * 
 * Upload to WordPress root and visit: https://your-site.com/test-cruise-data.php?post_id=123
 */

// Load WordPress
require_once('wp-load.php');

if (!isset($_GET['post_id'])) {
    die('Usage: ?post_id=YOUR_POST_ID');
}

$post_id = intval($_GET['post_id']);

echo "<h1>Cruise Data Test - Post ID: {$post_id}</h1>";
echo "<style>body{font-family:monospace;padding:20px;} pre{background:#f5f5f5;padding:15px;border-radius:5px;overflow:auto;}</style>";

// Get post
$post = get_post($post_id);
if (!$post) {
    die('<p style="color:red;">❌ Post not found!</p>');
}

echo "<h2>Post: {$post->post_title}</h2>";

// Get counters
$counters = get_post_meta($post_id, 'travel_counters', true);
echo "<h3>Counters:</h3>";
echo "<pre>" . print_r($counters, true) . "</pre>";

if (isset($counters['cruises'])) {
    echo "<p><strong>Cruises in counters:</strong> " . $counters['cruises'] . "</p>";
}

// Get cruise data
$cruises = get_post_meta($post_id, 'travel_cruises', true);
echo "<h3>Cruise Data (travel_cruises):</h3>";
if (empty($cruises)) {
    echo "<p style='color:red;'>❌ NO CRUISE DATA FOUND!</p>";
    echo "<p>This means the cruise array is empty or doesn't exist.</p>";
} else {
    echo "<p style='color:green;'>✅ CRUISE DATA EXISTS!</p>";
    echo "<pre>" . print_r($cruises, true) . "</pre>";
}

// Get all meta
echo "<h3>All Meta Fields:</h3>";
$all_meta = get_post_meta($post_id);
echo "<pre>";
foreach ($all_meta as $key => $value) {
    if (strpos($key, 'travel_') === 0) {
        echo "<strong>{$key}:</strong> ";
        if (is_array($value) && count($value) === 1) {
            echo "(serialized)\n";
        } else {
            echo print_r($value, true) . "\n";
        }
    }
}
echo "</pre>";

// Check taxonomy
echo "<h3>Taxonomies:</h3>";
$tour_types = wp_get_post_terms($post_id, 'tour-type', ['fields' => 'names']);
echo "<p><strong>Tour Types:</strong> " . implode(', ', $tour_types) . "</p>";
if (in_array('Cruise', $tour_types)) {
    echo "<p style='color:green;'>✅ Cruise taxonomy assigned</p>";
} else {
    echo "<p style='color:orange;'>⚠️ Cruise taxonomy NOT assigned</p>";
}

echo "<hr>";
echo "<h3>Conclusion:</h3>";
if (!empty($cruises)) {
    echo "<p style='color:green;font-size:18px;'>✅ CRUISE DATA IS PRESENT!</p>";
    echo "<p>The import is working correctly. Check the frontend template if cruise is not displaying.</p>";
} else if (isset($counters['cruises']) && $counters['cruises'] > 0) {
    echo "<p style='color:red;font-size:18px;'>❌ PROBLEM FOUND!</p>";
    echo "<p><strong>Counters say there are {$counters['cruises']} cruise(s), but no cruise data array!</strong></p>";
    echo "<p>This means:</p>";
    echo "<ul>";
    echo "<li>The API returns counters.cruises = 1</li>";
    echo "<li>But the API does NOT return the cruise details array</li>";
    echo "<li>OR the import function doesn't receive cruise data</li>";
    echo "</ul>";
    echo "<h4>Solution:</h4>";
    echo "<p>We need to check the Travel Compositor API response. This package might be using a different API endpoint.</p>";
} else {
    echo "<p style='color:orange;font-size:18px;'>⚠️ NO CRUISE IN THIS PACKAGE</p>";
    echo "<p>This package doesn't contain a cruise component.</p>";
}
