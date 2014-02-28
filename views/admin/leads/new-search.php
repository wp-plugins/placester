<?php 

$lead_details = PL_Lead_Helper::get_lead_details_by_id($_GET['id']);


 ?>

<h2>Create a New Search for <?php echo $lead_details['full_name'] ?></h2>

<div class="new_search_wrapper both">
	<p class="saved-searches-title both">1. Define the Users Search</p>
	<p class="both">Using the filters below, pick what search criteria should define what updates your lead should recieve.</p>
	<div class="global_filter_col">
		<?php PL_Global_Filters::get_listing_attributes(); ?>	
	</div>
	<div class="global_filter_col form_item">
		<form action="" id="global_filter_form">
			<?php PL_Form::generate_form( PL_Config::PL_API_LISTINGS('get', 'args'), array('method'=>'POST', 'include_submit' => false, 'wrap_form' => false, 'echo_form' => true, 'title' => false, 'id' => 'global_filter_form' ) ); ?>		
		</form>
	</div>
	<div class="global_filter_col filter_button">
		<a class="button-secondary" id="add-single-filter">Add Filter</a>	
	</div>	
	<div class="global_filter_col" id="global_filter_message"></div>
	<div class="both"></div>
	<form action="" id="active_filters" class="pls_active_filters">
			<ul>
				<li class="titles">
					<span class="col1">Attribute Name</span>
					<span class="col2">Value</span>
					<span class="col3">Remove</span>
				</li>
				<p id="empty">No Filters are active. Select one above.</p>
			</ul>
		
	</form>	
</div>

<div class="new_search_wrapper both">
	<p class="saved-searches-title both">2. Email Update Frequency</p>
	<p class="both">
		Select your update frequency: 
		<select>
			<option>No Email Updates</option>
			<option>Everyday</option>
			<option>Twice Weekly</option>
			<option>Once every week</option>
			<option>Once every 2 weeks</option>
			<option>Once every month</option>
		</select>
	</p>
</div>

<div class="new_search_wrapper both">
	<p class="saved-searches-title both">3. Confirm & Save</p>
	<p class="both">Double check the settings above and click "Confirm & Save".</p>
	<input type="submit" class="button-primary" value="Confirm & Save" tabindex="5" accesskey="p">
</div>

	
