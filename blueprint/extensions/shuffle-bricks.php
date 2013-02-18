<?php 
/**
 *  Wrapper function for the shuffle bricks function.
 */
function pls_shuffle_bricks( $args = '', $data = false ) {
    return PLS_Shuffle_Bricks::shuffle_bricks( $args, $data );
}

/**
 * Shuffle Bricks - A set of bricks of multiple post types that can shuffle for display.
 * 
 */

class PLS_Shuffle_Bricks {

    static function shuffle_bricks( $args = '' ) {
      
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

        // Start Bricks array
        $bricks = array();

        // Add Organize Posts to Bricks array
        $posts = self::get_brick_posts($post_options, $post_limit);
        $bricks = array_merge($bricks, $posts);

        // Add Featured Listings to Bricks array
        $listings = self::get_brick_listings($listing_params, 'home-featured-listings');
        $bricks = array_merge($bricks, $listings['listings']);

        // Add Sticky Testimonials to Bricks array
        $testimonials = self::get_brick_testimonials($post_limit);
        $bricks = array_merge($bricks, $testimonials);

        // Shuffle'em
        shuffle($bricks);

        return $bricks;
    }


    // retrieve Posts
    function get_brick_posts ($post_options, $post_limit) {

        // Sticky posts
        $get_post_args = array(
          'post__in' => get_option('sticky_posts'),
          'showposts' => $post_limit,
          // add categories
        );
        $posts = get_posts( $get_post_args, ARRAY_A );

        return $posts;
    }

    // retrieve Featured Listings
    function get_brick_listings ($listing_params, $featured_option_id) {
        if ($featured_option_id) {
           $brick_listings = PLS_Listing_Helper::get_featured($featured_option_id);
         }
         if (empty($brick_listings['listings'])) {
           $brick_listings = PLS_Plugin_API::get_property_list($listing_params);
         }
         
         return $brick_listings;
    }

    // retrieve Testimonials
    function get_brick_testimonials() {
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
    function process_defaults ( $args ) {
        $defaults = array(
          /** Define the default argument array. */
              'post_options' => array(
                'sticky_posts' => true,
                // 'categories' => false,
              ),
              'featured_option_id' => false,
              'testimonials' => false,
              'agents' => false,
              'openhouses' => false,
              'ads' => false,
              'listing_params' => 'limit=5&sort_by=price',
              'post_limit' => 10,
          );

          return wp_parse_args( $args, $defaults );
    }

}