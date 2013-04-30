<?php 
class PL_Router {

	private static function router($template, $params, $wrap = false, $directory = PL_VIEWS_ADMIN_DIR) {
		ob_start();
			self::load_builder_view('header.php');
			self::load_builder_view($template, $directory, $params);	
			self::load_builder_view('footer.php');
		echo ob_get_clean();	
		
	}

	public static function load_builder_partial($template, $params = array(), $return = false) {
		ob_start();
			if (!empty($params)) {
				extract($params);
			}
			include(trailingslashit(PL_VIEWS_PART_DIR) . $template);
		if ($return) {
			return ob_get_clean();
		} else {
			echo ob_get_clean();	
		}
	}

	public static function load_builder_library ($template, $directory = PL_JS_LIB_DIR) {
		include_once(trailingslashit($directory) . $template);
	}

	public static function load_builder_helper ($template, $directory = PL_HLP_DIR) {
		include_once(trailingslashit($directory) . $template);
	}

	private static function load_builder_view($template, $directory = PL_VIEWS_ADMIN_DIR, $params = array()) {
		ob_start();
			if (!empty($params)) {
				extract($params);
			}
			include_once(trailingslashit($directory) . $template);
			echo ob_get_clean();	
	}
	
	public static function pl_extensions() {
		return '';
	}

	/**
	 * List post type view paths (post types are hidden not to overlap admin dashboard)
	 * 
	 * @param string $post_type the post type in use
	 * @param enum $page_type list or add
	 */
	public static function post_type_path($post_type, $page_type = 'list') {
		if( $page_type == 'list' ) {
			return 'edit.php?post_type=' . $post_type;
		}
		else if( $page_type == 'add' ) {
			return 'post-new.php?post_type=' . $post_type;
		}
		 
		return '';
	}
	
	public static function my_listings() {
		self::router('my-listings.php', array('test'=>'donkey'), false);
	}

	public static function add_listings() {
		if (isset($_GET['id'])) {
			// Fetch listing and process it...
			$listing = PL_Listing_Helper::get_single_listing($_GET['id']);
			$_POST = PL_Listing_Helper::process_details($listing);
		}
		
		self::router('add-listing.php', array(), false);
	}

	public static function load_snippet($shortcode, $snippet, $type) {
		ob_start();
			// Add parameter validation code...
		  switch ($type) 
		  {
		  	case 'custom' :
		  	  $snippet_DB_key = ('pls_' . $shortcode . '_' . $snippet);
		  	  $snippet_body = get_option($snippet_DB_key, 'Cannot find custom snippet...');
		  	  echo html_entity_decode($snippet_body, ENT_QUOTES);
		  	  break;                                                                                                     
		  	case 'default' :
		  	default :
		  	  $filename = (trailingslashit(PL_VIEWS_SHORT_DIR) . trailingslashit($shortcode) . $snippet . '.php');
		  	  //echo $filename;
		  	  include $filename;
		  }
		return ob_get_clean();
	}

	public static function theme_gallery() {
		if (isset($_GET['theme_url'])) {
			self::router('install-theme.php', array('test'=>'donkey'), false);	
		} else {
			self::router('theme-gallery.php', array('test'=>'donkey'), false);	
		}
	}

	public static function settings() {
		self::router('settings/general.php', array(), false);
	}
	public static function settings_polygons() {
		self::router('settings/polygons.php', array(), false);
	}
	public static function settings_property_pages() {
		self::router('settings/property.php', array(), false);
	}
	public static function settings_international() {
		self::router('settings/international.php', array(), false);
	}
	public static function settings_neighborhood() {
		self::router('settings/neighborhood.php', array(), false);
	}
	public static function settings_filtering() {
		self::router('settings/filtering.php', array(), false);
	}
	public static function settings_template() {
		self::router('settings/template.php', array(), false);
	}
	public static function settings_client() {
		self::load_builder_helper('membership.php');
		self::router('settings/client.php', PL_Membership_Helper::get_client_settings(), false);
	}

	public static function support() {
		self::router('support.php', array(), false);
	}

	public static function integrations() {
		self::router('integrations.php', array(), false);
	}

//end of class
}