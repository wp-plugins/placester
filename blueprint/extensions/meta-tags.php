<?php 
/**
 * Meta Tags Class
 *
 * This class is the base class for creating meta tags and micro data for the site.
 * 
 * This includes handling the WordPress SEO plugin overrides, Schema.org micro data, and other social meta data handling.
 *
 */

PLS_Meta_Tags::init();

class PLS_Meta_Tags {

	private static $page_tags = array();
	private static $yoast_flag;

	public static function init () {
		add_filter('wp_head', array(__CLASS__, 'construct_meta_tags'));
		add_filter('wp_title', array(__CLASS__, 'hook_title_tag'), 18, 1);
	}

	public static function is_yoast_enabled () {
		// Check for memoized return value...
		if (isset(self::$yoast_flag)) {
			return self::$yoast_flag;
		}

		// Is Yoast WordPress SEO plugin is enabled this single site? (not network-wide)
		$active_plugins = get_option('active_plugins');
		if (in_array("wordpress-seo/wp-seo.php", $active_plugins)) {
			return self::$yoast_flag = true;
		}

		// Check the globally available plugins
		if (function_exists('wp_get_active_network_plugins')) {
			// Is Yoast WordPress SEO plugin is enabled Network-wide?
			$plugins = wp_get_active_network_plugins();
			foreach ($plugins as $key => $plugin) {
				// cut the last 24 char off each plugin
				$plugin_name = substr($plugin, -24);
				// check if it's Yoast plugin
				if ($plugin_name == 'wordpress-seo/wp-seo.php') {
					return self::$yoast_flag = true;
				}
			}
		}

		// Haven't found it
		return self::$yoast_flag = false;
	}

	public static function construct_meta_tags () {
		// take meta tag designations, and apply them to the HTML elements
		$tags = self::determine_appropriate_tags();

		ob_start();

		// If Yoast is enabled, we will not try to override it in any way.  However,
		//   if we're on a property page we'll add an og:image and an og:description
		//   drawn from the listing because Yoast doesn't know about property post
		//   types.
		if (self::is_yoast_enabled()) {
			if (is_singular('property')) { ?>

<!-- Additional OpenGraph Tags -->
<meta property="og:image" content="<?php echo $tags['image']; ?>">
<meta property="og:description" content="<?php echo $tags['description']; ?>" />

		<?php }
		} else {
			if (is_home()) { ?>

<!-- OpenGraph Tags -->
<meta property="og:url" content="<?php echo $tags['url']; ?>">
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo pls_get_option('pls-site-title'); ?>" />
<meta property="og:description" content="<?php echo $tags['description']; ?>" />

			<?php } else { ?>

<!-- OpenGraph Tags -->
<meta property="og:url" content="<?php echo $tags['url']; ?>">
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php echo $tags['title']; ?>" />
<meta property="og:image" content="<?php echo $tags['image']; ?>">
<meta property="og:site_name" content="<?php echo pls_get_option('pls-site-title'); ?>" />
<meta property="og:description" content="<?php echo $tags['description']; ?>" />

		<?php }
		} ?>

<!-- Schema.org Tags -->
<meta itemprop="url" content="<?php echo $tags['url']; ?>">
<meta itemprop="name" content="<?php echo $tags['title']; ?>">
<meta itemprop="email" content="<?php echo $tags['email']; ?>">
<meta itemprop="description" content="<?php echo $tags['description']; ?>">

<!-- Meta Tags -->
<meta name="author" content="<?php echo $tags['author']; ?>">
<meta name="description" content="<?php echo $tags['description']; ?>">

		<?php

		$tags_html = ob_get_clean();

		echo $tags_html;
	}

	public static function hook_title_tag ($original_title) {
		// Special handling if Yoast SEO plugin is enabled and has returned non-empty title tag content...
		if (self::is_yoast_enabled() & !empty($original_title)) {
			// By default, if Yoast produced a title, return that value unaltered...
			$return_orig = true;

			// Special handling for the home page...
			if (is_home()) {
				global $wpseo_front;
				global $sep;

				$seplocation = is_rtl() ? 'left' : 'right';
				$default_title = $wpseo_front->get_default_title($sep, $seplocation);

				// If the default title is what Yoast produced, use ours instead (i.e., do NOT return original)
				$return_orig = !($default_title == $original_title);
			}

			if ($return_orig) {
				return $original_title;
			}
		}

		// take meta tag designations, and apply them to the HTML elements
		$tags = self::determine_appropriate_tags();

		return $tags['title'];
	}

