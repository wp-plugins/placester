<?php
/**
 * Post type/Shortcode to generate a property search form
 *
 */

class PL_Form_CPT extends PL_SC_Base {

	protected $pl_post_type = 'pl_form';

	protected $shortcode = 'search_form';

	protected $title = 'Search Form';

	protected $help =
		'<p>
		You can insert your "activated" Search Form snippet by using the [search_form] shortcode in a page or a post.
		This control is intended to be used alongside the [search_listings] shortcode to display the search
		form\'s results.
		</p>';

	protected $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		'ajax'				=> array( 'type' => 'checkbox', 'label' => 'Disable AJAX', 'default' => false ),
		'formaction'		=> array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled', 'default' => '' ),
		'modernizr'			=> array( 'type' => 'checkbox', 'label' => 'Drop Modernizr', 'default' => false ),
	);

	//TODO build from the api
	protected $subcodes = array(
		'bedrooms'			=> array('help' => 'Drop list to select an exact number of bedrooms.'),
		'min_beds'			=> array('help' => 'Drop list to select the minimum number of bedrooms.'),
		'max_beds'			=> array('help' => 'Drop list to select the maximum number of bedrooms.'),
		'bathrooms'			=> array('help' => 'Drop list to select an exact number of bathrooms.'),
		'min_baths'			=> array('help' => 'Drop list to select the minimum number of bathrooms.'),
		'max_baths'			=> array('help' => 'Drop list to select the maximum number of bathrooms.'),
		'property_type'		=> array('help' => 'Drop list to select the property type.'),
		'listing_types'		=> array('help' => 'Drop list to select the listing type (housing/land/etc).'),
		'zoning_types'		=> array('help' => 'Drop list to select the zoning (commercial/residential/etc).'),
		'purchase_types'	=> array('help' => 'Drop list to select the purchase type (rent/buy).'),
		'available_on'		=> array('help' => 'Drop list to select date of when the property should be available.'),
		'cities'			=> array('help' => 'Drop list to select a city.'),
		'states'			=> array('help' => 'Drop list to select a state.'),
		'zips'				=> array('help' => 'Drop list to select a zip/postal code.'),
		'min_price'			=> array('help' => 'Drop list to select the minimum price.'),
		'max_price'			=> array('help' => 'Drop list to select the maximum price.'),
		'min_price_rental'	=> array('help' => 'Drop list to select the minimum rental price.'),
		'max_price_rental'	=> array('help' => 'Drop list to select the maximum rental price.'),
	);

	protected $template = array(
		'snippet_body'	=> array(
			'type' => 'textarea',
			'label' => 'HTML',
			'css' => 'mime_html',
			'default' => '
<div class="search-item">
	<label>Min Beds:</label>[min_beds]
</div>
<div class="search-item">
	<label>Min Baths:</label>[min_baths]
</div>
<div class="search-item">
	<label>Min Price:</label>[min_price]
</div>
<div class="search-item">
	<label>Max Price:</label>[max_price]
</div>
<div class="search-item">
	<label>City:</label>[cities]
</div>
<div class="search-item	">
	<label>Property Type:</label>[property_type]
</div>
			',
			'description'	=> 'You can use any valid HTML in this field to format the template tags.'
		),

		'css' => array(
			'type' => 'textarea',
			'label' => 'CSS',
			'css' => 'mime_css',
			'default' => '
form.pls_search_form_listings .search-item { float: left; margin-bottom: 20px; width: 30%; display: inline-block; margin-left: 2.9%; }
@media (max-width: 979px) { form.pls_search_form_listings .search-item { margin-bottom: 5px; } }
form.pls_search_form_listings .search-item label { float: left; width: 100%; }
@media (min-width: 768px) and (max-width: 979px) { form.pls_search_form_listings .search-item { margin-left: 2%; } }
@media (max-width: 767px) { form.pls_search_form_listings .search-item { margin-left: 2%; width: 47%; } }
@media (max-width: 420px) { form.pls_search_form_listings .search-item { margin-left: 2%; width: 97%; } }
form.pls_search_form_listings .search-item select, form.pls_search_form_listings .search-item .chzn-container { width: 80% !important; }
			',
			'description' => 'You can use any valid CSS in this field to customize the form, which will also inherit the CSS from the theme.'
		),

		'before_widget'	=> array(
			'type' => 'textarea',
			'label' => 'Add content before the form',
			'default' => '<div class="my-searchform">',
			'description' => 'You can use any valid HTML in this field and it will appear before the form. For example, you can wrap the whole form with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.'
		),

		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the form', 'default' => '<div style="clear:both"></div></div>',
			'description' => 'You can use any valid HTML in this field and it will appear after the form.'
		),
	);




	public static function init() {
		parent::_init(__CLASS__);
	}
}

PL_Form_CPT::init();
