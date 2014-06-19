<?php 

PL_Listing_Helper::init();

class PL_Listing_Helper {

	public static function init () {
		add_action('wp_ajax_datatable_ajax', array(__CLASS__, 'datatable_ajax'));
		add_action('wp_ajax_add_listing', array(__CLASS__, 'add_listing_ajax'));
		add_action('wp_ajax_update_listing', array(__CLASS__, 'update_listing_ajax'));
		add_action('wp_ajax_add_temp_image', array(__CLASS__, 'add_temp_image'));
		add_action('wp_ajax_delete_listing', array(__CLASS__, 'delete_listing_ajax'));
	}
	
	public static function results ($args = array(), $global_filters = true) {
		// Handle edge-case $args formatting and value...
		if (!is_array($args)) 
			{ $args = wp_parse_args($args); } 
		elseif (empty($args)) 
			{ $args = $_GET; }

		/* REMOVE */
		// if ($global_filters) {
		// 	error_log("\n[[[BEFORE:]]]\n" . var_export($args, true));
		// 	error_log("\n[[[FILTERS:]]]\n" . var_export(PL_Global_Filters::get_global_filters(), true));
		// }

		// If a list of specific property IDs was passed in, handle acccordingly...
		if (!empty($args['property_ids']))
			{ $args['listing_ids'] = $args['property_ids']; }

		// Respect the ability for this function to return results that do NOT respect global filters..
		if ($global_filters) 
			{ $args = PL_Global_Filters::merge_global_filters($args); }

		/* REMOVE */
		// if ($global_filters) {
		// 	error_log("\n[[[AFTER:]]]\n" . var_export($args, true));
		// }

		// Respect block address setting...
		$args['address_mode'] = ( PL_Option_Helper::get_block_address() ? 'exact' : 'polygon' );

		// Call the API with the given args...
		$listings = PL_Listing::get($args);

		// Make sure it contains listings, then process accordingly...
		if (!empty($listings['listings'])) {
			foreach ($listings['listings'] as $key => $listing) {
				$listings['listings'][$key]['cur_data']['url'] = PL_Page_Helper::get_url($listing['id']);
				$listings['listings'][$key]['location']['full_address'] = $listing['location']['address'] . ' ' . $listing['location']['locality'] . ' ' . $listing['location']['region'];
			}
		}

		// Make sure result is structured accordingly if empty/false/invalid...
		if (!is_array($listings) || !is_array($listings['listings'])) {
			$listings = array('listings' => array(), 'total' => 0); 
		}

		return $listings;
	}

	public static function details ($args) {
		if (empty($args['property_ids'])) { 
			return array('listings' => array(), 'total' => 0); 
		}

		// Global filters should be ignored if a specific set of property IDs are requested...
		return self::results($args, false);
	}

	/* For backwards compatibility with older themes stuck on old versions of Blueprint */
	public static function many_details ($args) { return self::details($args); }

	public static function single_listing ($property_id = null) {
		// Sanity check...
		if (empty($property_id)) { return null; }

		// Response is always bundled...
		$listings = self::details(array('property_ids' => array($property_id)));

		// If the listings key isn't empty, return it's first value (there should only be a single listing...)
		$listing = empty($listings['listings']) ? null : $listings['listings'][0];

		return $listing;
	}

	/*
	 * Used primarily to fetch listing details for the property inferred from the URL structure (i.e., for the property details template).
	 */
	public static function get_listing_in_loop () {
		global $post;
        
		// If the current $post is of type 'property', it's 'post_name' will be set to that listing's unique property ID (as set by the API)...
		$args = array('listing_ids' => array($post->post_name), 'address_mode' => 'exact');
		$response = PL_Listing::get($args);
		
		$listing_data = null;
		if ( !empty($response['listings']) ) {
			$listing_data = $response['listings'][0];
		}
		
		return $listing_data;		
	}

	public static function custom_attributes ($args = array()) {
		$custom_attributes = PL_Custom_Attributes::get(array('attr_class' => '2'));
		return $custom_attributes;
	}

