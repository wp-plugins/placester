<?php


class PL_Slideshow_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public $fields = array(
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
			'animation' => array( 'type' => 'select', 'label' => 'Animation', 'options' => array( 
									'fade' => 'fade',
									'horizontal-slide' => 'horizontal-slide',
									'vertical-slide' => 'vertical-slide',
									'horizontal-push' => 'horizontal-push',
								) ),
			'animationSpeed' => array( 'type' => 'text', 'label' => 'Animation Speed' ),
			'timer' => array( 'type' => 'checkbox', 'label' => 'Timer' ),
			'pauseOnHover' => array( 'type' => 'checkbox', 'label' => 'Pause on hover' ),
	);
		
	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Slideshows', 'pls' ),
						'singular_name' => __( 'slideshow', 'pls' ),
						'add_new_item' => __('Add New Slideshow', 'pls'),
						'edit_item' => __('Edit Slideshow', 'pls'),
						'new_item' => __('New Slideshow', 'pls'),
						'all_items' => __('All Slideshows', 'pls'),
						'view_item' => __('View Slideshows', 'pls'),
						'search_items' => __('Search Slideshows', 'pls'),
						'not_found' =>  __('No slideshows found', 'pls'),
						'not_found_in_trash' => __('No slideshows found in Trash', 'pls')),
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
	
		register_post_type('pl_slideshow', $args );
	}
	
	public function meta_box_save( $post_id ) {
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
		

		if( isset( $_POST['pl_template_listing_slideshow'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['listing_slideshow']);
		}

		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
	
	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;
		
		unset( $_GET['skipdb'] );
		$meta = $_GET;
		
		if( ! empty( $post ) && $post->post_type === 'pl_slideshow' ) {
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
			
			if( isset( $meta['pl_static_listings_option'] ) ) { unset( $meta['pl_static_listings_option'] ); }

			foreach( $meta as $key => $value ) {
				// if featured listings, pass to slideshow args	
				if( $key === 'pl_featured_listing_meta' && ! empty( $value ) ) {
					$args .= "post_meta_key = 'pl_featured_listing_meta' ";
				}
				// ignore underscored private meta keys from WP
				else if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					if( is_array( $value ) ) {
						// handle meta values as arrays
						$args .= "$key = '{$value[0]}' ";
					} else {
						// handle _GET vars as strings
						$args .= "$key = '{$value}' ";
					}
				}
			}
			
			$args .= "post_id = '{$post->ID}' ";
			
			if( isset( $meta['pl_cpt_template'] ) ) {
				$context = is_array( $meta['pl_cpt_template'] ) ? $meta['pl_cpt_template'][0] : $meta['pl_cpt_template'];
				$args .= "context = '$context' ";
			}
				
			$shortcode = '[listing_slideshow ' . $args . ']';
				
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
				
			die();
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
}

new PL_Slideshow_CPT();