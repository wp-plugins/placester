<?php 
PLS_Featured_Listing_Option::register();
class PLS_Featured_Listing_Option {

	public static function register () {
		add_action('wp_ajax_list_options', array(__CLASS__, 'get_listings' ));
	}

	public static function init ( $params = array() ) {
		// pls_dump($params);
		ob_start();
			do_action( 'pl_featured_listings_head' );
			extract( $params );
			include( trailingslashit( PLS_OPTRM_DIR ) . 'views/featured-listings-inline.php' );
		return ob_get_clean();
	}

	public static function load ( $params = array() ) {
		ob_start();
			extract( $params );
			include( trailingslashit( PLS_OPTRM_DIR ) . 'views/featured-listings.php' );
		echo ob_get_clean();
	}

	public static function get_filters ( $params = array() ) {
		ob_start();
			extract( $params );
			include( trailingslashit( PLS_OPTRM_DIR ) . 'views/featured-listings-filters.php' );
		echo ob_get_clean();	
	}

	public static function get_datatable ( $params = array() ) {
		ob_start();
			extract( $params );
			include( trailingslashit( PLS_OPTRM_DIR ) . 'views/featured-listings-datatable.php' );
		echo ob_get_clean();	
	}

	public static function get_listings () {
		$response = array();
		//exact addresses should be shown. 
		$_POST['address_mode'] = 'exact';

		// Sorting
		$columns = array('location.address');
		$_POST['sort_by'] = $columns[$_POST['iSortCol_0']];
		$_POST['sort_type'] = $_POST['sSortDir_0'];
		if ( isset( $_POST['agency_only'] ) && $_POST['agency_only'] == 'on' ) {
			$_POST['agency_only'] = 1;
		}
		if ( isset( $_POST['non_import'] ) && $_POST['non_import'] == 'on' ) {
			$_POST['non_import'] = 1;
		}
		
		// text searching on address
		// $_POST['location']['address'] = @$_POST['sSearch'];
		$_POST['location']['address_match'] = 'like';

		// Pagination
		$_POST['limit'] = $_POST['iDisplayLength'];
		$_POST['offset'] = $_POST['iDisplayStart'];		
		
		// Get listings from model
		$api_response = PLS_Plugin_API::get_listings($_POST, false);
		
		// build response for datatables.js
		$listings = array();
		foreach ($api_response['listings'] as $key => $listing) {
			$listings[$key][] = $listing['location']['address'] . ', ' . $listing['location']['locality'] . ' ' . $listing['location']['region']; 
			$listings[$key][] = !empty($listing['images']) ? '<a id="listing_image" href="' . $listing['images'][0]['url'] . '"  style="display: inline-block" onclick=\'return false;\'>Preview</a>' :  'No Image'; 
			$listings[$key][] = '<a id="pls_add_option_listing" href="#" ref="'.$listing['id'].'">Make Featured</a>';
		}

		// Required for datatables.js to function properly.
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $listings;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		echo json_encode($response);

		//wordpress echos out a 0 randomly. die prevents it.
		die();
	}

}