<?php

/**
 * Plugin initialization code, main dispatcher
 */

/**
 * Initialization of custom post type
 */
add_action('init', 'placester_init');

function placester_init() 
{
    // Create new custom post type
    register_post_type('property',
        array
        (
            'taxonomies'      => array(),
            'show_ui'         => false,
            'public'          => true,
            'capability_type' => 'post',
            'rewrite'         => array('slug' => placester_post_slug()),
            'hierarchical'    => false
        ));
}



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
add_action('pre_get_posts', 'placester_pre_get_posts');

function placester_pre_get_posts($query)
{
   $vars = $query->query_vars;
   if (isset($vars['post_type']) && $vars['post_type'] == 'property')
   {
       // Ensure we have actual post data
       $id = $vars['property'];
       $id = preg_replace("/[^0-9a-z]/", "", $id);

       global $wpdb;

       $sql =
           "SELECT ID, post_modified " .
           "FROM " . $wpdb->prefix . "posts " .
           "WHERE post_type = 'property' AND post_name = '$id' " .
           "LIMIT 0, 1";

       $row = $wpdb->get_row($sql);
       $post_id = 0;
       if ($row)
       {
           $post_id = $row->ID;
           $modified_timestamp = strtotime($row->post_modified);
           if ($modified_timestamp > time() - 3600 * 48)
               return;
       }

       try
       {
           $data = placester_property_get($id);
           $post = array(
                'post_type'   => 'property',
                'post_title'  => $id,
                'post_name'   => $id,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_content'=> json_encode($data),
                'filter'      => 'db'
             );

           if ($post_id <= 0)
               $post_id = wp_insert_post($post);
           else
           {
               $post['ID'] = $post_id;
               $post_id = wp_update_post($post);
           }
       }
       catch (Exception $e)
       {}
   }
}


/**
 * Allows theme builders to create pages on theme activation
 */
function set_page($post_title, $template_name, $option_name = '')
{
    $posts = get_posts(
        array
        (
            'post_type' => 'page',
            'meta_key' => '_wp_page_template',
            'meta_value' => $template_name,
            'post_status' => 'publish'
        ));

    if (count($posts) > 0)
        $post_id = $posts[0]->ID;
    else
    {
        $post = array(
             'post_type' => 'page',
             'post_title' => $post_title,
             'post_status' => 'publish',
             'post_author' => 1
          );

        $post_id = wp_insert_post($post);
        delete_post_meta($post_id, '_wp_page_template');
        add_post_meta($post_id, '_wp_page_template', $template_name);
    }

    if (strlen($option_name) > 0)
        update_option($option_name, $post_id);
}