	public static function datatable_ajax () {
		$response = array();
		//exact addresses should be shown. 
		$_POST['address_mode'] = 'exact';

		// Sorting
		// Controls the order of columns returned to the datatable
		$columns = array(
			'total_images',
			'location.address',
			'location.locality',
			'location.region',
			'location.postal',
			'zoning_types',
			'purchase_types',
			'property_type',
			'cur_data.beds',
			'cur_data.baths',
			'cur_data.price',
			'cur_data.sqft',
			'cur_data.avail_on'
		);
		$_POST['sort_by'] = $columns[$_POST['iSortCol_0']];
		$_POST['sort_type'] = $_POST['sSortDir_0'];
		
		// text searching on address
		$_POST['location']['address'] = @$_POST['sSearch'];
		$_POST['location']['address_match'] = 'like';

		// Pagination
		$_POST['limit'] = $_POST['iDisplayLength'];
		$_POST['offset'] = $_POST['iDisplayStart'];		

		// We need to check for and parse compound_type...
		if ( !empty($_POST['compound_type']) ) {
	      switch ($_POST['compound_type']) {
	        case "res_sale":
	          	$_POST['zoning_types'][] = 'residential';
	          	$_POST['purchase_types'][] = 'sale';
	          	break;
	        case "res_rental":
	          	$_POST['zoning_types'][] = 'residential';
	          	$_POST['purchase_types'][] = 'rental';
	          	break;
	        case "comm_sale":
	          	$_POST['zoning_types'][] = 'commercial';
	          	$_POST['purchase_types'][] = 'sale';
	          	break;
	        case "comm_rental":
	          	$_POST['zoning_types'][] = 'commercial';
	          	$_POST['purchase_types'][] = 'rental';
	          	break;
	        case "vac_rental":
	        case "park_rental":
	        case "sublet":
	        default:
	          	$_POST['zoning_types'] = false;
	          	$_POST['purchase_types'] = false;
	      }
		}
		
		// Get listings from model
		$api_response = PL_Listing::get($_POST);
		
		// build response for datatables.js
		$listings = array();
		foreach ($api_response['listings'] as $key => $listing) {
			$images = $listing['images'];
			$listings[$key][] = ((is_array($images) && isset($images[0])) ? '<img width=50 height=50 src="' . $images[0]['url'] . '" />' : 'empty');
			$listings[$key][] = '<a class="address" href="' . ADMIN_MENU_URL . '?page=placester_property_add&id=' . $listing['id'] . '">' . $listing["location"]["address"] . ' ' . $listing["location"]["locality"] . ' ' . $listing["location"]["region"] . '</a><div class="row_actions"><a href="' . ADMIN_MENU_URL . '?page=placester_property_add&id=' . $listing['id'] . '" >Edit</a><span>|</span><a href=' . PL_Page_Helper::get_url($listing['id']) . '>View</a><span>|</span><a class="red" id="pls_delete_listing" href="#" ref="'.$listing['id'].'">Delete</a></div>';
			$listings[$key][] = $listing["location"]["postal"];
			$listings[$key][] = implode($listing["zoning_types"], ', ') . ' ' . implode($listing["purchase_types"], ', ');
			$listings[$key][] = $listing["property_type"];
			$listings[$key][] = $listing["cur_data"]["beds"];
			$listings[$key][] = $listing["cur_data"]["baths"];
			$listings[$key][] = $listing["cur_data"]["price"];
			$listings[$key][] = $listing["cur_data"]["sqft"];
			$listings[$key][] = $listing["cur_data"]["avail_on"] ? date_format(date_create($listing["cur_data"]["avail_on"]), "jS F, Y g:i A.") : 'n/a';
		}

		// Required for datatables.js to function properly.
		$response['sEcho'] = $_POST['sEcho'];
		$response['aaData'] = $listings;
		$response['iTotalRecords'] = $api_response['total'];
		$response['iTotalDisplayRecords'] = $api_response['total'];
		echo json_encode($response);

		// WordPress echos out a 0 randomly, 'die' prevents it...
		die();
	}
	
