<?php

/**
 * Plugin initialization code, main dispatcher
 */

/**
 * Initialization of custom post type
 */
function placester_init() {
    // Create new custom post type
    register_post_type( 'property',
        array(
            'taxonomies'      => array(),
            'show_ui'         => false,
            'public'          => true,
            'capability_type' => 'post',
            'rewrite'         => array( 'slug' => placester_post_slug() ),
            'hierarchical'    => false
        ) );

    // post type for storing craiglist templates
    register_post_type( 'placester_template',
        array(
            'taxonomies'      => array(),
            'show_ui'         => false,
            'public'          => true,
            'capability_type' => 'post',
            'hierarchical'    => false
        ) );
    $base_url = WP_PLUGIN_URL . '/placester';
    wp_enqueue_script('jquery');
    wp_enqueue_script('fancybox', $base_url . '/js/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'));
    wp_enqueue_script('fancybox_easing', $base_url . '/js/fancybox/jquery.easing-1.3.pack.js', array('jquery', 'fancybox'));
    wp_enqueue_script('placester_fancybox', $base_url . '/js/placester.fancybox.js', array('jquery', 'fancybox', 'fancybox_easing'));
    wp_enqueue_style('fancybox_style', $base_url . '/js/fancybox/jquery.fancybox-1.3.4.css');
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

add_action( 'init', 'placester_init' );

/**
 * Called on plugin activation.
 */
function placester_activate()
{
    placester_init();

    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}



/**
 * When placester post object is requested - it's checked that
 * data are loaded from external storage
 *
 * @param object $query
 */
function placester_pre_get_posts( $query ) {
   $vars = $query->query_vars;

   if ( isset( $vars['post_type'] ) && $vars['post_type'] == 'property' ) {
       // Ensure we have actual post data
       $id = $vars['property'];
       $id = preg_replace( '/[^0-9a-z]/', '', $id);

       placester_get_post_id( $id );
   }
}




/**
 * Allows theme builders to create pages on theme activation
 */
function placester_create_pages( $page_list )
{
    // Retrieve the action, if it exists (overwrite or restore)
    $action = '';
    if (isset($_POST['action'])) 
        $action = $_POST['action'];

    $trashed = array();
    // Foreach of page defined by the theme 
    foreach ($page_list as $page_info) {
        $page = get_page_by_title($page_info['title']);

        // If the page exists and no action exists, 
        // if trashed save in an array, and update the template
        if ( isset($page->ID) && ( $action == '' ) ) {
            if ( $page->post_status == "trash" ) {
                $trashed[] = $page_info;
            }
            delete_post_meta( $page->ID, '_wp_page_template' );
            add_post_meta( $page->ID, '_wp_page_template', $page_info['template'] );        
        } else { // If the page does not exist or an action was requested
            $page = get_page_by_title($page_info['title']);
            // Do what needs to be done for each action
            switch ($action) {
            case 'overwrite':
                if ( $page->post_status == "trash" ) {
                    wp_delete_post($page->ID, true); 
                    unset($page);
                }
                break;
            case 'restore':
                if ( $page->post_status == "trash" ) {
                    wp_untrash_post( $page->ID );
                    wp_publish_post( $page->ID );
                }
                break;
            }
            // Add the page if appropiate
            if ( !isset($page->ID) )
                placester_insert_page($page_info);
        }
    } // #end foreach page

    // If pages couldn't be created because they were trashed,
    // print the admin messages.
    $trashed_count = count($trashed);
    if ( $trashed_count ) {
        $msg = 'To take full advantage of its features, this theme must have some pages created. ';
        $msg .= ($trashed_count > 1) ? '. The following needed pages exist, but have been trashed: ' : 'The following needed page exists, but has been trashed: ';
        foreach ($trashed as $index => $trashed_page) {
            $msg .= "\"" . $trashed_page['title'] . "\"";
            $msg .= ( $index+1 != count($trashed) ) ? ', ' : '.';
        }
        $msg .= ' <form action="' . admin_url() . 'themes.php" method="post" style="display:inline;"><input type="hidden" name="action" value="overwrite"><input type="submit" value="Overwrite trashed pages" /></form>';
        $msg .= ' <form action="' . admin_url() . 'themes.php" method="post" style="display:inline;"><input type="hidden" name="action" value="restore"><input type="submit" value="Restore trashed pages" /></form>';
        $css = 'form > input { cursor: pointer }';
        placester_admin_msg( $msg, 'error', true, $css );
    } 
}

