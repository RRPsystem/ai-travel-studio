<?php
    // Build travel destinations array - only include destinations with valid coordinates
    $travel_destinations = array();
    if (!empty($travel_meta_fields['travel_destinations']) && is_array($travel_meta_fields['travel_destinations'])) {
        foreach($travel_meta_fields['travel_destinations'] as $destination) {
            $lat = isset($destination['geolocation']['latitude']) ? floatval($destination['geolocation']['latitude']) : 0;
            $lng = isset($destination['geolocation']['longitude']) ? floatval($destination['geolocation']['longitude']) : 0;
            if ($lat != 0 && $lng != 0) {
                $travel_destinations[] = array(
                    'name' => isset($destination['name']) ? $destination['name'] : '',
                    'lat' => $lat,
                    'lng' => $lng,
                    'image' => isset($destination['imageUrls'][0]) ? $destination['imageUrls'][0] : '',
                    'description' => isset($destination['description']) ? $destination['description'] : ''
                );
            }
        }
    }
    
    // Only show map container if we have at least 2 destinations with valid coordinates
    if (count($travel_destinations) >= 2):
?>
<script>var travelDestinations = <?php echo json_encode($travel_destinations); ?>;</script>
<div id="rbstravel-idea__fullmap-container" style="margin: 10px 0;">
    <div id="rbstravel-idea__fullmap"></div>
</div>
<?php endif; ?>

