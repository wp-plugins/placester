<?php 

PLS_Saved_Search::init();
class PLS_Saved_Search {

	static $save_extension = 'pl_ss_';
	static $search_extension = 'pl_ssv_';

	function init () {
		add_action('wp_ajax_get_saved_search_filter', array(__CLASS__, 'ajax_check'));
		add_action('wp_ajax_nopriv_get_saved_search_filter', array(__CLASS__, 'ajax_check'));
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

	function save ($search_id, $value) {
		$key = self::generate_key( $search_id );
		update_option($key, $value);
	}

	function generate_key ( $search_id ) {
		$hash = sha1($search_id);
		$key = self::$save_extension . $hash;
		return $key;
	}

}