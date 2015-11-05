<?php

class PL_Custom_Attributes {

	private static $get_memo = null;

	public static function get($args = array()) {
		// If args array is empty, check for memoized response...
		if (empty($args) && !is_null(self::$get_memo)) {
			// error_log("Using memoized custom_attributes!!!");
			return self::$get_memo;
		}

		$config = PL_Config::PL_API_CUST_ATTR('get');
		$request = array_merge(array( "api_key" => PL_Option_Helper::api_key()), PL_Validate::request($args, $config['args']));
		$response = PL_HTTP::send_request($config['request']['url'], $request);
		// error_log(var_export($response, true));
		if ($response) {
			foreach ($response as $attribute => $value) {
		 		$response[$attribute] = PL_Validate::attributes($value, $config['returns']);
		 	}
		}
		else {
			$response = array();
		}

		// Memoize response if args array is empty...
		if (empty($args)) {
			// error_log("Memoizing custom_attributes...");
			self::$get_memo = $response;
		}
		
		return $response;
	}

//end class
}