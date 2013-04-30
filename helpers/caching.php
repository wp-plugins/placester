<?php 
PL_Caching_Helper::init();
class PL_Caching_Helper {

	public static function init () {
		add_action('wp_ajax_delete_cache_item', array(__CLASS__, 'delete_item' ) );
		add_action('wp_ajax_user_empty_cache', array(__CLASS__, 'ajax_clear' ) );
	}

	public static function delete_item () {
		$option_name = $_POST['option_name'];
		$result = PL_Cache::delete($option_name);
		if ($result) {
			echo json_encode(array('result' => true, 'message' => 'Cache item successfully removed'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'There was an error. Cache item not removed. Please try again.'));
		}
		die();
	}

	public static function ajax_clear() {
		PL_Cache::clear();
		echo json_encode(array('result' => true, 'message' => 'You\'ve successfully cleared your cache'));
		die();
	}

}