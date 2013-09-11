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

    public static function init () {
        // TODO:  Why is this wrapped in this conditional?  Use priorities and do not echo...
        if ( !self::is_yoast_enabled() ) {
            add_filter('wp_head', array(__CLASS__, 'construct_meta_tags'));
        }

        add_filter('wp_title', array(__CLASS__, 'hook_title_tag'), 18, 1);
    }

    public static function is_yoast_enabled () {
        // Assume it's not enabled unless we can prove otherwise...
        $enabled = false;

        // Is Yoast WordPress SEO plugin is enabled this single site? (not network-wide)
        $active_plugins = get_option('active_plugins');
        if (in_array("wordpress-seo/wp-seo.php", $active_plugins)) {
            return true;
        }

        if (function_exists('wp_get_active_network_plugins')) {
            // Is Yoast WordPress SEO plugin is enabled Network-wide?
            $plugins = wp_get_active_network_plugins();
            foreach ($plugins as $key => $plugin) {
                // cut the last 24 char off each plugin
                $plugin_name = substr($plugin, -24);
                // check if it's Yoast plugin
                if ($plugin_name == 'wordpress-seo/wp-seo.php') {
                    $enabled = true;
                }
            }
        }

        return $enabled;
    }

    public static function construct_meta_tags () {
        // take meta tag designations, and apply them to the HTML elements
        $tags = self::determine_appropriate_tags();

        ob_start();
        ?>
            <!-- OpenGraph Tags -->
            <meta property="og:site_name" content="<?php echo pls_get_option('pls-site-title'); ?>" />
            <meta property="og:title" content="<?php echo $tags['title']; ?>" />
            <meta property="og:url" content="<?php echo $tags['url']; ?>" />
            <meta property="og:image" content="<?php echo $tags['image']; ?>">
            <!-- <meta property="fb:admins" content="<?php //echo $tags['author']; ?>"> -->

            <!-- Meta Tags -->
            <meta name="description" content="<?php echo $tags['description']; ?>">
            <meta name="author" content="<?php echo $tags['author']; ?>">
        <?php

            // Add Twitter Card meta data
        
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
                $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                $tags['title'] = $term->name;

                $tags['address'] = "";
                $descrip = strip_tags($term->description);
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
                
                $image_array = get_tax_meta($term->term_id,'image_1');
                $tags['image'] = isset($image_array['src']) ? $image_array['src'] : '';
                
                break;
          
            case 'search':
                $tags['itemtype'] = 'http://schema.org/LocalBusiness';
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
                    $tags['title'] = @$listing['location']['address'] . ', ' . $listing['location']['unit'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
                    $tags['address'] = @$listing['location']['address'] . ', ' . $listing['location']['unit'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
                } 
                else {
                    $tags['title'] = @$listing['location']['address'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
                    $tags['address'] = @$listing['location']['address'] . ' ' . @$listing['location']['locality'] . ', ' . @$listing['location']['region'];
                }

                $tags['image'] = @$listing['images']['0']['url'];
                $tags['description'] = esc_html(strip_tags($listing['cur_data']['desc']));

                break;
          
            case 'agent':
                $tags['itemtype'] = 'http://schema.org/RealEstateAgent';
                $tags['title'] = $post->post_title;
                break;
          
            case 'service':
                $tags['itemtype'] = 'http://schema.org/ProfessionalService';
                $tags['title'] = $post->post_title;
                break;

            case 'testimonial':
                $tags['itemtype'] = 'http://schema.org/Review';
                $tags['title'] = $post->post_title;
                break;
          
            case 'community':
                $tags['title'] = $post->post_title;
                $tags['description'] = PLS_Format::shorten_excerpt($post, 155);
                break;
          
            case 'single':
                $tags['itemtype'] = 'http://schema.org/BlogPosting';
                $tags['title'] = $post->post_title;

                if (has_post_thumbnail($post->ID)) {
                    $post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
                    $tags['image'] = $post_image[0];
                }

                $tags['description'] = PLS_Format::shorten_excerpt($post, 155);
                $tags['address'] = @pls_get_option('pls-company-street') . " " . @pls_get_option('pls-company-locality') . ", " . @pls_get_option('pls-company-region');
                $tags['author'] = $post->post_author;

                break;
          
            case 'other':
            default:
                // Home and other pages
                $tags['itemtype'] = 'http://schema.org/LocalBusiness';

                if (is_home()) {
                    $tags['title'] = pls_get_option('pls-company-name');
                } 
                elseif (isset($post)) {
                    $values = get_post_custom( $post->ID );
                    // give Yoast SEO a hand setting the title
                    $tags['title'] = ( !empty($values['_yoast_wpseo_title'][0]) ? $values['_yoast_wpseo_title'][0] : $post->post_title );
                }
                else {
                    $tags['title'] = '';
                }

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
        else {
            $page_template = 'other'; // Home, etc.
        }

        return $page_template;
    }
  
    private static function process_defaults ($args) {
        // Process passed args against defaults...
        if (is_home()) {
            $title = pls_get_option('pls-company-name');
            $url = esc_html(home_url());
        } 
        else {
            $title = isset($post->post_title) ? $post->post_title : pls_get_option('pls-company-name');
            $url = get_permalink();
        }

        $defaults = array(
            'itemtype' => 'http://schema.org/LocalBusiness',
            'title' => $title,
            'image' => pls_get_option('pls-site-logo'),
            'description' => pls_get_option('pls-company-description'),
            'address' => pls_get_option('pls-company-street') . " " . pls_get_option('pls-company-locality') . ", " . pls_get_option('pls-company-region'),
            'author' => pls_get_option('pls-user-name'),
            'url' => $url,
            'email' => pls_get_option('pls-user-email')
            // 'page_template' => 'other'
        );

        return wp_parse_args( $args, $defaults );
    }

    // TEMPORARY FUNCTIONALITY STATUS: we will not try to override Yoast in any way. Instead
    // we will not try to provide title tags nor opengraph information if Yoast is enabled.

    // public static function does_yoast_override_tag ($action, $tag) {
    //   // use after checking if is_yoast_enabled()
    //   // give function a tag, return boolean whether Yoast is overriding it

    //   // get all filters being used
    //   global $wp_filter;
    //   // find hook in the wp_filters
    //   $hook = $wp_filter[$action];

    //   // flatted multidimensional $hook array
    //   $flatten_hook_array = array();
    //   array_walk_recursive($hook, function($a) use (&$flatten_hook_array) { $flatten_hook_array[] = $a; });

    //   // if tag is in flattened 
    //   if (in_array( $tag, $flatten_hook_array )) {
    //     return true;
    //   }

    //   return false;
    // }
}