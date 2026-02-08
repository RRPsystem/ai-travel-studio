"use strict";


/**
 * @todo:
 * - TESTING!!
 * 
 * - rbsMaps.php
 *      >> $rbsMaps->GetDependencies()   (required urls/script => leaflet, mapbox, token)
 * 
 * - check better practice for 'accesstoken'
 * 
 * - rbsMapMarker:
 *      - [ ] add 'popup contents'
 *      - [ ] set 'custom' icon (html)
 *      - [ ] add 'marker class'
 * 
 * - rbsMap:
 *      - [ ] add 'bindPopup' if marker has 'popup content'
 *      - [ ] customize 'tileLayer' (tileset)
 *      - [ ] create/load maps from json (inlcuding markers)
 *      - [ ]
 * 
 * - rbsMapManager
 *      - [ ] ?? move 'resfreshmap' to rbsMap
 *      - [ ] 
 */


/**
 * rbsMap.js        (requires 'rbsMaps.php' for dependcies)
 * 
 * 
 * - rbsMapManager: Manager for maps
 * - rbsMap: Class object for creating map
 * - rbsMapMarker: Class object for creating markers
 */



class rbsMapManager {

    constructor(options = {}) {
        this.maps = {};
        this.parseOptions(options);
    }


    parseOptions(options) {
        this.options = options;

        if (typeof this.options.debug !== "undefined") {
            this.debug = !!this.options.debug;
        }

        // if (typeof this.options.uploadEndpoint === "undefined") {
        //     this.options.uploadEndpoint = null;
        // }     
        
        if (this.debug) {
            console.log('Debug mode is enabled.');
            console.log('rbsMap Options: ', options);
        }         

    }


    printDebug(text) {
        if (this.debug) {
            console.log('rbsMapManager', text);
        } 
    }    

    addMap(mapName, map, options = {}) {
        if (typeof this.maps[mapName] !== 'undefined') {
            // alert('TEMP: Map with this name already exists!');
            this.printDebug('Map with this name already exists');
            return;
        }

        if (map instanceof rbsMap) {
            this.maps[mapName] = map;
        } else {
            this.printDebug('Map is not an rbsMap instance');
        }           
        return map;
    }

    getMap(mapName) {
        if (typeof this.maps[mapName] !== 'undefined') {
            return this.maps[mapName];
        }
        return false;
    }    
    
    removeMap(mapName) {
        if (typeof this.maps[mapName] !== 'undefined') {
            delete this.maps[mapName];
            return true;
        }
        return false;
    }

    countMaps() {        
        return Object.keys(this.maps).length;
    }

    refreshMaps(mapName) {
        // //setTimeout(function() {
        // for(var i = 0; i < leafletMaps.length; i++) {
        //     var defaultZoom = leafletMaps[i]._container.dataset.defaultZoom;

        //     // console.log(defaultZoom);
        //     leafletMaps[i].invalidateSize();

        //     if (typeof defaultZoom !== 'undefined') {
        //         leafletMaps[i].setZoom(defaultZoom);
        //     }

        //     // var currentZoom = leafletMaps[i].getZoom();
        //     // console.log(leafletMaps[i], currentZoom);
        // }

        // //}, 500);

        let map = this.getMap(mapName);
        map._instance_map.invalidateSize(true);
        map._instance_map.fitBounds(map._marker_group.getBounds());
 
    }    



    // Function to convert degrees to radians
    static deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    // Function to calculate distance using Haversine formula
    static calculateDistance(lat1, lon1, lat2, lon2) {
        var R = 6371; // Radius of the Earth in kilometers
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a = 
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var distance = R * c; // Distance in kilometers

        return distance;
    }


