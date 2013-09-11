<?php

// Js in js/public/saved-search.js

//TODO
//Methods for generating the saved_search_form
//Methods for adding the saved search link to subshort codes in widgets section.

PL_Saved_Search::init();
class PL_Saved_Search {

	public static $user_saved_keys = 'pls_saved_searches';
	public static $saved_key_prefix = 'pl_sk_';

	public static function init () {
		add_action( 'wp_ajax_save_search', array(__CLASS__,'ajax_save_search'));
	}
	
    public static function ajax_save_search () {

    	$link_to_search = $_POST['link_to_search'];
    	$saved_search_name = $_POST['name_of_saved_search'];

    	$clean_search_form_data = self::purge_unneeded_form_data($_POST['search_form_key_values']);
		
    	// add meta to user for searches
    	if( ! empty( $clean_search_form_data ) ) {

    		$response = self::add_new_saved_search($clean_search_form_data, $saved_search_name, $link_to_search );

    		echo json_encode( $response );

    	} else {
    		echo array('message' => 'No Form data to save!');
    	}

    	die();
    }

	public static function add_new_saved_search ($clean_search_form_data, $saved_search_name, $link_to_search ) {

		$user_id = get_current_user_id();
		$saved_searches = self::get_user_saved_searches();

		if( ! empty( $clean_search_form_data ) && ! empty( $user_id ) ) {			

			$search_value = json_encode( $clean_search_form_data );

			$search_hash = PLS_Saved_Search::generate_key( $search_value );
				
			$saved_searches[$search_hash] = $search_value;
			$saved_searches[$search_hash] = array('search_value' => $search_value, 'search_name' => $saved_search_name, 'link_to_search' => $link_to_search);

			$update_success = self::save_a_search($user_id, $saved_searches);

			return $update_success;
		} else {
			return false;
		}
	}

	public static function save_a_search ($user_id, $saved_searches) {

		if (!empty($saved_searches) && is_array($saved_searches)) {
			return update_user_meta($user_id, self::$user_saved_keys, $saved_searches);
		} else {
			return array('message' => "You didn't pass any saved searches");
		}
	}


	public static function get_user_saved_searches( $user_id = 0 ) {
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
			$response = array();
		} else {
			$response = $saved_searches[0];
		}
		// pls_dump($saved_searches);
		return $response;
	}

	// Returns the saved search form overlay
	public static function get_saved_search_registration_form () {

        ob_start();
        if ( ! is_user_logged_in() ) {
            include( trailingslashit(PL_FRONTEND_DIR) . 'saved-search-unauthenticated.php');
        } else {
            include( trailingslashit(PL_FRONTEND_DIR) . 'saved-search-unauthenticated.php');
            // include( trailingslashit(PL_FRONTEND_DIR) . 'saved-search-authenticated.php');
        }
        return ob_get_clean();
    }

    public static function get_saved_search_button () {
    	ob_start();
            include( trailingslashit(PL_FRONTEND_DIR) . 'saved-search-button.php');
        return ob_get_clean();	
    }

    private static function purge_unneeded_form_data ($form_data) {
    	// Irrelevant data to the search form filters
    	$internal_params = array( 'action', 'submit');
    	foreach( $internal_params as $internal ) {
    		if (isset( $form_data[$internal])) { unset($form_data[$internal]); }
    	}

    	return $form_data;
    }

    public static function get_save_search_link () {
		return '<a href="#" class="pls_save_search">Save Search</a>';
	}
}