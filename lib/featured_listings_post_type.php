<?php

add_action( 'init', 'pl_register_featured_listing_post_type' );


function pl_register_featured_listing_post_type() {
	$args = array(
				'labels' => array(
					'name' => __( 'Featured Listings', 'pls' ),
					'singular_name' => __( 'featured_listing', 'pls' ),
					'add_new_item' => __('Add New Featured Listing', 'pls'),
					'edit_item' => __('Edit Featured Listing', 'pls'),
					'new_item' => __('New Featured Listing', 'pls'),
					'all_items' => __('All Featured Listings', 'pls'),
					'view_item' => __('View Featured Listings', 'pls'),
					'search_items' => __('Search Featured Listings', 'pls'),
					'not_found' =>  __('No featured listings found', 'pls'),
					'not_found_in_trash' => __('No featured listings found in Trash', 'pls')),
					'menu_icon' => trailingslashit(PL_IMG_URL) . 'featured.png', 
					'public' => true,
					'publicly_queryable' => false,
					'show_ui' => false,
					'query_var' => true,
					'capability_type' => 'post',
					'hierarchical' => false,
					'menu_position' => null,
					'supports' => array('title'),
					'taxonomies' => array('category', 'post_tag')
			);
	
	register_post_type('featured_listing', $args );
}

add_action( 'add_meta_boxes', 'pl_featured_listings_meta_box' );

function pl_featured_listings_meta_box() {
	add_meta_box( 'my-meta-box-id', 'Page Subtitle', 'pl_featured_listings_meta_box_cb', 'featured_listing', 'normal', 'high' );
}