	private static function prepare_post_array () {
		foreach ($_POST as $key => $value) {
			if (is_int(strpos($key, 'property_type'))) {
				unset( $_POST[$key] );
				if( $value !== 'false' && ! empty( $value ) ) {
					$_POST['metadata']['prop_type'] = $value;
				}
			}
		}
	}

	public static function add_listing_ajax () {
		self::prepare_post_array();
		
		$api_response = PL_Listing::create($_POST);
		echo json_encode($api_response);
		if (isset($api_response['id'])) {
			PL_Listing::get( array('listing_ids' => array($api_response['id'])) );
			// If on, turn off demo data...
			PL_Option_Helper::set_demo_data_flag(false);
		}
		die();
	}	

	public static function update_listing_ajax () {
		self::prepare_post_array();
		
		$api_response = PL_Listing::update($_POST);
		echo json_encode($api_response);
		if (isset($api_response['id'])) {
			PL_Pages::delete_by_name($api_response['id']);
			PL_Listing::get( array('listing_ids' => array($api_response['id'])) );
		}
		die();
	}

	public static function delete_listing_ajax () {
		$api_response = PL_Listing::delete($_POST);
		//api returns empty, with successful header. Return actual message so js doesn't explode trying to check empty.
		if (empty($api_response)) { 
			echo json_encode(array('response' => true, 'message' => 'Listing successfully deleted. This page will reload momentarily.'));
		} elseif ( isset($api_response['code']) && $api_response['code'] == 1800 ) {
			echo json_encode(array('response' => false, 'message' => 'Cannot find listing. Try <a href="'.admin_url().'?page=placester_settings">emptying your cache</a>.'));
		}
		die();
	}

	public static function add_temp_image () {
		$api_response = array();
		$response = array();
		if (isset($_FILES['files'])) {
			foreach ($_FILES as $key => $image) {
				if (isset($image['name']) && is_array($image['name']) && (count($image['name']) == 1))  {
					$image['name'] = implode($image['name']);
				}
				if (isset($image['type']) && is_array($image['type']) && (count($image['type']) == 1))  {
					$image['type'] = implode($image['type']);
				}
				if (isset($image['tmp_name']) && is_array($image['tmp_name']) && (count($image['tmp_name']) == 1))  {
					$image['tmp_name'] = implode($image['tmp_name']);
				}
				if (isset($image['size']) && is_array($image['size']) && (count($image['size']) == 1))  {
					$image['size'] = implode($image['size']);
				}
				if (!in_array($image['type'], array('image/jpeg','image/jpg','image/png','image/gif'))) {
					$api_response['message'] = "Unsupported file type - the image file must be a jpeg, jpg, png or gif file.".$image['type'];
				}
				else {
					$api_response = PL_Listing::temp_image($_POST, $image['name'], $image['type'], $image['tmp_name']);
				}
				$api_response = wp_parse_args( $api_response, array('filename'=>'','url'=>'','message'=>'') ); 
				// If no image URL is returned, the call failed -- so pass along the error message...
				if (empty($api_response['url'])) {
					$response[$key]['message'] = $api_response['message'];
				}
				else {
					$response[$key]['url'] = $api_response['url'];
				}
				$response[$key]['name'] = $api_response['filename'];
				$response[$key]['orig_name'] = $image['name'];
			}
		}		
		header('Vary: Accept');
		header('Content-type: application/json');
		echo json_encode($response);
		die();
	}

	// helper sets keys to values
	public static function types_for_options () {
		$options = array();
		$response = PL_Listing::aggregates(array('keys' => array('cur_data.prop_type')));
		if(!$response) {
			return array();
		}
		// might be able to do this faster with array_fill_keys() -pk
		foreach ($response['cur_data.prop_type'] as $key => $value) {
			$options[$value] = $value;
		}
		ksort($options);
		$options = array_merge(array('false' => 'Any'), $options);
		return $options;	
	}
	
