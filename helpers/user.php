<?php 

PL_Helper_User::init();
class PL_Helper_User {
	
	public static function init() {
		add_action( 'wp_ajax_set_placester_api_key', array(__CLASS__, 'set_placester_api_key') );
		add_action( 'wp_ajax_existing_api_key_view', array(__CLASS__, 'existing_api_key_view') );
		add_action( 'wp_ajax_new_api_key_view', array(__CLASS__, 'new_api_key_view') );
		add_action( 'wp_ajax_free_trial_view', array(__CLASS__, 'free_trial_view') );
		add_action( 'wp_ajax_create_account', array(__CLASS__, 'create_account') );

		add_action( 'wp_ajax_ajax_log_errors', array(__CLASS__, 'ajax_log_errors') );
		add_action( 'wp_ajax_ajax_block_address', array(__CLASS__, 'ajax_block_address') );
		add_action( 'wp_ajax_ajax_default_address', array(__CLASS__, 'set_default_country') );
		add_action( 'wp_ajax_enable_community_pages', array(__CLASS__, 'enable_community_pages') );
		add_action( 'wp_ajax_whoami', array(__CLASS__, 'ajax_whoami') );
		add_action( 'wp_ajax_subscriptions', array(__CLASS__, 'ajax_subscriptions') );
		add_action( 'wp_ajax_start_subscription_trial', array(__CLASS__, 'start_subscription_trial') );
		add_action( 'wp_ajax_update_user', array(__CLASS__, 'ajax_update_user') );
		add_action( 'wp_ajax_update_google_places', array(__CLASS__, 'update_google_places') );

		add_action( 'wp_ajax_demo_data_on', array(__CLASS__, 'toggle_listing_demo_data' ) );
		add_action( 'wp_ajax_demo_data_off', array(__CLASS__, 'toggle_listing_demo_data' ) );
	}

	public static function ajax_subscriptions() {
		echo json_encode(PL_User::subscriptions());
		die();
	}

	public static function start_subscription_trial() {
		$args = array('source' => $_POST['source']);
		echo json_encode(PL_User::start_subscription_trial($args));
		die();
	}

	public static function update_google_places() {
		if (isset($_POST['places_key'])) {
			$response = PL_Option_Helper::set_google_places_key($_POST['places_key']);
			echo json_encode($response);
		}
		die();
	}

	public static function ajax_whoami() {
		echo json_encode(PL_User::whoami());
		die();
	}

	public static function whoami($args = array(), $api_key = null) {
		return PL_User::whoami($args, $api_key);
	}

	/* Creates a new placester account -- returns the new account's API key upon success */
	public static function create_account() {
		if ($_POST['email']) {
			$success = PL_User::create(array('email'=>$_POST['email']));
			$response = $success ? $success : array('outcome' => false, 'message' => 'There was an error. Is that a valid email address?');
		} else {
			$response = array('outcome' => false, 'message' => 'No Email Provided');
		}

		echo json_encode($response);
		die();
	}

	public static function set_placester_api_key() {
		$result = PL_Option_Helper::set_api_key($_POST['api_key']);
		echo json_encode($result);
		die();
	}

	public static function ajax_update_user() {
		$whoami = self::whoami();
		$_POST['id'] = $whoami['user']['id'];
		$_POST['email'] = $whoami['user']['email'];
		
		$api_response = self::update_user($_POST);
		if ($api_response['id']) {
			$response = array('result' => true, 'message' => 'Account successfully updated.');
		} 
		elseif (isset($api_response['validations'])) {
			$response = $api_response;
		}
		else {
			$response = array('result' => false, 'message' => 'There was an error. Please try again.');
		}

		echo json_encode($response);
		die();
	}

	public static function update_user ($args = array()) {
		$response = PL_User::update($args);
		return $response;
	}


	/*
	 * Returns rendered HTML for use in dialogs regarding plugin activation
	 */

	public static function new_api_key_view() {
		$admin_email = get_option('admin_email');
		PL_Router::load_builder_partial('sign-up.php', array('email' => $admin_email));
		die();	
	}

	public static function existing_api_key_view() {
		PL_Router::load_builder_partial('existing-placester.php');
		die();
	}

	public static function free_trial_view() {
		PL_Router::load_builder_partial('free-trial.php');
		die();
	}


	/*
	 * Get/Setter callbacks for generic plugin settings
	 */

	public static function ajax_log_errors() {
		if ( $_POST['report_errors'] == 'true') {
			$report_errors = 1;
		} else {
			$report_errors = 0;
		}
		$api_response = PL_Option_Helper::set_log_errors($report_errors);
		if ($api_response) {
			if ($report_errors) {
				echo json_encode(array('result' => true, 'message' => 'You successfully turned on error reporting'));
			} else {
				echo json_encode(array('result' => true, 'message' => 'You successfully turned off errror reporting'));
			}
		} else {
			echo json_encode(array('result' => false, 'message' => 'There was an error. Please try again.'));
		}
		die();
	}

	public static function ajax_block_address() {
		if ( $_POST['use_block_address'] == 'true') {
			$block_address = 1;
		} else {
			$block_address = 0;
		}
		$api_response = PL_Option_Helper::set_block_address($block_address);
		if ($api_response) {
			PL_Pages::delete_all();		
			if ($block_address) {
				echo json_encode(array('result' => true, 'message' => 'You successfully turned on block addresses'));
			} else {
				echo json_encode(array('result' => true, 'message' => 'You successfully turned off block addresses'));
			}
		} else {
			echo json_encode( array('result' => false, 'message' => 'There was an error. Please try again.') );
		}
		die();
	}

	public static function set_default_country() {
		if (isset($_POST['country'])) {
			$response = PL_Option_Helper::set_default_country($_POST['country']);
			if ($response) {
				echo json_encode(array('result' => true, 'message' => 'You successfully saved the default country'));
			} else {
				echo json_encode(array('result' => true, 'message' => 'Thats already your default country'));
			}
		} else {
			echo json_encode( array('result' => false, 'message' => 'There was an error. Country was not provided') );
		}
		die();
	}

	public static function get_default_country() {
		$response = PL_Option_Helper::get_default_country();
		if (empty($response)) {
			return array('default_country' => 'US');
		} 
		return array('default_country' => $response);
		
	}
	
	public static function enable_community_pages() {
		$enable_pages = 1; 
		if( $_POST['enable_pages'] === 'false' || ! $_POST['enable_pages'] ) {
			$enable_pages = 0;
		}
		 
		$updated = PL_Option_Helper::set_community_pages($enable_pages);
		$result = true;
		if( ! $updated || ! $enable_pages ) $result = false;

		// TODO: some bad caching occurs here, do fix 
		if( $result ) {
			echo json_encode(array('result' => true, 'message' => 'You successfully enabled community pages'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'You successfully disabled community pages'));
		}
		die();
	}

	/*
	 * Functionality to toggle listing demo data on and off...
	 */

	public static function toggle_listing_demo_data($state = false) {
		// Determine the new state of this flag...
		switch ($_POST["action"]) {
			case "demo_data_on":
				$state = true;
				$msg = "You're site is now set to use demo data";
				break;
			case "demo_data_off":
				$state = false;
				$msg = "Demo data successfully turned off";
				break;
		}
		
		// Conditionally toggle on or off...
		PL_Option_Helper::set_demo_data_flag($state);

		// Clear cache to get rid of all remnants of existing listings...
		PL_Cache::clear();

		echo json_encode(array("message" => $msg));
		die();
	}
	
}	