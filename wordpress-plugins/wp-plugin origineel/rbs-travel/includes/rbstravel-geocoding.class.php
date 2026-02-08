<?php
namespace RBS_TRAVEL\INCLUDES;
defined('RBS_TRAVEL') or die();

/**
 * @todo:
 * - [ ] refine 'select' columns
 *	>> see 'api documentation'
 * - [ ] 
 */

/**
 * 
 */


if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Geocoding')) {
    
    class RBS_TRAVEL_Geocoding {

        private $api_key;
        private $api_base_url;


        private $language;

        function __construct($api_key, $args = array()) {
            $this->api_key = $api_key;
            $this->api_base_url = 'https://api.bigdatacloud.net/data/reverse-geocode-client';


            $this->language = 'nl';         // get from args
        }



        // private function request($location) {
        //     $apiKey = 'YOUR_API_KEY_HERE'; // Replace with your BigDataCloud API key
        //     $apiUrl = "https://api.bigdatacloud.net/data/reverse-geocode-client?localityLanguage=en&location=" . urlencode($location) . "&key=" . $apiKey;
        
        //     // Initialize cURL
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $apiUrl);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //     $response = curl_exec($ch);
        //     curl_close($ch);
        
        //     if ($response) {
        //         $data = json_decode($response, true);
        
        //         if (!empty($data)) {
        //             // Extract details from the API response
        //             $details = [
        //                 'continent' => $data['continent'] ?? 'Unknown',
        //                 'country' => $data['countryName'] ?? 'Unknown',
        //                 'city' => $data['locality'] ?? 'Unknown',
        //                 'latitude' => $data['latitude'] ?? 'Unknown',
        //                 'longitude' => $data['longitude'] ?? 'Unknown',
        //             ];
        
        //             return $details;
        //         }
        //     }
        
        //     return false; // Return false if no data found or API error
        // }


        public function GetLocationDetails($latitude, $longitude) {
            //$apiKey = 'YOUR_API_KEY_HERE'; // Replace with your BigDataCloud API key
            // $apiUrl = "https://api.bigdatacloud.net/data/reverse-geocode-client?localityLanguage=en&location=" . urlencode($location) . "&key=" . $apiKey;
            // $apiUrl = $this->api_base_url. '?localityLanguage=' . $this->language . '&location=' . urlencode($location) . '&key=' . $this->api_key;

            $apiUrl = $this->api_base_url. '?localityLanguage=' . $this->language . '&latitude=' . $latitude . '&longitude=' . $longitude . '&key=' . $this->api_key;


            //                . '&latitude=' . $coordinates['latitude'] 
            // . '&longitude=' . $coordinates['longitude'] 

            // var_dump($apiUrl);
            // die();

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $response = curl_exec($ch);
            curl_close($ch);
        
            if ($response) {
                $data = json_decode($response, true);
        
                if (!empty($data)) {
                    // Extract details from the API response
                    $details = [
                        'continent' => $data['continent'] ?? 'Unknown',
                        'country' => $data['countryName'] ?? 'Unknown',
                        'city' => $data['locality'] ?? 'Unknown',
                        'latitude' => $data['latitude'] ?? 'Unknown',
                        'longitude' => $data['longitude'] ?? 'Unknown',
                    ];
        
                    return $details;
                }
            }
        
            return false; // Return false if no data found or API error        }
   
        }

    }

}



//bdc_4ee0e2fe73e04e998406d42efe504801

// [geolocation] => Array
// (
//     [latitude] => 39.4699075
//     [longitude] => -0.3762881
// )


// $idea = rbstravel_get_single_idea(45);
// // echo '<pre>' . print_r($idea['idea']['travel_destinations'], true) . '</pre>';
// die('COMMENT ME);
// $RBS_TRAVEL_Geocoding = new RBS_TRAVEL_Geocoding('bdc_4ee0e2fe73e04e998406d42efe504801');

// $result = array();
// foreach($idea['idea']['travel_destinations'] as $destination) {
//     $result[] = $RBS_TRAVEL_Geocoding->GetLocationDetails($destination['geolocation']['latitude'], $destination['geolocation']['longitude']);
// }

// echo '<pre>' . print_r($result, true) . '</pre>';
// // Array
// // (
// //     [0] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Reykjanesbær
// //             [latitude] => 64.000189741864
// //             [longitude] => -22.55417413782
// //         )

// //     [1] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Reykjavik
// //             [latitude] => 64.133333
// //             [longitude] => -21.933333
// //         )

// //     [2] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Bláskógabyggð
// //             [latitude] => 64.3103719
// //             [longitude] => -20.3023605
// //         )

// //     [3] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Mýrdalshreppur
// //             [latitude] => 63.4186315
// //             [longitude] => -19.0060479
// //         )

// //     [4] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Höfn
// //             [latitude] => 64.070414
// //             [longitude] => -16.9751755
// //         )

// //     [5] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Höfn
// //             [latitude] => 64.252855827495
// //             [longitude] => -15.208419809053
// //         )

// //     [6] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Skútustaðahreppur
// //             [latitude] => 65.642848
// //             [longitude] => -16.910133
// //         )

// //     [7] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Akureyri
// //             [latitude] => 65.683333
// //             [longitude] => -18.1
// //         )

// //     [8] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Skagafjörður
// //             [latitude] => 65.744646565869
// //             [longitude] => -19.638438778132
// //         )

// //     [9] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Hvammstangi
// //             [latitude] => 65.3235844
// //             [longitude] => -20.8854579
// //         )

// //     [10] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Borgarnes
// //             [latitude] => 64.664707404521
// //             [longitude] => -21.291970862325
// //         )

// //     [11] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Reykjavik
// //             [latitude] => 64.133333
// //             [longitude] => -21.933333
// //         )

// //     [12] => Array
// //         (
// //             [continent] => Europa
// //             [country] => IJsland
// //             [city] => Reykjanesbær
// //             [latitude] => 64.000189741864
// //             [longitude] => -22.55417413782
// //         )

// // )


// die('$RBS_TRAVEL_Geocoding');