// add meta box for featured listings- adding custom fields
function pl_featured_listings_meta_box_cb( $post ) {
	$values = get_post_custom( $post->ID );

	// get meta values from custom fields
	$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? unserialize($values['pl_featured_listing_meta'][0]) : '';
	$pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? '' : $pl_featured_listing_meta['featured-listings-type'];

	$pl_static_listings_option = isset( $values['pl_static_listings_option'] ) ? unserialize($values['pl_static_listings_option'][0]) : '';
	if( is_array( $pl_static_listings_option ) ) {
		foreach( $pl_static_listings_option as $key => $value ) {
			$_POST[$key] = $value;
		}
	}
	
	$pl_listing_type = isset( $values['pl_listing_type'] ) ? $values['pl_listing_type'][0] : 'featured';
	$single_listing = isset( $values['pl_fl_meta_box_single_listing'] ) ? esc_attr( $values['pl_fl_meta_box_single_listing'][0] ) : '';
	wp_nonce_field( 'pl_fl_meta_box_nonce', 'meta_box_nonce' );
	
	$shortcode_pattern = '';
	if( isset( $_GET['post'] ) ) {
		$featured_post_id = $_GET['post'];
		if( $pl_listing_type == 'featured' ) {
			$shortcode_pattern = "[featured_listings id='{$featured_post_id}']";
		}
		else if( $pl_listing_type == 'static' ) {
			$shortcode_pattern = "[static_listings id='{$featured_post_id}']";
		} 
	}
	
	?>
	
	<?php if( isset( $shortcode_pattern ) ): ?>
		<div id="featured_shortcode">
			<h2>Listing Shortcode</h2>
			<p>Use this shortcode inside of a page: <strong><?php echo $shortcode_pattern; ?></strong></p>
			<em>By copying this code and pasting it into a page you display your custom list of listings.</em>
		</div>
	<?php endif; ?>
	
	<script type="text/javascript">
		jQuery(document).ready(function($) {

			// If static listings have been stored
			<?php if($pl_listing_type == 'static'): ?>
				$('#pl_featured_radio').attr('checked', '');
				$('#pl_static_radio').attr('checked', 'checked');
				$('#pl_featured_listing_block').css('display', 'none');
				$('#pl_static_listing_block').css('display', 'block');
			<?php endif; ?>

			// control radio box toggles
			$('#pl_featured_radio').change(function() {
				if($(this).attr('checked', 'checked')) {
					$('#pl_featured_listing_block').css('display', 'block');
					$('#pl_static_listing_block').css('display', 'none');
				}
			});

			$('#pl_static_radio').change(function() {
				if($(this).attr('checked', 'checked')) {
					$('#pl_featured_listing_block').css('display', 'none');
					$('#pl_static_listing_block').css('display', 'block');
				}
			});

			// Hide advanced and add 'Advanced' switch
			$('#pl_static_listing_block #advanced').css('display', 'none');
			$('#pl_static_listing_block #amenities').css('display', 'none');
			$('#pl_static_listing_block #custom').css('display', 'none');
			$('<a href="#basic" id="pl_show_advanced" style="line-height: 50px;">Show Advanced filters</a>').insertBefore('#pl_static_listing_block #advanced');

			$('#pl_show_advanced').click(function() {
				$(this).css('display', 'none');
				$('#pl_static_listing_block #advanced').css('display', 'block');
				$('#pl_static_listing_block #amenities').css('display', 'block');
				$('#pl_static_listing_block #custom').css('display', 'block');
			});
		});
	</script>
	<h2>Pick a Listing</h2>
	<div id="pl-fl-meta">
		<div style="width: 400px; min-height: 200px">
		
			<p><?php _e('Listing Type:', 'pls'); ?></p>
			<p><?php _e('Featured Listing', 'pls'); ?> <input id="pl_featured_radio" type="radio" name="pl_listing_type" value="featured" checked="checked" /></p>
			<p><?php _e('Static Listing', 'pls'); ?> <input id="pl_static_radio" type="radio" name="pl_listing_type" value="static" /></p>
			<div id="pl_featured_listing_block">
			<?php 
				include PLS_OPTRM_DIR . '/views/featured-listings.php';
				// Enqueue all required stylings and scripts
				wp_enqueue_style('featured-listings', OPTIONS_FRAMEWORK_DIRECTORY.'css/featured-listings.css');
				
				wp_register_script( 'datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
				wp_enqueue_script('datatable'); 
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_style('jquery-ui-dialog', OPTIONS_FRAMEWORK_DIRECTORY.'css/jquery-ui-1.8.22.custom.css');
				wp_enqueue_script('jquery-ui-dialog');
				wp_enqueue_script('options-custom', OPTIONS_FRAMEWORK_DIRECTORY.'js/options-custom.js', array('jquery'));
				wp_enqueue_script('featured-listing', OPTIONS_FRAMEWORK_DIRECTORY.'js/featured-listing.js', array('jquery'));
		
				// Generate the popup dialog with featured			
				echo pls_generate_featured_listings_ui(array(
									'name' => 'Featured Meta',
									'desc' => '',
									'id' => 'featured-listings-type',
									'type' => 'featured_listing'
									) ,$pl_featured_meta_value
									, 'pl_featured_listing_meta');
			?>
			</div><!-- end of #pl_featured_listing_block -->
			<div id="pl_static_listing_block" style="display: none;">
				<?php echo PL_Form::generate_form(
							PL_Config::PL_API_LISTINGS('get', 'args'),
							array('method' => "POST", 
									'title' => true,
									'wrap_form' => false, 
							 		'echo_form' => false, 
									'include_submit' => false, 
									'id' => 'pls_admin_my_listings')); ?>
			</div><!-- end of #pl_static_listing_block -->
		</div>
		<div>
			<?php 
				PL_Snippet_Template::prepare_template( 
							array(
								'codes' => array( 'featured_listings', 'static_listings' ), 
								'p_codes' => array( 
									'featured_listings' => 'Featured Listings',
									'static_listings' => 'Static Listings' 
									)) 
								); 
			?>
		</div>
	</div>
<?php
}

add_action( 'save_post', 'pl_featured_listings_meta_box_save' );
function pl_featured_listings_meta_box_save( $post_id ) {
	// Avoid autosaves
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	// Verify nonces for ineffective calls - from featured listings and general widgets
	if( !isset( $_POST['meta_box_nonce'] )) { return; }
	
	$verify_nonce = wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_fl_meta_box_nonce' ) || wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' );
	if( ! $verify_nonce ) {
		return;
	}
	
	$static_listings_option = array();
	
	// Save search form fields if not empty
	if( ! empty( $_POST['listing_types'] ) && 'false' !== $_POST['listing_types'] ) { $static_listings_option['listing_types'] = $_POST['listing_types']; }
	if( ! empty( $_POST['zoning_types'] ) &&  'false' !== $_POST['zoning_types'] ) { $static_listings_option['zoning_types'] = $_POST['zoning_types']; }
	if( ! empty( $_POST['purchase_types'] ) && 'false' !== $_POST['purchase_types'] ) { $static_listings_option['purchase_types'] = $_POST['purchase_types']; }
	
	// Save locations - verify is array or serialized from jQuery AJAX JSON data
	if( isset( $_POST['location'] ) ) {
		$location_arr = array();
		$post_location = $_POST['location'];
		
		if( is_array( $post_location ) ) {
			$location_arr = $post_location;
		} else {
			$post_location = str_replace('\\', '', $post_location); 
			$location_arr = json_decode( $post_location );
		}
		
		
		// add the location data to the static listings array
		foreach( $location_arr as $key => $value ) {
			if( ! empty( $value ) ) {
				$static_listings_option['location'][$key] = $value;
			}
		}
	}
	
	// Prepare metadata array - from POST as array or as a json encoded data from a jQuery call
	if( isset( $_POST['metadata'] ) ) {
		$metadata_arr = array();
		$post_metadata = $_POST['metadata'];
		
		if( is_array( $post_metadata ) ) {
			$metadata_arr = $post_metadata;
		}
		else {
			$post_metadata = str_replace( '\\', '', $post_metadata );
			$metadata_arr = json_decode( $post_metadata );
		}

		foreach( $metadata_arr as $key => $value ) {
			if( ! empty( $value ) ) {
				$static_listings_option['metadata'][$key] = $value;
			}
		}
	}
	
	update_post_meta( $post_id, 'pl_static_listings_option', $static_listings_option );
	if( isset( $_POST['pl_listing_type'] ) ) {
		update_post_meta( $post_id, 'pl_listing_type', $_POST['pl_listing_type'] );
	}

	$pl_post_type = ! empty( $_POST['pl_post_type'] ) ? $_POST['pl_post_type'] : '';
	
	if( $pl_post_type === 'pl_general_widget' ) {
		return;
	}
	
	update_post_meta( $post_id, 'pl_post_type', $pl_post_type );
	
	if( $pl_post_type === 'featured_listings' && ! empty( $_POST['pl_template_featured_listings'] ) ) {
		update_post_meta( $post_id, 'pl_template_featured_listings',  $_POST['pl_template_featured_listings'] );
	} else if( $pl_post_type === 'static_listings' && ! empty( $_POST['pl_template_static_listings'] )) {
		update_post_meta( $post_id, 'pl_template_static_listings',  $_POST['pl_template_static_listings'] );
	}
	
	if( isset( $_POST['pl_cpt_template'] ) && ! empty( $_POST['pl_cpt_template'] ) ) {
		update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template'] );
	}
	
	// Verify if the time field is set
	if( isset( $_POST['pl_featured_listing_meta'] ) ) {
		update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
	}
		
}
