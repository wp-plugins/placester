<?php if (get_current_user_id()): ?>
	<div class="save_search_button_wrapper">
		<a target="_blank" class="pls_save_search" href="#pl_saved_search_register_form">Save this Search</a>
		<span id="pls_successful_saved_search" style="display: none">Search Successfully Saved! View it <a href="<?php echo PL_Membership::get_client_area_url(); ?>">here</a></span>
	</div>
<?php else: ?>
	<div>
		<a class="pl_register_lead_link" href="#pl_lead_register_form" style="font-size: 16px; color: white; margin-top: 20px; font-weight: bold">Save this Search</a>
	</div>
<?php endif; ?>
