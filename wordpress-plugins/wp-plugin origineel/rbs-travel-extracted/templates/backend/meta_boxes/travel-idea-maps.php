<?php

?>
<div id="rbstravel-travel-idea-maps">
    
    <table class="wp-list-table widefat fixed striped">
        <tr class="rbstravel-idea-meta-field-row">
        <th class="rbstravel-idea-meta-field_label">
            <?php echo $meta_label; ?>  
        </th>
    </table>

</div>

<button type="button" id="generate-map" name="generate_map" value="1" class="button-secondary">TEMP: Generate Map</button>

<div id="testMap"></div>





<button type="button" id="test-geocoder" name="geocoder_test" value="1" class="button-secondary">TEMP: geocoder test</button>

<script src="https://cdn.jsdelivr.net/npm/@mapbox/mapbox-gl-geocoder/dist/mapbox-gl-geocoder.js"></script>


<script>
    document.getElementById('test-geocoder').addEventListener('click', () => {
        mapboxgl.accessToken = 'your_mapbox_access_token';
        const geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            types: 'place'
            });

            geocoder.on('result', function(e) {
            const country = e.result.context.find(c => c.id.includes('country'));
            console.log(country); // Contains the country data
        });    
    })

</script>