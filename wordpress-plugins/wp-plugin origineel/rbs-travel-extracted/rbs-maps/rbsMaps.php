<?php

/**
 * rbsMaps Class for creating leaflet-maps
 * 
 */




class rbsMaps {

    private $id;
    private $name;
    private $api_key;
    private $args;


    function __construct($id, $name, $api_key, $args = array()) {
        $this->id = $id;
        $this->name = $name;
        $this->api_key = $api_key;
        $this->args = $args;
    }





    public function GetDependencies() {

        // <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        // <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
        // <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
        // <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
        
        
        $return = array();
        $return[] = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>';        
        $return[] = '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>';
        $return[] = '<script src="https://rawgit.com/mapbox/leaflet-image/gh-pages/leaflet-image.js"></script>';   
        
        // $return[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-image/0.4.0/leaflet-image.min.js"></script>';

        $rbsmaps_script_url = 'https://snippets.robasdev.nl/hinko//maps/rbs-leaflet/lib/rbs-maps.js';
        $return[] = '<script src="' . $rbsmaps_script_url . '"></script>';  

        $return[] = '<script>const accessToken = \'' . $this->api_key . '\';</script>';


        echo implode("\r\n", $return);

    }




}