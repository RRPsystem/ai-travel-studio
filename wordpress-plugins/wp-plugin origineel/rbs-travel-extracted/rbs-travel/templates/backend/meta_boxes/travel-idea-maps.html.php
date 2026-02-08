<?php
    $map_image = get_post_meta($post->ID, 'rbstravel_map_image', true);

    $travel_destinations = RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Meta::GetMetaFields(null, array('travel_destinations'));
    if (is_array($travel_destinations) && count($travel_destinations) !== 0) {
        $markers = array();
        foreach($travel_destinations as $destination) {
            $markers[] = array(
                'name' => $destination['name'],
                'lat' => $destination['geolocation']['latitude'],
                'lng' => $destination['geolocation']['longitude']
            ); 
        }

        echo '<script>const ideaMapMarkers = ' . json_encode($markers) . ';</script>';
    }

    //echo '<pre>' . print_r($travel_destinations, true) . '</pre>';
?>
<div id="rbstravel-travel-idea-maps">
    
    <table class="wp-list-table widefat fixed striped">
        <tr class="rbstravel-map--tr">
            <th class="rbstravel-map--th" colspan="2">
                <button type="button" id="generate-map" name="generate_map" value="1" class="button-secondary">TEMP: Generate Map</button>
            </th>
        </tr>

        <tr class="rbstravel-map--tr">
            <td colspan="2">
                <div id="ideaMap"></div>
            </td>
        </tr>

        <tr class="rbstravel-map--tr">
            <th class="rbstravel-map--th" colspan="2">
                <button type="button" id="generate-map-image" name="generate_map_image" value="1" class="button-secondary">TEMP: Generate Map Image</button>
                <p id="generate-image--wait" style="display: none;">TEMP: Generating image, one moment please...</p>
            </th>
        </tr>

        <tr class="rbstravel-map--tr">
            <td colspan="2">
                <h2>TEMP: Generated map image:</h2>
                <div id="ideaMapImage"></div>

                <hr>

                <?php if (is_string($map_image) && strlen($map_image) === 0): ?>
                    <p style="color: red;">TEMP: No map image available</p> 
                <?php else: ?>
                    <h2>TEMP: Current map image:</h2>
                    <img src="<?php echo $map_image;?>" />
                <?php endif; ?>
            </td>
        </tr>        

        <tr class="rbstravel-map--tr">
            <td colspan="2">
                <div id="ideaMapImage--Base64">
                    <textarea id="idea-map-image-base64" name="base64_map_image" rows="5" cols="30" class="text-field "></textarea>
                </div>
            </td>
        </tr>         
    </table>

  
</div>
