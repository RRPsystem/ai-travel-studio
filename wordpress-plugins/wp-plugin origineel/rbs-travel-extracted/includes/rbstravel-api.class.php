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


if (!class_exists('RBS_TRAVEL\INCLUDES\RBS_TRAVEL_Api')) {
    
    class RBS_TRAVEL_Api {
        
	
	private $api_url;
	private $api_username;
	private $api_password;
	private $microsite_id;
	
	private $token;
	
	
	private $args;
	
	
	private $use_authentication;

	
	
	
	function __construct($args = array()) {			//$api_user, $api_password, $microsite_id,
	    $this->api_url = RBS_TRAVEL_API_URL;
	    
	    // Get active API credentials from settings (supports multiple API sets)
	    $active_credentials = RBS_TRAVEL_Settings::GetActiveApiCredentials();
	    
	    // DEBUG: Log which credentials are being used
	    error_log('=== RBS TRAVEL API CONSTRUCTOR ===');
	    error_log('Active credentials: ' . print_r($active_credentials, true));
	    
	    if ($active_credentials) {
	        $this->api_username = $active_credentials['username'];
	        $this->api_password = $active_credentials['password'];
	        $this->microsite_id = $active_credentials['micrositeid'];
	        
	        error_log('API Username: ' . $this->api_username);
	        error_log('API Password: ' . (strlen($this->api_password) > 0 ? '[SET]' : '[EMPTY]'));
	        error_log('Microsite ID: ' . $this->microsite_id);
	    } else {
	        // Fallback if no credentials are configured
	        $this->api_username = '';
	        $this->api_password = '';
	        $this->microsite_id = '';
	        
	        error_log('WARNING: No active credentials found!');
	    }
	    
	    $this->parse_args($args);
	    $this->check_credentials();
	}
	
	
	private function  check_credentials() {
	    $message = array();
	    
	    if (strlen($this->api_url)  === 0) {
		$message[] = __('API Url is not set.', 'rbs-travel');
	    }
	    
	    if (strlen($this->api_username)  === 0) {
		$message[] = __('API Username is not set.', 'rbs-travel');
	    }

	    if (strlen($this->api_password)  === 0) {
		$message[] = __('API Password is not set.', 'rbs-travel');
	    }

	    if (strlen($this->microsite_id)  === 0) {
		$message[] = __('API MicrositeId is not set.', 'rbs-travel');
	    }	    
	    
	    if (count($message) !== 0) {
		$message[] = '<a href="' . get_admin_url(null, 'admin.php?page=rbstravel-settings') . '">' . __('Goto Settings', 'rbs-travel') . '</a>';
		wp_die(implode("\r\n", $message));
	    }
	    
	}
	
	private function parse_args($args) {
	    $this->args = array(
		'debug' => isset($args['debug']) && is_bool($args['debug']) ? $args['debug'] : false
	    );
	}
	

	private function fetch_token() {
	    $request_url = $this->api_url . '/authentication/authenticate';
	    $data = array(
		'username' => $this->api_username,
		'password' => $this->api_password,
		'micrositeId' => $this->microsite_id
	    );
	      
	    error_log('RBS Travel API: Fetching new token for microsite: ' . $this->microsite_id);
	      
	    $ch = curl_init();
	    curl_setopt_array($ch, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            )
	    ));

	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	      
	    $result = json_decode($response, true);
	      
	    error_log('RBS Travel API: Token fetch HTTP ' . $http_code);
	      
	    if (isset($result['token']) && isset($result['expirationInSeconds'])) {
            $token_data = $result;
            $token_data['timestamp'] = time();
            $token_data['microsite_id'] = $this->microsite_id; // Store which microsite this token is for
            
            // Store token per microsite to support multiple API sets
            $token_key = 'rbstravel_token_' . sanitize_key($this->microsite_id);
            update_option($token_key, $token_data);
            
            error_log('RBS Travel API: Token cached for ' . $this->microsite_id . ' (expires in ' . $result['expirationInSeconds'] . 's)');
	    } else {
	        error_log('RBS Travel API: Token fetch FAILED - ' . print_r($result, true));
		    wp_die('TEMP: Could not get token... Response: ' . $response);
	    }	      
	      
	    return $result['token'];    
	}


	private function get_token() {
	    // Use microsite-specific token cache to support multiple API sets
	    $token_key = 'rbstravel_token_' . sanitize_key($this->microsite_id);
	    $token_data = get_option($token_key, null);
	      
	    // No cached token - fetch new one
	    if ($token_data === null) {
	        error_log('RBS Travel API: No cached token found, fetching new token');
		$token = $this->fetch_token();
		return $token;
	    }
	      
	    // Check if token is expired (with 60 second safety margin)
	    $expires_at = $token_data['timestamp'] + $token_data['expirationInSeconds'];
	    $now = time();
	      
	    if ($now > ($expires_at - 60)) {
	        error_log('RBS Travel API: Token expired or expiring soon, fetching new token');
		$token = $this->fetch_token();
		return $token;
	    }
	      
	    $remaining = $expires_at - $now;
	    error_log('RBS Travel API: Using cached token (expires in ' . $remaining . 's)');
	      
	    return $token_data['token'];
	}
	
