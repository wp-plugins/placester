<?php
	// Returns activation functionality for turning on an integrated CRM.
	//
	// NOTE: $id and $api_key must be set...

	if (empty($id)) { return; }

	// Populate the CRM info object if it's not set...
	if (empty($api_key)) {
		$crm_obj = PL_CRM_Controller::getCRMInstance($id);
		$api_key = $crm_obj->getAPIKey();
	}
?>

<div class="activate-crm-box">
	<div class="current-key">
		API Key: <span><?php echo $api_key; ?></span>
	</div>
	<div class="button-group">
		<a href="#" id="reset_<?php echo $id ?>" class="reset-creds-button button-secondary">Enter new API Key</a>
		<a href="#" id="activate_<?php echo $id ?>" class="activate-button button-primary">Activate CRM</a>
	</div>
</div>