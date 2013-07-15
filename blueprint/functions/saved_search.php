<?php 

PLS_Saved_Search::init();
class PLS_Saved_Search {

	static $save_extension = 'pl_ss_';
	static $search_extension = 'pl_ssv_';

	function init () {
		//register ajax endpoints
		add_action('wp_ajax_get_saved_search_filter', array(__CLASS__, 'ajax_check'));
		add_action('wp_ajax_nopriv_get_saved_search_filter', array(__CLASS__, 'ajax_check'));

		//ajax functions for client pages.
		add_action('wp_ajax_delete_client_saved_search', array(__CLASS__, 'delete_client_saved_search'));

		//register shortcodes
		add_shortcode( 'saved_search_list', array(__CLASS__, 'shortcode_render_search_list'));
	}

	public static function shortcode_render_search_list () {
		echo self::render_search_list();
		//TODO: echo js in here too.
	}

	public static function delete_client_saved_search () {

		$user_id = get_current_user_id();

		if (!empty($user_id)) {
			$saved_search_hash_to_be_deleted = $_POST['saved_search_option_key'];

			$saved_searches = PLS_Plugin_API::get_user_saved_searches();

			if ( isset($saved_searches[$saved_search_hash_to_be_deleted]) ) {
				unset( $saved_searches[$saved_search_hash_to_be_deleted] );
			}

			$response = PLS_Plugin_API::save_a_search($user_id, $saved_searches);

			echo $response;
			die();
		} else {
			echo json_encode(array('message' => 'User is not logged in'));
			die();
		}

		


	}

	public static function render_search_list () {
		$saved_searches = PLS_Plugin_API::get_user_saved_searches();

		ob_start();
			extract(array('saved_searches' => $saved_searches));
			include(trailingslashit(PLS_TPL_DIR) . 'saved-search.php');
		$saved_search_html = ob_get_clean();
		return $saved_search_html;
		// echo add_filter('pls_saved_search_list');
	}

	public static function search_to_skip ($key) {

		$keys_to_skip = array('location[address_match' => true);

		if (isset($keys_to_skip[$key])) {
			return true;
		} 
		return false;

	}

	function ajax_check () {
		$result = array();
		$saved_search = self::check($_POST['search_id']);

		foreach ($saved_search as $key => $value) {
			if (is_array($value)) {
				// this is how multidimensional arrays are stored in the name attribute
				// in js
				foreach ($value as $k => $v) {
					$result[ $key . '[' . $k . ']' ] = $v;
				}
			} else {
				//otherwise just store it regularly
				$result[$key] = $value;
			}
		}

		echo json_encode($result);
		die();
	}

	function check ($search_id) {
		
		$key = self::generate_key( $search_id );
		
		if ( $result = get_option($key, false) ) {
			return $result;	
		} else {
			self::save($search_id, $_POST);
			return false;
		}
	}

	public static function save ($search_id, $value, $new = true) {
		$key = self::generate_key( $search_id );
		if ( $new ) {
			// Setting 'no' ensures these option-entries are NOT autoloaded on every request...
			add_option($key, $value, '', 'no');
		}
		else {
			update_option($key, $value);
		}
	}

	public static function generate_key ( $search_id ) {
		$hash = sha1($search_id);
		$key = self::$save_extension . $hash;
		return $key;
	}

	// Clear all saved searches stored in the DB...
	function clear () {
		$saved_searches = $wpdb->get_results('SELECT option_name FROM ' . $wpdb->prefix . 'options ' ."WHERE option_name LIKE 'pls_ss_%'");
	    foreach ($saved_searches as $option) {
	        delete_option( $option->option_name );
	    }
	}

	public static function translate_key($key) {

		$translations = array(
			'location[locality' => 'City',
			'location[postal' => 'Zip Code',
			'location[neighborhood' => 'Neighborhood',
			'metadata[min_sqft' => 'Min Sqft',
			'purchase_types[' => 'Purchase Type',
			'price_off' => 'Min Price',
			'metadata[min_beds' => 'Min Beds',
			'metadata[min_baths' => 'Min Baths',
			'metadata[min_price' => 'Min Price'
			);

		if ( isset($translations[$key]) ) {
			return $translations[$key];
		} else {
			return $key;
		}
	}

}