<?php 

PL_Pages::init();
/**
 * @todo expose list of taxonomies in helpers/taxonomy.php and get list from there instead
 */
class PL_Pages {

	public static $property_post_type = 'property';
	public static $all_taxonomies = array(
		'state',
		'zip',
		'city',
		'neighborhood',
		'street',
		'beds',
		'baths',
		'half-baths',
		'mlsid'
	);

	public static function init () {
		add_action( 'init', array(__CLASS__, 'create_taxonomies') );
		add_action( 'wp_footer', array(__CLASS__,'force_rewrite_update') );
		add_action( 'admin_footer', array(__CLASS__,'force_rewrite_update') );
		add_action( '404_template', array( __CLASS__, 'dump_permalinks') );
		add_action( 'wp', array(__CLASS__, 'catch_404s') );
	}

	//return many page urls
	public static function get () {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT * FROM ' . $wpdb->posts .' WHERE post_type = %s', self::$property_post_type);
	    $rows = $wpdb->get_results($sql, ARRAY_A);
		
		return $rows;
	}

	// Return a page URL
	public static function details ($placester_id) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT ID, post_modified FROM " . $wpdb->posts . " WHERE post_type = %s AND post_name = %s LIMIT 0, 1", self::$property_post_type, $placester_id);
	    $row = $wpdb->get_row($sql, OBJECT, 0);
	    
	    $post_id = ( isset($row->ID) ? $row->ID : null );
	    
	    return $post_id; 	
	}

	// Create listing property CPT
	public static function manage_listing ($api_listing) {
		$page_details = array();
		$page_details['post_id'] = self::details($api_listing['id']);
		$page_details['type'] = self::$property_post_type;
		$page_details['title'] = $api_listing['location']['address'];
		$page_details['name'] = $api_listing['id'];
		$page_details['content'] = '';
		$page_details['taxonomies'] = array(
			'zip' => $api_listing['location']['postal'], 
			'city' => $api_listing['location']['locality'],
			'state' => $api_listing['location']['region'],
			'neighborhood' => $api_listing['location']['neighborhood'],
			'street' => $api_listing['location']['address'],
			'beds' => (string) $api_listing['cur_data']['beds'],
			'baths' => (string) $api_listing['cur_data']['beds'],
			'half-baths' => (string) $api_listing['cur_data']['half_baths'],
			'mlsid' => (string) $api_listing['rets']['mls_id']
		);
		
		return self::manage($page_details);
	}

	public static function create_once ($pages_to_create, $force_template = true) {
		foreach ($pages_to_create as $page_info) {
			$page = get_page_by_title($page_info['title']);
			if (!isset($page->ID)) {
				$page_details = array();
				$page_details['title'] = $page_info['title'];
				if (isset($page_info['template'])) {
          			$page_details['post_meta'] = array('_wp_page_template' => $page_info['template']);
				}
				if (isset($page_info['content'])) {
          			$page_details['content'] = $page_info['content'];
				}

        		self::manage($page_details);
			} 
			elseif ($force_template) {
		        if (isset($page_info['template'])) {
		        	delete_post_meta( $page->ID, '_wp_page_template' );
		        	add_post_meta( $page->ID, '_wp_page_template', get_template_directory_uri().'/'.$page_info['template']);
		        }
			}
		}
	}

	//create page
	public static function manage ($args = array()) {
		$defaults = array('post_id' => false, 'type' => 'page', 'title' => '', 'name' => false, 'content' => ' ', 'status' => 'publish', 'post_meta' => array(), 'taxonomies' => array());
		extract(wp_parse_args($args, $defaults));
		
		$post = array(
			'post_type'   => $type,
			'post_title'  => $title,
			'post_name'   => $name,
			'post_status' => $status,
			'post_author' => 1,
			'post_content'=> $content,
			'filter'      => 'db',
			'guid'        => @$guid
		);
             
		if ($post_id <= 0) {
			$post_id = wp_insert_post($post);

			if (!empty($post_meta)) {
				foreach ($post_meta as $key => $value) {
					add_post_meta($post_id, $key, $value, TRUE);
				}
			}

			if (!empty($taxonomies)) {
				foreach ($taxonomies as $taxonomy => $term) {
					wp_set_object_terms($post_id, $term, $taxonomy);
				}
			}
		} 
		else {	
			$post['ID'] = $post_id;
			$post_id = wp_update_post($post);
		}

        return $post_id;
	}

	/**
	 * Deletes all properties and their associated post meta.
	 * 
	 * @return bool true if delete successful
	 */
	public static function delete_all () {
		global $wpdb;

		$q_ids = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s", self::$property_post_type);
		$prop_ids = $wpdb->get_col($q_ids);

		if (!is_array($prop_ids) || count($prop_ids) === 0) {
			return false;
		}

		$id_str = implode(',', $prop_ids);
    	$results = $wpdb->query( "DELETE FROM $wpdb->posts WHERE ID IN ($id_str)");

    	if (empty($results)) {
    		return false;
    	}

		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE post_id IN ($id_str)");
		
		// NOTE: This call produces highly negative side-effects, as neighborhood meta-info created by clients
		// relies on the related term and taxonomy to exist, even after clearing properties...re-evaluate ASAP!
		//
		// self::delete_all_terms();

		self::ping_yoast_sitemap();

    	return true;
	}

	/**
	 * Given a name (property id), deletes the corresponding WP post and all associated data
	 * 
	 * @param  string $name post_name (property id)
	 * @return bool 	true if successful
	 */
	public static function delete_by_name ($name) {
		global $wpdb;

		if (!$name) {
			return false;
		}

		$q_id = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type = %s", $name, self::$property_post_type);
		$post_id_arr = $wpdb->get_col($q_id);

		if (!is_array($post_id_arr) || count($post_id_arr) === 0) {
			return false;
		}

		$post_id = $post_id_arr[0];
		$result = (bool) wp_delete_post($post_id, true);

		if ($result) {
			self::ping_yoast_sitemap();
		}

		return $result;
	}

	/**
	 * Deletes all terms (and their relationships) associated with Property taxonomies.
	 * 
	 * @todo can we prompt Yoast to rebuild its sitemap?
	 * 
	 * NOTE: Decomissioned until further evaluation -- see note in the call to this function inside of "delete_all"
	 */
