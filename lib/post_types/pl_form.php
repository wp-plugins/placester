<?php

class PL_Form_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
				'width' => array( 'type' => 'text', 'label' => 'Width' ),
				'height' => array( 'type' => 'text', 'label' => 'Height' ),
				'context' => array( 'type' => 'text', 'label' => 'Context' ),
				'ajax' => array( 'type' => 'checkbox', 'label' => 'Disable AJAX' ),
				'formaction' => array( 'type' => 'text', 'label' => 'Form URL when AJAX is disabled' ),
				'modernizr' => array( 'type' => 'checkbox', 'label' => 'Drop Modernizr' ),
			);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Forms', 'pls' ),
						'singular_name' => __( 'pl_form', 'pls' ),
						'add_new_item' => __('Add New Form', 'pls'),
						'edit_item' => __('Edit Form', 'pls'),
						'new_item' => __('New Form', 'pls'),
						'all_items' => __('All Forms', 'pls'),
						'view_item' => __('View Forms', 'pls'),
						'search_items' => __('Search Forms', 'pls'),
						'not_found' =>  __('No forms found', 'pls'),
						'not_found_in_trash' => __('No forms found in Trash', 'pls')),
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
	
		register_post_type('pl_form', $args );
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
			} else if( $values['type'] === 'checkbox' && ! isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, false );
			}
		}
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
	}

	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;
		
		unset( $_GET['skipdb'] );
		$meta = $_GET;
		
		$ignore_keys = array( 'context', 'pl_static_listings_option', 'pl_featured_listing_meta' );
		
		if( ! empty( $post ) && $post->post_type === 'pl_form' ) {
			$args = '';
			
			// verify if skipdb param is passed
			if( ! $skipdb ) {
				$meta_custom = get_post_custom( $post->ID );
				$meta = array_merge( $meta_custom, $meta );
			}
			
			// unset before/after for shortcode, might get messy with markup and
			// doesn't make sense for standalone shortcode
			if( isset( $meta['pl_template_before_block'] ) ) unset( $meta['pl_template_before_block'] );
			if( isset( $meta['pl_template_after_block'] ) ) unset( $meta['pl_template_after_block'] );
			
			foreach( $meta as $key => $value ) {
				// ignore underscored private meta keys from WP
				if( $key === 'pl_cpt_template' ) {
					$args .= "context='{$value[0]}' ";
				}
				else if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) && ( ! in_array( $key, $ignore_keys ) ) ) {
					if( is_array( $value ) ) {
						// handle meta values as arrays
						$args .= "$key = '{$value[0]}' ";
					} else {
						// handle _GET vars as strings
						$args .= "$key = '{$value}' ";
					}
				}
				if( $key === 'modernizr' && $value[0] == 'true' ) {
					$drop_modernizr = true;
				}
			}
			
			// $shortcode = '[search_form ' . $args . '] [search_listings]';
			$shortcode = '[search_form ' . $args . ']';
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
				
			die();
		}
	}
}

new PL_Form_CPT();