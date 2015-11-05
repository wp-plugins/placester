<?php 
/**
 * Shuffle Bricks - A set of bricks of multiple post types that can shuffle for display.
 * 
 */

class PLS_Shuffle_Bricks {

    // Standard brick types included by default
    private static $standard_types = array('posts', 'listings');

    // Standard CPTs included by default
    private static $standard_CPTs = array(
        'ads' => array(
            'post_type' => 'ad',
            'meta_key' => 'ad_featured'
        ),
        'agents' => array(
            'post_type' => 'agent',
            'meta_key' => 'agent_featured'
        ),
        'openhouses' => array(
            'post_type' => 'openhouse',
            'meta_key' => 'openhouse_featured'
        ),
        'testimonials' => array(
            'post_type' => 'testimonial',
            'meta_key' => 'testimonial_featured'
        )
    );

    public static function shuffle_bricks ($args = array()) {
      
        // Check for existence of plugin and not go forward if it is not here
        if ( pls_has_plugin_error() ) {
            return array();
        }

        // process default args
        $args = self::process_defaults($args);

        //cache the whole html snippet if we can.
        $cache = new PLS_Cache('brick');
        if ($result = $cache->get($args)) {
            // return $result;
        }

        extract($args, EXTR_SKIP);

        // Merge standard types with the keys of the standard CPTs to get a consolidated list of standard brick types...
        $CPT_types = array_keys(self::$standard_CPTs);
        $types = array_merge(self::$standard_types, $CPT_types);
        
        // Remove excluded types from standard types to get a list of permissible brick types...
        $brick_types = array_diff($types, $excluded_types);

        // Construct bricks
        $bricks = array();

        foreach ($brick_types as $supported_type) {
            $bricks_to_add = array();

            switch ($supported_type) {
                case 'posts':
                    $bricks_to_add = self::get_brick_posts($post_options, $post_limit);
                    break;
                case 'listings':
                    $bricks_to_add = self::get_brick_listings($listing_params, $featured_option_id);
                    break;
                default:
                    // Check to see if type is a supported CPT...
                    if (array_key_exists($supported_type, self::$standard_CPTs)) {
                        // Fetch CPT's bricks via passing in its config...
                        $config = self::$standard_CPTs[$supported_type];
                        $bricks_to_add = self::get_bricks_CPT($config);
                    }
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

    // Retrieve normal posts
    public static function get_brick_posts ($post_options, $post_limit) {
        // Try to retrieve all post IDs marked as 'sticky'...
    	$sticky_array = get_option('sticky_posts');
    	
    	// If non-sticky posts are explicity disabled and no stickies posts exist, return an empty
        // array to prevent 'get_posts' from returning a random selection of non-sticky posts...
    	if ( empty($sticky_array) && $post_options['disable_non_sticky'] ) {
    		return array();
    	}
    	
        $get_post_args = array(
          'post__in' => get_option('sticky_posts'),
          'showposts' => $post_limit
          // TODO: add categories...
        );
        $posts = get_posts( $get_post_args, ARRAY_A );
        
        return $posts;
    }

    // Retrieve featured listings
    public static function get_brick_listings ($listing_params, $featured_option_id) {
        // Try to set to featured listings if param is set...
        $brick_listings = $featured_option_id ? PLS_Listing_Helper::get_featured($featured_option_id) : array();

        // If featured listings param is false OR there are no featured listings, chose some randomly...
        if (empty($brick_listings['listings'])) {
            $brick_listings = PLS_Plugin_API::get_listings($listing_params);
        }

        return $brick_listings['listings'];
    }

    // Retrieve featured posts of the passed CPT
    public static function get_bricks_CPT ($config) {
        if (post_type_exists($config['post_type'])) {
            $custom_type_args = array( 
                'post_type' => $config['post_type'],
                'meta_key' => $config['meta_key'],
                'meta_value' => 'on',
                'meta_compare' => '=='
            );
            $featured_CPT_posts = get_posts( $custom_type_args, ARRAY_A );
          
            return $featured_CPT_posts;
        }
    }

    // Process passed args against defaults...
    private static function process_defaults ($args) {
    	$sticky_posts = get_option('sticky_posts');

        $defaults = array(
        /** Define the default argument array. */
            'excluded_types' => array(),
            'featured_option_id' => false,
            'listing_params' => 'limit=5&sort_by=price',
            'post_options' => array(
                'disable_non_sticky' => false
                // 'categories' => false
            ),
            'post_limit' => 10
        );

        return wp_parse_args( $args, $defaults );
    }
}