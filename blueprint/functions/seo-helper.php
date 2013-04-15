<?php

/**
 * Helper class for getting SEO-related data such as title, description, author etc
 * 
 * @author nofearinc
 *
 */

class PLS_SEO_Helper {
	private static $instance;
	
	private static $wpseo_defined_name = 'WPSEO_VERSION';
	
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new PLS_SEO_Helper();
		
		}
		return self::$instance;
	}
	
	private function __construct() {
		/* Singleton */
	}

	// Shouldn't be used at first, covered by WPSEO 
	/* public function get_title() {
		global $post;

		// if Yoast's WordPress SEO is active
		if( defined( self::$wpseo_defined_name ) ) {
			global $wpseo_front;
			if( ! empty( $wpseo_front ) ) {
				$title = $wpseo_front->get_content_title( $post );
				return $wpseo_front->title( $title );
			}
		} else {
			
		}
	} */
	public function get_image() {
		
	}
	public function get_description() {
		global $post;
		
		// if Yoast's WordPress SEO is active
		if( defined( self::$wpseo_defined_name ) ) {
			global $wpseo_front;
			if( ! empty( $wpseo_front ) ) {
				return $wpseo_front->metadesc();
			}
		} else {
			return self::get_pls_description();
		}
	}
	
	// Use Placester logic to get the description
	public function get_pls_description() {
		global $post;
		$description = '';
		
		if ( is_search() ) {
			$description = pls_get_option('pls-company-description');
		} elseif (is_category()) {
			$description = $category[0]->description;
		} elseif (is_date()) {
			$description = pls_get_option('pls-company-description');
		} elseif ( is_tax('neighborhood') || is_tax('city') ) {
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			
			$descrip = strip_tags($term->description);
			$descrip_more = '';
			if (strlen($descrip) > 155) {
				$descrip = substr($descrip,0,155);
				$descrip_more = ' ...';
			}
			$descrip = str_replace('"', '', $descrip);
			$descrip = str_replace("'", '', $descrip);
			$descripwords = preg_split('/[\n\r\t ]+/', $descrip, -1, PREG_SPLIT_NO_EMPTY);
			array_pop($descripwords);
			$description = implode(' ', $descripwords) . $descrip_more;
		} elseif (is_tag() || is_author()) {
			$description = tag_description();
		} elseif ( is_singular('property') ) {
			$listing = PL_Listing_Helper::get_listing_in_loop();
			$description = @$listing['cur_data']['desc'];
		} elseif( is_single() ) {
			$descrip = strip_tags($post->post_content);
			$descrip_more = '';
			if (strlen($descrip) > 155) {
				$descrip = substr($descrip,0,155);
				$descrip_more = ' ...';
			}
			$descrip = str_replace('"', '', $descrip);
			$descrip = str_replace("'", '', $descrip);
			$descripwords = preg_split('/[\n\r\t ]+/', $descrip, -1, PREG_SPLIT_NO_EMPTY);
			array_pop($descripwords);
			$description = implode(' ', $descripwords) . $descrip_more;
		}
		
		return $description;
	}
	
	// Not provided by WordPress SEO. Entirely handled by Placester algorithm
	public function get_address() {
		global $post;
		$address = '';
		
		if( is_singular('property') ) {
			$listing = PL_Listing_Helper::get_listing_in_loop();
		
			// Single Property
			$itemtype = 'http://schema.org/Offer';
			if (isset($listing['location']['unit']) && $listing['location']['unit'] != null) {
				$address = @$listing['location']['address'] . ', ' . $listing['location']['unit'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
			} else {
				$address = @$listing['location']['address'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
			}
		} else {
			$address = @pls_get_option('pls-company-street') . " " . @pls_get_option('pls-company-locality') . ", " . @pls_get_option('pls-company-region');
		}
		
		return $address;
	}
	
	public function get_author() {
		global $post;
		
		// if Yoast's WordPress SEO is active
		if( defined( self::$wpseo_defined_name ) ) {
			global $wpseo_front;
			if( ! empty( $wpseo_front ) ) {
				$author_title = $wpseo_front->get_author_title();
				if( strpos($author_title, ',') !== false && strpos($author_title, ',') === 0 ) {
					return '';
				}
				
				return $author_title;
			}
		} else {
			return self::get_pls_author();
		}
	}
	
	// Use Placester logic to get the author
	public function get_pls_author() {
		global $post;
		$author = '';
		
		if (is_author()) {
			$author = get_the_author();
		} elseif ( is_singular('property') ) {
			$author = @pls_get_option('pls-user-name');
		} elseif ( is_single() ) {
			$author = $post->post_author;
		} else {
			$author = pls_get_option('pls-user-name');
		}
		
		return $author;
	}
	
	public function get_itemtype() {
		global $post;
		$itemtype = '';
		
		/**
		 * // Open Houses: http://schema.org/SaleEvent
			// Property Listing: http://schema.org/Offer, http://schema.org/Residence http://schema.org/Rating (for rich snippets)
			// Office Locations: http://schema.org/LocalBusiness
			// Agents / Bio's: http://schema.org/Person
			Templates: http://schema.org/SiteNavigationElement, http://schema.org/WPAdBlock, http://schema.org/WPFooter, http://schema.org/WPHeader, http://schema.org/WPSideBar, http://schema.org/Table, http://schema.org/Comment, http://schema.org/ItemList, http://schema.org/Map, http://schema.org/MediaObject (post formats), http://schema.org/Photograph (gallery and any image embed including post formats), http://schema.org/Movie (video embeds and post formats)
			If we have reviews: http://schema.org/Review
			Blog posts / post formats: http://schema.org/Blog
			// Pages: http://schema.org/Article
		 */
		
		if( is_page( 'Open Houses' ) ) {
			$itemtype = 'http://schema.org/SaleEvent';
		} elseif( is_page( 'Contact' ) ) {
			$itemtype = 'http://schema.org/LocalBusiness';
		} elseif( is_page() ) {
			$itemtype = 'http://schema.org/Article';
		} elseif ( is_singular('property') ) {
			$itemtype = 'http://schema.org/Offer';
		} elseif (is_category()
		 		|| is_date()
		 		|| is_tag() ) {
			$itemtype = 'http://schema.org/Blog';
		} elseif( is_author() ) {
			$itemtype = 'http://schema.org/Person';
		} elseif ( is_single() ) {
			$itemtype = 'http://schema.org/BlogPosting';
		} else {
			// Home and other pages
			$itemtype = 'http://schema.org/LocalBusiness';
		}
		
		return $itemtype;
	}
	
	/**
	 * Main facade method for printing the header data
	 */
	public function display_meta_tags( $echo = TRUE ) {
		ob_start();
		
		$name = wp_title(' | ', false);
		$description = self::get_description();
		$image = self::get_image();
		$address = self::get_address();
		$author = self::get_author();
		?>
		<!-- Facebook Tags -->
		<?php
		// If WordPress SEO is active
		if( ! defined( self::$wpseo_defined_name ) ) {
			?>	<meta property="og:site_name" content="<?php echo pls_get_option('pls-site-title'); ?>" />
		  	<meta property="og:title" content="<?php echo $name; ?>" />
		  	<meta property="og:url" content="<?php the_permalink(); ?>" />
			<?php
		} ?><meta property="og:image" content="<?php echo @$image; ?>">
		  <meta property="fb:admins" content="<?php echo pls_get_option('pls-facebook-admins'); ?>">
		  <!-- Meta Tags -->
		  <meta name="description" content="<?php echo strip_tags($description); ?>">
		  <meta name="author" content="<?php echo @$author; ?>">
		  <!-- Schema.org Tags -->
		  <meta itemprop="name" content="<?php echo $name; ?>">
		  <meta itemprop="email" content="<?php echo @pls_get_option('pls-company-email') ?>">
		  <meta itemprop="address" content="<?php echo @$address; ?>">
		  <meta itemprop="description" content="<?php echo strip_tags($description); ?>">
		  <meta itemprop="url" content="<?php the_permalink(); ?>">
		<?php 

		$meta_tags = ob_get_clean();
		
		if( $echo ) {
			echo $meta_tags;
		} else {
			return $meta_tags;
		}
	}
	
	/**
	 * Print default charset, X-UA-Compatible, viewport
	 * @param bool $echo echo if true or return if false
	 */
	public function display_generic_meta_tags( $echo = TRUE ) {
		ob_start();
		?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		
		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		
		<!-- Mobile viewport optimized: j.mp/bplateviewport -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<?php 
		
		$meta_tags = ob_get_clean();
		
		if( $echo ) {
			echo $meta_tags;
		} else {
			return $meta_tags;
		}
	}
}