	public static function locations_for_options ($return_only = false, $allow_globals = true) {
		$options = array();
		$response = null;
		
		// Use merge (with no arguments) to get the existing filters properly formatted for API calls...
		$global_filters = PL_Global_Filters::merge_global_filters();

		// If global filters related to location are set, incorporate those and use aggregates API...
		if ( $allow_globals && !empty($global_filters) && !empty($global_filters['location']) ) {
			// TODO: Move these to a global var or constant...
			$global_filters['keys'] = array('location.locality', 'location.region', 'location.postal', 'location.neighborhood', 'location.county');
			$response = PL_Listing::aggregates($global_filters);
		
			// Remove "location." from key names to conform to data standard expected by caller(s)...
			$alt = array();
			foreach ( $response as $key => $value ) {
				$new_key = str_replace('location.', '', $key);
				$alt[$new_key] = $value;
			}
			$response = $alt;
		}
		else {
			$response = PL_Listing::locations();
		}

		if (!$return_only) {
			return $response;
		}

		// Handle special case of 'return_only' being set to true...
		if ($return_only && isset($response[$return_only])) {
			foreach ($response[$return_only] as $key => $value) {
				$options[$value] = $value;
			}

			ksort($options);
			$options = array('false' => 'Any') + $options;
			
			return $options;	
		} 
		else {
			return array();	
		}
	}

	/* 
	 * Aggregates listing data to produce all unique values that exist for the given set of keys passed
	 * in as array.  Classified as "basic" because no filters are incorporated (might add this later...)
	 *
	 * Keys must be passed in a slightly different format than elsewhere, for example, to aggregate on
	 * city and state (i.e., find all unique cities and states present in all available listings), you'd
	 * pass the following value for $keys:
	 *     array('location.region', 'location.locality') // Notice the 'dot' notation in contrast to brackets...
	 *
	 * Returns an array containing keys for all those passed in (i.e. $keys) that themselves map to arrays 
	 * filled with the coresponding unique values that exist.
	 */
	public static function basic_aggregates ($keys) {
		// Need to specify an array that contains at least one key..
		if (!is_array($keys) || empty($keys)) { return array(); }

		$args = array('keys' => $keys);
		$response = PL_Listing::aggregates($args);

		return $response;
	}

	public static function polygon_locations ($return_only = false) {
		$response = array();
		$polygons = PL_Option_Helper::get_polygons();

		foreach ($polygons as $polygon) {
			if (!$return_only || $polygon['tax'] == $return_only) {
				$response[] = $polygon['name'];
			}
		}
		
		return $response;
	}

  /*
    I think the pricing choices returned here are confusing.
    Typically I would expect ranges to be in 1,000; 10,000; 100,000 increments.
    This might be friendlier if we:
    a. find the max-priced listing
    b. set the range max to that max rounded up to the nearest $10,000
    c. set the range min to the minimum rounded down to the nearest $100 (rentals will be affected, so not $1000)
    d. the range array should be returned with 20 items (that's manageble) in some decent increment determined by the total price range.
    e. also consider calculating two groups of prices -- find the min and max of lower range, min and max of higher range, and build array accordingly.
    HOWEVER: That will all come later, as I'm just trying to solve the initial problem of the filter not working. -pek
  */
	public static function pricing_min_options ($type = 'min') {

		$api_response = PL_Listing::get();
		$prices = array();
		foreach ($api_response['listings'] as $key => $listing) {
			$prices[] = $listing['cur_data']['price'];
		}
		
		sort($prices);
		
		if (is_array($prices) && !empty($prices)) {
		  // difference between highest- and lowest-priced listing, divided into 20 levels
			$range = round( ( end( $prices ) - $prices[0] ) / 20 );
			
			if ($type == 'max') {
				$range = range($prices[0], end($prices), $range);
				// add the highest price as the last element
				$range[] = end( $prices );
				// should flip max price to show the highest value first
				$range = array_reverse( $range );		
			} else {
				$range = range($prices[0], end($prices), $range);
			}
		} else {
		  $range = array('');		  
		}
	    // we need to return the array with keys == values for proper form creation
	    // (keys will be the option values, values will be the option's human-readable)
	    if( ! empty( $range ) && $range[0] !== '' ) {
	    	$range = array_combine( $range, $range );
	    	// let's format the human-readable; do not use money_format() because its dependencies are not guaranteed
	    	array_walk( $range, create_function( '&$value,$key', '$value = "$" . number_format($value,2);'));
	    }
		return $range;
	}