    static uploadImage(base64Image, uploadEndpoint, uploadMethod = 'POST', uploadHeaders = {'Content-Type': 'application/x-www-form-urlencoded'}) {
        // Prepare the base64 string by removing the prefix (data:image/png;base64,)
        const base64Data = base64Image.split(',')[1];

        // Send the base64 string to the PHP endpoint
        fetch(uploadEndpoint, {
            method: uploadMethod,
            headers: uploadHeaders,
            body: `image=${encodeURIComponent(base64Data)}`
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);  // Handle the response
        })
        .catch(error => {
            console.error('Error:', error);
        });        
    }


    static async geocodeAddress(address) {
        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`;
        try {
            const response = await fetch(url);
            const data = await response.json();
            if (data && data.length > 0) {
                return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
            } else {
                throw new Error('No results found');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }    

}


class rbsMap {
    constructor(mapId, mapName, options = {}) {
        this.mapId = mapId;
        this.mapName = mapName;
        this.markers = [];
        this.parseOptions(options);
    }


    parseOptions(options) {
        this.options = options;

        if (typeof this.options.debug !== "undefined") {
            this.debug = !!this.options.debug;
        }

        if (typeof this.options.zoom === "undefined") {
            this.options.zoom = 13;
        }

        if (typeof this.options.maxZoom === "undefined") {
            this.options.maxZoom = 19;
        }

        if (typeof this.options.fit_zoom === "undefined") {
            this.options.fit_zoom = true;
        }        

        if (typeof this.options.mapIdPrefix === "undefined") {
            this.options.mapIdPrefix = 'rbs-map--';
        }

        if (typeof this.options.width === "undefined") {
            this.options.mapWidth = '600px';
        }

        if (typeof this.options.width === "undefined") {
            this.options.mapHeight = '400px';
        }

        if (typeof this.options.language === "undefined") {
            this.options.language = null;
        }

        if (typeof this.options.add_polyline === "undefined") {
            this.options.add_polyline = true;       // false
        }

        if (typeof this.options.polyline_color === "undefined") {
            this.options.polyline_color = '#2A81CB';       // false
        }        

        if (typeof this.options.create_markers === "undefined") {
            this.options.create_markers = true;
        }


        if (this.debug) {
            console.log('Debug mode is enabled.');
            console.log('rbsMap Options: ', options);
        } 
    }


    printDebug(text) {
        if (this.debug) {
            console.log(text);
        } 
    }

    setCenterLocation(latitude, longitude, zoom = null) {
        this.centerLocation = [latitude, longitude];
        if (zoom !== null) {
            this.options.zoom = zoom;
        }
    }
    


    addMarker(marker) {
        if (marker instanceof rbsMapMarker) {
            //console.log('addMarker', marker);    //.getMarker());
            this.markers.push(marker);
        } else {
            this.printDebug('Marker is not an rbsMapMarker instance');
        }        
    }


    renderMap(targetContainer) {
        const mapContainer = document.createElement('div');
        mapContainer.id = this.options.mapIdPrefix + this.mapId;
        mapContainer.style.width = this.options.mapWidth;
        mapContainer.style.height = this.options.mapHeight;
        document.getElementById(targetContainer).appendChild(mapContainer);

        const map = L.map(mapContainer.id).setView(this.centerLocation, this.options.zoom);

        const tileSet = `https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=${accessToken}`;
        const tileSetLanguage = this.options.language !== null ? `&language=${this.options.language}` : '';
        const attribution = '&copy; <a href="https://www.mapbox.com/about/maps/">Mapbox</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        L.tileLayer(tileSet + tileSetLanguage, {
            maxZoom: this.options.maxZoom,
            attribution: attribution
        }).addTo(map);    
 


        this.markerGroup = null;
        if (this.options.create_markers) {
            this.markerGroup = this.createMarkers();
            this.markerGroup.addTo(map);

            if (this.options.add_polyline) {
                this.createPolyline(map);
            }            
        }


        if (this.options.fit_zoom) {
            map.fitBounds(this.markerGroup.getBounds());
        }

        mapContainer._instance_map = map;
        mapContainer._marker_group = this.markerGroup;

        this.renderedMap = map;
        this.mapContainer = mapContainer;

    }


    // Function to create the custom canvas marker icon
    createStyledCanvasIcon(text) {
      const size = 30; // Width and height in pixels
      const borderWidth = 2;

      // Create canvas
      const canvas = document.createElement('canvas');
      canvas.width = size + borderWidth * 2;
      canvas.height = size + borderWidth * 2;
      const ctx = canvas.getContext('2d');

      // Draw background circle
      ctx.fillStyle = '#2A81CB'; // Background color
      ctx.beginPath();
      ctx.arc(
        canvas.width / 2, // x center
        canvas.height / 2, // y center
        size / 2, // Radius
        0,
        Math.PI * 2
      );
      ctx.fill();

      // Draw border
      ctx.strokeStyle = 'white'; // Border color
      ctx.lineWidth = borderWidth;
      ctx.stroke();

      // Add shadow
      ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
      ctx.shadowBlur = 3;

      // Draw text
      ctx.fillStyle = 'white'; // Text color
      ctx.font = 'bold 16px Arial'; // Font style
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(text, canvas.width / 2, canvas.height / 2);

      return L.icon({
        iconUrl: canvas.toDataURL(), // Convert canvas to a data URL
        iconSize: [size + borderWidth * 2, size + borderWidth * 2], // Adjust icon size
        iconAnchor: [(size + borderWidth * 2) / 2, (size + borderWidth * 2) / 2], // Center anchor
      });
    }


    createMarkers() {
        const markerGroup = L.featureGroup();
        let markerNumber = 0;

        for (const marker of this.markers) {
            let markerData = marker.getMarkerData();
            if (markerData.options.numbered_marker) {
                markerNumber += 1;
                // let color = markerData.options.color;
                // let html = '<div class="rbs-maps--numbered-marker" style="background-color: ' + color + '">' + markerNumber + '</div>';
                // let icon = L.divIcon({
                //     html: html,
                //     className: '', // Remove default class
                //     iconSize: [30, 30],
                //     iconAnchor: [15, 30]
                // });      
                let icon = this.createStyledCanvasIcon(markerNumber);   //, markerData.options);
                console.log('createMarkers', icon);
                markerData.marker.setIcon(icon);
            }
            markerData.marker.addTo(markerGroup);   //.bindPopup(`<b>${address}</b>`);
        }

        return markerGroup;
    }


    createPolyline(map) {
        let latlngs = [];

        for (const marker of this.markers) {
            let markerData = marker.getMarkerData();
            latlngs.push(markerData.location);
        }
        L.polyline(latlngs, {color: this.options.polyline_color}).addTo(map);
    }


    // ?? Move to 'map manager'
    renderImage(targetContainer, uploadImage = false) {
        if (typeof this.renderedMap === 'undefined') {
            this.renderMap(targetContainer);
        }
        
        // console.log('renderImage', this.mapContainer, this.mapContainer._instance_map);

        // this.mapContainer._instance_map.whenReady(() => {

        //     leafletImage(this.mapContainer._instance_map, (err, canvas) => {
        //         console.log('leafletImage', err, canvas, this.mapContainer._instance_map);

        //         // Now you have the canvas, you can save it as an image
        //         var img = document.createElement('img');
        //         var dimensions = this.mapContainer._instance_map.getSize();
        //         img.width = dimensions.x;
        //         img.height = dimensions.y;
        //         img.src = canvas.toDataURL();
        //         this.mapImage = canvas.toDataURL();
        //         document.getElementById(targetContainer).appendChild(img);

        //         // // Optionally, you can download the image
        //         // var link = document.createElement('a');
        //         // link.href = img.src;
        //         // link.download = 'map.png';
        //         // link.click();
        //     });        
        // });
        
        leafletImage(this.mapContainer._instance_map, (err, canvas) => {
            console.log('leafletImage', err, canvas, this.mapContainer._instance_map);

            // Now you have the canvas, you can save it as an image
            var img = document.createElement('img');
            var dimensions = this.mapContainer._instance_map.getSize();
            img.width = dimensions.x;
            img.height = dimensions.y;
            img.src = canvas.toDataURL();
            this.mapImage = canvas.toDataURL();
            document.getElementById(targetContainer).appendChild(img);

            // // Optionally, you can download the image
            // var link = document.createElement('a');
            // link.href = img.src;
            // link.download = 'map.png';
            // link.click();
        });            
    }


      


}


class rbsMapMarker {
    
    constructor(name, latitude, longitude, options = {}) {
        this.markerName = name;
        this.markerLocation = [latitude, longitude];       
        this.parseOptions(options);
    }

    parseOptions(options) {
        this.options = options;

        if (typeof this.options.debug !== "undefined") {
            this.debug = !!this.options.debug;
        }

        if (typeof this.options.numbered_marker === "undefined") {
            this.options.numbered_marker = false;            // false
        }
        
        // note: color only for 'numbered' markers (for now, due requirement of 'custom icon html')
        if (typeof this.options.color === "undefined") {
            this.options.color = '#2A81CB';
        }        
        

        if (typeof this.options.popup_content !== "undefined") {
            this.setPopupContent(this.options.popup_content);
        } else {
            this.setPopupContent(null);
        }

        if (this.debug) {
            console.log("Debug mode is enabled.");
            console.log('rbsMap Options: ', options);
        } 

    }

    setPopupContent(html) {
        this.popupContent = html;
    }




    createMarker() {
        // var html = '<div class="numbered-marker">' + number + '</div>';
        // var icon = L.divIcon({
        //     html: html,
        //     className: '', // Remove default class
        //     iconSize: [30, 30],
        //     iconAnchor: [15, 30]
        // });
         
        const marker = L.marker(this.markerLocation);
        if (this.popupContent !== null) {
            marker.bindPopup(this.popupContent);
        }
        return marker;
    }


    getMarkerData() {
        const markerData = {
            name: this.markerName,
            location: this.markerLocation,
            options: this.options,
            marker: this.createMarker()
        }
        return markerData;
    }

}


