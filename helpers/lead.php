<?php 

PL_Lead_Helper::init();

class PL_Lead_Helper {

	const USER_META_KEY = 'pl_lead_id';

	private static $lead_details_default = array(
		'id' => '',
		'email' => '(Not Provided)',
		'first_name' => '(Not Provided)',
		'last_name' => '(Not Provided)',
		'phone' => '(Not Provided)',
		'created' => '(Not Provided)',
		'updated' => '(Not Provided)',
		'saved_searches' => 0
	);

	public static function init () {
		// Basic AJAX endpoints
		add_action('wp_ajax_datatable_my_leads', array(__CLASS__, 'ajax_get_leads'));
		add_action('wp_ajax_update_lead', array(__CLASS__, 'ajax_update_lead'));
		add_action('wp_ajax_delete_lead', array(__CLASS__, 'ajax_delete_lead'));
	}

	public static function ajax_delete_lead () {
		echo json_encode(array('result' => 1, 'data_received' => json_encode($_POST)));
		die();
	}

	public static function ajax_update_lead () {
		echo json_encode(array('result' => 1, 'data_received' => json_encode($_POST)));
		die();
	}

	public static function create_lead ($args = array()) {
		// Try to push lead to CRM (if one is linked/active)...
		self::add_lead_to_CRM($args);	
		
		return PL_Lead::create($args);
	}	

	public static function create_lead_ajax () {
		$api_response = self::create_lead($_POST);
		echo json_encode($api_response);
		die();
	}

	public static function add_lead_to_CRM ($args = array()) {
		// Check to see if site is actively linked to a CRM...
		$activeCRMKey = 'pl_active_CRM';
		$crm_id = PL_Options::get($activeCRMKey);
		
		if (!empty($crm_id)) {
			// Load CRM libs...
			$path_to_CRM = trailingslashit(PL_LIB_DIR) . 'CRM/controller.php';
			include_once($path_to_CRM);

			// Call necessary lib to add the contact to the active/registered CRM...
			if (class_exists('PL_CRM_Controller')) {
				PL_CRM_Controller::callCRMLib('createContact', $args);
			}
		}
	}

	public static function get_lead_id ($wp_user_id = null) {
		// Default this to null (indicates failure to callers...)
		$lead_id = null;

		// Get currentauthenticated user's Wordpress ID if no invalid one is passed in...
		$user_id = empty($wp_user_id) ? get_current_user_id() : $wp_user_id;

		if (!empty($user_id)) {
			$lead_id = get_user_meta($user_id, self::USER_META_KEY);
		}

		return $lead_id;
	}

	// Fetch a site user's details by his/her unique lead ID (managed externally, stored in WP's usermeta table)
	public static function lead_details ($args = array(), $wp_user_id = null) {
		// $details = array();

		$details = array(
			'id' => '2',
			'email' => 'john@smith.com',
			'first_name' => 'Jane',
			'last_name' => 'Johnson',
			'phone' => '123 123 1234',
			'created' => 'Today',
			'updated' => 'Yesterday',
			'saved_searches' => 5,
			'favorited_listings' => 3
		);

		$lead_id = null;

		// See if the lead id was passed -- if not, try to fetch it based on a WP user id...
		$lead_id = empty($args['id']) ? self::get_lead_id($wp_user_id) : $args['id'];

		if (!empty($lead_id)) {	
			// Fetch details from the API...
			$details = PL_Lead::details($lead_id, $args);

			// Format response...
			$details['full_name'] = $details['first_name'] . ' ' . $details['last_name'];
			$details = wp_parse_args($details, self::$lead_details_default);
		}

		return $details;
	}

	public static function get_lead_details_by_id ($lead_id) {
		$args = array('id' => $lead_id);
		return self::lead_details($args);
	}

	public static function update_lead ($args = array()) {
		// See if the lead id was passed -- if not, try to fetch it based on a WP user id...
		if (empty($args['id'])) {
			$args['id'] = self::get_lead_id();
		}

		return PL_Lead::update($args);
	}

	public static function get_leads ($filters = array()) {
		// Get leads from model...
		// $api_response = PL_Lead::get($filters);
		$api_response = array(
			'total' => 2,
			'leads' => array(
				array(
					'id' => '1',
					'email' => 'john@smith.com',
					'first_name' => 'john',
					'last_name' => 'smith',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				),
				array(
					'id' => '2',
					'email' => 'john@smith.com',
					'first_name' => 'Jane',
					'last_name' => 'Johnson',
					'phone' => '123 123 1234',
					'created' => 'Today',
					'updated' => 'Yesterday',
					'saved_searches' => 5
				)
			)
		);

		return $api_response;
	}

	public static function ajax_get_leads () {
		// Get all leads associated with this site...
		$api_response = self::get_leads();
		
		// build response for datatables.js
		$leads = array();
		foreach ($api_response['leads'] as $key => $lead) {
			// $images = $listing['images'];
			$leads[$key][] = $lead['created'];
			$lead['full_name'] = $lead['first_name'] . ' ' . $lead['last_name'];
			// $leads[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$leads[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">' .
			 					$lead['full_name'] . 
			 				'</a>
			 				<div class="row_actions">
			 				<a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id='. $lead['id'] .'&edit=1" >
			 					Edit
			 				</a>
			 				<span>|</span>
			 				<a href="' . ADMIN_MENU_URL . '?page=placester_my_leads&id=' . $lead['id'] . '">
			 					View
			 				</a>
			 				<span>|</span>
			 				<a class="red" id="pls_delete_listing" href="#" ref="'.$lead['id'].'">
			 					Delete
			 				</a>
			 				</div>';
			// $leads[$key][] = $listing["location"]["postal"];
			
			$leads[$key][] = $lead['email'];
			$leads[$key][] = $lead['phone'];
			$leads[$key][] = $lead['updated'];
			$leads[$key][] = $lead['saved_searches'];
		}

		// Required for datatables.js to function properly
		$response = array();
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $leads;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		
		echo json_encode($response);
		die();
	}

}