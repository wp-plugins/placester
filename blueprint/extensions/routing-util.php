<?php 

PLS_Route::init();

class PLS_Route {

	const CACHE_NONE = 0;
	const CACHE_PER_PAGE = 1;

	public static $request = array();
	public static $wrap_requests = true;

	// Hooks take care of everything, developer has full control over file system
	public static function init () {
		if (current_theme_supports('pls-routing-util-templates'))  {
			// Hook into each classification, so we can store the request locally. 
			add_action( '404_template', array( __CLASS__, 'handle_404'  ));
			add_action( 'search_template', array( __CLASS__, 'handle_search'  ));
			add_action( 'home_template', array( __CLASS__, 'handle_home'  ));	
			add_action( 'front_page_template', array( __CLASS__, 'handle_front_page'  ));	
			add_action( 'paged_template', array( __CLASS__, 'handle_paged'  ));	
			add_action( 'attachment_template', array( __CLASS__, 'handle_attachment'  ));	
			add_action( 'taxonomy_template', array( __CLASS__, 'handle_taxonomy'  ));	
			add_action( 'archive_template', array( __CLASS__, 'handle_archive'  ));	
			add_action( 'date_template', array( __CLASS__, 'handle_date'  ));	
			add_action( 'tag_template', array( __CLASS__, 'handle_tag'  ));	
			add_action( 'author_template', array( __CLASS__, 'handle_author'  ));	
			add_action( 'single_template', array( __CLASS__, 'handle_single'  ));	
			add_action( 'page_template', array( __CLASS__, 'handle_page'  ));	
			add_action( 'category_template', array( __CLASS__, 'handle_category'  ));	
			add_action( 'comments_popup_template', array( __CLASS__, 'handle_popup_comments'  ));	
			add_action( 'comments_template', array( __CLASS__, 'handle_comments'  ));

			// Hooks into template_routing for auto wrapping header and footer.
			// NOTE: This will fire AFTER one of the hooks above is called and the request info is stored...
			add_filter( 'template_include', array( __CLASS__, 'routing_logic' ) );
		}
	}

	public static function routing_logic ($template) {
		// error_log("Hit routing_logic...\n");
		// error_log("Request: " . var_export(self::$request, true) . "\n");
		// error_log("Wordpress wants: {$template} \n");
		
		$new_template = '';

		if (self::$wrap_requests) {
			// NOTE: If wrapper is used, it will handle the proper loading -- returning blank will 
			// clear the filter causing no additional pages to be included.
			self::wrapper();
		} else {
			// Check the request var to see what template is being requested, load that template
			// if theme, child, or blueprint has it -- handle dynamic does this naturally. 
			$new_template = self::handle_dynamic();
		}		
		
		return $new_template;
	}

	/*
	 * Adds theme wrapping functionality -- allows theme developers to avoid code repetition by adding the common 
	 * surrounding code from templates to a wrapper.php file.
	 *
	 * Based on the ideas of http://scribu.net/wordpress/theme-wrappers.html, modified for blueprint's needs...
	 */
	public static function wrapper () {
		// Initialize vars...
		$base = '';
		$templates = array();

		// We need to construct a list of wrapper files we're looking for. 
		// The basic construction is to look for wrapper-[base].php and then for blueprint/wrappers/wrapper-[base].php
		// This is done for situations when we can have multiple templates used for the same file (i.e., pages, archives, etc.)
		foreach ((array)self::$request as $variation) {
			$base = substr( basename($variation), 0, -4 );	
			$templates[] = sprintf('wrapper-%s', $variation);
		}
		
		$templates[] = 'wrapper.php';
		
		// If wrapper is being used, it will load attempt to load the various wrapper iterations.
		// wrapper() needs to have PLS_Route::handle_dynamic to actually load the requested page after wrapper is loaded. 
		return self::router($templates, true);
	}

	/* Checks for a user defined file, if not present returns the required blueprint template.
	 *
	 * Direct copy + paste of WP's locate function modified to alternate searching for the dev's
	 * templates, then look for blueprints.
	 */
	public static function router ($template_names, $load = false, $require_once = true, $include_vars = false, $cache_type = self::CACHE_NONE) {
		// error_log("Templates: " . var_export($template_names, true) . "\n");

		// Try to locate the template file to use in order of those that appear in the 'template_names' array...
		$located = self::locate_blueprint_template($template_names);
		// error_log("Template found/selected: {$located}\n");

		if ($load && !empty($located)) {
			// Capture/cache rendered HTML of GET requests...
			$cache_on = ($cache_type === self::CACHE_PER_PAGE) && ($_SERVER['REQUEST_METHOD'] === 'GET');
			
			// error_log("method: {$_SERVER['REQUEST_METHOD']}");
			// error_log("cache_type: {$cache_type}");
			// error_log("Will cache: " . ($cache_on ? "YES" : "NO") . "\n");

			if ($cache_on) {     
				$cache = new PLS_Cache('Template');
				$cache_args = array('template' => $located, 'uri' => trailingslashit($_SERVER['REQUEST_URI']));
				// error_log(var_export($cache_args, true));
				if ($result = $cache->get($cache_args)) {
					// error_log("[[Router cache hit!]] Returning rendered HTML for : {$located}\n");
					echo $result;
					return;
				}
				
				// Cache miss, so buffer the rendered template so it can be cached...
				ob_start();
			}

			load_template($located, $require_once);

			// Capture/cache rendered HTML unless we're in debug mode
			if ($cache_on) {
				$result = ob_get_clean();
				$cache->save($result);
				echo $result;
			}

		} 
		elseif ($include_vars) {
			ob_start();
				extract($include_vars);
				load_template( $located, $require_once);
			echo ob_get_clean();
		}
			
		return $located;
		
	}

