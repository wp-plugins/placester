<div class="wrap">
	<?php if (isset($_GET['id'])): ?>
		<div id="loading_overlay" style="display:none">Updating Listing...</div>	
	<?php else: ?>
		<div id="loading_overlay" style="display:none">Creating Listing...</div>	
	<?php endif ?>
	
	<div id="manage_listing_message"></div>
	<form action="<?php echo admin_url('/admin-ajax.php')?>" method="<?php echo isset($_GET['id']) ? 'PUT' : 'POST' ?>" enctype="multipart/form-data" id="add_listing_form">  
		<?php if (isset($_GET['id'])): ?>
			<input type="hidden" name="id" value="<?php echo $_GET['id'] ?>">
			<?php
				$curated_data = is_array($_POST['cur_data']) ? $_POST['cur_data'] : array();
				$uncurated_data = is_array($_POST['uncur_data']) ? $_POST['uncur_data'] : array();

				// Alter structure of $_POST (contains listing details) to match listing edit/creation structure...
				$_POST['metadata'] = array_merge($curated_data, $uncurated_data);
				
				// These are no longer needed...
				unset($_POST['cur_data'], $_POST['uncur_data']);
			?>
		<?php endif ?>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
			<div id="side-info-column" class="inner-sidebar"> <!-- Right Sidebar -->
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php PL_Router::load_builder_partial('publish-box-sidebar.php'); ?>
					
				</div>
			</div>
			<div id="post-body">
				<div id="post-body-content">
					<div class="property-type-selects">
						<!-- Compound Type Select -->
						<?php echo PL_Form::item('compound_type', PL_Config::PL_API_LISTINGS('create', 'args', 'compound_type'), 'POST'); ?>
					    <!-- Property Type Input -->
					    <?php echo PL_Form::generate_form( PL_Config::bundler('PL_API_LISTINGS', array('create', 'args'), array('property_type')), array('method'=>'POST', 'include_submit' => false, 'wrap_form' => false, 'echo_form' => false) ); ?>
					</div>					
					<div class="clear"></div>
					<?php PL_Router::load_builder_partial('admin-box.php',
						array('title' => 'Location',
						  'content' => PL_Form::generate_form( 
						  	PL_Config::bundler('PL_API_LISTINGS',
						  		$keys = array('create', 'args'),
						  		$bundle = array('location')
						  	), 
						array('method'=>'POST',
							 'include_submit' => false,
							  'wrap_form' => false,
							  'echo_form' => false
						) ) ) ) ?>

					<!-- Residential Sales -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Residential Sales Details',
						 	'id' => 'res_sale_details_admin_ui_basic',
						 	'style' => '',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('beds', 'baths', 'half_baths','price', 'avail_on', 'sqft') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="res_sale" class="advanced_toggle show_advanced" >Show Advanced</a>'
						  ) ) ?>

					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Residential Sales Details',
						 	'id' => 'res_sale_details_admin_ui_advanced',
						 	'style' => '',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lt_sz', 'lt_sz_unit', 'pk_spce','hoa_mand','hoa_fee','landr_own','style', 'ngb_trans', 'ngb_shop','ngb_swim','ngb_court','ngb_park','ngb_trails','ngb_stbles','ngb_golf', 'ngb_med', 'ngb_bike','ngb_cons','ngb_hgwy','ngb_mar','ngb_pvtsch','ngb_pubsch','ngb_uni','grnt_tops','air_cond','cent_ac','frnshed','cent_ht','frplce','hv_ceil','wlk_clst','hdwdflr','tle_flr','fm_lv_rm','lft_lyout','off_den','dng_rm','brkfst_nk','dshwsher','refrig','stve_ovn','stnstl_app','attic','basemnt','washer','dryer','lndry_in','lndry_gar','blc_deck_pt','yard','swm_pool','jacuzzi','sauna','cble_rdy','hghspd_net') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) ) ) ?>

					<!-- Residential Rental -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Residential Rental Details',
						 	'id' => 'res_rental_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('beds', 'baths', 'half_baths','price', 'avail_on', 'sqft') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="res_rental" class="advanced_toggle show_advanced" >Show Advanced</a>'
						 ) ) ?>
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Residential Rental Details',
						 	'id' => 'res_rental_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lt_sz_unit', 'pk_spce', 'lse_type', 'lse_trms', 'deposit', 'pk_lease', 'ngb_trans', 'ngb_shop','ngb_swim','ngb_court','ngb_park','ngb_trails','ngb_stbles','ngb_golf', 'ngb_med', 'ngb_bike','ngb_cons','ngb_hgwy','ngb_mar','ngb_pvtsch','ngb_pubsch','ngb_uni','grnt_tops','air_cond','cent_ac','frnshed','cent_ht','frplce','hv_ceil','wlk_clst','hdwdflr','tle_flr','fm_lv_rm','lft_lyout','off_den','dng_rm','brkfst_nk','dshwsher','refrig','stve_ovn','stnstl_app','attic','basemnt','washer','dryer','lndry_in','lndry_gar','blc_deck_pt','yard','swm_pool','jacuzzi','sauna','cble_rdy','hghspd_net', 'lt_sz') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="res_rental" class="advanced_toggle show_advanced" >Show Advanced</a>'
						 ) ) ?>

					 <!-- Vacation Rentals -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Vacation Details',
						 	'id' => 'vac_rental_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('accoms','beds', 'baths', 'half_baths','price', 'avail_on', 'sqft', 'pk_spce') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="vac_rental" class="advanced_toggle show_advanced" >Show Advanced</a>'
						) ) ?>

					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Vacation Details',
						 	'id' => 'vac_rental_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lt_sz', 'lt_sz_unit', 'avail_info', 'lse_type', 'lse_trms', 'deposit', 'pk_lease', 'ngb_trans', 'ngb_shop','ngb_swim','ngb_court','ngb_park','ngb_trails','ngb_stbles','ngb_golf', 'ngb_med', 'ngb_bike','ngb_cons','ngb_hgwy','ngb_mar','ngb_pvtsch','ngb_pubsch','ngb_uni','grnt_tops','air_cond','cent_ac','frnshed','cent_ht','frplce','hv_ceil','wlk_clst','hdwdflr','tle_flr','fm_lv_rm','lft_lyout','off_den','dng_rm','brkfst_nk','dshwsher','refrig','stve_ovn','stnstl_app','attic','basemnt','washer','dryer','lndry_in','lndry_gar','blc_deck_pt','yard','swm_pool','jacuzzi','sauna','cble_rdy','hghspd_net') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) ) ) ?>

					<!-- Sublets -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Sublet Details',
						 	'id' => 'sublet_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('beds', 'baths', 'half_baths','price', 'avail_on', 'sqft') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="sublet" class="advanced_toggle show_advanced" >Show Advanced</a>' 
					) ) ?>

					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Sublet Details',
						 	'id' => 'sublet_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('cats','dogs','cond', 'lt_sz', 'lt_sz_unit', 'pk_spce', 'lse_type', 'lse_trms', 'deposit', 'pk_lease', 'ngb_trans', 'ngb_shop','ngb_swim','ngb_court','ngb_park','ngb_trails','ngb_stbles','ngb_golf', 'ngb_med', 'ngb_bike','ngb_cons','ngb_hgwy','ngb_mar','ngb_pvtsch','ngb_pubsch','ngb_uni','grnt_tops','air_cond','cent_ac','frnshed','cent_ht','frplce','hv_ceil','wlk_clst','hdwdflr','tle_flr','fm_lv_rm','lft_lyout','off_den','dng_rm','brkfst_nk','dshwsher','refrig','stve_ovn','stnstl_app','attic','basemnt','washer','dryer','lndry_in','lndry_gar','blc_deck_pt','yard','swm_pool','jacuzzi','sauna','cble_rdy','hghspd_net') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) ) ) ?>

					<!-- Commercial Rentals -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Commercial Rental Details',
						 	'id' => 'comm_rental_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('prop_name', 'cons_stts', 'bld_suit', 'avail_on', 'sqft', 'price') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="comm_rental" class="advanced_toggle show_advanced" >Show Advanced</a>' 
						 ) ) ?>

					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Commercial Rental Details',
						 	'id' => 'comm_rental_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lse_trms', 'lse_type', 'sublease', 'rate_unit', 'min_div', 'max_cont', 'bld_sz', 'lt_sz', 'lt_sz_unit', 'year_blt' ) 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) ) ) ?>

					 <!-- Commercial Sales -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Commercial Sales Details',
						 	'id' => 'comm_sale_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('prop_name', 'cons_stts', 'sqft', 'price', 'pk_spce', 'min_div', 'max_cont', 'bld_sz') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="comm_sale" class="advanced_toggle show_advanced" >Show Advanced</a>'
						 ) ) ?>

					 <?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Commercial Sales Details',
						 	'id' => 'comm_sale_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lt_sz', 'lt_sz_unit', 'year_blt') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 )
						 ) ) ?>

					<!-- Parking -->
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Basic Parking Details',
						 	'id' => 'park_rental_details_admin_ui_basic',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('park_type', 'avail_on', 'price') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) . '<a id="park_rental" class="advanced_toggle show_advanced" >Show Advanced</a>'
						) ) ?>

					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Advanced Parking Details',
						 	'id' => 'park_rental_details_admin_ui_advanced',
						 	'style' => 'display: none',
						 	'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('lse_trms', 'lse_type', 'deposit', 'valet', 'guard','heat','carwsh') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false,
							 	'title' => true
						 	) 
						 ) ) ) ?>


					<?php PL_Router::load_builder_partial('admin-box.php',
						array('title' => 'Images',
							 'content' => PL_Router::load_builder_partial(
							 	'add-listing-image.php',
							 	array('images' => @$_POST['images']),
							 	true
							 ) 
						) 
					) ?>
					<?php PL_Router::load_builder_partial('admin-box.php',
						 array('title' => 'Description',
						 	 'content' => PL_Form::generate_form(
						 	 	 PL_Config::bundler('PL_API_LISTINGS',
						 	 	 	 $keys = array('create', 'args'), 
						 	 	 	 $bundle = array( 
						 	 	 	 	array('metadata' => array('desc') 
						 	 	 	 ) 
						 	 	 )
						 	),
						 	array('method'=>'POST', 
							 	'include_submit' => false, 
							 	'wrap_form' => false, 
							 	'echo_form' => false
						 	) 
						 ) ) ) ?>
				</div>
			</div>
		</div>
		<br class="clear">
	</form>
</div>