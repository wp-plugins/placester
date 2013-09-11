<?php
// Add Custom Post Type - Ads
add_action('init', 'ad_register');

function ad_register() {
	$labels = array(
			'name' => _x('Ads', 'post type general name'),
			'singular_name' => _x('Ads', 'post type singular name'),
			'add_new' => _x('Add New', 'Ad'),
			'add_new_item' => __('Add New Ad'),
			'edit_item' => __('Edit Ad'),
			'new_item' => __('New Ad'),
			'view_item' => __('View Ad'),
			'search_items' => __('Search Ads'),
			'not_found' =>  __('Nothing found'),
			'not_found_in_trash' => __('Nothing found in Trash'),
			'parent_item_colon' => ''
	);

	$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => true,
			'has_archive' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','thumbnail')
	);

	register_post_type( 'ad' , $args );
}

