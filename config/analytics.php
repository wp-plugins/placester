<?php

define("PL_ANALYTICS_SCRIPT_URL", "https://d3uemyw1e5n0jw.cloudfront.net/assets/analytics-1.0.js");

global $PL_ANALYTICS_CONFIG;
$PL_ANALYTICS_CONFIG = array(
	"listing_view" => array(
		"category" => "listing",
		"allowed_params" => array("page_id")
	),
	"listing_search" => array(
		"category" => "search",
		"allowed_params" => array( 
			"min_beds", 
			"max_beds", 
			"min_baths", 
			"max_baths", 
			"purchase_type", 
			"zoning_type", 
			"listing_type", 
			"property_type", 
			"country", 
			"street_address", 
			"locality", 
			"region", 
			"postal",
			"neighborhood", 
			"county")
			// MISSING: "min_price", "max_price", "beds", "baths", "min_sqft", "max_sqft"
	),
	"contact_submitted" => array(
		"category" => "contact",
		"allowed_params" => array("phone", "e-mail")
	)
);

?>