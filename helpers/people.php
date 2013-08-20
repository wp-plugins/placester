<?php 

PL_People_Helper::init();
class PL_People_Helper {
	
	public static $user_saved_keys = 'pls_saved_searches';
	public static $saved_key_prefix = 'pl_sk_';

	public static function init() {
		add_action('wp_ajax_add_person', array(__CLASS__, 'add_person_ajax' ) );
		add_action('wp_ajax_get_favorites', array(__CLASS__, 'get_favorites_ajax' ) );
	}

	public static function add_person($args = array()) {
		return PL_People::create($args);
	}	

	public static function add_person_ajax() {
		$api_response = PL_People::create($_POST);
		echo json_encode($api_response);
		die();
	}

	public static function get_favorites_ajax () {
		$placester_person = self::person_details();
		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			echo json_encode($placester_person['fav_listings']);
		} else {
			echo json_encode(array());
		}
		die();
	}
	
	public static function add_member_saved_search( $search_args ) {
		$user_id = get_current_user_id();
		if( empty( $user_id ) ) {
			echo false; 
			die();
		}
		
		$saved_searches = self::get_user_saved_links();
		
		$search_value = json_encode( $search_args );
		
		// TODO: sync with existing saved searches
		if( ! empty( $search_value ) ) {
			// add to user searches
			$search_hash = PLS_Saved_Search::generate_key( $search_value );
// 			$saved_searches[] = $search_hash;
			$saved_searches[$search_hash] = $search_value;
			$update_success = update_user_meta($user_id, self::$user_saved_keys, $saved_searches);

			// add to options table
// 			$new = get_option($search_hash, false) ? true : false;
// 			PLS_Saved_Search::save($search_hash, $search_value, $new);
		} else {
			$update_success = false;
		}
		
		echo $update_success;
		die();
	}

	public static function update_person_details ($person_details) {
		$placester_person = self::person_details();
		return PL_People::update(array_merge(array('id' => $placester_person['id']), $person_details));
	}

	public static function person_details () {
		$wp_user = PL_Membership::get_user();
		$placester_id = get_user_meta($wp_user->ID, 'placester_api_id');
		if (is_array($placester_id)) { $placester_id = implode($placester_id, ''); }
		if (empty($placester_id)) {
			return array();
		}
		return PL_People::details(array('id' => $placester_id));
	}

	public static function associate_property($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array($property_id);
		if (isset($placester_person['fav_listings']) && is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				$new_favorites[] = $fav_listings['id'];
			}
		}
		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}	

	public static function unassociate_property($property_id) {
		$placester_person = self::person_details();
		$new_favorites = array();
		if (is_array($placester_person['fav_listings'])) {
			foreach ($placester_person['fav_listings'] as $fav_listings) {
				if ($fav_listings['id'] != $property_id) {
					$new_favorites[] = $fav_listings['id'];
				}
			}
		}
		return PL_People::update(array('id' => $placester_person['id'], 'fav_listing_ids' => $new_favorites ) );
	}

	/**
	 * Helper function for a user's unique Placester ID (managed by Rails, stored in WP's usermeta table)
	 * @return User's Placester ID
	 */
	public static function get_placester_user_id() {
		$wp_user = PL_Membership::get_user();
		$placester_id = get_user_meta($wp_user->ID, 'placester_api_id');
		if (is_array($placester_id)) { $placester_id = implode($placester_id, ''); }
		
		return $placester_id;
	}
	
	public static function get_user_saved_links( $user_id = 0 ) {
		// fallback to current user if user_id is not set
		if( empty( $user_id ) ) {
			if( ! is_user_logged_in() ) {
				return array();
			}
			$user_id = get_current_user_id();
		}
		
		// fetch saved searches
		$saved_searches = get_user_meta($user_id, self::$user_saved_keys );
		if( empty( $saved_searches ) && ! is_array( $saved_searches ) ) {
			$saved_searches = array();
		}
		
		return $saved_searches;
	}
		
}