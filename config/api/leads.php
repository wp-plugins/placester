<?
// This should be the same for all actions...
$lead_URI = 'http://localhost:8081/api/v1/lead/';

global $PL_API_LEAD;
$PL_API_LEAD = array(
	'get' => array(
		'request' => array(
			'url' => $lead_URI,
			'type' => 'GET'
		),
		'args' => array(
			'email' => array('type' => 'text','group' => 'Basic', 'label' => 'Email'),
			'first_name' => array('type' => 'text','group' => 'Basic', 'label' => 'First Name'),
			'last_name' => array('type' => 'text','group' => 'Basic', 'label' => 'Last Name'),
			// 'created' => array('type' => 'date','group' => 'Basic', 'label' => 'Date Created'),
			// 'saved_searches' => array('type' => 'text','group' => 'Basic', 'label' => '# of Saved Searches'),
			// 'favorited_listings' => array('type' => 'text','group' => 'Basic', 'label' => '# of Favorites'),
			'limit' => 20,
			'offset' => 0
		),
		'returns' => array(
			'lead' => array(
				'id' => '1',
				'email' => '',
				'first_name' => '',
				'last_name' => '',
				'phone' => '',
				'created' => '',
				'last_updated' => ''
			)
		)
	),
	'create' => array(
		'request' => array(
			'url' => $lead_URI,
			'type' => 'POST'
		),
		'args' => array(
			'id' => '',
			'email' => array('type' => 'text','group' => '', 'label' => 'Email'),
			'first_name' => array('type' => 'text','group' => '', 'label' => 'First Name'),
			'last_name' => array('type' => 'text','group' => '', 'label' => 'Last Name'),
			'phone' => array('type' => 'text','group' => '', 'label' => 'Phone'),
			'created' => '',
			'last_updated' => '',
			'saved_searches' => '',
			'favorited_listings' => ''
		)
	),
	'details' => array(
		'request' => array(
			'url' => $lead_URI,
			'type' => 'POST'
		),
		'args' => array(
			'id' => '',
			'meta' => array(
				'meta_id' => '',
				'meta_key' => '',
				'meta_value' => ''
			),
			'notifications' => array(
				'type' => '',
				'meta_id' => '',
				'schedule' => ''
			)
		),
		'returns' => array(
			'id' => '',
			'email' => '',
			'first_name' => '',
			'last_name' => '',
			'phone' => '',
			'created' => '',
			'last_updated' => '',
			'meta' => array(),
			'notifications' => array()
		)
	),
	'update' => array(
		'request' => array(
			'url' => $lead_URI,
			'type' => 'POST'
		),
		'args' => array(
			'id' => '',
			'meta' => array(
				'meta_id' => '',
				'meta_key' => '',
				'meta_value' => '',
				'meta_op' => ''
			),
			'notifications' => array(
				'type' => '',
				'meta_id' => '',
				'schedule' => '',
				'notification_op'
			)
		),
		'returns' => array(
			'success' => '',
			'message' => ''
		)
	),
	'delete' => array(
		'request' => array(
			'url' => $lead_URI,
			'type' => 'POST'
		),
		'args' => array(
			'id' => ''
		),
		'returns' => array(
			'success' => '',
			'message' => ''
		)
	)
);

?>