	public static function convert_default_country () {
		$country_array = PL_Helper_User::get_default_country();
		$country = (isset($country_array['default_country']) ? $country_array['default_country'] : 'US');
		return $country;
	}

	public static function supported_countries () {
		return array(
			"AD" => "Andorra (AD)",
			"AE" => "United Arab Emirates (AE)",
			"AF" => "Afghanistan (AF)",
			"AG" => "Antigua &amp; Barbuda (AG)",
			"AI" => "Anguilla (AI)",
			"AL" => "Albania (AL)",
			"AM" => "Armenia (AM)",
			"AO" => "Angola (AO)",
			"AQ" => "Antarctica (AQ)",
			"AR" => "Argentina (AR)",
			"AS" => "Samoa (American) (AS)",
			"AT" => "Austria (AT)",
			"AU" => "Australia (AU)",
			"AW" => "Aruba (AW)",
			"AX" => "Aaland Islands (AX)",
			"AZ" => "Azerbaijan (AZ)",
			"BA" => "Bosnia &amp; Herzegovina (BA)",
			"BB" => "Barbados (BB)",
			"BD" => "Bangladesh (BD)",
			"BE" => "Belgium (BE)",
			"BF" => "Burkina Faso (BF)",
			"BG" => "Bulgaria (BG)",
			"BH" => "Bahrain (BH)",
			"BI" => "Burundi (BI)",
			"BJ" => "Benin (BJ)",
			"BL" => "St Barthelemy (BL)",
			"BM" => "Bermuda (BM)",
			"BN" => "Brunei (BN)",
			"BO" => "Bolivia (BO)",
			"BQ" => "Bonaire Sint Eustatius &amp; Saba (BQ)",
			"BR" => "Brazil (BR)",
			"BS" => "Bahamas (BS)",
			"BT" => "Bhutan (BT)",
			"BV" => "Bouvet Island (BV)",
			"BW" => "Botswana (BW)",
			"BY" => "Belarus (BY)",
			"BZ" => "Belize (BZ)",
			"CA" => "Canada (CA)",
			"CC" => "Cocos (Keeling) Islands (CC)",
			"CD" => "Congo (Dem. Rep.) (CD)",
			"CF" => "Central African Rep. (CF)",
			"CG" => "Congo (Rep.) (CG)",
			"CH" => "Switzerland (CH)",
			"CI" => "Cote d'Ivoire (CI)",
			"CK" => "Cook Islands (CK)",
			"CL" => "Chile (CL)",
			"CM" => "Cameroon (CM)",
			"CN" => "China (CN)",
			"CO" => "Colombia (CO)",
			"CR" => "Costa Rica (CR)",
			"CU" => "Cuba (CU)",
			"CV" => "Cape Verde (CV)",
			"CW" => "Curacao (CW)",
			"CX" => "Christmas Island (CX)",
			"CY" => "Cyprus (CY)",
			"CZ" => "Czech Republic (CZ)",
			"DE" => "Germany (DE)",
			"DJ" => "Djibouti (DJ)",
			"DK" => "Denmark (DK)",
			"DM" => "Dominica (DM)",
			"DO" => "Dominican Republic (DO)",
			"DZ" => "Algeria (DZ)",
			"EC" => "Ecuador (EC)",
			"EE" => "Estonia (EE)",
			"EG" => "Egypt (EG)",
			"EH" => "Western Sahara (EH)",
			"ER" => "Eritrea (ER)",
			"ES" => "Spain (ES)",
			"ET" => "Ethiopia (ET)",
			"FI" => "Finland (FI)",
			"FJ" => "Fiji (FJ)",
			"FK" => "Falkland Islands (FK)",
			"FM" => "Micronesia (FM)",
			"FO" => "Faroe Islands (FO)",
			"FR" => "France (FR)",
			"GA" => "Gabon (GA)",
			"GB" => "Britain (UK) (GB)",
			"GD" => "Grenada (GD)",
			"GE" => "Georgia (GE)",
			"GF" => "French Guiana (GF)",
			"GG" => "Guernsey (GG)",
			"GH" => "Ghana (GH)",
			"GI" => "Gibraltar (GI)",
			"GL" => "Greenland (GL)",
			"GM" => "Gambia (GM)",
			"GN" => "Guinea (GN)",
			"GP" => "Guadeloupe (GP)",
			"GQ" => "Equatorial Guinea (GQ)",
			"GR" => "Greece (GR)",
			"GS" => "South Georgia &amp; the South Sandwich Islands (GS)",
			"GT" => "Guatemala (GT)",
			"GU" => "Guam (GU)",
			"GW" => "Guinea-Bissau (GW)",
			"GY" => "Guyana (GY)",
			"HK" => "Hong Kong (HK)",
			"HM" => "Heard Island &amp; McDonald Islands (HM)",
			"HN" => "Honduras (HN)",
			"HR" => "Croatia (HR)",
			"HT" => "Haiti (HT)",
			"HU" => "Hungary (HU)",
			"ID" => "Indonesia (ID)",
			"IE" => "Ireland (IE)",
			"IL" => "Israel (IL)",
			"IM" => "Isle of Man (IM)",
			"IN" => "India (IN)",
			"IO" => "British Indian Ocean Territory (IO)",
			"IQ" => "Iraq (IQ)",
			"IR" => "Iran (IR)",
			"IS" => "Iceland (IS)",
			"IT" => "Italy (IT)",
			"JE" => "Jersey (JE)",
			"JM" => "Jamaica (JM)",
			"JO" => "Jordan (JO)",
			"JP" => "Japan (JP)",
			"KE" => "Kenya (KE)",
			"KG" => "Kyrgyzstan (KG)",
			"KH" => "Cambodia (KH)",
			"KI" => "Kiribati (KI)",
			"KM" => "Comoros (KM)",
			"KN" => "St Kitts &amp; Nevis (KN)",
			"KP" => "Korea (North) (KP)",
			"KR" => "Korea (South) (KR)",
			"KW" => "Kuwait (KW)",
			"KY" => "Cayman Islands (KY)",
			"KZ" => "Kazakhstan (KZ)",
			"LA" => "Laos (LA)",
			"LB" => "Lebanon (LB)",
			"LC" => "St Lucia (LC)",
			"LI" => "Liechtenstein (LI)",
			"LK" => "Sri Lanka (LK)",
			"LR" => "Liberia (LR)",
			"LS" => "Lesotho (LS)",
			"LT" => "Lithuania (LT)",
			"LU" => "Luxembourg (LU)",
			"LV" => "Latvia (LV)",
			"LY" => "Libya (LY)",
			"MA" => "Morocco (MA)",
			"MC" => "Monaco (MC)",
			"MD" => "Moldova (MD)",
			"ME" => "Montenegro (ME)",
			"MF" => "St Martin (French part) (MF)",
			"MG" => "Madagascar (MG)",
			"MH" => "Marshall Islands (MH)",
			"MK" => "Macedonia (MK)",
			"ML" => "Mali (ML)",
			"MM" => "Myanmar (Burma) (MM)",
			"MN" => "Mongolia (MN)",
			"MO" => "Macau (MO)",
			"MP" => "Northern Mariana Islands (MP)",
			"MQ" => "Martinique (MQ)",
			"MR" => "Mauritania (MR)",
			"MS" => "Montserrat (MS)",
			"MT" => "Malta (MT)",
			"MU" => "Mauritius (MU)",
			"MV" => "Maldives (MV)",
			"MW" => "Malawi (MW)",
			"MX" => "Mexico (MX)",
			"MY" => "Malaysia (MY)",
			"MZ" => "Mozambique (MZ)",
			"NA" => "Namibia (NA)",
			"NC" => "New Caledonia (NC)",
			"NE" => "Niger (NE)",
			"NF" => "Norfolk Island (NF)",
			"NG" => "Nigeria (NG)",
			"NI" => "Nicaragua (NI)",
			"NL" => "Netherlands (NL)",
			"NO" => "Norway (NO)",
			"NP" => "Nepal (NP)",
			"NR" => "Nauru (NR)",
			"NU" => "Niue (NU)",
			"NZ" => "New Zealand (NZ)",
			"OM" => "Oman (OM)",
			"PA" => "Panama (PA)",
			"PE" => "Peru (PE)",
			"PF" => "French Polynesia (PF)",
			"PG" => "Papua New Guinea (PG)",
			"PH" => "Philippines (PH)",
			"PK" => "Pakistan (PK)",
			"PL" => "Poland (PL)",
			"PM" => "St Pierre &amp; Miquelon (PM)",
			"PN" => "Pitcairn (PN)",
			"PR" => "Puerto Rico (PR)",
			"PS" => "Palestine (PS)",
			"PT" => "Portugal (PT)",
			"PW" => "Palau (PW)",
			"PY" => "Paraguay (PY)",
			"QA" => "Qatar (QA)",
			"RE" => "Reunion (RE)",
			"RO" => "Romania (RO)",
			"RS" => "Serbia (RS)",
			"RU" => "Russia (RU)",
			"RW" => "Rwanda (RW)",
			"SA" => "Saudi Arabia (SA)",
			"SB" => "Solomon Islands (SB)",
			"SC" => "Seychelles (SC)",
			"SD" => "Sudan (SD)",
			"SE" => "Sweden (SE)",
			"SG" => "Singapore (SG)",
			"SH" => "St Helena (SH)",
			"SI" => "Slovenia (SI)",
			"SJ" => "Svalbard &amp; Jan Mayen (SJ)",
			"SK" => "Slovakia (SK)",
			"SL" => "Sierra Leone (SL)",
			"SM" => "San Marino (SM)",
			"SN" => "Senegal (SN)",
			"SO" => "Somalia (SO)",
			"SR" => "Suriname (SR)",
			"SS" => "South Sudan (SS)",
			"ST" => "Sao Tome &amp; Principe (ST)",
			"SV" => "El Salvador (SV)",
			"SX" => "Sint Maarten (SX)",
			"SY" => "Syria (SY)",
			"SZ" => "Swaziland (SZ)",
			"TC" => "Turks &amp; Caicos Is (TC)",
			"TD" => "Chad (TD)",
			"TF" => "French Southern &amp; Antarctic Lands (TF)",
			"TG" => "Togo (TG)",
			"TH" => "Thailand (TH)",
			"TJ" => "Tajikistan (TJ)",
			"TK" => "Tokelau (TK)",
			"TL" => "East Timor (TL)",
			"TM" => "Turkmenistan (TM)",
			"TN" => "Tunisia (TN)",
			"TO" => "Tonga (TO)",
			"TR" => "Turkey (TR)",
			"TT" => "Trinidad &amp; Tobago (TT)",
			"TV" => "Tuvalu (TV)",
			"TW" => "Taiwan (TW)",
			"TZ" => "Tanzania (TZ)",
			"UA" => "Ukraine (UA)",
			"UG" => "Uganda (UG)",
			"UM" => "US minor outlying islands (UM)",
			"US" => "United States (US)",
			"UY" => "Uruguay (UY)",
			"UZ" => "Uzbekistan (UZ)",
			"VA" => "Vatican City (VA)",
			"VC" => "St Vincent (VC)",
			"VE" => "Venezuela (VE)",
			"VG" => "Virgin Islands (UK) (VG)",
			"VI" => "Virgin Islands (US) (VI)",
			"VN" => "Vietnam (VN)",
			"VU" => "Vanuatu (VU)",
			"WF" => "Wallis &amp; Futuna (WF)",
			"WS" => "Samoa (western) (WS)",
			"YE" => "Yemen (YE)",
			"YT" => "Mayotte (YT)",
			"ZA" => "South Africa (ZA)",
			"ZM" => "Zambia (ZM)",
			"ZW" => "Zimbabwe (ZW)"
		);
	}
} // end of class
