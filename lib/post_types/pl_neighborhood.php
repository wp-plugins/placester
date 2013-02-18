<?php


class PL_Neighborhood_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public $fields = array(
			'width' => array( 'type' => 'text', 'label' => 'Width' ),
			'height' => array( 'type' => 'text', 'label' => 'Height' ),
	);

	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Neighborhoods', 'pls' ),
						'singular_name' => __( 'neighborhood', 'pls' ),
						'add_new_item' => __('Add New Neighborhood', 'pls'),
						'edit_item' => __('Edit Neighborhood', 'pls'),
						'new_item' => __('New Neighborhood', 'pls'),
						'all_items' => __('All Neighborhoods', 'pls'),
						'view_item' => __('View Neighborhoods', 'pls'),
						'search_items' => __('Search Neighborhoods', 'pls'),
						'not_found' =>  __('No neighborhoods found', 'pls'),
						'not_found_in_trash' => __('No neighborhoods found in Trash', 'pls')),
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
	
		register_post_type('pl_neighborhood', $args );
	}
	
	public function meta_box_save( $post_id ) {
	// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) return;
		
		if( $_POST['post_type'] != 'pl_neighborhood' ) {
			return;
		}
		// if our current user can't edit this post, bail
		// if( !current_user_can( 'edit_post' ) ) return;
		
		foreach( $this->fields as $field => $values ) {
			if( !empty( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, $_POST[$field] );
			}
		}
		
		// different input values, type or radio-type (conflict avoided)
		// $radio_type = isset( $_POST['type'] ) ? $_POST['type'] : '';
		$radio_type = empty( $radio_type ) && isset( $_POST['radio-type'] ) ? $_POST['radio-type'] : '';
		
		// persist radio box and dropdown
		if( ! empty( $radio_type ) ) {
			$select_type = 'nb-select-' . $radio_type;
			if( isset( $_POST[$select_type] ) ) {
				// persist radio box storage based on what is saved
				update_post_meta( $post_id, 'radio-type', $radio_type );
				update_post_meta( $post_id, $select_type, $_POST[ $select_type ] );
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
		
		$location_taxonomies = PL_Taxonomy_Helper::$location_taxonomies;
		$taxonomy_args = array_keys( $location_taxonomies );

		if( ! empty( $post ) && $post->post_type === 'pl_neighborhood' ) {
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
				// dashes break shortcode attributes, convert to underscores
				$key = str_replace('-', '_', $key);
				// ignore underscored private meta keys from WP
				if( strpos( $key, '_', 0 ) !== 0 && ! empty( $value[0] ) ) {
					if( 'pl_static_listings_option' === $key  || 'pl_featured_listing_meta' === $key) {
						continue;
					}
					if( $key === 'type' ) { // handle neighborhood items
						// interpret differently in backend and frontend
						$type_value = is_array( $value ) ? $value[0] : $value;
						if( in_array( $type_value, $taxonomy_args ) ) {
							$nb_type = $type_value;
							$nb_value_key = 'nb-select-' . $nb_type;
							$nb_value = isset( $meta[$nb_value_key] ) ? $meta[$nb_value_key][0] : ''; 
							// dashes break shortcode attributes, convert to underscores
							$nb_type = str_replace( '-', '_', $nb_type );
							$nb_value = str_replace( '-', '_', $nb_value );
							$args .= "$nb_type = '{$nb_value}' ";
							
							// $args .= "$nb_value_key = '{$nb_value}' ";
						}
					} else if( ! in_array( $key, array('pl_cpt_template', 'type') ) ) {
						if( is_array( $value ) ) {
							// handle meta values as arrays
							$args .= "$key='{$value[0]}' ";
						} else {
							// handle _GET vars as strings
							$args .= "$key='{$value}' ";
						}
					}
					
				}
			}
			
			if( ! empty( $meta['pl_cpt_template'] ) ) {
				$args .= "context = '{$meta['pl_cpt_template'][0]}' ";
			}
			
			// Workaround for autosave with incorrect post type
			// update_post_meta( $post->ID, 'pl_post_type', 'pl_neighborhood' );
		
			$shortcode = '[pl_neighborhood ' . $args . ']';

			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
		
			die();
		}
	}
}

new PL_Neighborhood_CPT();