<?php

class PL_Lead {

	const API_KEY_LABEL = 'pl_site_api_key';

	// Fetch new API key for leads endpoint...
	public static function getAPIKey () {
		return PL_Options::get(self::API_KEY_LABEL);
	}

	public static function constructRequestArgs ($args, $endpoint) {
		// Validate the passed args... 
		$req_args = PL_Validate::request($args, PL_Config::PL_API_LEAD($endpoint, 'args'));

		// Add the API to the args list...
		$req_args['api_key'] = self::getAPIKey();

		// Fetch request params from config...
		$url = PL_Config::PL_API_LEAD($endpoint, 'request', 'url');
		$http_type = PL_Config::PL_API_LEAD($endpoint, 'request', 'type');

		// Put it all together...
		$req = array(
			'args' => $req_args,
			'url' => $url,
			'http_type' => $http_type
		);

		return $req;
	}

	// Retrieve a list of all leads associated with this site based on the provided args...
	public static function get ($args = array()) {
		// Construct request args...
		$request = self::constructRequestArgs($args, 'get');
		
		// Make HTTP call to API...
		$response = PL_HTTP::send_request($request['url'], $request['args'], $request['http_type']);
		
		// Validate response...
		if (isset($response) && isset($response['leads']) && is_array($response['leads'])) {
			foreach ($response['leads'] as $key => $listing) {
				$response['leads'][$key] = PL_Validate::attributes($listing, PL_Config::PL_API_LEAD('get','returns', 'lead'));
			}
		} 
		else {
			$response = PL_Validate::attributes($response, array('leads' => array(), 'total' => 0));
		}
		
		return $response;
	}

	// Create a new lead entity...
	public static function create ($args = array()) {
		// Construct request args...
		$request = self::constructRequestArgs($args, 'create');

		// Make HTTP call to API...
		$response = PL_HTTP::send_request($request['url'], $request['args'], $request['http_type']);
	}

	// Given a lead ID, fetch that lead's details... (includes meta, notifications, etc.)
	public static function details ($args = array()) {
		// Construct request args...
		$request = self::constructRequestArgs($args, 'details');
		
		// Make HTTP call to API...
		$response = PL_HTTP::send_request($request['url'], $request['args'], $request['http_type']);
	}

	// Update the lead entity using its ID...
	public static function update ($args = array()) {
		// Construct request args...
		$request = self::constructRequestArgs($args, 'update');
		
		// Make HTTP call to API...
		$response = PL_HTTP::send_request($request['url'], $request['args'], $request['http_type']);
	}

	// Delete the lead entity using its ID...
	public static function delete ($args = array()) {
		// Construct request args...
		$request = self::constructRequestArgs($args, 'delete');

		// Make HTTP call to API...
		$response = PL_HTTP::send_request($request['url'], $request['args'], $request['http_type']);
	}
}

?>