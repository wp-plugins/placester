<?php

class PL_General_Widget_CPT extends PL_Post_Base {

	// Leverage the PL_Form class and it's fields format (and implement below)
	public $codes = array(
				'search_map' => 'Search Map',
				'search_form' => 'Search Form',
				'search_listings' => 'Search Listings',
				'pl_neighborhood' => 'Neighborhood',
				'listing_slideshow' => 'Listings Slideshow',
				'featured_listings' => 'Featured Listings',
				'static_listings' => 'List of Listings'
			);
	
	public static $post_types =  array(
				'pl_map' => 'Map',
				'pl_form' => 'Search Form',
				'pl_search_listings' => 'Search Listings',
				'pl_slideshow' => 'Slideshow',
				'pl_neighborhood' => 'Neighborhood',
//  				'featured_listings' => 'Featured Listings',
				'static_listings' => 'List of Listings'
	);
	
	public $default_post_type = 'pl_map';
	 
	public $fields = array(
// 			'map_type' => array( 'type' => 'select', 'label' => 'Map Type', 'options' => array( 
// 																	'listings' => 'listings',
// 																	 'lifestyle' => 'lifestyle',
// 																	'lifestyle_poligon' => 'lifestyle_poligon' 
// 							), 'css' => 'pl_map' ),
			'width' => array( 'type' => 'text', 'label' => 'Width', 'css' => 'pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings' ),
			'height' => array( 'type' => 'text', 'label' => 'Height', 'css' => 'pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings' ),
			'animation' => array( 'type' => 'select', 'label' => 'Animation', 'options' => array(
					'fade' => 'fade',
					'horizontal-slide' => 'horizontal-slide',
					'vertical-slide' => 'vertical-slide',
					'horizontal-push' => 'horizontal-push',
			), 'css' => 'pl_slideshow' ),
			'animationSpeed' => array( 'type' => 'text', 'label' => 'Animation Speed', 'css' => 'pl_slideshow' ),
			'timer' => array( 'type' => 'checkbox', 'label' => 'Timer', 'css' => 'pl_slideshow' ),
			'pauseOnHover' => array( 'type' => 'checkbox', 'label' => 'Pause on hover', 'css' => 'pl_slideshow' ),
			'hide_sort_by' => array( 'type' => 'checkbox', 'label' => 'Hide Sort By dropdown', 'css' => 'pl_static_listings' ),
			'form_action_url' => array( 'type' => 'text', 'label' => 'Form Address', 'css' => 'pl_form' ),
			'hide_sort_direction' => array( 'type' => 'checkbox', 'label' => 'Hide Sort Direction', 'css' => 'pl_static_listings' ),
			'hide_num_results' => array( 'type' => 'checkbox', 'label' => 'Hide Show Number of Results', 'css' => 'pl_static_listings' ),
 			'num_results_shown' => array( 'type' => 'text', 'label' => 'Number of Results Displayed', 'css' => 'pl_static_listings' ),
			'widget_class' => array( 'type' => 'text', 'label' => 'Widget Class', 'css' => 'pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings' ),
	);
	
	public function register_post_type() {
		$args = array(
				'labels' => array(
						'name' => __( 'Placester Widget', 'pls' ),
						'singular_name' => __( 'pl_map', 'pls' ),
						'add_new_item' => __('Add New Placester Widget', 'pls'),
						'edit_item' => __('Edit Placester Widget', 'pls'),
						'new_item' => __('New Placester Widget', 'pls'),
						'all_items' => __('All Placester Widgets', 'pls'),
						'view_item' => __('View Placester Widgets', 'pls'),
						'search_items' => __('Search Placester Widgets', 'pls'),
						'not_found' =>  __('No widgets found', 'pls'),
						'not_found_in_trash' => __('No widgets found in Trash', 'pls')),
				'menu_icon' => trailingslashit(PL_IMG_URL) . 'logo_16.png',
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'show_in_menu' => false,
				'query_var' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title'),
		);

		register_post_type('pl_general_widget', $args );
	}
	