//	private function parse_query($search, $filters, $select, $limit, $page) {
//	    $query = array();
//	    
//	    if ($search !== null) {
//		$query[] = 'search=' . $search;
//	    }
//	    
//	    if (is_array($filters) && count($filters) !== 0) {
//		foreach($filters as $key => $value) {
//		    $query[] = $key . '=' . $value;
//		}
//	    }
//	    
//	    if (is_string($select) && strlen($select) !== 0) {
//		$query[] = 'select=' . $select;
//	    }
//	    
//	    $query[] = 'limit=' .$limit;
//	    $query[] = 'page=' . $page;
//	    
//	    
////	    return urlencode(implode('&', $query));
//	    return implode('&', $query);
//	    
//	}
	
	
//	public function ListSelectableFields($echo = false) {
//	    $selectables = array (
//		'isbn13',
//		'book_title',
//		'author_name',
//		'series_title',
//		'release_number',
//		'publisher_name',
//		'book_type',
//		'page_count',
//		'min_age',
//		'max_age',
//		'release_date',
//		'tag_name'
//	    );
//	    
//	    if ($echo) {
//		echo '<pre>' . print_r($selectables, true) . '</pre>';
//	    }
//	    return $selectables;
//	}
	
	public function GetIdeas($search = null, $filters = array(), $first = 0, $limit = 5) {	   
	    $token = $this->get_token();

	    $request_url = $this->api_url . '/travelidea/' . $this->microsite_id;

        if ($search !== null && strlen(trim($search)) !== 0) {
            $request_url .= '/info/' . trim($search);
        } else {
            $request_url .= '?first=' . $first;
            $request_url .= '&limit=' . $limit;
            $request_url .= '&lang=nl';
        }


	    
	    //$request_url .= '&search=' . $search;
	    
	    // var_dump($request_url);
//	    return;

	    $ch = curl_init();
	    $curl_opts = array(
            CURLOPT_URL => $request_url,
    //		CURLOPT_HEADER => true,
    //		CURLOPT_NOBODY => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'auth-token: ' . $token
            )
	    );
	    curl_setopt_array($ch, $curl_opts);
	    

	    $info = curl_getinfo($ch);
	    $error = array(
            'message' => curl_error($ch),
            'errno' => curl_errno($ch)
	    );
	    //echo '<pre>' . print_r($info, true) . '</pre>';
	    //echo '<pre>' . print_r($error, true) . '</pre>';
	    
	    
	    $response = curl_exec($ch);
	    curl_close($ch);
	    
	    
//// Then, after your curl_exec call:
//$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
//$header = substr($response, 0, $header_size);
//$body = substr($response, $header_size);	    
//echo '<pre>' . print_r($header, true) . '</pre>';
//echo '<pre>' . print_r($body, true) . '</pre>';	    
	    
	    
	    
	    $result = json_decode($response, true);
        $return = $result;
        // echo '<pre>' . print_r($result, true) . '</pre>';

        if ($search !== null) {
            if (isset($result['id'])) {
                $return = array(
                    'pagination' => array(
                        'firstResult' => 0,
                        'pageResults' => 1,
                        'totalResults' => 1
                    ),
                    'idea' => array(
                        0 => $result
                    )
                );
            } elseif(isset($result['status']) && $result['status'] == 'NOT_FOUND') {
                $return = array(
                    'pagination' => array(
                        'firstResult' => 0,
                        'pageResults' => 0,
                        'totalResults' => 0
                    ),
                    'idea' => array(
                        0 => null
                    ),
                    'not_found' => __('Idea Not Found.', 'rbs-travel')
                );                
            }

        } else {
            $return = $result;
        }
	    

	    return $return;
	    