/*
	public static function delete_all_terms() {
		global $wpdb;

		$args = array(
			'hide_empty' => false,
			'fields' => 'ids'
		);

		$all_terms = get_terms( self::$all_taxonomies, $args );

		if (!is_array($all_terms) || count($all_terms) === 0) {
			return;
		}

		$term_str = implode(',', $all_terms);
		$wpdb->query("DELETE FROM $wpdb->terms WHERE term_id IN ($term_str)");
		$term_tax_ids = $wpdb->get_col("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id IN ($term_str)");

		if (!is_array($term_tax_ids) || count($term_tax_ids) === 0) {
			return;
		}

		$term_tax_str = implode(',', $term_tax_ids);
		$wpdb->query("DELETE FROM $wpdb->term_taxonomy WHERE term_taxonomy_id IN ($term_tax_str)");
		$q_term_rel = $wpdb->query("DELETE FROM $wpdb->term_relationships WHERE term_taxonomy_id IN ($term_tax_str)");
	}
*/

	public static function create_taxonomies () {
		register_post_type(self::$property_post_type, array('labels' => array('name' => __( 'Properties' ),'singular_name' => __( 'property' )),'public' => true,'has_archive' => true, 'rewrite' => true, 'query_var' => true, 'taxonomies' => array('category', 'post_tag')));
		
		global $wp_rewrite;
		
		// Allows for <URL>?property=<ID> access...
		$wp_rewrite->add_rewrite_tag("%property%", "([^/]+)", "property=");
	   	
	    $property_structure = "/property/%state%/%city%/%zip%/%neighborhood%/%street%/%" . self::$property_post_type . "%";
        $wp_rewrite->add_permastruct("property", $property_structure, false);
        
        remove_post_type_support(self::$property_post_type, "comments");
	}

	public static function force_rewrite_update () {
		if (defined('PL_PLUGIN_VERSION')) {
			$current_version = get_option('pl_plugin_version');
			if ($current_version != PL_PLUGIN_VERSION) {
				// Run the updater script before updating the version number...
				include_once(trailingslashit(PL_PARENT_DIR) . 'updater.php');

				// Update version in DB
				update_option('pl_plugin_version', PL_PLUGIN_VERSION);
				
				// global $wp_rewrite;
				// $wp_rewrite->flush_rules();

				PL_Cache::invalidate();

				// self::delete_all();
			}
		}
	}

	public static function dump_permalinks () {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	 * Check for a request for properties that 404ed. Get the property (thus creating the cpt), and if
	 * property exists redirect to its permalink.
	 */
	public static function catch_404s () {
		global $wp_query;

		if (!is_404()) {
			return;
		}

		if ($wp_query->query_vars['post_type'] === self::$property_post_type 
			&& $wp_query->query_vars[self::$property_post_type] != '') {

			$req_id = $wp_query->query_vars[self::$property_post_type];
			$args = array( 'listing_ids' => array($req_id) );
			$response = PL_Listing::get($args);

			if ( !is_array($response) || !isset($response['listings']) || 
				!is_array($response['listings']) || !count($response['listings']) > 0 ) {
				return;
			}

			$pmlink = get_permalink($response['listings'][0]['id']);
			if ($pmlink) {
				wp_redirect($pmlink);
				exit;
			}
		}
	}

	/**
	 * If Yoast sitemaps are enabled, causes Yoast to request the sitemap (populating caches)
	 * and to request that search engines re-index the site.
	 */
	public static function ping_yoast_sitemap() {
		global $wpseo_sitemaps;

		if (!$wpseo_sitemaps) {
			$path = WP_PLUGIN_DIR . '/wordpress-seo/inc/class-sitemaps.php';
			if (file_exists($path)) {
				require_once $path;
				$wpseo_sitemaps = new WPSEO_Sitemaps();
			} else {
				return;
			}
		}

		if (method_exists($wpseo_sitemaps, 'hit_sitemap_index')) {
			$wpseo_sitemaps->hit_sitemap_index();
		}

		if (method_exists($wpseo_sitemaps, 'ping_search_engines')) {
			$wpseo_sitemaps->ping_search_engines();
		}
	}

}