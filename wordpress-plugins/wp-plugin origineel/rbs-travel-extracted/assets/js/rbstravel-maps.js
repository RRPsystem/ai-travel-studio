console.log('rbstravel-maps.js', travelDestinations);


// const mapboxToken = '123pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY20wcW04NDVlMGduMTJrczUzcGw5MTJxayJ9.HqM7Y8WgPZwyMmazwNMY3g';
const mapboxToken = 'pk.eyJ1Ijoicm9icm9iYXMiLCJhIjoiY200djkxd2drMDA5dTJqc2Q1MXpvYTR6NCJ9.XEsEVOmAPcOUkfcTms-VbA';

const mapboxUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxToken + '&language=nl';
                   
// let accessToken = 'pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY2x5cjdtdnI2MDNkejJqczc0dDdoN216MSJ9.ELUSsxcj4M_vOm1HVW-JpA'; // 'pk.eyJ1Ijoicm9iYXMtaGlua28iLCJhIjoiY2x5cjdzODBwMDNsejJyczduanZ3dDhoYiJ9.8IvRvuKO92noH6OK4fYJQg';
// let tiletset = `https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${accessToken}&language=nl`;
// let attribution = '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';



function createNumberedMarker(markerGroup, latlng, number, popupText) {
    var html = '<div class="numbered-marker">' + number + '</div>';
    var icon = L.divIcon({
        html: html,
        className: '', // Remove default class
        iconSize: [30, 30],
        iconAnchor: [15, 30]
    });

    var marker = L.marker(latlng, { icon: icon });;
    if (popupText) {
        marker.bindPopup(popupText);
    }
    marker.addTo(markerGroup);
    // console.log('createNumberedMarker', marker, markerGroup);
    return marker;
}



function getColorForRoute(index) {
    var colors = ['#ff5722', '#4caf50', '#2196f3']; // Customize these colors
    return colors[index % colors.length]; // Cycle through colors
}


function createCarRoute(map, latLngs) {
    var control = L.Routing.control({
        // routeWhileDragging: true, // Allows dragging the waypoints
        // draggableWaypoints: true, // Allows adding new waypoints by dragging
        // addWaypoints: true, // Users can click and drag the route to add waypoints
        waypoints : latLngs,
        // summaryTemplate : '',
        show: false,
        // lineOptions: {
        //     styles: [{ color: getColorForRoute(0) }, {color: getColorForRoute(1)}, {color: getColorForRoute(2)}] // Custom color for each route
        // },
        router: L.Routing.mapbox(mapboxToken),
        createMarker: function() { return null; } // Do not add markers      
    }).addTo(map);    

        // You can still handle map clicks to add additional waypoints if needed
        // map.on('click', function(e) {
        //     var latlng = e.latlng;
        //     var waypoints = control.getWaypoints();
        //     waypoints.push({ latLng: latlng });
        //     control.setWaypoints(waypoints.map(function(wp) { return wp.latLng; }));
        // });


    // Optional: Handling alternate routes if supported by the router
    // L.Routing.mapbox(mapboxToken).route({
    //     waypoints: waypoints,
    //     alternatives: true // Request alternative routes
    // }).on('routesfound', function(e) {
    //     // e.routes contains alternative routes
    //     console.log('Alternatives:', e.routes);
    // });


    /** TEST: different color per 'line / route' between 2 markers */
    // var routes = [];
    // for(var i = 0; i < latLngs.length - 1; i++) {
    //     routes.push([latLngs[i], latLngs[i + 1]]);
    // }    

    // console.log(routes);
    // routes.forEach(function(route, index) {
    //     L.Routing.control({
    //         waypoints: [
    //             L.latLng(route[0][0], route[0][1]),
    //             L.latLng(route[1][0], route[1][1])
    //         ],
    //         routeWhileDragging: true,
    //         lineOptions: {
    //             styles: [{ color: getColorForRoute(index) }] // Custom color for each route
    //         },
    //         createMarker: function() { return null; } // Do not add markers
    //     }).addTo(map);
    // });    
   

        // Calculate and log the total distance of the route
        control.on('routesfound', function(e) {
            // console.log('routesfound', e);
            var totalDistance = (e.routes[0].summary.totalDistance / 1000).toFixed(2);
            document.getElementById('rbstravel-idea__distance-value').textContent = totalDistance;
        });    

}


function initFullMap() {  
    // Initialize the map
    let mapId = 'rbstravel-idea__fullmap';
    let mapOptions = {
        // zoomControl : false
    };

    //console.log('initFullMap', travelDestinations[0]);

    let centerPosition = [travelDestinations[0]['lat'], travelDestinations[0]['lng']];     // [0,0];
    let zoomLevel = 6;

    const map = L.map(mapId, mapOptions).setView(centerPosition, zoomLevel); // Center on a location




    // Add tile layer (OpenStreetMap tiles)
    // note: attribution is required and different, depending on the 'tileset (tileLayerUrl)'
    /** openstreetmap: */
    // let tileLayerUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
    // let tileLayerOptions = {
    //     attribution : '&copy; OpenStreetMap contributors'
    // }

    /** Mapbox: */
    let tileLayerUrl = mapboxUrl;
    let tileLayerOptions = {
        attribution : '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        accessToken: mapboxToken,
        id: 'mapbox/streets-v11'
    }


    L.tileLayer(tileLayerUrl, tileLayerOptions).addTo(map);


    // L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + mapboxToken, {
    //     maxZoom: 18,
    //     id: 'mapbox/streets-v11',
    //     tileSize: 512,
    //     zoomOffset: -1,
    //     accessToken: mapboxToken
    // }).addTo(map);

    const markerGroup = L.featureGroup();
    const markers = [];
    const latlngs = [];    
    travelDestinations.forEach(function(val, idx) {        
        // var marker = L.marker([val.lat, val.lng]).addTo(map);
        var popupText = '<h3>' + val.name + '</h3>';
        popupText += '<img src="' + val.image + '" />';
        popupText += '<p>' + val.description + '</p>';

        var marker = createNumberedMarker(markerGroup, [val.lat, val.lng], idx + 1, popupText);
        markers.push(marker);
        latlngs.push([val.lat, val.lng]);        
    });

    markerGroup.addTo(map);
    
    
    // console.log('fitBounds', map, markerGroup);

    addCarRoute = false;        // temp hardcoded (maybe based on idea >> is fly-drive yes/no)

    if (addCarRoute === true) {
        createCarRoute(map, latlngs);
    } else {
        const polyline = L.polyline(latlngs, {color: 'blue'}).addTo(map);
        // map.fitBounds(polyline.getBounds());
    
        // var distance = 0;
        // for(var i = 0; i < latlngs.length - 1; i++) {
        //     distance += map.distance(latlngs[i], latlngs[i + 1]);
        // }
        // distance = distance / 1000;     // meters to kilometers
        // document.getElementById('rbstravel-idea__distance-value').textContent =  distance.toFixed(2);
    }


    map.fitBounds(markerGroup.getBounds());

    const mapElement = document.getElementById(mapId);
    if (mapElement) {
        mapElement._map_instance = map;
        mapElement._marker_group = markerGroup;
    }

}

initFullMap();