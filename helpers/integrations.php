<?php 

PL_Integration_Helper::init();
class PL_Integration_Helper {
	
	public static function init() {
		add_action('wp_ajax_create_integration', array(__CLASS__, 'create' ) );
		add_action('wp_ajax_new_integration_view', array(__CLASS__, 'new_integration_view') );
		add_action('wp_ajax_idx_prompt_view', array(__CLASS__, 'idx_prompt_view') );
		add_action('wp_ajax_idx_prompt_completed', array(__CLASS__, 'idx_prompt_completed_ajax') );
	}

	public static function create () {
		// TODO: Handle Phone Number if it exists!!!
		if (isset($_POST['phone'])) {
			// Send update to user options with new phone...
			$usr_response = PL_Helper_User::update_user(array('phone' => $_POST['phone']));
			//pls_dump($usr_response);

			unset($_POST['phone']);
		}

		$response = array('result' => false, 'message' => 'There was an error. Please try again.');
		$api_response = PL_Integration::create(wp_kses_data($_POST));
		// pls_dump($api_response, $api_response['id']);
		if (isset($api_response['id'])) {
			$response = array('result' => true, 'message' => 'You\'ve successfully submitted your integration request. This page will update momentarily');
		} elseif (isset($api_response['validations'])) {
			$response = $api_response;
		} elseif (isset($api_response['code']) && $api_response['code'] == '102') {
			$response = array('result' => false, 'message' => 'You are already integrated with an MLS. To enable multiple integrations call sales at (800) 728-8391');
		}
		echo json_encode($response);
		die();
	}

	public static function new_integration_view () {
		PL_Router::load_builder_partial('integration-form.php', array('wizard' => true));
		die();
	}

	public static function idx_prompt_view () {
		PL_Router::load_builder_partial('idx-prompt.php');
		die();
	}

	/*
	 * If either through the plugin sign-up dialog flow, or the customizer
	 */
	public static function idx_prompt_completed ($mark_completed = false) {
		$key = 'idx_prompt_completed';

		// If $mark_complete is true, try to set the option and store the outcome -- otherwise,
		// check if it currently exists and retrieve its value...
		$exists = ( $mark_completed ? PL_Options::set($key, true) : $exists = PL_Options::get($key) );
		
		return $exists;
	}

	public static function idx_prompt_completed_ajax () {
		self::idx_prompt_completed(isset($_POST['mark_completed']));
		die();
	}

	public static function integration_pending () {
		$integration = PL_Integration::get();
		return !empty($integration[0]['id']);
	}

	public static function get () {
		$response = array();
		$integration = PL_Integration::get();
		$whoami = PL_Helper_User::whoami();
		$listings = PL_Listing::get(array('limit' => 1));
		$locations = PL_Listing::locations();
		return array('integration_status' => array('integration' => $integration, 'whoami' => $whoami, 'listings' => $listings, 'locations' => $locations));
	}

	public static function mls_list () {
		$mls_list = PL_Integration::mls_list();
		return $mls_list;
	}

//end of class
}