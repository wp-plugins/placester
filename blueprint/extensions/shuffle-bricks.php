<?php 
/**
 * Shuffle Bricks - A set of bricks of multiple post types that can shuffle for display.
 * 
 */

class PLS_Shuffle_Bricks {

    // Standard brick types included by default
    private static $standard_types = array('posts', 'listings', 'testimonials');

    public static function shuffle_bricks ($args = '') {
      
        // Check for existence of plugin and not go forward if it is not here
        if ( pls_has_plugin_error() ) {
            return '';
        }

        // process default args
        $args = self::process_defaults($args);

        //cache the whole html snippet if we can.
        $cache = new PLS_Cache('brick');
        if ($result = $cache->get($args)) {
            return $result;
        }

        // get args
        extract( $args, EXTR_SKIP );

        // Remove excluded types from standard type to get a list of permissible brick types...
        $brick_types = array_diff(self::$standard_types, $excluded_types);

        // Start Bricks array
        $bricks = array();

        foreach ($brick_types as $supported_type) {
            $bricks_to_add = array();

            switch ($supported_type) {
                case 'posts':
                    $bricks_to_add = self::get_brick_posts($post_options, $post_limit);
                    break;
                case 'listings':
                    $bricks_to_add = self::get_brick_listings($listing_params, 'home-featured-listings');
                    break;
                case 'testimonials':
                    $bricks_to_add = self::get_brick_testimonials($post_limit);
                    break;
                default:
                    // Given type is not supported...
            }

            // Add bricks if necessary...
            if (!empty($bricks_to_add)) { 
                $bricks = array_merge($bricks, $bricks_to_add);
            }
        }
        
        // Shuffle'em
        shuffle($bricks);

        return $bricks;
    }


    // retrieve Posts
    public static function get_brick_posts ($post_options, $post_limit) {
        // NOTE: Need to actually use $post_options -- they are passed in, but ignored for defaults...
        $get_post_args = array(
          'post__in' => get_option('sticky_posts'),
          'showposts' => $post_limit,
          // add categories
        );
        $posts = get_posts( $get_post_args, ARRAY_A );

        return $posts;
    }

    // retrieve Featured Listings
    public static function get_brick_listings ($listing_params, $featured_option_id) {
        if ($featured_option_id) {
            $brick_listings = PLS_Listing_Helper::get_featured($featured_option_id);
        }
        if (empty($brick_listings['listings'])) {
            $brick_listings = PLS_Plugin_API::get_property_list($listing_params);
        }

        return $brick_listings['listings'];
    }

    // retrieve Testimonials
    public static function get_brick_testimonials() {
        if (post_type_exists ('testimonial')) {
            $testimonial_args = array( 
                'post_type' => 'testimonial',
                'meta_key' => 'testimonial_featured',
                'meta_value' => 'on',
                'meta_compare' => '=='
            );
            $testimonials = get_posts( $testimonial_args, ARRAY_A );
          
            return $testimonials;
        }
    }

    // Process Args
    private static function process_defaults ( $args ) {
    	$sticky_posts = get_option( 'sticky_posts' );

        $defaults = array(
        /** Define the default argument array. */
            'excluded_types' => array(),
            'featured_option_id' => false,
            'listing_params' => 'limit=5&sort_by=price',
            'post_options' => array(
                'post__in' => $sticky_posts,
                // 'categories' => false
            ),
            'post_limit' => 10
        );

        return wp_parse_args( $args, $defaults );
    }
}