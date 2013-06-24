<?php

class PL_Map_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
			'type' => array( 'type' => 'select', 'label' => 'Map Type', 'options' => array( 
																	'listings' => 'listings',
																	 'lifestyle' => 'lifestyle',
																	'lifestyle_poligon' => 'lifestyle_poligon' ) ),
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Maps', 'pls' ),
						'singular_name' => __( 'pl_map', 'pls' ),
						'add_new_item' => __('Add New Map', 'pls'),
						'edit_item' => __('Edit Map', 'pls'),
						'new_item' => __('New Map', 'pls'),
						'all_items' => __('All Maps', 'pls'),
						'view_item' => __('View Maps', 'pls'),
						'search_items' => __('Search Maps', 'pls'),
						'not_found' =>  __('No maps found', 'pls'),
						'not_found_in_trash' => __('No maps found in Trash', 'pls')),
				'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title', 'editor'),
				'taxonomies' => array('category', 'post_tag')
		);
	
		register_post_type('pl_map', $args );
	}
	
	public  function meta_box_save( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) return;
	
		// if our current user can't edit this post, bail
		// if( !current_user_can( 'edit_post' ) ) return;
	
		foreach( $this->fields as $field => $values ) {
			if( isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, $_POST[$field] );
			}
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
	
	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;
		
		unset( $_GET['skipdb'] );
		$meta = $_GET;
		
		$allowed_atts = array(
				'width',
				'height',
				'pl_cpt_template'	
			);
		
		if( ! empty( $post ) && $post->post_type === 'pl_map' ) {
			$args = '';
			// verify if skipdb param is passed
			if( ! $skipdb ) {
				$meta_custom = get_post_custom( $post->ID );
				$meta = array_merge( $meta_custom, $meta );
			}
			
			foreach( $meta as $key => $value ) {
				if( in_array( $key, $allowed_atts ) ) {
					if( is_array($value) ) {
						$args .= "$key = '{$value[0]}' ";
					} else {
						$args .= "$key = '{$value}' ";
					}
				}
				// ignore underscored private meta keys from WP
				// if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					/* if( 'pl_static_listings_option' !== $key  && 'pl_featured_listing_meta' !== $key) {
						$args .= "$key = '{$value[0]}' ";
					}
					if( is_array( $value ) ) {
						// handle meta values as arrays
						$args .= "$key = '{$value[0]}' ";
					} else {
						// handle _GET vars as strings
						$args .= "$key = '{$value}' ";
					} */
				//}
			}
			$args .= ' map_id="' . $post->ID . '"';
			
			$shortcode = '[search_map ' . $args . ']'; 
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
					
			die();
		}
	}
}

new PL_Map_CPT();