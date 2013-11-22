<?php

// Js in js/public/saved-search.js

//TODO
//Methods for generating the saved_search_form
//Methods for adding the saved search link to subshort codes in widgets section.

PL_Saved_Search::init();
class PL_Saved_Search {

	public static $save_extension = 'pl_ss_';

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));
		add_action('wp_ajax_nopriv_get_saved_search_filters', array(__CLASS__, 'ajax_get_saved_search_filters'));

		// AJAX endpoints for attaching saved searches to users (currently, ONLY exposed for authenticated users...)
		add_action('wp_ajax_add_saved_search_to_user', array(__CLASS__,'ajax_add_saved_search_to_user'));
		add_action('wp_ajax_delete_user_saved_search', array(__CLASS__, 'ajax_delete_user_saved_search'));
	}

	public static function generate_key ($search_id) {
		$hash = sha1($search_id);
		$key = self::$save_extension . $hash;
		
		return $key;
	}

	public static function save_search ($search_id, $search_filters) {
		$key = self::generate_key($search_id);

		// error_log("Search ID: $search_id");
		// error_log("Option key: $key");
		// error_log(var_export($search_filters, true));

		// Ensure these option-entries are NOT autoloaded on every request...
		return PL_Options::set($key, $search_filters, false);
	}

	public static function get_saved_search_filters ($search_id) {
		$key = self::generate_key($search_id);
		$result = PL_Options::get($key, false);

		return $result;
	}

	public static function ajax_get_saved_search_filters () {
		$result = array();
		$search_id = $_POST['search_id'];

		// Retrieve search filters associated with the given saved search ID...
		$filters = self::get_saved_search_filters($search_id);

		if (is_array($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					// This is how multidimensional arrays are stored in the name attribute in JS
					foreach ($value as $k => $v) {
						$result["{$key}[{$k}]"] = $v;
					}
				}
				else {
					// Otherwise, just store it regularly
					$result[$key] = $value;
				}
			}
		}

		echo json_encode($result);
		die();
	}

	// Clear all saved searches stored in the DB...
	public static function clear () {
		$saved_searches = $wpdb->get_results('SELECT option_name FROM ' . $wpdb->prefix . 'options ' ."WHERE option_name LIKE 'pl_ss_%'");
	    foreach ($saved_searches as $option) {
	        PL_Options::delete($option->option_name);
	    }
	}

	/*
	 * Functionality to handle associating saved searches with site users...
	 */

	public static function user_saved_search_key () {
		global $blog_id;

		return self::$save_extension . '_list_' . $blog_id;
	}
	
	public static function get_user_saved_searches ($user_id = null) {
		// Default return value is an empty array (i.e., no saved searches)
		$response = array();

		// Fallback to current user if user_id is not set...
		if (empty($user_id)) {
			// If the current user isn't authenticated, no point in continuing...
			if (!is_user_logged_in()) {
				return $response;
			}

			$user_id = get_current_user_id();
		}
		
		// Fetch saved searches
		$saved_searches = get_user_meta($user_id, self::user_saved_search_key(), true);
		if (!empty($saved_searches) && is_array($saved_searches)) {
			$response = $saved_searches;
		}

		return $response;
	}

    public static function ajax_add_saved_search_to_user () {
    	// error_log(var_export($_POST, true));

    	$search_url_path = $_POST['search_url_path'];
    	$saved_search_name = $_POST['search_name'];
    	$search_filters = $_POST['search_filters'];
		
    	// Add meta to user for saved searches...
    	if (!empty($search_filters) && is_array($search_filters)) {
    		$response = self::add_saved_search_to_user($search_filters, $saved_search_name, $search_url_path);
    	}
    	else {
    		$response = array("success" => false, "message" => "No search filters to save -- select some and try again");
    	}
    	
    	echo json_encode($response);
    	die();
    }

	public static function add_saved_search_to_user ($search_filters, $saved_search_name, $search_url_path) {
		// Default result...
		$success = false;
		$message = "";

		// Only works if request is coming from an authenticated user...
		$user_id = get_current_user_id();
		$saved_searches = self::get_user_saved_searches();

		if (!empty($search_filters) && is_array($search_filters) && !empty($user_id)) {			
			// Sort filter array by key so unique hash produced is consistent regardless of element order...
			ksort($search_filters);
			$unique_filters_hash = sha1(serialize($search_filters));
				
			// Make sure an entry with the same unique search has does not already exist -- if it does, don't add...
			if (isset($saved_searches[$unique_filters_hash])) {
				$message =  "A search with the same filters has already been saved";
			}
			else {
				// Construct full search URL based on current site's URL...
				$search_url_path = ($search_url_path[0] == '/') ? substr($search_url_path, 1) : $search_url_path;
				$search_url = trailingslashit(site_url()) . $search_url_path;

				$saved_searches[$unique_filters_hash] = array(
					'filters' => $search_filters, 
					'name' => $saved_search_name, 
					'url' => $search_url
				);

				$update_success = update_user_meta($user_id, self::user_saved_search_key(), $saved_searches);
				
				$success = empty($update_success) ? false : true;
				$message = ($success === false) ? "Could not save search -- please try again" : "";

				// error_log("Unique search hash: $unique_filters_hash");
				// error_log(var_export($saved_searches, true));
				// error_log("user_saved_search_key: " . self::user_saved_search_key());
			}
		}

		return array("success" => $success, "message" => $message);
	}

	public static function ajax_delete_user_saved_search () {
		// Get authenticated user's Wordpress ID...
		$user_id = get_current_user_id();

		if (!empty($user_id)) {
			$saved_search_hash_to_be_deleted = $_POST['unique_search_hash'];

			$saved_searches = self::get_user_saved_searches();

			if (isset($saved_searches[$saved_search_hash_to_be_deleted])) {
				unset($saved_searches[$saved_search_hash_to_be_deleted]);
			}

			$update_success = update_user_meta($user_id, self::user_saved_search_key(), $saved_searches);
			
			$response['success'] = empty($update_success) ? false : true;
			$response['message'] = ($response['success'] === false) ? "Could not save search -- please try again" : "";
		} 
		else {
			$response = array("success" => false, "message" => "User is not logged in");
		}

		echo json_encode($response);
		die();
	}

	/*
	 * UI + Views
	 */

	// Renders the saved search form overlay...
	public static function get_saved_search_registration_form () {
        ob_start();
        if (is_user_logged_in()) {
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-authenticated.php');
        }
        else {
			// include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-unauthenticated.php');
        }

        return ob_get_clean();
    }

    public static function get_saved_search_button () {
    	ob_start();
            include(trailingslashit(PL_FRONTEND_DIR) . 'saved-search-button.php');
        return ob_get_clean();	
    }

    public static function translate_key ($key) {
		static $translations = array(
			'location[locality]' => 'City',
			'location[postal]' => 'Zip Code',
			'location[neighborhood]' => 'Neighborhood',
			'metadata[min_sqft]' => 'Min Sqft',
			'purchase_types[]' => 'Purchase Type',
			'price_off' => 'Min Price',
			'metadata[min_beds]' => 'Min Beds',
			'metadata[min_baths]' => 'Min Baths',
			'metadata[min_price]' => 'Min Price',
			'sort_by' => 'Sort By',
        	'sort_type' => 'Sort Order'
		);

		$val = ( isset($translations[$key]) ? $translations[$key] : $key );
		return $val;
	}
}