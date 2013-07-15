<?php
/**
 * Post type/Shortcode to generate a list of featured listings
 *
 */
include_once(PL_LIB_DIR . 'shortcodes/search_listings.php');

class PL_Featured_Listings_CPT extends PL_Search_Listing_CPT {

	protected static $pl_post_type = 'featured_listings';

	protected static $shortcode = 'featured_listings';

	protected static $title = 'Featured Listings';

	protected static $help = 
		'<p>
		</p>';

	protected static $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	protected static $filters = array();

	// Use the same subcodes, template as search listings shortcode
	// protected static $subcodes = array();
	// protected static $template = array();
	

	public function __construct() {
		parent::__construct();
		add_shortcode($this::$shortcode, array(__CLASS__, 'handle_shortcode'));
	}
	
	public function handle_shortcode($args, $content) {
		return 'aaa';
	}
	
	/**
	 * No filters
	 * @see PL_SC_Base::_get_filters()
	 */
	protected function _get_filters() {
		return array();
	}
}

PL_Featured_Listings_CPT::init(__CLASS__);
