<?php 

PL_Integration_Helper::init();
class PL_Integration_Helper {
	
	public function init() {
		add_action('wp_ajax_create_integration', array(__CLASS__, 'create' ) );
		add_action('wp_ajax_new_integration_view', array(__CLASS__, 'new_integration_view') );
	}

	public function create () {
		// TODO: Handle Phone Number if it exists!!!
		if (isset($_POST['phone']))
		{
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

	public function new_integration_view() {
		PL_Router::load_builder_partial('integration-form.php', array('wizard' => true));
		die();
	}

	public function get () {
		$response = array();
		$integration = PL_Integration::get();
		$whoami = PL_Helper_User::whoami();
		$listings = PL_Listing::get();
		$locations = PL_Listing::locations();
		return array('integration_status' => array('integration' => $integration, 'whoami' => $whoami, 'listings' => $listings, 'locations' => $locations));
	}

	public function mls_list () {
		$mls_list = PL_Integration::mls_list();
		return $mls_list;
	}

//end of class
}