	public static function determine_appropriate_tags () {
		// Check for memoized return value...
		if (!empty(self::$page_tags)) {
			return self::$page_tags;
		}

		global $post;
		$tags = array();

		// get page template
		$page_template = self::determine_page_template();

		// determine $meta_tag_designations
		switch ($page_template) {

			case 'neighborhood':
				// Neighborhood / City Page
				$location = PLS_Taxonomy::get_location_by('slug', get_query_var('term'), get_query_var('taxonomy'), true);
				$tags['title'] = $location ? $location['name'] : '';

				$tags['address'] = "";
				$descrip = $location ? strip_tags($location['description']) : '';
				$descrip_more = '';

				if (strlen($descrip) > 155) {
					$descrip = substr($descrip, 0, 155);
					$descrip_more = ' ...';
				}

				$descrip = str_replace('"', '', $descrip);
				$descrip = str_replace("'", '', $descrip);
				$descripwords = preg_split('/[\n\r\t ]+/', $descrip, -1, PREG_SPLIT_NO_EMPTY);
				array_pop($descripwords);
				$tags['description'] = implode(' ', $descripwords) . $descrip_more;

				$tags['image'] = '';

				break;

			case 'search':
				$tags['title'] = 'Search results for: ' . get_search_query();

				break;

			case 'category':
				$category = get_the_category();
				$tags['title'] = $category[0]->cat_name;
				$tags['description'] = $category[0]->description;

				break;

			case 'date':
				if (is_day()) {
					$tags['title'] = get_the_date() . ' Archives';
				}
				elseif (is_month()) {
					$tags['title'] = get_the_date('F Y') . ' Archives';
				}
				elseif (is_year()) {
					$tags['title'] = get_the_date('Y') . ' Archives';
				}
				else {
					$tags['title'] = 'Blog Archives';
				}

				break;

			case 'tag':
				$tag = single_tag_title('',false);
				$tags['title'] = $tag . ' tagged posts';
				$tags['itemtype'] = 'http://schema.org/Blog';
				$tags['description'] = tag_description();

				break;

			case 'author':
				$tags['author'] = get_the_author();
				$tags['itemtype'] = 'http://schema.org/Blog';
				$tags['title'] = 'Author Archives: ' . get_the_author_meta( 'display_name', get_query_var( 'author' ) );
				// $image - should be author's face is one is set... could also check for same name in agent's list too.
				$tags['description'] = tag_description();

				break;

			case 'property':
				$content = get_option('placester_listing_layout');
				if (isset($content) && $content != '') { return $content; }

				$html = '';
				$listing = PLS_Plugin_API::get_listing_in_loop();
				if (is_null($listing)) {
					break;
				}

				// Single Property
				$tags['itemtype'] = 'http://schema.org/Offer';
				if (isset($listing['location']['unit']) && $listing['location']['unit'] != null) {
					$tags['title'] = @$listing['location']['address'] . ' ' . $listing['location']['unit'] . ', ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
					$tags['address'] = @$listing['location']['address'] . ' ' . $listing['location']['unit'] . ', ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
				}
				else {
					$tags['title'] = @$listing['location']['address'] . ', ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
					$tags['address'] = @$listing['location']['address'] . ', ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
				}

				$tags['url'] = @$listing['cur_data']['url'];	// the_permalink() is not always available
				$tags['image'] = @$listing['images']['0']['url'];
				$tags['description'] = esc_html(strip_tags($listing['cur_data']['desc']));

				break;

			case 'agent':
				$tags['itemtype'] = 'http://schema.org/RealEstateAgent';
				break;

			case 'service':
				$tags['itemtype'] = 'http://schema.org/ProfessionalService';
				break;

			case 'testimonial':
				$tags['itemtype'] = 'http://schema.org/Review';
				break;

			case 'community':
				$tags['description'] = PLS_Format::shorten_excerpt($post, 155);
				break;

			case 'single':
				$tags['itemtype'] = 'http://schema.org/BlogPosting';

				if (has_post_thumbnail($post->ID)) {
					$post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
					$tags['image'] = $post_image[0];
				}

				$tags['description'] = PLS_Format::shorten_excerpt($post, 155);
				$tags['author'] = get_the_author();

				break;

			case 'home':
			case 'other':
			default:
				// Home and other pages

				break;
		}

		$meta_data = self::process_defaults($tags);

		// Memoize this output...
		self::$page_tags = $meta_data;

		return $meta_data;
	}

	private static function determine_page_template () {
		// Figure out current page's template
		if ( is_tax('neighborhood') || is_tax('city') || is_tax('state') ) {
			$page_template = 'neighborhood';
		}
		elseif ( is_search() ) {
			$page_template = 'search';
		}
		elseif ( is_category() ) {
			$page_template = 'category';
		}
		elseif ( is_date() ) {
			$page_template = 'date';
		}
		elseif ( is_tag() ) {
			$page_template = 'tag';
		}
		elseif ( is_author() ) {
			$page_template = 'author';
		}
		elseif ( is_singular('property') ) {
			$page_template = 'property';
		}
		elseif ( is_singular('agent') ) {
			$page_template = 'agent';
		}
		elseif ( is_singular('service') ) {
			$page_template = 'service';
		}
		elseif ( is_singular('testimonial') ) {
			$page_template = 'testimonial';
		}
		elseif ( is_singular('community') ) {
			$page_template = 'community';
		}
		elseif ( is_single() ) {
			$page_template = 'single';
		}
		elseif ( is_home()) {
			$page_template = 'home';
		}
		else {
			$page_template = 'other';
		}

		return $page_template;
	}
  
	private static function process_defaults ($args) {
		// Process passed args against defaults...
		global $post;

		if (is_home() || !isset($post)) {
			$url = home_url();

			$title = pls_get_option('pls-company-name');
			$title = $title ? $title : pls_get_option('pls-site-title');
			$title = $title ? $title : get_bloginfo('name');

			$description = pls_get_option('pls-company-description');
			$description = $description ? $description : pls_get_option('pls-site-subtitle');
			$description = $description ? $description : get_bloginfo('description');
		}
		else {
			$url = get_permalink();
			$title = $post->post_title;
			$description = PLS_Format::shorten_excerpt($post, 155);
		}

		$defaults = array(
			'itemtype' => 'http://schema.org/LocalBusiness',
			'url' => $url,
			'title' => $title,
			'description' => $description,
			'author' => pls_get_option('pls-user-name'),
			'email' => pls_get_option('pls-user-email'),
			'image' => pls_get_option('pls-site-logo')
		);

		return wp_parse_args( $args, $defaults );
	}
}