	public function __construct() {
		parent::__construct();
		
		add_action( 'save_post', array( $this, 'meta_box_save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_head', array( $this, 'admin_head_plugin_path' ) );
		add_filter( 'manage_edit-pl_general_widget_columns' , array( $this, 'widget_edit_columns' ) );
 		add_filter( 'manage_pl_general_widget_posts_custom_column', array( $this, 'widget_custom_columns' ) );
		add_action( 'wp_ajax_autosave', array( $this, 'autosave_refresh_iframe' ), 1 );
		add_action( 'wp_ajax_autosave_widget', array( $this, 'autosave_save_post_for_iframe' ) );
		add_action( 'wp_ajax_handle_widget_script', array( $this, 'handle_iframe_cross_domain' ) );
		add_action( 'wp_ajax_nopriv_handle_widget_script', array( $this, 'handle_iframe_cross_domain' ) );
		add_filter( 'pl_form_section_after', array( $this, 'filter_form_section_after' ), 10, 3 );
		add_filter('post_row_actions', array( $this, 'remove_quick_edit_view'), 10, 1 );
		add_action( 'restrict_manage_posts', array( $this, 'listing_posts_add_filter_widget_type' ) );
		add_filter( 'parse_query', array( $this, 'widget_type_posts_filter' ) );
		add_action( 'admin_menu', array( $this, 'correct_metabox_setup' ), 50);
	}
	
	/**
	 * Handle cross-domain script insertion and pass back to the embedded script for the iwdget
	 */
	public function handle_iframe_cross_domain() {
		// don't process if widget ID is missing
 		if( ! isset( $_GET['id'] ) ) {
 			die();
 		}
 		
 		// defaults
 		$args['width'] = '250';
 		$args['height'] = '250';
 		
 		// get the post and the meta
 		$post_id = $_GET['id'];
		$meta = get_post_custom( $post_id );

		// default GET should have at least id, callback and action
		$ignore_array = array(
			'pl_static_listings_option',
			'pl_featured_listings_option',
		);
		
		foreach( $meta as $key => $value ) {
			// ignore several options that we don't need to pass
			if( ! in_array( $key, $ignore_array ) ) {
				// ignore underscored private meta keys from WP
				if( strpos( $key, '_', 0 ) !== 0 && is_array( $value ) && ! empty( $value[0] ) ) {
					$args[$key] = $value[0];
				}
			}
		}
		
		$args['width'] = ! empty( $_GET['width'] ) ? $_GET['width'] : $args['width'];
		$args['height'] = ! empty( $_GET['height'] ) ? $_GET['height'] : $args['height'];
		$args['widget_class'] = ! empty( $meta['widget_class'] ) && is_array( $meta['widget_class'] ) ? $meta['widget_class'][0] : ''; 
		
		unset( $args['action'] );
		unset( $args['callback'] );
		
		$args['post_id'] = $_GET['id'];
		
		if( isset( $args['widget_original_src'] ) ) {
			$args['widget_url'] =  $args['widget_original_src'] . '/?p=' . $_GET['id'];
			unset( $args['widget_original_src'] );
		} else {
			$args['widget_url'] =  home_url() . '/?p=' . $_GET['id'];
		}
		
		header("content-type: application/javascript");
		echo $_GET['callback'] . '(' . json_encode( $args ) . ');';
	}
 	
	
	public  function meta_box() {
		add_meta_box( 'pl-controls-metabox-id', 'Placester Widgets', array( $this, 'pl_widgets_meta_box_cb'), 'pl_general_widget', 'normal', 'high' );
		add_meta_box( 'pl-previewer-metabox-id', 'Widget Preview', array( $this, 'pl_previewer_meta_box_cb'), 'pl_general_widget', 'side', 'low' );
		
	}
	
	public function correct_metabox_setup() {
		remove_meta_box( 'socialize-buttons-meta', 'pl_general_widget', 'side');
		remove_meta_box( 'socialize-action-meta', 'pl_general_widget', 'normal');
	}
	
	public function pl_previewer_meta_box_cb( $post ) {
		?>
		<div>
			<div id='preview-wrapper'>
				<div id='preview-meta-widget'>
					<img id="preview_load_spinner" src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." width="30px" height="30px" style="margin-left: 100px; margin-top: 100px;" />
				</div>
				<div id="pl-review-wrapper">
					<a id="pl-review-link" href="" style="display:none;">Open Preview in a popup</a>
					<div id="pl-review-popup" class="dialog" style="display: none;">Loading preview...</div>
				</div>
			</div>
		</div>		
		<?php 
	}
	
	// add meta box for featured listings- adding custom fields
	public  function pl_widgets_meta_box_cb( $post ) {
		$is_post_new = true;
		if( ! empty( $_GET['post'] ) ) {
			$is_post_new = false;
		}
		
		// get all CPT custom field values
		$values = get_post_custom( $post->ID );

		// read the post type
		$pl_post_type = isset( $values['pl_post_type'] ) ? $values['pl_post_type'][0] : '';
		
		// manage featured and static listing form values
		$pl_featured_meta_value = ''; 
		if( ! empty( $values['pl_featured_listing_meta'] ) ) {
			if( is_array( $values['pl_featured_listing_meta'] ) ) {
				$pl_featured_meta_value = $values['pl_featured_listing_meta'][0];
				$pl_featured_meta_value = @unserialize( $pl_featured_meta_value );
				
				if( false === $pl_featured_meta_value ) {
					$pl_featured_meta_value = @json_decode( $values['pl_featured_listing_meta'][0], true );
				} else if( is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value[0] ) ) { 
					$pl_featured_meta_value = $pl_featured_meta_value[0]; 
				}
				if(is_array( $pl_featured_meta_value ) && isset( $pl_featured_meta_value['featured-listings-type'] )) {
					$pl_featured_meta_value = $pl_featured_meta_value['featured-listings-type'];
				}
			} else if(isset( $values['pl_featured_listing_meta']['featured-listings-type'] )) {
				$pl_featured_meta_value = $values['pl_featured_listing_meta']['featured-listings-type'];
			}
		}
		
		
		
		$_POST['pl_featured_meta_value'] = $pl_featured_meta_value;
		
		$pl_static_listings_option = isset( $values['pl_static_listings_option'] ) ? unserialize($values['pl_static_listings_option'][0]) : '';
		if( is_array( $pl_static_listings_option ) ) {
			foreach( $pl_static_listings_option as $key => $value ) {
				if( ! empty( $value ) ) {
					$_POST[$key] = $value;
				}
			}
		}
		
		// get link for iframe
		$permalink = '';
		if( ! $is_post_new ) {
			$permalink = get_permalink($post->ID);
		}
		?>
		<script type="text/javascript">
		</script>
	
		<div id="post_types_list">
				<div class="post_types_list_wrapper" style="clear: both; padding-top: 10px;">
					<span>Select Type: </span>
					<select id="pl_post_type_dropdown" name="pl_post_type_dropdown">
					<option id="pl_post_type_undefined" value="pl_post_type_undefined">Select</option> 
					<?php 
					
					 $num_of_post_types = count( self::$post_types );
					 $i = 0;
					 
					 foreach( self::$post_types as $post_type => $label ):
					 		$i++;
							$link_class = ''; 
							if( $post_type == $pl_post_type ) {
								$link_class = 'selected_type';
							}
						?>			
							<option id="pl_post_type_<?php echo $post_type; ?>" class="<?php echo $link_class; ?>" value="pl_post_type_<?php echo $post_type; ?>" <?php if( ! empty( $link_class ) ) echo ' selected="selected"'  ?>><?php echo $label; ?></option>
							<?php if( $i < $num_of_post_types ):
								echo '<span class="pl_type_separator"> |</span>';
							endif; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		<?php 
		echo '<div id="widget-meta-wrapper" style="display: none; min-height: 370px">';
		
		// read width/height and slideshow values
		$width =  isset( $values['width'] ) && ! empty( $values['width'][0] ) ? $values['width'][0] : '250';
		$_POST['width'] = $width;
		$height = isset( $values['height'] ) && ! empty( $values['height'][0] ) ? $values['height'][0] : '250';
		$_POST['height'] = $height;
		$animationSpeed = isset( $values['animationSpeed'] ) && ! empty( $values['animationSpeed'][0] ) ? $values['animationSpeed'][0] : '800';
		$_POST['animationSpeed'] = $animationSpeed;
		$widget_class = isset( $values['widget_class'] ) && ! empty( $values['widget_class'][0] ) ? 'class="'  . $values['widget_class'][0] . '"' : '';
		
		$style = ' style="width: ' . $width . 'px;height: ' . $height . 'px"';
		
		// for post edits, prepare the frame related variables (iframe and script)
		if( ! empty( $permalink ) ):
			$iframe = '<iframe src="' . $permalink . '"'. $style . $widget_class .'></iframe>';
			$iframe_controller = '<script id="plwidget-' . $post->ID . '" src="' . PL_PARENT_URL . 'js/fetch-widget.js?id=' . $_GET['post'] . '"'  . $style . ' ' . $widget_class . '></script>';
		endif; ?>
		<div class="pl_widget_block">
			<section class="pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings">
				<h2>Attributes</h2>
			</section>
			<?php // get meta values from custom fields
			// fill POST array for the forms (required after new widget is created)
		foreach( $this->fields as $field => $arguments ) {
			$value = isset( $values[$field] ) ? $values[$field][0] : '';
		
			if( !empty( $value ) && empty( $_POST[$field] ) ) {
				$_POST[$field] = $value;
			}
				
			echo PL_Form::item($field, $arguments, 'POST', false, 'general_widget_');
		}
		?>
		</div>
		
		<section class="featured_listings">
			<h2>Pick a Listing</h2>
		</section>
			<div id="pl-fl-meta">
				<div style="width: 400px;">
					<div id="pl_featured_listing_block" class="featured_listings pl_slideshow" style="min-height: 40px;">
					<?php 
						
						include PLS_OPTRM_DIR . '/views/featured-listings.php';
						// Enqueue all required stylings and scripts
						wp_enqueue_style('featured-listings', OPTIONS_FRAMEWORK_DIRECTORY.'css/featured-listings.css');
						
						wp_register_script( 'datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
						wp_enqueue_script('datatable'); 
						wp_enqueue_script('jquery-ui-core');
						wp_enqueue_style('jquery-ui-datepicker');
						wp_enqueue_script('jquery-ui-datepicker');
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
					<section id="pl_static_listing_block" class="static_listings pl_search_listings">
						<?php 
							$static_list_form = PL_Form::generate_form(
										PL_Config::PL_API_LISTINGS('get', 'args'),
										array('method' => "POST", 
												'title' => true,
												'wrap_form' => false, 
										 		'echo_form' => false, 
												'include_submit' => false, 
												'id' => 'pls_admin_my_listings'),
										'general_widget_');

							echo $static_list_form;
						 ?>
					</section><!-- end of #pl_static_listing_block -->
				</div>
			</div>
			<input type="hidden" name="pl_post_type" id="pl_post_type" value="pl_map" />
		<?php $atts = array();
		
		// get radio values for neighborhood
		$radio_def = isset( $values['radio-type'] ) ? $values['radio-type'][0] : 'state';
		$select_id = 'nb-select-' . $radio_def;
		$select_def = isset( $values[ $select_id ] ) ? $values[ $select_id ][0] : '0';
		?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// manage neighborhood
				$('#<?php echo $radio_def; ?>').attr('checked', true);
				$('#nb-taxonomy-<?php echo $radio_def; ?>').css('display', 'block');
				$('#nb-id-select-<?php echo $radio_def; ?>').val(<?php echo $select_def; ?>);
		
				$('#pl_location_tax input:radio').on('click', radioClicks);
		
				function radioClicks() {
					var radio_value = this.value;
		
					$('.nb-taxonomy').each(function() {
						if( radio_value !== 'undefined') {
							if( this.id.indexOf(radio_value, this.id.length - radio_value.length) !== -1 ) {
								$(this).css('display', 'block');
							} else {
								$(this).css('display', 'none');
							}
						}
					});
				}

				$('#metadata-max_avail_on_picker').datepicker();
				$('#metadata-min_avail_on_picker').datepicker();

				// click a new post type as a widget type
				$('#post_types_list select').change(function() {
					if( $('#title').val() === '' ) {
						alert('Please enter widget title first.');
						return;
					} 
					
					//var selected_cpt = $(this).attr('id').substring('pl_post_type_'.length);
					var selected_cpt = $(this).parent().find(':selected').val().substring('pl_post_type_'.length);

					if( selected_cpt == 'undefined' ) {
						// clicking "Select" shouldn't reflect the choice
						return;
					}
					
					// $('#post_types_list a').removeClass('selected_type');
					// $(this).addClass('selected_type');
					$('#pl_post_type').val(selected_cpt);

					// hide values not related to the post type and reveal the ones to be used
					$('#widget-meta-wrapper .pl_widget_block > section, #pl_location_tax').each(function() {
						var section_class = $(this).attr('class');
						if( section_class !== undefined  ) {
							if( section_class.indexOf( selected_cpt ) !== -1  ) {
								$(this).show();
								// $(this).find('input').removeAttr('disabled');
								// $(this).find('select').removeAttr('disabled');
							} else {
								$(this).hide();
								// $(this).find('input, select').attr('disabled', true);
							}
						}
					});

					// fix inner sections for some CPTs
					if( selected_cpt == 'static_listings' || selected_cpt == 'pl_search_listings' ) {
						$('.form_group, .form_group section').show();
						$('#pl_static_listing_block #advanced').hide();
						$('#pl_static_listing_block #amenities').hide();
						$('#pl_static_listing_block #custom').hide();
						$('#general_widget_zoning_types').hide();
						$('#general_widget_purchase_types').hide();
					} else if( selected_cpt == 'pl_neighborhood' ) {
						$('.pl_neighborhood.pl_widget_block, .pl_neighborhood section').show();
					}

					// display template blocks
					$('.pl_template_block').each(function() {
						var selected_cpt = $('#pl_post_type').val();
						var block_id = $(this).attr('id');
						selected_cpt = selected_cpt.replace('pl_', '');

						if( block_id.indexOf( selected_cpt ) !== -1 ) {
							$(this).css('display', 'block');
						} else {
							$(this).css('display', 'none');
						}
					});

					$('.pl_template_section_title').show();

					$('#general_widget_pl_template_before_block').show();
					$('#general_widget_pl_template_after_block').show();

					// display/hide featured/static listings
					var featured_class = $('#pl_featured_listing_block').attr('class');
					var static_class = $('#pl_static_listing_block').attr('class');

					if( featured_class.indexOf( selected_cpt ) === -1 ) {
						$('#pl_featured_listing_block').hide();
					} else {
						$('#pl_featured_listing_block').show();
					}

					if( static_class.indexOf( selected_cpt ) === -1 ) {
						$('#pl_static_listing_block').hide();
					} else {
						$('#pl_static_listing_block').show();
					}
					
					$('#preview-meta-widget').html('<img id="preview_load_spinner" src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Widget options are Loading..." width="30px" height="30px" style="position: absolute; top: 100px; left: 100px" />');

					// call the custom widget_autosave to send values to backend
					widget_autosave();
					
					$('#widget-meta-wrapper input, #widget-meta-wrapper select').css('background', '#ffffff');
					$('#widget-meta-wrapper input:disabled, #widget-meta-wrapper select:disabled').css('background', '#dddddd');
				});

				// call the custom autosave for every changed input and select
				$('#widget-meta-wrapper section input, #widget-meta-wrapper section select').on('change', function() {
					widget_autosave();				
				});
				$('#pl_template_before_block, #pl_template_after_block').on('change', function() {
					widget_autosave();				
				});
				$('#save-featured-listings').on('click', function() {
					setTimeout( widget_autosave, 1000 );
				});

				$('#pl-review-link').on('click', function(e) {
					e.preventDefault();

					var iframe_content = $('#preview-meta-widget').html();

					var options_width = jQuery('#widget-meta-wrapper input#width').val() || 750;
					var options_height = jQuery('#widget-meta-wrapper input#height').val() || 500;
					
					$('#pl-review-popup').html( iframe_content );
					$('#pl-review-popup iframe').css('width', options_width + 'px');
					$('#pl-review-popup iframe').css('height', options_height + 'px');

					$('#pl-review-popup').dialog({
							width: 800,
							height: 600
						});
				
				});

				// hide advanced values for static listings area
				$('#pl_static_listing_block #advanced').css('display', 'none');
				$('#pl_static_listing_block #amenities').css('display', 'none');
				$('#pl_static_listing_block #custom').css('display', 'none');
				$('<a href="#basic" id="pl_show_advanced" style="line-height: 50px;">Show Advanced filters</a>').insertBefore('#pl_static_listing_block #advanced');
				$('<a href="#basic" id="pl_hide_advanced" style="line-height: 50px; display: none;">Hide Advanced filters</a>').insertAfter('#pl_static_listing_block #custom');

				$('#pl_show_advanced').on('click', function() {
					$(this).hide();
					$('#pl_static_listing_block #advanced').css('display', 'block');
					$('#pl_static_listing_block #amenities').css('display', 'block');
					$('#pl_static_listing_block #custom').css('display', 'block');
					$('#pl_hide_advanced').show();
				});

				$('#pl_hide_advanced').on('click', function() {
					$(this).hide();
					$('#pl_static_listing_block #advanced').css('display', 'none');
					$('#pl_static_listing_block #amenities').css('display', 'none');
					$('#pl_static_listing_block #custom').css('display', 'none');
					$('#pl_show_advanced').show();
				});

				// populate slug box for the edit screen
				<?php if( ! $is_post_new ) { ?>
					$('#edit-slug-box').after('<div class="iframe-link"><strong>Embed Code:</strong> <?php echo esc_html( $iframe_controller ); ?></div><div class="shortcode-link"></div>');
					$('#pl_post_type_dropdown').trigger('change');
				<?php }	?>

				// reset before the view, hide everything
				$('#widget-meta-wrapper section, #pl_featured_listing_block').hide();
				$('.pl_template_block section').show();
				$('#widget-meta-wrapper').show();

				// Update preview when creating a new template
				$('.save_snippet').on('click', function() {
					$('#pl_post_type_dropdown').trigger('change');
				});

				<?php if( ! $is_post_new ) { ?>
					$('#pl_post_type_dropdown').trigger('change');
				<?php }	?>

				$('#pl-previewer-metabox-id .handlediv').on('click', function() {
					if ( $('#pl-previewer-metabox-id').hasClass('closed') ){
						$('#pl-previewer-metabox-id').css('min-height', '350px');
					} else {
						$('#pl-previewer-metabox-id').css('min-height', '0');
					}
				});
				
				// $('#pl_post_type_dropdown').trigger('change');
				$('#preview_load_spinner').remove();
				$('#preview-meta-widget').html('<?php echo isset($iframe) ? $iframe : '' ?>');
			});
			</script>	
				
