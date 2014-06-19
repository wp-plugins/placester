<?php

PL_Lead_Capture_Helper::init();
class PL_Lead_Capture_Helper {

	private static $forward_address_options_key = 'pls_lead_forward_addresses';

	public static function init () {
		//register ajax endpoints
		add_action('wp_ajax_set_forwarding_addresses', array(__CLASS__, 'update_lead_forwarding_addresses')); 
	}

	public static function update_lead_forwarding_addresses () {
		$email_addresses = explode(',', $_POST['email_addresses']);

		PL_Options::set(self::$forward_address_options_key, $email_addresses);
		echo true;
		die();
	}

	public static function get_lead_forwarding_addresses () {
		return get_option(self::$forward_address_options_key, array());
	}

	// used as a quick way of always including the right email addresses 
	// in outgoing mail based on the users forwarding settings.
	// reference: http://codex.wordpress.org/Function_Reference/wp_mail
	public static function merge_bcc_forwarding_addresses_for_sending ( $headers = array() ) {

		$email_addresses = self:: get_lead_forwarding_addresses();
		
		foreach ($email_addresses as $email) {
			$headers[] = 'Bcc: ' . $email;
		}

		return $headers;
	}

}