/* 
 * Function that inserts a page
 * @param array $args Array of ( title, template ) arrays
 */
function placester_insert_page( $args ) {
    $args = wp_parse_args( $args );
    extract( $args );

    $page = array(
        'post_type' => 'page',
        'post_title' => $title,
        'post_status' => 'publish',
        'post_author' => 1);

    $page_id = wp_insert_post( $page );
    delete_post_meta( $page_id, '_wp_page_template' );
    add_post_meta( $page_id, '_wp_page_template', $template );
}

/**
 * When placester post object is requested - it's checked that
 * data are loaded from external storage
 *
 * @param object $query
 */
function placester_get_posts( $query ) {
    if(!is_admin() && !$query->is_page) {
      //var_dump($query);
   $vars = $query->query_vars;
    if(empty($_REQUEST) ) {
       if ( isset( $vars['post_type'] ) && $vars['post_type'] == 'property' ) {
           // Ensure we have actual post data
           $id = isset( $vars['property'] ) ?  preg_replace( '/[^0-9a-z]/', '', $vars['property']) : NULL ;
           //$id = preg_replace( '/[^0-9a-z]/', '', $id);
           try {
           placester_get_post_id( $id );
           }
            catch (PlaceSterNoApiKeyException $e) 
            {}
       } elseif ($query->is_home && !isset( $vars['post_type'] ) ) {
           try {
           $listings = placester_property_list(NULL);
            if($listings && $listings->count > 0) {
                foreach ($listings->properties as $property) {
                    $post_id[] =  placester_get_post_id($property->id);
                    $query->set('post__in', $post_id );
                    $query->set('post_type', 'property' );
                    $query->set('meta_query', '' );
                }
            }
            }
            catch (PlaceSterNoApiKeyException $e) 
            {}
       } elseif ( $query->is_home && isset($vars['post_type']) && $vars['post_type'] == 'property' ) {
           $query->set('post_type', 'property' );
           $query->set('meta_query', '' );
       } else {}
   } else {
        try {
            $request_filter = placester_filter_parameters_from_http();
           placester_cut_empty_fields($request_filter);
       $listings = placester_property_list($request_filter);
       if($listings && $listings->count > 0) {
           foreach ($listings->properties as $property) {
                    $post_id[] = placester_get_post_id($property->id);
                }
                    $query->set('post__in', $post_id );
                    $query->set('post_type', 'property' );
                    $query->set('meta_query', '');
                     
        } elseif($listings->count = 0) {
            $query->set('post_type', 'property' );
            $query->set('meta_query', '');
                }
        } catch (PlaceSterNoApiKeyException $e)
        {}
      }
}
}

/**
 *  Check if the current theme is a Placester theme from backbone/functions/admin.php
 *  If it's not, add needed functionality
 */
function check_placester_theme() {

    add_action( 'pre_get_posts', 'placester_get_posts', 0 );

}
add_action('init', 'check_placester_theme');

/**
 *  Utility function that outputs an admin notice 
 */
function placester_admin_msg( $msg = '', $class = "updated", $inline = false, $css = false ) {
    if ( $css ) {
        $css = '<style type="text/css">' . $css . '</style>';
    }
    if ( !empty( $msg ) ) {
        if ($inline)
            echo "<div class='$class fade' style=\"padding: 0.5em\">$css$msg</div>\n";
        else
            echo "<div class='$class fade'>$css$msg</div>\n";
    }
}