		<?php 
		
		wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );
	
		echo '<section id="pl_location_tax" class="pl_neighborhood">';
		$taxonomies = PL_Taxonomy_Helper::get_taxonomies();
		?>
				<?php foreach ($taxonomies as $slug => $label): ?>
					<section>
						<input type="radio" id="<?php echo $slug ?>" name="radio-type" value="<?php echo $slug ?>">
						<label for="<?php echo $slug ?>"><?php echo $label ?></label>
					</section>
				<?php endforeach ?>	
		<?php
		echo '</section>';
		
		$taxonomies = PL_Taxonomy_Helper::$location_taxonomies;
		
		echo '<section class="pl_widget_block pl_neighborhood">';
		foreach( $taxonomies as $slug => $label ) {
			$terms = PL_Taxonomy_Helper::get_taxonomy_items( $slug );
				
			echo "<div id='nb-taxonomy-$slug' class='nb-taxonomy' style='display: none;'>";
			echo "<select id='nb-id-select-$slug' name='nb-select-$slug'>";
			foreach( $terms as $term ) {
			echo "<option value='" . $term['term_id'] . "'>" . $term['name'] . "</option>";
			}
				echo "</select>";
			echo "</div>";
		}
		echo '</section>';
		
		echo '<div class="clear"></div>';
		
		echo '<section class="pl_template_section_title"><h2>Template Manager</h2></section>';
		
		// arguments for before/after blocks
		$before_after_block_args = array(
			'type' => 'textarea',
			'css' => 'pl_map pl_form pl_search_listings pl_slideshow pl_neighborhood featured_listings static_listings',
			'rows' => 7,
			'cols' => 60
		);

		$_POST['pl_template_before_block'] = ! empty( $values['pl_template_before_block'] ) ? $values['pl_template_before_block'][0] : '';
		$_POST['pl_template_after_block'] = ! empty( $values['pl_template_after_block'] ) ? $values['pl_template_after_block'][0] : '';
		
		// Print template blocks with pre/post blocks for extra markup
		echo PL_Form::item('pl_template_before_block', 
						array_merge( $before_after_block_args, array( 'label' => 'Before template' ) ),
						 'POST', false, 'general_widget_');
		
		$this->print_template_blocks();
		
		echo PL_Form::item('pl_template_after_block',
					 	array_merge( $before_after_block_args, array( 'label' => 'After template') ),
						 'POST', false, 'general_widget_');
		
		echo '</div>'; // end of #widget-meta-wrapper
	}
	
	public function meta_box_save( $post_id ) {
		// Avoid autosaves
 		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
 		
		// Verify nonces for ineffective calls
		if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'pl_cpt_meta_box_nonce' ) ) return;
	
		// if our current user can't edit this post, bail
		// if( !current_user_can( 'edit_post' ) ) return;
	
		$pl_post_type = $_POST['pl_post_type'];
		
		// This should be a determined widget type already.
		if( $pl_post_type === 'pl_general_widget' ) {
			return;
		}
		
		// Fetch the context template
		$context_template = self::get_context_template( $pl_post_type );
		
		if( isset( $_POST['pl_template_' . $context_template ] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_template_' . $context_template] );
		} else if( isset( $_POST['pl_cpt_template'] ) && ! empty( $_POST['pl_cpt_template'] ) ) {
			update_post_meta( $post_id, 'pl_cpt_template', $_POST['pl_cpt_template'] );
		}
		
		// Send the before/after snippets for the template
		if( ! empty( $_POST['pl_template_before_block'] ) ) {
	 		update_post_meta( $post_id, 'pl_template_before_block', $_POST['pl_template_before_block'] );
		}
		if( ! empty( $_POST['pl_template_after_block'] ) ) {
			update_post_meta( $post_id, 'pl_template_after_block', $_POST['pl_template_after_block'] );
		}
		
		if( $pl_post_type === 'featured_listings' ||  $pl_post_type === 'static_listings') {
			pl_featured_listings_meta_box_save( $post_id );
		}
		
		if( $pl_post_type === 'pl_slideshow') {
			if( isset( $_POST['pl_featured_listing_meta'] ) ) {
				update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
			}
		}
		
		update_post_meta( $post_id, 'pl_post_type', $pl_post_type );
		
		foreach( $this->fields as $field => $values ) {
			if( $values['type'] === 'checkbox' && ! isset( $_POST[$field] ) ) {
				update_post_meta( $post_id, $field, false );
			} else if( isset( $_POST[$field] ) ) {
				if( $field != 'pl_cpt_template' ) {
					update_post_meta( $post_id, $field, $_POST[$field] );
				}
			}
		}
		
		if( isset( $_POST['radio-type'] ) ) {
			$radio_type = $_POST['radio-type'];
			$select_type = 'nb-id-select-' . $radio_type;
			if( isset( $_POST[$select_type] ) ) {
				// persist radio box storage based on what is saved
				update_post_meta( $post_id, 'radio-type', $_POST['radio-type'] );
				update_post_meta( $post_id, 'nb-select-' . $radio_type, $_POST[ $select_type ] );
			}
		}
		
		if( isset( $_POST['pl_featured_listing_meta'] ) ) {
			update_post_meta( $post_id, 'pl_featured_listing_meta',  $_POST['pl_featured_listing_meta'] );
		}
	}
	
	public function post_type_templating( $single ) {
// 		global $post;
		
		$post = get_queried_object();

		if( empty( $post ) || ! isset( $post->post_type ) ) {
			return $single;
		}
		
		if( ! in_array( $post->post_type, PL_Post_Type_Manager::$post_types )
				&& 'pl_general_widget' !== $post->post_type ) {
			return $single;
		}
		
		if( ! empty( $post ) ) {
			// map the post type from the meta key (as we use a single widget here)
			$post_type = get_post_meta($post->ID, 'pl_post_type', true);
			$post->post_type = $post_type;
		}
		$skipdb = false;
		// if( !empty ( $_GET['skipdb'] ) && $_GET['skipdb'] == 'true' ) {
		if( isset( $_GET['action'] ) && isset( $_GET['id'] ) && count( $_GET ) > 3 ) {
			$skipdb = true;
		}
		
		if( ! empty( $post ) ) {
			// TODO: make a more thoughtful loop here, interfaces or so
			if( $post->post_type == 'pl_map' ) {
				PL_Map_CPT::post_type_templating( $single, $skipdb );
			} else if( $post->post_type == 'pl_form' ) {
				PL_Form_CPT::post_type_templating( $single, $skipdb );
			} else if( $post->post_type == 'pl_slideshow' ) {
				PL_Slideshow_CPT::post_type_templating( $single, $skipdb );
			} else if( $post->post_type == 'pl_search_listings' ) {
				PL_Search_Listing_CPT::post_type_templating( $single, $skipdb );
			} else if( $post->post_type == 'pl_neighborhood' ) {
				PL_Neighborhood_CPT::post_type_templating( $single, $skipdb );
			} else if( $post->post_type == 'featured_listings' ) {
				$this->prepare_featured_template( $single, $skipdb );
			} else if( $post->post_type == 'static_listings' ) {
				$this->prepare_static_template( $single, $skipdb );
			} 
		} 
		// Silence is gold.
	}
	
	public function admin_styles( $hook ) {
		if( ( $hook === 'post.php' && ! empty( $_GET['post'] ) )
			|| ( $hook === 'post-new.php' && ! empty( $_GET['post_type'] ) && $_GET['post_type'] == 'pl_general_widget' ) ) {
			global $post;
			if( ! empty( $post ) && $post->post_type === 'pl_general_widget' ) {
				wp_enqueue_script('settings-template', trailingslashit(PL_JS_URL) .  'admin/settings/template.js', array( 'jquery'));
				wp_enqueue_style( 'placester-widget', trailingslashit( PL_CSS_ADMIN_URL ) . 'placester-widget.css' );
				wp_enqueue_script( 'placester-widget-script', trailingslashit( PL_JS_URL ) . 'admin/widget-handler.js', array( 'jquery' ), '1.1.8' );
			}
		}
	}
		
	public function admin_head_plugin_path( ) {
	?>
		<script type="text/javascript">
			var placester_plugin_path = '<?php echo PL_PARENT_URL; ?>';
		</script>
	<?php 
	}
	
	public function widget_edit_columns( $columns ) {
		$new_columns = array(); 
		$new_columns['title'] = $columns['title']; 
		$new_columns['type'] = "Widget";
		$new_columns['date'] = $columns['date'];
	
		return $new_columns;
	}
	
	public function widget_custom_columns( $column ) {
		global $post;
		$widget_type = get_post_meta( $post->ID, 'pl_post_type', true );
	
		switch ($column) {
			case "type":
				if( ! empty( $widget_type ) ) {
					echo PL_Post_Type_Manager::get_post_type_title_helper( $widget_type );
				}
				break;
		}
	}
	
 	public function autosave_refresh_iframe( ) {
		if ( isset($_POST['pl_post_type']) ) {    	
			$id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0;
			
			if ( ! $id )
				wp_die( -1 );
			
			if( ! headers_sent() ):
				?>
					<script type="text/javascript">
						jQuery('#post').trigger('submit');
					</script>
				<?php 
			endif;
			 
			$this->meta_box_save( $id );
		}	
	}
	
	private function print_template_blocks( ) {
		
	   foreach( $this->codes as $code => $label ) {
			echo '<div class="pl_template_block" id="' .$code  . '_template_block" style="display: none;">';

			PL_Snippet_Template::prepare_template(
				array(
						'codes' => array( $code ),
						'p_codes' => array(
							$code => $label
						),
						'select_name' => 'pl_template_' . $code
				)
			);
			
			echo '</div>';
			
		    add_action( 'pl_template_extra_styles', array( $this, 'update_template_block_styles' ) );
		}
	}
	
	// Helper function for featured listings
	// They are already available via other UI
	private function prepare_featured_template( $single ) {
		global $post;
		
		if( ! empty( $post ) && $post->post_type === 'featured_listings' ) {
			$meta = get_post_meta( $post->ID );
			$template = '';
			
			if( ! empty( $meta['pl_cpt_template'] ) ) {
				$template = 'template="' . $meta['pl_cpt_template'][0] . '"';
			}
			
			$shortcode = '[featured_listings id="' . $post->ID . '" '. $template . ']';
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
		
			die();
		}
	}
	
	// Helper function for static listings
	// They are already available via other UI
	private function prepare_static_template( $single ) {
		global $post;
		
		$args = '';

		if( ! empty( $post ) && $post->post_type === 'static_listings' ) {

			$meta = get_post_meta( $post->ID );
			$query_limit = '';
			$template = '';
			if( ! empty( $meta['pl_template_static_listings'] ) ) {
				$args .= 'template="static_listings_' . $meta['pl_template_static_listings'][0] . '"';
			} else if( ! empty( $meta['pl_cpt_template'] ) ) {
				$args .= 'template="static_listings_' . $meta['pl_cpt_template'][0] . '"';
			}

			if( ! empty( $meta['num_results_shown'] ) ) {
				$args .= sprintf( ' query_limit="%s"', $meta['num_results_shown'][0] );
			}
			if( ! empty( $meta['hide_num_results'] ) ) {
				$args .= sprintf( ' hide_num_results="%s"', $meta['hide_num_results'][0] );
			}
			if( ! empty( $meta['hide_sort_by'] ) ) {
				$args .= sprintf( ' hide_sort_by="%s"', $meta['hide_sort_by'][0] );
			}
			if( ! empty( $meta['hide_sort_direction'] ) ) {
				$args .= sprintf( ' hide_sort_direction="%s"', $meta['hide_sort_direction'][0] );
			}

			$shortcode = '[static_listings id="' . $post->ID . '" ' . $args . ']';
			
			include PL_LIB_DIR . '/post_types/pl_post_types_template.php';
		
			die();
		}
	}
	
	// Autosave function when any of the input fields is called
	public function autosave_save_post_for_iframe( ) {
		if( ! empty ($_POST['post_id'] ) ) {
			$post_id = (int) $_POST['post_id'];
			$pl_post_type = ! empty( $_POST['pl_post_type'] ) ? $_POST['pl_post_type'] : $this->default_post_type;

			if( $pl_post_type === 'featured_listings' ||  $pl_post_type === 'static_listings' || 
					$pl_post_type === 'pl_static_listings' || $pl_post_type === 'pl_search_listings') {			
				pl_featured_listings_meta_box_save( $post_id );
			}
// 			if( $pl_post_type === 'pl_neighborhood' ) {
				$this->meta_box_save( $post_id );
// 			}

			update_post_meta( $post_id, 'pl_post_type', $pl_post_type );
		}

 		die();
	}
	
	public function update_template_block_styles( ) {
		ob_start();
	?>	
	<style type="text/css">
		.snippet_container {
			width: 400px;
			margin-top: 0px;
		}
		.shortcode_container {
			width: 100%;
		}
	</style>	
	<?php 
		echo ob_get_clean();
	}
	
	public static function get_context_template( $post_type ) {
		switch( $post_type ) {
			case 'pl_search_listings':		return 'search_listings';
			case 'pl_map':					return 'search_map';
			case 'pl_form':					return 'search_form';
			case 'pl_listing_slideshow':	return 'listing_slideshow';
			case 'pl_static_listings':		return 'static_listings';
				
			// for all the others with the same name
			default:
				return $post_type;
		}	
	}
	
	public function filter_form_section_after( $form, $index, $count ) {
		if( $index < $count ) {
			return $form . '<div style="border-bottom: 1px solid white;"></div>';
		}
		return $form;
	}
	
	/**
	 * Remove quick edit and view 
	 */
	public function remove_quick_edit_view( $actions ) {
		global $post;
		
		if( $post->post_type === 'pl_general_widget' ) {
			unset( $actions['inline hide-if-no-js'] );
			unset( $actions['view'] );
		}
		return $actions;
	}
	
	/**
	 * Display widget types filter
	 */
	public function listing_posts_add_filter_widget_type() {
		$type = 'pl_general_widget';
		if ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != 'pl_general_widget' ) {
			return;
		}
	
		$values = array_flip( self::$post_types ); 
		?>
        <select name="pl_widget_type">
        <option value="">All widget types</option>
        <?php
            $current_v = isset($_GET['pl_widget_type'])? $_GET['pl_widget_type']:'';
            foreach ($values as $label => $value) {
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $value,
                        $value == $current_v? ' selected="selected"':'',
                        $label
                    );
                }
        ?>
        </select>
        <?php
	}
	
	/**
	 * Filter by widget types
	 */
	public function widget_type_posts_filter( $query ) {
		global $pagenow;
		$type = 'pl_general_widget';
		
		if ( is_admin() && $pagenow=='edit.php' && ! empty( $_GET['pl_widget_type'] ) ) {
			$query->query_vars['meta_key'] = 'pl_post_type';
			$query->query_vars['meta_value'] = $_GET['pl_widget_type'];
		}
	}
	
}


new PL_General_Widget_CPT();
