<?php $lead_details = PL_Lead_Helper::get_lead_details_by_id($_GET['id']); ?>

<input id="lead_id" type="hidden" value="<?php echo $_GET['id'] ?>" >
<h2 class="person-name-contact">
  <span class="name">Edit <?php echo $lead_details['full_name'] ?></span>  
  <a href="#" id="cancel" class="add-new-h2">Cancel</a>
</h2>
<div class="both"></div>
<?php 

//var_dump(PL_Config::PL_API_LEAD('create', 'args'));

 ?>

<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Lead Information',
						 	'id' => 'edit-lead-box',
						 	'style' => '',
						 	'content' => PL_Form::generate_form(
						 		PL_Config::PL_API_LEAD('create', 'args'),
							 	array('method'=>'POST', 
								 	'include_submit' => true, 
								 	'wrap_form' => true, 
								 	'echo_form' => false,
								 	'title' => true
							 	) 
						 ) ) ) ?>

<?php //PL_Form::generate_form( PL_Config::PL_API_LEAD('create', 'args'), array('method'=>'POST', 'include_submit' => true, 'wrap_form' => false, 'echo_form' => true, 'title' => true, 'id' => 'global_filter_form' ) ); ?>		