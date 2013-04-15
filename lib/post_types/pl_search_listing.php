<?php

class PL_Search_Listing_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public  $fields = array(
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Search Listings', 'pls' ),
						'singular_name' => __( 'search_listing', 'pls' ),
						'add_new_item' => __('Add New Search Listing', 'pls'),
						'edit_item' => __('Edit Search Listing', 'pls'),
						'new_item' => __('New Search Listing', 'pls'),
						'all_items' => __('All Search Listings', 'pls'),
						'view_item' => __('View Search Listings', 'pls'),
						'search_items' => __('Search Search Listings', 'pls'),
						'not_found' =>  __('No search listings found', 'pls'),
						'not_found_in_trash' => __('No search listings found in Trash', 'pls')),
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
	
		register_post_type('pl_search_listing', $args );
	}
	
	public function meta_box_save( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) return;
	
		// if our current user can't edit this post, bail
		// if( !current_user_can( 'edit_post' ) ) return;
	
		foreach( $this->fields as $field => $values ) {
			if( !empty( $_POST ) ) {
				if( !empty( $_POST[$field] ) ) {
					update_post_meta( $post_id, $field, $_POST[$field] );
				}	
			}
		}
		
		$static_listings_option = array();
		
		// Save search form fields if not empty
		if( ! empty( $_POST['listing_types'] ) && 'false' !== $_POST['listing_types'] ) { $static_listings_option['listing_types'] = $_POST['listing_types']; }
		if( ! empty( $_POST['zoning_types'] ) &&  'false' !== $_POST['zoning_types'] ) { $static_listings_option['zoning_types'] = $_POST['zoning_types']; }
		if( ! empty( $_POST['purchase_types'] ) && 'false' !== $_POST['purchase_types'] ) { $static_listings_option['purchase_types'] = $_POST['purchase_types']; }
		
		if( isset( $_POST['location'] ) && is_array( $_POST['location'] ) ) {
			foreach( $_POST['location'] as $key => $value ) {
				if( ! empty( $value ) ) {
					$static_listings_option['location'][$key] = $value;
				}
			}
		}
		
		if( isset( $_POST['metadata'] ) && is_array( $_POST['metadata'] ) ) {
			foreach( $_POST['metadata'] as $key => $value ) {
				if( ! empty( $value ) ) {
					$static_listings_option['metadata'][$key] = $value;
				}
			}
		}
		
		update_post_meta( $post_id, 'pl_static_listings_option', $static_listings_option );
		
		// save templates
		if( isset( $_POST['pl_template_search_listings'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_template_search_listings']);
		}
		
		if( isset( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template']);
		}
	}
	
	public static function post_type_templating( $single, $skipdb = false ) {
		global $post;
		
		unset( $_GET['skipdb'] );
		$meta = $_GET;
		
		if( ! empty( $post ) && $post->post_type === 'pl_search_listings' ) {
			$args = '';
			// verify if skipdb param is passed
			if( ! $skipdb ) {
				$meta_custom = get_post_custom( $post->ID );
				$meta = array_merge( $meta_custom, $meta );
			}
		
			foreach( $meta as $key => $value ) {
				if( $key === 'pl_cpt_template' ) {
					$args .= "context='search_listings_{$value[0]}' ";
				}
			}

			$shortcode = '[search_listings ' . $args . ']';
			
			// prepare filters
			if( isset( $meta['pl_static_listings_option'] ) ) {
				$filters = unserialize( $meta['pl_static_listings_option'][0] ); 				

				if( is_array( $filters) ) {
					foreach( $filters as $top_key => $top_value ) {
						if( is_array( $top_value ) ) {
							foreach( $top_value as $key => $value ) {
								$shortcode .= ' [pl_filter group="' . $top_key. '" filter="' . $key . '" value="' . $value . '"] ';
							}
						} else {
							$shortcode .= ' [pl_filter filter="' . $top_key . '" value="'. $top_value . '"] ';
						}
					}
				}
			}
			
			$shortcode .= '[/search_listings]';
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
		
			die();
		}
	}
}

new PL_Search_Listing_CPT();