<?php 

class PL_Compliance {

	function mls_message ($args) {
		extract(wp_parse_args($args, array('context' => false, 'agent_name' => false, 'office_name' => false, 'office_phone' => false, 'provider_id' => false)));
		$whoami = PL_Helper_User::whoami();

		//if this account has multiple providers. Accounts with just one appear in "provider"
		//if the requester passes in a provider_id, try to match it, else, do nothing.
		if ( $whoami['providers'] && $provider_id && isset($whoami['providers'][$provider_id] ) ) {
			//if the provider id matches, just set it to "provider" since that's what the rest of the code uses.
			//validate it too!
			$whoami['provider'] = PL_Validate::attributes($whoami['providers'][$provider_id], PL_Config::PL_API_USERS('whoami', 'returns', 'provider'));
		} 

		// pls_dump($whoami);
		if (!empty($whoami['provider']['disclaimer_on']) || !empty($whoami['provider']['office_on']) || !empty($whoami['provider']['agent_on'])) {
			$provider = $whoami['provider'];
			$response = array();

			// massage $provider['disclaimer'] by replacing {brokerage_name} with the actual brokerage name.
			if( $provider['disclaimer'] ) {
				$company_name = $whoami['name'];
				if( $company_name == '' || !$company_name ) {
					$company_name = 'the publisher of this website';
				}
				$provider['disclaimer'] = str_replace( '{brokerage_name}', $company_name, $provider['disclaimer'] );
			}

			if ( $context == 'listings') {
				$response['last_import'] = date_format(date_create($provider['last_import']), "jS F, Y g:i A.");
				if (isset($provider['disclaimer_on']['listings']) && !empty($provider['disclaimer_on']['listings'])) {
					$response['disclaimer'] = $provider['disclaimer'];	
					$response['img'] = $provider['first_logo'];
				}
				if (isset($provider['agent_on']['listings']) && !empty($provider['agent_on']['listings']) && $agent_name) {
					$response['agent_name'] = $agent_name;
				}
				if (isset($provider['office_on']['listings']) && !empty($provider['office_on']['listings']) && $office_name) {
					$response['office_name'] = $office_name;
				}
				if (isset($provider['office_phone_on']['listings']) && !empty($provider['office_phone_on']['listings']) && $office_name) {
					$response['office_phone'] = $office_phone;
				}

			} elseif ( $context == 'search') {
				$response['last_import'] = date_format(date_create($provider['last_import']), "jS F, Y g:i A.");
				if (isset($provider['disclaimer_on']['search']) && !empty($provider['disclaimer_on']['search'])) {
					$response['disclaimer'] = $provider['disclaimer'];	
					$response['img'] = $provider['first_logo'];
				}
				if (isset($provider['agent_on']['search']) && !empty($provider['agent_on']['search']) && $agent_name) {
					$response['agent_name'] = $agent_name;
				}
				if (isset($provider['office_on']['search']) && !empty($provider['office_on']['search']) && $office_name) {
					$response['office_name'] = $office_name;
				}
				if (isset($provider['office_phone_on']['search']) && !empty($provider['office_phone_on']['search']) && $office_name) {
					$response['office_phone'] = $office_phone;
				}

			} elseif ( $context == 'inline_search') {
				if (isset($provider['disclaimer_on']['inline_search']) && !empty($provider['disclaimer_on']['inline_search'])) {
					$response['disclaimer'] = $provider['disclaimer'];	
				}
				if (isset($provider['second_logo']['inline_search']) && !empty($provider['second_logo']['inline_search'])) {
				  $response['img'] = $provider['second_logo'];
				}
				if (isset($provider['agent_on']['inline_search']) && !empty($provider['agent_on']['inline_search']) && $agent_name) {
					$response['agent_name'] = $agent_name;
				}
				if (isset($provider['office_on']['inline_search']) && !empty($provider['office_on']['inline_search']) && $office_name) {
					$response['office_name'] = $office_name;
				}
				if (isset($provider['office_phone_on']['inline_search']) && !empty($provider['office_phone_on']['inline_search']) && $office_name) {
					$response['office_phone'] = $office_phone_on;
				}
			}
			return $response;
		} 
		return false;
	}
}