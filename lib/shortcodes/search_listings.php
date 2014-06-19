<?php
/**
 * Post type/Shortcode to generate a list of listings
 *
 */
class PL_Search_Listing_CPT extends PL_SC_Base {

	protected $pl_post_type = 'pl_search_listings';

	protected $shortcode = 'search_listings';

	protected $title = 'Search Listings';

	protected $help =
		'<p>
        You can insert your "activated" Listings snippet by using the [search_form] shortcode in a page or a post.
        The listings view is intended to be used alongside the [search_form] shortcode defined above as a container
        for the results of the search, with the snippet representing how an <i>individual</i> listing that matches
        the search criteria will be displayed.
		</p>';

	protected $subcodes = array(
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

	protected $template = array(
		'snippet_body'	=> array(
			'type' => 'textarea',
			'label' => 'HTML to format each individual listing',
			'css' => 'mime_html',
			'default' => '
<!-- Listing -->
<div class="wf-listing">
	<div class="wf-image">
		<a href="[url]">
			[image width=300]
		</a>
		<p class="wf-price">[price]</p>
	</div>
	<p class="wf-address">
		<a href="[url]">[address] [locality], [region]</a>
	</p>
	<p class="wf-basics">
		<span class="hidden-phone">Beds: <strong>[beds]</strong>&nbsp;</span> <span class="hidden-phone">Baths: <strong>[baths]</strong>&nbsp;</span> <span class="wf-mls">MLS #: [mls_id]</span>
	</p>
</div>
			',
			'description' => 'You can use any valid HTML in this field to format the subcodes.'
		),

		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'default' => '
.visible-phone { display: none !important; }

.visible-tablet { display: none !important; }

.hidden-desktop { display: none !important; }

.visible-desktop { display: inherit !important; }

@media (min-width: 768px) and (max-width: 979px) { .hidden-desktop { display: inherit !important; }
  .visible-desktop { display: none !important; }
  .visible-tablet { display: inherit !important; }
  .hidden-tablet { display: none !important; } }
@media (max-width: 767px) { .hidden-desktop { display: inherit !important; }
  .visible-desktop { display: none !important; }
  .visible-phone { display: inherit !important; }
  .hidden-phone { display: none !important; } }
.visible-print { display: none !important; }

@media print { .visible-print { display: inherit !important; }
  .hidden-print { display: none !important; } }
.non-row-wrapper { padding-bottom: 40px; margin-left: -3%; max-width: 1080px; width: 100%; }
@media (min-width: 1280px) { .non-row-wrapper { margin-left: 1%; } }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper { margin-left: 0%; } }
@media (max-width: 767px) { .non-row-wrapper { margin-left: -1%; } }
@media (max-width: 420px) { .non-row-wrapper { margin-left: -1%; } }
.non-row-wrapper .sort_wrapper { margin-left: 3%; padding: 10px 0 !important; }
.non-row-wrapper .sort_wrapper .sort_item { float: left !important; width: 30% !important; }
.non-row-wrapper .sort_wrapper .sort_item label { float: left; width: 100%; }
.non-row-wrapper #container { width: 100% !important; }
.non-row-wrapper #container tr { width: 30%; display: inline-block; margin-left: 2.9%; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper #container tr { margin-left: 2%; } }
@media (max-width: 767px) { .non-row-wrapper #container tr { margin-left: 2%; width: 47%; } }
@media (max-width: 420px) { .non-row-wrapper #container tr { margin-left: 2%; width: 97%; } }
.non-row-wrapper #container tr .wf-listing { width: 100%; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
@media (max-width: 767px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
@media (max-width: 420px) { .non-row-wrapper #container tr .wf-listing { width: 100%; } }
.non-row-wrapper #container thead { display: none; }
.non-row-wrapper #container .dataTables_paginate .paginate_active { font-weight: 600; }

.wf-listing { width: 30%; display: inline-block; margin-left: 2.9%; }
@media (min-width: 768px) and (max-width: 979px) { .wf-listing { margin-left: 2%; } }
@media (max-width: 767px) { .wf-listing { margin-left: 2%; width: 47%; } }
@media (max-width: 420px) { .wf-listing { margin-left: 2%; width: 97%; } }

.wf-listing .wf-image { width: 100%; }
.wf-listing .wf-image a img { width: 100%; }

.wf-listing { vertical-align: top; padding-bottom: 30px; }
.wf-listing .wf-image img { border: none !important; float: left !important; width: 100% !important; max-width: 100% !important; }
.wf-listing .wf-image .wf-price { color: white; text-decoration: none; font-size: 0.9em; padding: 6px 12px; margin: -37px 0 0 0 !important; float: left; background: black; background: rgba(0, 0, 0, 0.8); }
.wf-listing .wf-address, .wf-listing .wf-basics { float: left; width: 100%; font-family: Arial, sans-serif; }
.wf-listing .wf-address { margin: 10px 0 0 !important; font-size: 18px; line-height: 20px; height: 42px; overflow: hidden; }
.wf-listing .wf-basics { margin: 10px 0 0; font-size: 14px; color: #4b4b4b; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper .dataTables_info { margin-left: 2%; } }
@media (max-width: 767px) { .non-row-wrapper .dataTables_info { margin-left: 2%; } }
@media (max-width: 420px) { .non-row-wrapper .dataTables_info { margin-left: 2%; } }
.non-row-wrapper .dataTables_paginate { margin-top: 20px; text-align: center; }
@media (min-width: 768px) and (max-width: 979px) { .non-row-wrapper .dataTables_paginate { margin-left: 2%; } }
@media (max-width: 767px) { .non-row-wrapper .dataTables_paginate { margin-left: 2%; }
  .non-row-wrapper .dataTables_paginate a.first, .non-row-wrapper .dataTables_paginate a.last { display: none; } }
@media (max-width: 420px) { .non-row-wrapper .dataTables_paginate { margin-left: 2%; } }
.non-row-wrapper .dataTables_paginate a, .non-row-wrapper .dataTables_paginate a:visited { font-size: 11pt; padding: 6px 8px; text-decoration: none; cursor: pointer; }
.non-row-wrapper .dataTables_paginate a.first, .non-row-wrapper .dataTables_paginate a:visited.first { float: left; margin-top: -6px; }
.non-row-wrapper .dataTables_paginate a.last, .non-row-wrapper .dataTables_paginate a:visited.last { float: right; margin-top: -6px; }
.non-row-wrapper .dataTables_paginate a.paginate_active, .non-row-wrapper .dataTables_paginate a:visited.paginate_active { font-weight: 600; }
.non-row-wrapper .dataTables_paginate a.previous, .non-row-wrapper .dataTables_paginate a:visited.previous { padding-right: 30px; margin-top: -8px; }
.non-row-wrapper .dataTables_paginate a.next, .non-row-wrapper .dataTables_paginate a:visited.next { padding-left: 30px; margin-top: -8px; }
@media (max-width: 767px) { .non-row-wrapper #placester_listings_list_length, .non-row-wrapper .sort_wrapper { display: none !important; } }
			',
			'description' => 'You can use any valid CSS in this field to customize the listings, which will also inherit the CSS from the theme.'
		),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the listings',
			'css' => 'mime_html',
			'default' => '<div class="non-row-wrapper">',
			'description' => 'You can use any valid HTML in this field and it will appear before the listings. For example, you can wrap the whole list with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.'
		),

		'after_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content after the listings',
			'css' => 'mime_html',
			'default' => '</div>',
			'description' => 'You can use any valid HTML in this field and it will appear after the listings.'
		),
	);




	public static function init() {
		parent::_init(__CLASS__);
	}

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

PL_Search_Listing_CPT::init();
