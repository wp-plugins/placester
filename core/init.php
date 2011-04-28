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

add_action( 'pre_get_posts', 'placester_pre_get_posts' );



/**
 * Allows theme builders to create pages on theme activation
 */
function placester_create_page( $post_title = FALSE, $template_name,  $option_name = '' )
{
    
    /**
     *      Check to see if the page already exists
     *      If it does, change its template, if not create it.
     */
    if ($post_title) {
        $post_id = get_page_by_title($post_title)->ID;
        if ( isset($post_id)) {
            delete_post_meta( $post_id, '_wp_page_template' );
            add_post_meta( $post_id, '_wp_page_template', $template_name );        
        } else {
            $post = array(
                'post_type' => 'page',
                'post_title' => $post_title,
                'post_status' => 'publish',
                'post_author' => 1);

            $post_id = wp_insert_post( $post );
            delete_post_meta( $post_id, '_wp_page_template' );
            add_post_meta( $post_id, '_wp_page_template', $template_name );
        }

        if ( strlen( $option_name ) > 0 ) {
            update_option( $option_name, $post_id );
        }
    }
}
