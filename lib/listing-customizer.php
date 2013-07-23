<?php
/**
 * Class for creating a custom listing page layout type.
 */

class PL_Listing_Customizer {

	const LIST_KEY = 'pl_listing_template_list';
	const LIST_COUNT_KEY = 'pl_listing_template_count';
	const MAP_KEY = 'pl_listing_template_map';
	const TPL_KEY = 'pl_listing_template__';
	
	// title
	protected $title = '';
	// help text
	protected static $help = '';
	// subcodes
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
		'gallery'		=> array('help' => 'Image gallery'),
		'amenities'		=> array('help' => 'List of amenties'),
		'price_unit'	=> array('help' => 'Unit price'),
		'compliance'	=> array('help' => 'MLS compliance statement'),
		'favorite_link_toggle' => array('help' => 'Link to add/remove from favorites'),
	);
	// tags allowed inside text boxes
	protected static $allowable_tags = "<a><p><script><div><span><section><label><br><h1><h2><h3><h4><h5><h6><scr'+'ipt><style><article><ul><ol><li><strong><em><button><aside><blockquote><footer><header><form><nav><input><textarea><select>";
	// built in templates
	// TODO: build dynamically
	protected static $default_tpls = array();
	// default layout for template
	protected static $template = array(							// defines template fields
		'css'	=> array(
			'type'			=> 'textarea',
			'label'			=> 'CSS',
			'description'	=> '
You can use any valid CSS in this field to customize the listing, which will also inherit the CSS from the theme.',
			'css'			=> 'mime_css', 						// used for CodeMirror
			'default'		=> '',
		),
		'snippet_body'		=> array(
			'type'			=> 'textarea',
			'label'			=> 'Page Body',
			'description'	=> '
You can use any valid HTML in this field to format the subcodes. 
If you leave this section empty the page will be rendered using the default template, which you can style using CSS in the block above.',
			'css'			=> 'mime_html', 					// used for CodeMirror
			'default'		=> '',
		),
	);
	protected static $active_template = array();




	/**
	 * Create an instance and get templates
	 */
	static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'post_type_templating' ), 1 );
	}


	/**
	 * Return the parameters that describe this
	 * @return multitype:
	 */
	public static function get_args() {
		return array('template' => self::$template, 'subcodes' => self::$subcodes);
	}
		
	
	public static function get_template($id) {
		$tpl = array();
		if ($id && strpos($id, self::TPL_KEY)===0) {
			$tpl = get_option($id, $tpl);
		}
		return $tpl;
	}
	
	
	public static function save_template($id, $atts) {
		$atts = (array)$atts;
		// sanity check
		if (empty($atts['title']) || ($id && strpos($id, self::TPL_KEY)!==0)) {
			return '';
		}
		if (empty($id)) {
			$count = get_option(self::LIST_COUNT_KEY, 0) + 1;
			$id = self::TPL_KEY . $count;
			update_option(self::LIST_COUNT_KEY, $count);
		}
		$args = self::get_args();
		$data = $args['template'] + array('title'=>'', 'date'=>time());
		foreach($data as $key => &$val) {
			if (isset($atts[$key])) {
				$val = trim(stripslashes($atts[$key]));
			}
		}
		update_option($id, $data);
		self::build_tpl_list();
		return $id;
	}

	
	public static function delete_template($id) {
		if (strpos($id, self::TPL_KEY) === 0) {
			delete_option($id);
			self::build_tpl_list();
		}
	}
	
	
	public static function get_active_template() {
		if (empty(self::$active_template)) {
			self::$active_template = self::get_template('pl_listing_template__1');
		}
		return self::$active_template;
	}
	
	
	// Template list management
	
	
	
	public static function get_template_list($all = false) {
		$tpl_type_map = array();
		$tpl_list = get_option(self::LIST_KEY, array());
		foreach ($tpl_list as $id => $name) {
			if ($id == self::TPL_KEY . '_preview' && !$all) continue;
			$tpl_type_map[$id] = array('type'=>'custom', 'title'=>$name, 'id'=>$id);
		}
		return $tpl_type_map;
	}
	
	
	public static function build_tpl_list() {
		global $wpdb;
		$tpls = $wpdb->get_results("SELECT * FROM $wpdb->options WHERE option_name LIKE '".str_replace('_', '\_', self::TPL_KEY)."%'");
		$tpl_list = array();
		foreach($tpls as $tpl){
			$tpl_data = get_option($tpl->option_name, array());
			if(empty($tpl_data['title'])) {
				$tpl_data['title'] = '';
			}
			$tpl_list[$tpl->option_name] = $tpl_data['title'];
		}

		ksort($tpl_list);
		update_option(self::LIST_KEY, $tpl_list);
		return $tpl_list;
	}
	
	
	// Template usage mapping
	
	
	public static function templates_in_use() {
		return array();
	}
	
	
	public static function update_listing_map($map) {
		update_option(self::MAP_KEY, $map);
	}

	
	public static function template_used_by($id) {
		$usedby = array();
		$map = get_option(self::MAP_KEY, array());
		foreach($map as $type=>$tpl_id) {
			if ($tpl_id == $id) $usedby[] = $type;
		}
		return $usedby;
	}

	
	// Render page
	
	
	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;

		if( !empty($post) && $post->post_type == 'property') {
			$tpl = self::get_active_template();
			if (!empty($tpl['css'])) {
				// Hook in to render our css
				add_filter('wp_head', array( __CLASS__ ,'custom_property_details_css_filter'));
			}
			
			if (!empty($tpl['snippet_body'])) {
				// Hook in to render our template
				remove_all_filters('the_content');
				add_filter('the_content', array( __CLASS__ ,'custom_property_details_html_filter'));
			}
		}
	}

	public static function custom_property_details_css_filter() {
		echo '<style type="text/css">'.self::$active_template['css'].'</style>';
	}
	
	public static function custom_property_details_html_filter($content) {
		global $post;
		
		$listing_data = PLS_Plugin_API::get_listing_in_loop();
		// add in js to init the map
		// TODO: move this to subcode handler?
		$js = "
			<script type=\"text/javascript\">
			jQuery(document).ready(function( $ ) {
				var map = new Map();
				var listing = new Listings({
					single_listing : ".json_encode($listing_data).",
					map: map
				});
				map.init({
					type: 'single_listing',
					listings: listing,
					lat : ".json_encode($listing_data['location']['coords'][0]).",
					lng : ".json_encode($listing_data['location']['coords'][1]).",
					zoom : 14
				});
				listing.init();
			});
			</script>
		";

		return PL_Shortcodes::single_listing_template(self::$active_template['snippet_body'], $listing_data).$js;
	}
}

PL_Listing_Customizer::init();