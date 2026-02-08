<?php
/**
 * DEBUG: Add this code TEMPORARILY to single-rbs-travel-idea.php
 * Place it right after line 6: $vars = ['travel_meta_fields' => $travel_meta_fields];
 */

// DEBUG: Show what cruise data is available
echo "<div style='background: yellow; padding: 20px; margin: 20px; border: 3px solid red;'>";
echo "<h2>🔍 DEBUG: Cruise Data Check</h2>";

if (isset($travel_meta_fields['travel_cruises']) && !empty($travel_meta_fields['travel_cruises'])) {
    echo "<p style='color: green; font-weight: bold;'>✅ Cruise data FOUND!</p>";
    echo "<pre>";
    print_r($travel_meta_fields['travel_cruises']);
    echo "</pre>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ NO cruise data in travel_cruises field!</p>";
}

echo "<hr>";
echo "<h3>All available meta fields:</h3>";
echo "<pre>";
print_r(array_keys($travel_meta_fields));
echo "</pre>";

echo "</div>";
?>
