<?php 

global $PL_API_LISTINGS;
$PL_API_LISTINGS = array(
	'get' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2.1/listings',
			'type' => 'GET'
		),
		'args' => array(
			'listing_ids'  => array(),
			'compound_type' => array(
				'label' => 'Listing Type',
				'group' => 'Basic Details',
				'type' => 'select',
				'options' => array(
					'false' => 'Not Set',
					'res_sale' => 'Residential Sale',
					'res_rental' => 'Residential Rental',
					'vac_rental' => 'Vacation Rental',
					'park_rental' => 'Parking',
					'comm_rental' => 'Commercial Rental',
					'comm_sale' => 'Commercial Sale',
					'sublet' => 'Sublet'
				)
			),
			'zoning_types' => array(
				'type' => 'select',
				'label' => 'Zoning',
				'group' => 'listing types',
				'options' => array(
					'false' => 'Any',
					'residential' => 'Residential',
					'commercial' => 'Commercial'
				)
			),
			'purchase_types' => array(
				'type' => 'select',
				'label' => 'Purchase',
				'group' => 'listing types',
				'options' => array(
					'false' => 'Any',
					'sale' => 'Sale',
					'rental' => 'Rental'
				)
			),
			'property_type' => array(),
			// binds to building id
			'building_id' => array(),// => array('type' => 'text'),
			'location' => array(
				'postal' => array(
					'label' => 'Zip',
					'type' => 'select',
					'group' => 'location',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'locations_for_options',
						'params' => array('postal', false)
					)
				),
				'region'  => array(
					'label' => 'State',
					'type' => 'select',
					'group' => 'location',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'locations_for_options',
						'params' => array('region', false)
					)
				),
				'locality'  => array(
					'label' => 'City',
					'type' => 'select',
					'group' => 'location',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'locations_for_options',
						'params' => array('locality', false)
					)
				),
				'neighborhood'  => array(
					'label' => 'Neighborhood',
					'type' => 'select',
					'group' => 'location',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'locations_for_options',
						'params' => array('neighborhood', false)
					)
				),
				'county'  => array(
					'label' => 'County',
					'type' => 'select',
					'group' => 'location',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'locations_for_options',
						'params' => array('county', false)
					)
				)
				
			),
			// binds to keys / values of all attributes (cur + uncur)
			'metadata' => array(
				'beds' => array(
					'label' => 'Beds',
					'type' => 'select',
					'group' => 'basic',
					'options' => array(
						'false' => 'Any',
						'0' => 'Studio',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
					)
				),
				'max_beds' => array(),// => array('type' => 'text', 'group' => 'advanced', 'label' => 'Max Beds'),
				'min_beds' => array(),//=> array('type' => 'text', 'group' => 'advanced', 'label' => 'Min Beds'),
                'baths' => array(
                	'label' => 'Baths',
	                'type' => 'select',
	                'group' => 'basic',
	                'options' => array(
						'false' => 'Any',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
					)
	            ),
                'max_baths' => array(),// => array('type' => 'text', 'group' => 'advanced', 'label' => 'Max Baths'),
                'min_baths' => array(),// => array('type' => 'text', 'group' => 'advanced', 'label' => 'Min Baths'),
                'half_baths' => array(
                	'label' => 'Half Baths',
	                'type' => 'select',
	                'group' => 'basic',
	                'options' => array(
						'false' => 'Any',
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
						'7' => '7',
						'8' => '8',
						'9' => '9',
						'10' => '10',
						'11' => '11',
						'12' => '12',
						'13' => '13',
						'14' => '14',
						'15' => '15',
					)
	            ),
                'max_half_baths' => array(),// => array('type' => 'text', 'group' => 'advanced'),
                'min_half_baths' => array(),// => array('type' => 'text', 'group' => 'advanced'),
                'price'  => array(),// => array('type' => 'text', 'group' => 'basic'),
                'max_price' => array(
                	'label' => 'Max Price',
	                'type' => 'select',
	                'group' => 'basic',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'pricing_min_options',
						'params' => 'max'
					)
					
	            ),
                'min_price' => array(
                	'label' => 'Min Price',
	                'type' => 'select',
	                'group' => 'basic',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'pricing_min_options',
						'params' => 'min'
					)
					
	            ),
                'sqft' => array(),// => array('type' => 'text', 'group' => 'basic'),
                'max_sqft' => array('type' => 'text', 'group' => 'advanced', 'label' => 'Max Sqft'),
                'min_sqft' => array('type' => 'text', 'group' => 'advanced', 'label' => 'Min Sqft'),
                'avail_on' => array(),// => array('type' => 'date', 'group' => 'advanced'),
                'max_avail_on' => array('type' => 'date', 'group' => 'basic', 'label' => 'Latest Available Date'),
                'min_avail_on' => array('type' => 'date', 'group' => 'basic', 'label' => 'Earliest Available Date'),
                'lt_sz' => array(),
                'max_lt_sz' => array('type' => 'text', 'group' => 'advanced', 'label' => 'Max Lot Size'),
                'min_lt_sz' => array('type' => 'text', 'group' => 'advanced', 'label' => 'Min Lot Size'),
                'desc' => array('type' => 'checkbox', 'group' => 'advanced', 'label' => 'Has Description'),
                'ngb_shop' => array(),
                'ngb_hgwy' => array(),
                'grnt_tops' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'Granite Countertops'),
                'ngb_med' => array(),
                'ngb_trails' => array(),
                'cent_ht' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'Central Heat'),
                'pk_spce' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'Parking'),
                'air_cond' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'A/C'),
                'lse_trms' => array(),// => array('type' => 'select','options' => array('per_mnt' => 'Per Month')),
                'ngb_trans' => array(),
                'off_den' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'Office/Den'),
                'frnshed' => array('type' => 'checkbox', 'group' => 'amenities', 'label' => 'Furnished'),
                'refrig' => array(),
                'deposit' => array(),
                'ngb_pubsch' => array(),
			),
			'agency_only' => array('type' => 'checkbox', 'group' => 'advanced', 'label' => 'My Offices Listings'),
			'non_import' => array('type' => 'checkbox',  'group' => 'advanced', 'label' => 'Non MLS Listings'),
			'custom' => array(
				'type' => 'bundle',
				'group' => '',
				'id' => 'custom',
				'bound' => array(
					'class' => 'PL_Listing_Helper',
					'method' => 'custom_attributes',
				)
			),
			'total_images' => array(),
			'box' => array(
				'min_latitude' => array(),// => array('type' => 'text'),
				'min_longitude' => array(),// => array('type' => 'text'),
				'max_latitude' => array(),// => array('type' => 'text'),
				'max_longitude' => array()// => array('type' => 'text')
			),
			'include_disabled' => array('type' => 'checkbox', 'group' => 'basic','label' => 'Include Inactive Listings'),
			'address_mode' => array(),// => array('type' => 'select','options' => array('exact' => 'Exact','polygon' => 'Polygon')),
			'limit' => array(), // => array('type' => 'text'),
			'offset' => array(), // => array('type' => 'text'),
			// Field to sort by, can be any field returned from the API, for uncurated fields use _uncur_data.<key>_ for curated use _cur_data.<key>_
			'sort_by' => array(),
			'sort_type' => array()// => array('type' => 'select','options' => array('asc' => 'Ascending','desc' =>'Decending'))
		),
		'returns' => array(
			'id' => false,
			'compound_type' => false,
			'property_type' => array(),
			'zoning_types' => array(),
			'purchase_types' => array(),
			'listing_types' => false,
			'building_id' => false,
			'cur_data' => array(
				'half_baths' => false,
                'price' => false,
                'sqft' => false,
                'baths' => false,
                'avail_on' => false,
                'beds' => false,
                'url' => false,
                'desc' => false,
                'lt_sz' => false,
                'ngb_shop' => false,
                'ngb_hgwy' => false,
                'grnt_tops' => false,
                'ngb_med' => false,
                'ngb_trails' => false,
                'cent_ht' => false,
                'pk_spce' => false,
                'air_cond' => false,
                'price_unit' => false,
                'lt_sz_unit' => false,
                'lse_trms' => false,
                'ngb_trans' => false,
                'off_den' => false,
                'frnshed' => false,
                'refrig' => false,
                'deposit' => false,
                'ngb_pubsch' => false
			),
			'uncur_data' => false,
			'location' => array(
				'address' => false,
				'locality' => false,
				'region' => false,
				'postal' => false,
				'neighborhood' => false,
				'county' => false,
				'country' => false,
				'coords' => array(
					'latitude' => false,
					'longitude' => false
				)
			),
			'contact' => array(
				'email' => false,
				'phone' => false
			),
			'images' => false,
			'tracker_url' => false,
			'rets' => array(
				'aname' => false,
				'oname' => false,
				'mls_id' => false
			)
		)
	),
	'create' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/listings',
			'type' => 'POST'
		),
		'args' => array(
			'compound_type' => array(
				'label' => 'Listing Type',
				'group' => 'Basic Details',
				'type' => 'select',
				'options' => array(
					'false' => 'Not Set',
					'res_sale' => 'Residential Sale',
					'res_rental' => 'Residential Rental',
					'vac_rental' => 'Vacation Rental',
					'park_rental' => 'Parking',
					'comm_rental' => 'Commercial Rental',
					'comm_sale' => 'Commercial Sale',
					'sublet' => 'Sublet'
				)
			),
			'property_type' => array(
				'type' => 'text',
				'label' => 'Property Type',
				'group' => 'Basic Details'
			),
			'location' => array(
				'address' => array('type' => 'text','group' => 'location', 'label' => 'Address'), 
				'locality'  => array('type' => 'text','group' => 'location', 'label' => 'City'),
				'region'  => array('type' => 'text','group' => 'location', 'label' => 'State'),
				'postal' => array('type' => 'text','group' => 'location', 'label' => 'Zip Code'),
				'unit'  => array('type' => 'text','group' => 'location', 'label' => 'Unit'),
				'neighborhood'  => array('type' => 'text','group' => 'location', 'label' => 'Neighborhood'),
				'county' => array('type' => 'text','group' => 'location', 'label' => 'County'),
				'country'  => array(
					'type' => 'select',
					'group' => 'location',
					'label' => 'Country',
					'bound' => array(
						'class' => 'PL_Listing_Helper',
						'method' => 'supported_countries',
						'default' => array('PL_Listing_Helper','convert_default_country')
					)
				 )
			),
			// // binds to keys / values of all attributes (cur + uncur)	
			'metadata' => array(
				//comm_rental
				'prop_name' => array('type' => 'text','group' => 'basic details', 'label' => 'Property Name'),
				'cons_stts' => array('type' => 'select','options' => array('exstng' => 'Existing', 'und_prop' => 'Under Construction / Proposed'), 'group' => 'basic details', 'label' => 'Construction Status'),
				'bld_suit' => array('type' => 'checkbox','group' => 'basic details', 'label' => 'Built to Suit'),
				'min_div' => array('type' => 'text','group' => 'building details', 'label' => 'Minimum Divisible'),
				'max_cont' => array('type' => 'text','group' => 'building details', 'label' => 'Maximum Contiguous'),
				'bld_sz' => array('type' => 'text','group' => 'building details', 'label' => 'Total Building Size'),
				'bld_sz' => array('type' => 'text','group' => 'building details', 'label' => 'Total Building Size'),
				//res_rental
				'beds' => array('type' => 'text','group' => 'basic details', 'label' => 'Bedrooms'),
                'baths' => array('type' => 'text', 'group' => 'basic details', 'label' => 'Bathrooms'),
                'half_baths' => array('type' => 'text', 'group' => 'basic details', 'label' => 'Half Bathrooms'),
                'price' => array('type' => 'text', 'group' => 'lease details', 'label' => 'Price'),
                'sqft' => array('type' => 'text', 'group' => 'basic details', 'label' => 'Square Feet'),
                'avail_on' => array('type' => 'date', 'group' => 'basic details', 'label' => 'Available On'),
                'desc' => array('type' => 'textarea', 'group' => 'description', 'label' => 'Description'),
                //rentals
                'lse_trms' => array('type' => 'select', 'options' => array('false' => 'Not Set', 'per_mnt' => 'Per Month','per_ngt' => 'Per Month', 'per_wk' => 'Per Week', 'per_yr' => 'Per Year'), 'group' => 'Transaction Details','label' => 'Lease Terms'),
                'lse_type' => array('type' => 'select', 'options' => array('false' => 'Not Set', 'ind_grs' => 'Full Service','ind_grs' => 'Industrial Gross', 'mod_grs' => 'Modified Gross', 'mod_net' => 'Modified Net', 'na' => 'N/A', 'other' => 'Other' ), 'group' => 'Transaction Details','label' => 'Lease Type'),
                'sublse' => array('type' => 'checkbox', 'group' => 'Transaction Details','label' => 'Sublease'),
                'rate_unit' => array('type' => 'select', 'options' => array('false' => 'Not Set', 'amt_mnt' => 'Amount/Month','amt_yr' => 'Amount/Year', 'sf_mnt' => 'Sqft/Month', 'sf_yr' => 'Sqft/Year'), 'group' => 'Transaction Details','label' => 'Rental Rate'),
                //General
                'lt_sz' => array('type' => 'text', 'group' => 'Lot Details', 'label' => 'Lot Size'),
                'lt_sz_unit' => array('type' => 'select','options' => array('false' => 'Not Set', 'acres' => 'Acres', 'sqft' => 'Square Feet'), 'group' => 'Lot Details', 'label' => 'Lot Unit Type'),
                'year_blt' => array('type' => 'text', 'group' => 'Lot Details', 'label' => 'Year Built'),
                'pk_spce' => array('type' => 'text', 'group' => 'basic details', 'label' => 'Parking Spaces'),
                'park_type' => array('type' => 'select','options' => array('false' => 'Not Set','atch_gar' => 'Attached Garage', 'cov' => 'Covered', 'dtch_gar' => 'Detached Garage', 'off_str' => 'Off-street', 'strt' => 'On-street', 'tan' => 'Tandem'), 'group' => 'basic details', 'label' => 'Parking Type'),
                'pk_lease' => array('type' => 'checkbox', 'group' => 'lease details', 'label' => 'Parking Included'),
                'deposit' => array('type' => 'text', 'group' => 'Transation Details', 'label' => 'Deposit'),
                'floors' => array('type' => 'text', 'group' => 'basic Details', 'label' => 'Floors'),
                'hoa_mand' => array('type' => 'checkbox', 'group' => 'finacial details', 'label' => 'HOA Mandatory'),
                'hoa_fee' => array('type' => 'text', 'group' => 'finacial details', 'label' => 'HOA Fee'),
                'lndr_own' => array('type' => 'select','options' => array('false' => 'Not Set','true' => 'Yes', 'false' => 'No', 'undis' => 'Undisclosed'), 'group' => 'finacial details', 'label' => 'Floors'),
                'style' => array('type' => 'select','options' => array('false' => 'Not Set','bungal' => 'Bungalow', 'cape' => 'Cape Cod', 'colonial' => 'Colonial' ,'contemp' => 'Contemporary', 'cott' => 'Cottage', 'farmh' => 'Farmhouse','fnt_bk_splt' => 'Front to Back Split', 'gamb_dutc'=>'Gambrel/Dutch','garrison' => 'Garrison', 'greek_rev' => 'Greek Revival', 'loft_splt' => 'Lofted Split','mult_lvl' => 'Multi-level','rai_ranch' => 'Raised Ranch','ranch' => 'Ranch','saltb' => 'Saltbox', 'split_ent' => 'Split Entry', 'tudor' => 'Tudor', 'victor' => 'Victorian', 'antiq' => 'Antique'), 'group' => 'structure details', 'label' => 'Style'),
                //Pet Details
                'cats' => array('type' => 'checkbox', 'group' => 'Pets', 'label' => 'Cats'),
                'dogs' => array('type' => 'checkbox', 'group' => 'Pets', 'label' => 'Dogs'),
                'cond' => array('type' => 'checkbox', 'group' => 'Pets', 'label' => 'Conditional'),
                //Vacation
                'accoms' => array('type' => 'textarea', 'group' => 'basic details', 'label' => 'Accomodates Description'),
                'avail_info' => array('type' => 'textarea', 'group' => 'availability details', 'label' => 'Availability Description'),
                //Parking Amenities
                'valet' => array('type' => 'checkbox', 'group' => 'Amenities', 'label' => 'Valet'),
                'guard' => array('type' => 'checkbox', 'group' => 'Amenities', 'label' => 'Guarded'),
                'heat' => array('type' => 'checkbox', 'group' => 'Amenities', 'label' => 'Heated'),
                'carwsh' => array('type' => 'checkbox', 'group' => 'Amenities', 'label' => 'Carwash'),
                //Neighborhood Amenities
                'ngb_trans' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Public Transportation'),
				'ngb_shop' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Shopping'),
				'ngb_swim' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Swimming Pool'),
				'ngb_court' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Tennis Court'),
				'ngb_park' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Park'),
				'ngb_trails' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Walk/Jog Trails'),
				'ngb_stbles' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Stables'),
				'ngb_golf' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Golf Courses'),
				'ngb_med' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Medical Facilities'),
				'ngb_bike' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Bike Path'),
				'ngb_cons' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Conservation Area'),
				'ngb_hgwy' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Highway Access'),
				'ngb_mar' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Marina'),
				'ngb_pvtsch' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Private School'),
				'ngb_pubsch' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'Public School'),
				'ngb_uni' => array('type' => 'checkbox', 'group' => 'Neighborhood Amenities', 'label' => 'University'),
                //Listing Amenities
                'grnt_tops' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Granite Countertops'),
                'air_cond' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Air Conditioning'),
                'cent_ac' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Central AC'),
                'frnshed' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Furnished'),
                'cent_ht' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Central Heat'),
                'frplce' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Fireplace'),
                'hv_ceil' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'High/Vaulted Ceiling'),
                'wlk_clst' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Walk-in Closet'),
                'hdwdflr' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Hardwood Floor'),
                'tle_flr' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Tile Floor'),
                'fm_lv_rm' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Family/Living Room'),
                'bns_rec_rm' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Bonus/Rec Room'),
                'lft_lyout' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Loft Layout'),
                'off_den' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Office/Den'),
                'dng_rm' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Dining Room'),
                'brkfst_nk' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Breakfast Nook'),
                'dshwsher' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Dishwasher'),
                'refrig' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Refigerator'),
                'stve_ovn' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Stove/Oven'),
                'stnstl_app' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Stainless Steel Appliances'),
                'attic' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Attic'),
                'basemnt' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Basement'),
                'washer' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Washer'),
                'dryer' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Dryer'),
                'lndry_in' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Laundry Area - Inside'),
                'lndry_gar' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Laundry Area - Garage'),
                'blc_deck_pt' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Balcony/Deck/Patio'),
                'yard' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Yard'),
                'swm_pool' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Swimming Pool'),
                'jacuzzi' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Jacuzzi'),
                'sauna' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Sauna'),
                'cble_rdy' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'Cable-ready'),
                'hghspd_net' => array('type' => 'checkbox', 'group' => 'Listing Amenities', 'label' => 'High-speed Internet'),
			),
			'uncur_data' => array(
				'type' => 'bundle',
				'group' => '',
				'bound' => array(
					'class' => 'PL_Listing_Helper',
					'method' => 'custom_attributes',
				)
			),
			'custom_data' => array(
				'type' => 'custom_data',
				'group' => 'Custom Amenities'
			),
			'images' => array(
				'type' => 'image',
				'group' => 'Upload Images',
				'label' => 'Select Files'
			)
		),
		'returns' => array(
		)
	),
	'temp_image' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/listings/media/temp/image',
			'type' => 'POST'
		),
		'args' => array(
			'file'
		),
		'returns' => array()
	),
	'update' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/listings',
			'type' => 'PUT'
		),
		'args' => array(),
		'returns' => array()
	),
	'delete' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/listings',
			'type' => 'DELETE'
		),
		'args' => array(
			'id' => array()
		),
		'returns' => array()
	),
	'get.locations' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2/listings/locations/',
			'type' => 'GET'
		),
		'args' => array(
			'include_disabled' => array(
				'type' => 'checkbox'
			)
		),
		'returns' => array(
			'postal' => array(),
			'region'  => array(),
			'locality' => array(),
			'neighborhood' => array(),
			'county' => array(),
			'neighborhood_polygons' => array()
		)
	),
	'get.aggregate' => array(
		'request' => array(
			'url' => 'https://api.placester.com/v2.1/listings/aggregate/',
			'type' => 'GET'
		),
		'args' => array(
			'keys' => array()
		),
		'returns' => array()
	)
);