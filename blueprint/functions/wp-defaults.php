<?php 
/**
 * The WP Defaults functions file. This file is for modifying default WordPress
 * functionality.
 *
 * @package PlacesterBlueprint
 * @subpackage Functions
 */

/**
* Removing comments menu item, in posts/pages, and from admin bar
*
*/

// Removes Comments from admin menu
if ( current_theme_supports('pls-remove-comments-from-admin') ) {
    add_action( 'admin_menu', 'remove_admin_comments_menu' );
}

// Removes Comments from post and pages
if ( current_theme_supports('pls-remove-comments-from-posts') ) {
    add_action('init', 'remove_comment_support', 100);
}

// Removes Comments from admin bar
if ( current_theme_supports('pls-remove-comments-from-admin-bar') ) {
    add_action( 'wp_before_admin_bar_render', 'admin_bar_remove_comments' );
}

function remove_admin_comments_menu() {
    remove_menu_page( 'edit-comments.php' );
}

function remove_comment_support() {
    remove_post_type_support( 'post', 'comments' );
    remove_post_type_support( 'page', 'comments' );
}

function admin_bar_remove_comments() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
}

add_filter( 'wp_default_editor', 'set_visual_editor_default' );
function set_visual_editor_default() {
	return 'tinymce';
}