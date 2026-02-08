<?php
// let accessToken = 'pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY2x5cjdtdnI2MDNkejJqczc0dDdoN216MSJ9.ELUSsxcj4M_vOm1HVW-JpA'; // 'pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY2x5cjdzODBwMDNsejJyczduanZ3dDhoYiJ9.8IvRvuKO92noH6OK4fYJQg';
// let tiletset = `https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${accessToken}&language=nl`;
// let attribution = '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';



$api_key = 'pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY2x5cjdtdnI2MDNkejJqczc0dDdoN216MSJ9.ELUSsxcj4M_vOm1HVW-JpA';

require __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'rbsMaps.php';
$rbsMaps = new rbsMaps('testmap01', 'Test Map 01', $api_key);
?>

<!DOCTYPE html>
<html>
<head>
    <title>rbsLeaflet Map</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <?php echo $rbsMaps->GetDependencies(); ?>



    <style>
		html, body {
			height: 100%;
			margin: 0;
		}
		.leaflet-container {
			height: 400px;
			width: 600px;
			max-width: 100%;
			max-height: 100%;
		}


        /* .rbs-maps--numbered-marker {
            background-color: #2A81CB;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            border: 2px solid white;
            box-shadow: 0 0 3px rgba(0,0,0,0.3);
            font-size: 16px;
        }         */
    </style>
    
</head>

<body>

    <header>
        <h1>rbsMaps</h1>
    </header>

    <div id="testMap" class="container">


    </div>

    <hr>

        <div id="mapId"></div>



    <script>
        const testMap = new rbsMap('test-map-01', 'Test Map 01', {debug : true});
        testMap.setCenterLocation(51.7791246, 4.6689966);

        const mapMarker01 = new rbsMapMarker('marker01', 51.7791246, 4.6689966);
        testMap.addMarker(mapMarker01);

        const mapMarker02 = new rbsMapMarker('marker02', 51.8816079, 4.5393749);
        testMap.addMarker(mapMarker02);        

        testMap.renderMap('testMap');

        // document.addEventListener('DOMContentLoaded', () => {
            testMap.renderImage('testMap');
        // });
        
    </script>


<script>

    // // Create the map
    // const map = L.map('mapId').setView([51.505, -0.09], 13);

    // // Add a tile layer
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    //   maxZoom: 19,
    //   attribution: '© OpenStreetMap contributors',
    // }).addTo(map);

    // // Function to create the custom canvas marker icon
    // function createStyledCanvasIcon(text) {
    //   const size = 30; // Width and height in pixels
    //   const borderWidth = 2;

    //   // Create canvas
    //   const canvas = document.createElement('canvas');
    //   canvas.width = size + borderWidth * 2;
    //   canvas.height = size + borderWidth * 2;
    //   const ctx = canvas.getContext('2d');

    //   // Draw background circle
    //   ctx.fillStyle = '#2A81CB'; // Background color
    //   ctx.beginPath();
    //   ctx.arc(
    //     canvas.width / 2, // x center
    //     canvas.height / 2, // y center
    //     size / 2, // Radius
    //     0,
    //     Math.PI * 2
    //   );
    //   ctx.fill();

    //   // Draw border
    //   ctx.strokeStyle = 'white'; // Border color
    //   ctx.lineWidth = borderWidth;
    //   ctx.stroke();

    //   // Add shadow
    //   ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
    //   ctx.shadowBlur = 3;

    //   // Draw text
    //   ctx.fillStyle = 'white'; // Text color
    //   ctx.font = 'bold 16px Arial'; // Font style
    //   ctx.textAlign = 'center';
    //   ctx.textBaseline = 'middle';
    //   ctx.fillText(text, canvas.width / 2, canvas.height / 2);

    //   return L.icon({
    //     iconUrl: canvas.toDataURL(), // Convert canvas to a data URL
    //     iconSize: [size + borderWidth * 2, size + borderWidth * 2], // Adjust icon size
    //     iconAnchor: [(size + borderWidth * 2) / 2, (size + borderWidth * 2) / 2], // Center anchor
    //   });
    // }

    // // Create and add a marker with the custom canvas icon
    // const customMarker = L.marker([51.505, -0.09], {
    //   icon: createStyledCanvasIcon('1'), // Pass your text here
    // }).addTo(map);

    // // When map is ready, capture it as an image
    // map.whenReady(function () {
    //   leafletImage(map, function (err, canvas) {
    //     if (err) {
    //       console.error('Leaflet-Image Error:', err);
    //       return;
    //     }
    //     // Create and append an image from the canvas
    //     const img = document.createElement('img');
    //     img.src = canvas.toDataURL();
    //     document.body.appendChild(img);
    //   });
    // });    
</script>

    
</body>

</html>