	// Determines which file to load broken off from router so it can be reused. 
	public static function locate_blueprint_template ($template_names) {
		// This must be an array...
		if (!is_array($template_names)) { return null; }

		// Initialize...
		$located = "";

		foreach ($template_names as $template_name) {
			if ( !$template_name ) { 
				continue; 
			}
			// First check for the template in the theme dir...
			elseif (file_exists(get_stylesheet_directory() . '/' . $template_name)) {
				$located = get_stylesheet_directory() . '/' . $template_name;
				break;
			}
			// Failback to looking for the template in blueprint...
			elseif (file_exists(PLS_TPL_DIR . '/' . $template_name)) {
				$located = PLS_TPL_DIR . '/' . $template_name;
				break;
			}
		}

		return $located;
	}

	// Determines which file to load broken off from router so it can be reused. 
	public static function locate_blueprint_option ($template_names) {
		$located = '';

		foreach ( (array) $template_names as $template_name ) {
			if ( !$template_name )
				continue;
			if ( file_exists(trailingslashit(get_stylesheet_directory()) . 'options/' . $template_name)) {
				$located = trailingslashit(get_stylesheet_directory()) . 'options/' . $template_name;
				break;
			} else if ( file_exists(trailingslashit( PLS_OP_DIR ) . $template_name) ) {
				$located = trailingslashit( PLS_OP_DIR ) . $template_name;
				break;
			}
		}
		return $located;
	}
	
	/* Displays the HTML of a given page. 
	 * NOTE: Needs to be updated so it can be safely overwritten by dropping in a properly named file into the theme root...
	 */
	public static function get_template_part ($slug, $name = null) {
		do_action("get_template_part_{$slug}", $slug, $name);

		$templates = array();
		if ( isset($name) )
			$templates[] = "{$slug}-{$name}.php";

		$templates[] = "{$slug}.php";

		self::router($templates, true, false);
	}

	/*
	 * Public utility functions that can be used to intelligently request the correct template. 
	 *
	 * These will naturally use the the router which respects templates from the theme, and 
	 * child theme before falling back to blueprint.
	 */

	public static function handle_dynamic () {
		return self::router(self::$request, true, null, null, self::CACHE_PER_PAGE);
	}

	public static function handle_header () {
		// Header is loaded directly rather than being set as a request and then looping the routing table.
		return self::router(array('header.php'), true, null, null, self::CACHE_PER_PAGE);
	}

	public static function handle_sidebar () {
		// Sidebar is loaded directly rather than being set as a request and then looping the routing table.
        $sidebars = array();
        foreach ((array)self::$request as $item) {
            $sidebars[] = 'sidebar-' . $item;
        }
        $sidebars[] = 'sidebar.php';
		return self::router($sidebars, true, null, null, self::CACHE_PER_PAGE);
	}

	public static function handle_default_sidebar () {
		// The default sidebar is loaded directly rather then being set as a request and then looping the routing table.
        $sidebars = array();
        foreach ((array)self::$request as $item) {
            $sidebars[] = 'default-sidebar-' . $item;
        }
        $sidebars[] = 'default-sidebar.php';
		return self::router($sidebars, true, null, null, self::CACHE_PER_PAGE);
	}

	public static function handle_footer () {
		// Footer is loaded directly rather then being set as a request and then looping the routing table.
		return self::router(array('footer.php'), true, null, null, self::CACHE_PER_PAGE);
	}


	/* 
	 *	Hooked functions, likely not a good idea to mess-around down here...
	 */

	public static function handle_comments () {
		return self::router(array('comments.php'));
	}

	public static function handle_popup_comments () {
		return self::router(array('popup_comments.php'));	
	}

	public static function handle_404 () {
		self::$request[] = '404.php';
	}

	public static function handle_search () {
		self::$request[] = 'search.php';
	}

	// hooked to home + index
	public static function handle_home () {
    	self::$request = array_merge(self::$request, array( 'home.php', 'index.php' ));
	}

