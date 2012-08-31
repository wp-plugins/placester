<?php
/**
 * Template Name: Listings Search
 *
 * This is the template for "Listings" search results page.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */
?>
<style type="text/css">
	#custom {
		display:none;
	}
</style>
<section class="complex-search grid_8 alpha">
	<?php echo PLS_Partials::get_listings_search_form('context=listings&ajax=1&theme_option_id=listing-search-page'); ?>
	<div class="clear"></div>
	<div style="font-size: 16px; font-weight: bold; float: right;" id="pls_listings_search_results"><span id="pls_num_results"></span> listing match your search.</div>
</section>
<div class="grid_8 alpha" id="content" role="main">
    <?php echo PLS_Partials::get_listings_list_ajax('context=listings_search&table_id=placester_listings_list'); ?>
    <?php PLS_Listing_Helper::get_compliance(array('context' => 'search', 'agent_name' => false, 'office_name' => false)); ?>
</div>

