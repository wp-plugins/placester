<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
class PL_Search_Listing_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_search_listings';

	protected static $shortcode = 'search_listings';

	protected static $title = 'Search Listings';

	protected static $help = 
		'<p>
        You can insert your "activated" Listings snippet by using the [search_form] shortcode in a page or a post.
        The listings view is intended to be used alongside the [search_form] shortcode defined above as a container
        for the results of the search, with the snippet representing how an <i>individual</i> listing that matches
        the search criteria will be displayed.
		</p>';

	protected static $filters = array();
	
	protected static $subcodes = array(
		'price'			=> array('help' => 'Property price'),
		'sqft'			=> array('help' => 'Total square feet'),
		'beds'			=> array('help' => 'Number of bedrooms'),
		'baths'			=> array('help' => 'Number of bathrooms'),
		'half_baths'	=> array('help' => 'Number of half bathrooms'),
		'avail_on'		=> array('help' => 'Date the property will be available'),
		'url'			=> array('help' => 'Link to page for the listing'),
		'address'		=> array('help' => 'Street address'),
		'locality'		=> array('help' => 'Locality'),
		'region'		=> array('help' => 'Region'),
		'postal'		=> array('help' => 'Zip/postal code'),
		'neighborhood'	=> array('help' => 'Neighborhood'),
		'county'		=> array('help' => 'County'),
		'country'		=> array('help' => 'Country'),
		//'coords'		=> array('help' => ''),
		'unit'			=> array('help' => 'Unit'),
		'full_address'	=> array('help' => 'Full address'),
		'email'			=> array('help' => 'Email address for this listing'),
		'phone'			=> array('help' => 'Contact phone'),
		'desc'			=> array('help' => 'Property description'),
		'image'			=> array('help' => 'Property thumbnail image'),
		'mls_id'		=> array('help' => 'MLS #'),
		//'map'			=> array('help' => ''),
		'listing_type'	=> array('help' => 'Type of listing'),
		'img_gallery'	=> array('help' => 'Image gallery'),
		//'amenities'		=> array('help' => ''),
		'price_unit'	=> array('help' => 'Unit price'),
		'compliance'	=> array('help' => 'MLS compliance statement'),
	);

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML to format each individual listing', 'css' => 'mime_html', 'default' => '
<section class="my-lu">
	<div class="my-lu-head">
		<a href="[url]">[address] [locality], [region]</a>
	</div>
	<div class="my-lu-body">
		<div class="my-lu-image">[image]</div>

		<div class="my-lu-details">
			<ul>
				<li>[beds]<span> Bed(s)</span>
				</li>
				<li>[baths]<span> Bath(s)</span>
				</li>
				<li>[sqft]<span> Sqft</span>
				</li>
			</ul>
			<p class="my-lu-mls">MLS #: [mls_id]</p>
			<p class="my-lu-price">
				Price: <span>[price]</span>
			</p>
			<p class="my-lu-desc">[desc]</p>
			[compliance]
			<a class="my-lu-link" href="[url]">View Listing Details</a>
		</div>
	</div>
</section>
',
			'description'	=> '
You can use any valid HTML in this field to format the subcodes.' ),

		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'css' => 'mime_css', 'default' => '
/* sample list box */
.my-listings {
	clear: both;
	border: 1px solid #000;
	padding: 5px;
	font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	overflow: hidden;
}
/* sort selector group */
.my-listings .sort_wrapper {
	margin: 0;
}
/* individual sort selector */
.my-listings .sort_item {
	float: left;
	margin-right: .5em;
}
/* individual sort selector label */
.my-listings label {
}
/* list length selector */
#placester_listings_list_length label {
}

/* format the table that holds the listings */
				
.my-listings .placester_properties,
.my-listings table#placester_listings_list tr {
	margin: 0;
	width: 100%;
}
.entry-content .my-listings td {
	padding: 0;
}

/* format the pagination links */
.my-listings .paginate_button,
.my-listings .paginate_active {
	padding-right: 1em;
}
/* page numbers */
.my-listings .dataTables_paginate span {
}

/* section defined above to hold a single listing */				
section.my-lu {
	margin-bottom: 2px;
	background: #efefef;
	padding: 10px;
}
/* section defined above to hold the body of the listing */				
.my-lu-body {
	width: 100%;
	position: relative;
	overflow: hidden;
}
/* section defined above to hold the listing heading */				
.my-lu-head {
	margin: 3px 0;
}
/* section defined above to hold the listing image */				
.my-lu-image {
	float: left;
	margin-right: 10px;
	width: 180px;
}
.my-lu-image img {
	width: 100%;
	height: auto;
}
/* sections defined above to hold the details of the listing */				
.my-lu-details {
	margin-left: 190px;
	font-size: 12px;
}
/* property description */
.my-lu-desc {
	font-size: 12px;
}
/* MLS compliance statement */
.compliance-wrapper {
	margin-top: .5em;
	font-size: 10px;
}
.my-lu ul {
	float: none;
	margin: 0;
	padding-left: 1.2em;
}
.my-lu li, .my-lu p {
	float: none;
	margin: 0;
	padding: 0;
}
.my-lu-link {
	float:right;
}',
			'description'	=> '
You can use any valid CSS in this field to customize the listings, which will also inherit the CSS from the theme.' ),

		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the listings', 'css' => 'mime_html', 'default' => '<div class="my-listings">',
			'description'	=> '
You can use any valid HTML in this field and it will appear before the listings.
For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.' ),

		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the listings', 'css' => 'mime_html', 'default' => '</div>',
			'description'	=> '
You can use any valid HTML in this field and it will appear after the listings.' ),
	);




	/**
	 * Get list of filter options from the api.
	 * @see PL_SC_Base::_get_filters()
	 */
	protected function _get_filters() {
		if (class_exists('PL_Config')) {
			return PL_Config::PL_API_LISTINGS('get', 'args');
		}
		else {
			return array();
		}
	}
}

PL_Search_Listing_CPT::init(__CLASS__);