	// hooked to front-page.php
	public static function handle_front_page () {
		self::$request[] = 'front-page.php';
	}

	public static function handle_paged () {
		self::$request[] = 'paged.php';
	}

	public static function handle_date () {
		self::$request[] = 'date.php';
	}

	// Needs additional logic to handle different types of post type archives. 
	public static function handle_archive ($templates) {

		$post_type = get_query_var( 'post_type' );

		$templates = array();

		if ( $post_type ) {
			$templates[] = "archive-{$post_type}.php";
		}
			
		$templates[] = 'archive.php';

		self::$request = array_merge(self::$request, $templates);
	}

	public static function handle_author () {
		$author = get_queried_object();

		$templates = array();

		$templates[] = "author-{$author->user_nicename}.php";
		$templates[] = "author-{$author->ID}.php";
		$templates[] = 'author.php';

		self::$request = array_merge(self::$request, $templates);
	}

	public static function handle_category ($templates) {
		$category = get_queried_object();

		$templates = array();

		$templates[] = "category-{$category->slug}.php";
		$templates[] = "category-{$category->term_id}.php";
		$templates[] = 'category.php';

		self::$request = array_merge(self::$request, $templates);
	}

	public static function handle_tag () {

		$tag = get_queried_object();

		$templates = array();

		$templates[] = "tag-{$tag->slug}.php";
		$templates[] = "tag-{$tag->term_id}.php";
		$templates[] = 'tag.php';

		self::$request = array_merge(self::$request, $templates);
	}

	// Hooked to handle single post templates
	public static function handle_single () {

		$object = get_queried_object();

		$templates = array();

		$templates[] = "single-{$object->post_type}.php";
		$templates[] = "single.php";
		
		self::$request = array_merge(self::$request, $templates);
	}

	// Hooked to handle page templates
	public static function handle_page ($template) {
		
		// This is a direct copy and paste from WP.
		// Because wordpress isn't verbose about what it discovers it does this request -- we'll need to duplicate it.
		$id = get_queried_object_id();
		$template = get_post_meta($id, '_wp_page_template', true);
		$pagename = get_query_var('pagename');

		if (!$pagename && $id > 0) {
			// If a static page is set as the front page, $pagename will not be set. Retrieve it from the queried object
			$post = get_queried_object();
			$pagename = $post->post_name;
		}

		if ('default' == $template) {
			$template = '';
		}
		
		$templates = array();
		if ( !empty($template) && !validate_file($template) )
			$templates[] = $template;
		if ( $pagename )
			$templates[] = "page-$pagename.php";
		if ( $id )
			$templates[] = "page-$id.php";
		$templates[] = 'page.php';

		// The possible templates are stored in the request var so router can use them later when the filter is called 
		// to decide which pages to look for... 
		self::$request = array_merge(self::$request, $templates);
	}

	public static function handle_taxonomy () {
		global $query_string;
		$args = wp_parse_args($query_string, array('state' => false, 'city' => false, 'neighborhood' => false, 'zip' => false, 'street' => false, 'mlsid' => false));
		extract($args);

		$templates = array();

		if ($state || $city || $zip || $neighborhood || $street || $mlsid) {
			if ($street) {
				$templates[] = 'attribute-street.php';
			} elseif ($neighborhood) {
				$templates[] = 'attribute-neighborhood.php';
			} elseif ($zip) {
				$templates[] = 'attribute-zip.php';
			} elseif ($city) {
				$templates[] = 'attribute-city.php';
			} elseif ($state) {
				$templates[] = 'attribute-state.php';
			} elseif ($mlsid) {
				$templates[] = 'attribute-mlsid.php';
			}
			$templates[] = 'attribute.php';
			self::$request = array_merge(self::$request, $templates);
		} else {
			$term = get_queried_object();
			$taxonomy = $term->taxonomy;

			$templates[] = "taxonomy-$taxonomy-{$term->slug}.php";
			$templates[] = "taxonomy-$taxonomy.php";
			$templates[] = 'taxonomy.php';

		}

		self::$request = array_merge(self::$request, $templates);
	}

	// Attachment pages, not sure what to do with this.
	//
	// NOTE: Needs some additional logic so blueprint can handle all the different template types
	// modified to pass list of possible templates properly...
	public static function handle_attachment () {
		global $posts;
		$templates[] = array();
				
		$type = explode('/', $posts[0]->post_mime_type);
		
		if ( $template = get_query_template($type[0]) ) {
			// return $template;
			$templates[] = $template;
		}
		if ( $template = get_query_template($type[1]) ) {
			// return $template;
			$templates[] = $template;
		}
		if ( $template = get_query_template("$type[0]_$type[1]") ) {
			// return $template;
			$templates[] = $template;
		}
		return self::router($templates, true, null, null, self::CACHE_PER_PAGE);
	}

}

?>