<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Static_Listing_CPT extends PL_Search_Listing_CPT {

	protected static $pl_post_type = 'static_listings';

	protected static $shortcode = 'static_listings';

	protected static $title = 'List of Listings';

	protected static $help =
		'<p>
		You can insert your Static Listings snippet by using the [static_listings id="<em>listingid</em>"] shortcode in a page or a post.
		The shortcode requires an ID parameter of the static listing ID number published in your
		Featured Listings post type control on the left side of the admin panel.
		</p>';
	
	protected static $options = array(
		'context'				=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'					=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'				=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'hide_sort_by'			=> array( 'type' => 'checkbox', 'label' => 'Hide "Sort By" dropdown', 'default' => true ),
		'hide_sort_direction'	=> array( 'type' => 'checkbox', 'label' => 'Hide "Sort Direction" dropdown', 'default' => true ),
		'hide_num_results'		=> array( 'type' => 'checkbox', 'label' => 'Hide "Show # entries" dropdown', 'default' => true ),
		'query_limit'			=> array( 'type' => 'numeric', 'label' => 'Number of results to display', 'default' => 5 ),
		'widget_class'			=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	);
}

PL_Static_Listing_CPT::init(__CLASS__);