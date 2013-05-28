<?php 

// This global wrapper eventually needs to be removed -- need time to alter its consumers...
function placester_get_api_key() { return PL_Option_Helper::api_key(); }

class PL_Option_Helper {
	
	public static function api_key() {
	    $api_key = PL_Options::get('placester_api_key');
		if (strlen($api_key) <= 0) {
			return false;
		}
	    return $api_key;	    
	}

	public static function set_api_key($new_api_key) {
		if (empty($new_api_key) ) {
			return array('result' => false,'message' => 'API key must not be empty');
		}
		if (get_option('placester_api_key') == $new_api_key) {
			return array('result' => false,'message' => 'You\'re already using that Placester API Key.');
		}
		$response = PL_Helper_User::whoami(array(), $new_api_key);
		if ($response && isset($response['user'])) {
			$option_result = PL_Options::set('placester_api_key', $new_api_key);
			if ($option_result) {
				// Nuke the cache if they change their API Key
				PL_Cache::invalidate();
				return array('result' => true,'message' => 'You\'ve successfully changed your Placester API Key. This page will reload in momentarily.');
			} else {
				return array('result' => false,'message' => 'There was an error. Are you sure that\'s a valid Placester API key?');
			}
		} 
		return array('result' => false,'message' => 'That \'s not a valid Placester API Key.');
	}

	public static function set_google_places_key ($new_places_key) {
		if (get_option('placester_places_api_key') == $new_places_key) {
			return array('result' => false,'message' => 'You\'re already using that Places API Key.');
		} else {
			$response = update_option('placester_places_api_key', $new_places_key);
			if ($response) {
				return array('result' => true, 'message' => 'You\'ve successfully updated your Google Places API Key');
			} else {
				return array('result' => false, 'message' => 'There was an error. Please try again.');
			}
		}
	}

	public static function get_google_places_key () {
		$places_api_key = get_option('placester_places_api_key', '');
		return $places_api_key;
	}
	
	public static function get_community_pages_enabled() {
		$response = get_option('pls_enable_community_pages', false);
		
		if ($response == NULL ) {
			return false;
		}
		return $response;
	}

	public static function post_slug() {
	    $url_slug = get_option('placester_url_slug');
	    if (strlen($url_slug) <= 0) {
	    	$url_slug = 'listing';
	    }
	    return $url_slug;
	}

	public static function set_polygons ($add = false, $remove_id = false) {
		$polygons = PL_Options::get('pls_polygons', array() );
		if ($add) {
			$polygons[] = $add;
		}
		if ($remove_id !== false) {
			if (isset($polygons[$remove_id])) {
				unset($polygons[$remove_id]);

			}
		}
		$response = PL_Options::set('pls_polygons', $polygons);
		return $response;
	}

	public static function get_polygons () {
		return PL_Options::get('pls_polygons', array());	
	}

	public static function set_global_filters ($args) {
		extract(wp_parse_args($args, array('filters' => array())));
		return PL_Options::set('pls_global_search_filters', $filters);		
	}

	public static function get_global_filters () {
		return PL_Options::get('pls_global_search_filters');		
	}

	public static function set_community_pages ($enable_pages = 0) {
		$response = PL_Options::set('pls_enable_community_pages', $enable_pages);
		return $response;
	}

	public static function set_log_errors ($report_errors = 1) {
		$response = PL_Options::set('pls_log_errors', $report_errors);
		return $response;
	}

	public static function get_log_errors () {
		$response = PL_Options::get('pls_log_errors');	
		if ($response == NULL) {
			return true;
		}
		return $response;
	}

	public static function set_block_address ($block_address = 0) {
		$response = PL_Options::set('pls_block_address', $block_address);
		return $response;
	}

	public static function get_block_address () {
		$response = PL_Options::get('pls_block_address');	
		if ($response == NULL) {
			return true;
		}
		return $response;
	}

	public static function set_default_country ($default_country) {
		return PL_Options::set('pls_default_country', $default_country);
	}

	public static function get_default_country () {
		$response = PL_Options::get('pls_default_country');	
		return $response;
	}

	public static function set_translations ($dictionary) {
		return PL_Options::set('pls_amenity_dictionary', $dictionary);
	}

	public static function get_translations () {
		$response = PL_Options::get('pls_amenity_dictionary');	
		return $response;
	}

	public static function set_demo_data_flag ($desired_state = false) {
		// If the desired state is true, demo data is turned on (opposite for false)
		PL_Options::set('pls_demo_data_flag', $desired_state);
	}

	public static function get_demo_data_flag () {
		$response = PL_Options::get('pls_demo_data_flag');
		return $response;
	}

//end of class
}