//	    return false;
	    
	}
	
	
	public function GetInfo($travel_id) {
	    $token = $this->get_token();

	    // TRY IDEAS ENDPOINT FIRST
	    $request_url = $this->api_url . '/travelidea/' . $this->microsite_id . '/info/' . $travel_id;
        $request_url .= '?lang=nl';
        
        error_log('RBS Travel API (Info): Trying IDEAS endpoint: ' . $request_url);

	    $ch = curl_init();
	    $curl_opts = array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'auth-token: ' . $token
            )
	    );
	    curl_setopt_array($ch, $curl_opts);
	    
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    
	    $result = json_decode($response, true);
	    
	    // If IDEAS endpoint fails or returns error, try PACKAGE endpoint
	    if ($http_code != 200 || !isset($result) || isset($result['error']) || isset($result['status'])) {
	        error_log('RBS Travel API (Info): IDEAS endpoint failed (HTTP ' . $http_code . '), trying PACKAGE endpoint');
	        
	        // TRY PACKAGE ENDPOINT
	        $request_url = $this->api_url . '/package/' . $this->microsite_id . '/info/' . $travel_id;
	        $request_url .= '?lang=nl';
	        
	        error_log('RBS Travel API (Info): Trying PACKAGE endpoint: ' . $request_url);
	        
	        $ch = curl_init();
	        curl_setopt_array($ch, array(
                CURLOPT_URL => $request_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'auth-token: ' . $token
                )
	        ));
	        
	        $response = curl_exec($ch);
	        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        curl_close($ch);
	        
	        $result = json_decode($response, true);
	        error_log('RBS Travel API (Info): PACKAGE endpoint returned HTTP ' . $http_code);
	    } else {
	        error_log('RBS Travel API (Info): IDEAS endpoint succeeded (HTTP ' . $http_code . ')');
	    }
	    
	    return $result;

	}
	
	
	public function GetDetails($travel_id) {
	    $token = $this->get_token();

	    // TRY IDEAS ENDPOINT FIRST
	    $request_url = $this->api_url . '/travelidea/' . $this->microsite_id . '/' . $travel_id;
        $request_url .= '?lang=nl';
        
        error_log('RBS Travel API: Trying IDEAS endpoint: ' . $request_url);

	    $ch = curl_init();
	    $curl_opts = array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'auth-token: ' . $token
            )
	    );
	    curl_setopt_array($ch, $curl_opts);
	    
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    
	    $result = json_decode($response, true);
	    
	    // If IDEAS endpoint fails or returns error, try PACKAGE endpoint
	    if ($http_code != 200 || !isset($result) || isset($result['error']) || isset($result['status'])) {
	        error_log('RBS Travel API: IDEAS endpoint failed (HTTP ' . $http_code . '), trying PACKAGE endpoint');
	        
	        // TRY PACKAGE ENDPOINT
	        $request_url = $this->api_url . '/package/' . $this->microsite_id . '/' . $travel_id;
	        $request_url .= '?lang=nl';
	        
	        error_log('RBS Travel API: Trying PACKAGE endpoint: ' . $request_url);
	        
	        $ch = curl_init();
	        curl_setopt_array($ch, array(
                CURLOPT_URL => $request_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'auth-token: ' . $token
                )
	        ));
	        
	        $response = curl_exec($ch);
	        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	        curl_close($ch);
	        
	        $result = json_decode($response, true);
	        error_log('RBS Travel API: PACKAGE endpoint returned HTTP ' . $http_code);
	    } else {
	        error_log('RBS Travel API: IDEAS endpoint succeeded (HTTP ' . $http_code . ')');
	    }
	    
	    return $result;
	}
	
	
	public function GetCruiseDetails($cruise_id) {
	    $token = $this->get_token();
	    
	    // Get cruise/ship details
	    $request_url = $this->api_url . '/cruise/' . $this->microsite_id . '/' . $cruise_id;
	    $request_url .= '?lang=nl';
	    
	    error_log('RBS Travel API: Getting cruise details: ' . $request_url);
	    
	    $ch = curl_init();
	    curl_setopt_array($ch, array(
            CURLOPT_URL => $request_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'auth-token: ' . $token
            )
	    ));
	    
	    $response = curl_exec($ch);
	    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    
	    if ($http_code == 200) {
	        $result = json_decode($response, true);
	        error_log('RBS Travel API: Cruise details retrieved successfully');
	        return $result;
	    } else {
	        error_log('RBS Travel API: Failed to get cruise details (HTTP ' . $http_code . ')');
	        return null;
	    }
	}
	
	
	
    }
    
}
