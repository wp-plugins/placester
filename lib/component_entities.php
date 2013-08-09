<?php

/**
 * Generate output for the shortcodes
 *
 */

PL_Component_Entity::init();

class PL_Component_Entity {

	public static $defaults = array( 'twentyten', 'twentyeleven' );

	public static $listing;

	public static $form_html;

	public static $neighborhood_term;

	public static $slideshow_caption_index;



	public static function init() {
		// add_action('init', array( __CLASS__, 'filter_featured_context' ) );

		// TODO: make dynamic function control over templates
		// currently they have different logic and diff input parameters
		$featured_templates = PL_Shortcode_CPT::template_list('featured_listings', true);
		foreach ($featured_templates as $template => $type) {
			add_filter( 'pls_listing_featured_listings_' . $template, array(__CLASS__,'featured_listings_templates'), 10, 3 );
		}
		add_filter( 'pls_listing_featured_listings_shortcode', array(__CLASS__,'featured_listings_templates'), 10, 3 );

		$search_form_templates = PL_Shortcode_CPT::template_list('search_form', true);
		foreach ($search_form_templates as $id => $attr) {
			add_filter( 'pls_listings_search_form_inner_' . $id, array(__CLASS__,'search_form_inner_template'), 10, 5 );
			add_filter( 'pls_listings_search_form_outer_' . $id, array(__CLASS__,'search_form_outer_template'), 10, 7 );
		}
		add_filter( 'pls_listings_search_form_inner_shortcode', array(__CLASS__,'search_form_inner_template'), 10, 5 );
		add_filter( 'pls_listings_search_form_outer_shortcode', array(__CLASS__,'search_form_outer_template'), 10, 7 );

		$listing_slideshow_templates = PL_Shortcode_CPT::template_list('listing_slideshow', true);
		foreach ($listing_slideshow_templates as $id => $attr) {
			add_filter( 'pls_slideshow_single_caption_' . $id, array( __CLASS__, 'listing_slideshow_templates' ), 10, 5 );
			// add_filter( 'pls_slideshow_html_' . $template, array(__CLASS__,'listing_slideshow_templates'), 10, 6 );
			// add_filter( 'pls_slideshow_data_' . $template, array(__CLASS__,'listing_slideshow_templates'), 10, 3 );
		}
		add_filter( 'pls_slideshow_single_caption_shortcode', array( __CLASS__, 'listing_slideshow_templates' ), 10, 5 );

		$search_listings_templates = PL_Shortcode_CPT::template_list('search_listings', true);
		foreach ($search_listings_templates as $id => $attr) {
			add_filter( 'pls_listings_list_ajax_item_html_search_listings_' . $id, array(__CLASS__,'search_listings_templates'), 10, 3 );
		}
		//add_filter( 'pls_listings_list_ajax_item_html_search_listings_shortcode', array(__CLASS__,'search_listings_templates'), 10, 3 );

		$static_listings_templates = PL_Shortcode_CPT::template_list('static_listings', true);
		foreach ($static_listings_templates as $id => $attr) {
			add_filter( 'pls_listings_list_ajax_item_html_static_listings_' . $id, array(__CLASS__, 'search_listings_templates'), 10, 3 );
		}
		//add_filter( 'pls_listings_list_ajax_item_html_static_listings_shortcode', array(__CLASS__, 'search_listings_templates'), 10, 3 );

		$neighborhood_templates = PL_Shortcode_CPT::template_list('pl_neighborhood', true);
		foreach ($neighborhood_templates as $id => $attr) {
			add_filter( 'pls_neighborhood_html_' . $id, array(__CLASS__, 'neighborhood_templates'), 10, 4 );
		}
	}

	public static function featured_listings_entity( $atts, $filters = '' ) {
		if (!empty($atts['id'])) {
			// if we are a custom shortcode fetch the record so we can display the correct options
			$options = PL_Shortcode_CPT::get_shortcode_options('featured_listings', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
				$property_ids = self::get_property_ids($atts['id']);
				if (!empty($property_ids)) {
					$atts['property_ids'] = array_keys($property_ids);
				}
			}
			else {
				unset($atts['id']);
			}
		}
		$atts = wp_parse_args($atts, array('context' => 'shortcode', 'limit' => 0, 'sort_type' => ''));

		// add template formatting
		$header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($atts['context'], 'featured_listings');
		if (!empty($template['before_widget']) && empty($_GET['embedded'])) {
			$header = $template['before_widget'].$header;
		}
		if (!empty($template['css'])) {
			$header = '<style type="text/css">'.$template['css'].'</style>'.$header;
		}
		if (!empty($template['after_widget']) && empty($_GET['embedded'])) {
			$footer .= $template['after_widget'];
		}

		// namespace the context:
		if (!empty($atts['context'])) {
			$atts['context'] = 'featured_listings_'.$atts['context'];
		}
		ob_start();
		// output listings formatted w/ template
		echo PLS_Partials::get_listings($atts);
		return do_shortcode($header.ob_get_clean().$footer);
	}

	/**
	 * Generate static_listings shortcode output
	 */
	public static function static_listings_entity( $atts, $filters = '' ) {

		if (empty($atts['id'])) {
			// default filter options
			$filters_string = '';
		}
		else {
			// if we are a custom shortcode fetch the record so we can display the correct filters
			// for the js
			$listing_filters = PL_Shortcode_CPT::get_shortcode_filters('static_listings', $atts['id']);
			$filters_string = self::convert_filters( $listing_filters );
			// and template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options('static_listings', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		// get default values
		$sc_attrs = PL_Shortcode_CPT::get_shortcode_attrs('static_listings');
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($vals['default'])) {
				$atts[$key] = $vals['default'];
			}
			if (!empty($atts[$key]) && $vals['type']=='multiselect') {
				if (!is_array($atts[$key])) {
					$atts[$key] = array_map('trim',explode(",", $atts[$key]));
				}
				$values = array();
				$optvals = get_option('pl_static_listings_formval_'.$key, $vals['options']);
				foreach($atts[$key] as $okey) {
					if (!empty($optvals[$okey])) {
						$values[$okey] = $optvals[$okey];
					}
				}
				if (empty($values)) {
					unset($atts[$key]);
				}
				else {
					$atts[$key] = $values;
				}
			}
		}
		$atts = wp_parse_args($atts, array('id' => 0, 'query_limit' => 5, 'featured_id' => 'custom', 'context' => 'shortcode', 'sort_by' => 'cur_data.price', 'table_id' => 'placester_listings_list'));

		// set limit per page if any
		if( ! empty( $atts['query_limit'] ) ) {
			global $pl_listings_query_limit;
			$pl_listings_query_limit = $atts['query_limit'];
			// TODO init the js directly instead
			add_action( 'listings_limit_default', array( __CLASS__, 'add_length_limit_default'  ));
			unset ( $pl_listings_query_limit );
		}

		// add template formatting
		$header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($atts['context'], 'static_listings');
		if (!empty($template['before_widget']) && empty($_GET['embedded'])) {
			$header = $template['before_widget'].$header;
		}
		if (!empty($template['css'])) {
			$header = '<style type="text/css">'.$template['css'].'</style>'.$header;
		}
		if (!empty($template['after_widget']) && empty($_GET['embedded'])) {
			$footer .= $template['after_widget'];
		}
		// way to request template when ajax gets listings
		$atts['context'] = 'static_listings' . (empty($atts['context']) ? '' : '_'.$atts['context']);

		ob_start();
		self::hide_unnecessary_controls($atts);
		self::print_filters( $filters . $filters_string, $atts['context'] );
		echo PLS_Partials::get_listings_list_ajax($atts);
		return do_shortcode($header.ob_get_clean().$footer);
	}

	public static function add_length_limit_default() {
		global $pl_listings_query_limit;

		echo "limit_default: " . $pl_listings_query_limit . ",";
	}


	/**
	 * Generate search_listings shortcode output
	 */
	public static function search_listings_entity( $atts, $filters = '' ) {

		if (empty($atts['id'])) {
			// default filter options
			$filters_string = '';
		}
		else {
			// if we are a custom shortcode fetch the record so we can display the correct filters
			// for the js
			$listing_filters = PL_Shortcode_CPT::get_shortcode_filters('search_listings', $atts['id']);
			$filters_string = self::convert_filters( $listing_filters );
			// and template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options('search_listings', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		// get default values
		$sc_attrs = PL_Shortcode_CPT::get_shortcode_attrs('search_listings');
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($vals['default'])) {
				$atts[$key] = $vals['default'];
			}
			if (!empty($atts[$key]) && $vals['type']=='multiselect') {
				if (!is_array($atts[$key])) {
					$atts[$key] = array_map('trim',explode(",", $atts[$key]));
				}
				$values = array();
				$optvals = get_option('pl_search_listings_formval_'.$key, $vals['options']);
				foreach($atts[$key] as $okey) {
					if (!empty($optvals[$okey])) {
						$values[$okey] = $optvals[$okey];
					}
				}
				if (empty($values)) {
					unset($atts[$key]);
				}
				else {
					$atts[$key] = $values;
				}
			}
		}
		$atts = wp_parse_args($atts, array('context' => 'shortcode', 'sort_by' => 'cur_data.price'));

		// set limit per page if any
		if( ! empty( $atts['query_limit'] ) ) {
			global $pl_listings_query_limit;
			$pl_listings_query_limit = $atts['query_limit'];
			// TODO init the js directly instead
			add_action( 'listings_limit_default', array( __CLASS__, 'add_length_limit_default'  ));
			unset ( $pl_listings_query_limit );
		}

		// add template formatting
		$header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($atts['context'], 'search_listings');
		if (!empty($template['before_widget']) && empty($_GET['embedded'])) {
			$header = $template['before_widget'].$header;
		}
		if (!empty($template['css'])) {
			$header = '<style type="text/css">'.$template['css'].'</style>'.$header;
		}
		if (!empty($template['after_widget']) && empty($_GET['embedded'])) {
			$footer .= $template['after_widget'];
		}
		// way to request template when ajax gets listings
		$atts['context'] = 'search_listings' . (empty($atts['context']) ? '' : '_'.$atts['context']);

		ob_start();
		self::print_filters( $filters . $filters_string, $atts['context'] );
		PLS_Partials_Get_Listings_Ajax::load($atts);
		return do_shortcode($header.ob_get_clean().$footer);
	}

	/**
	 * Generate search_map shortcode output
	 */
	public static function search_map_entity( $atts ) {

		if (!empty($atts['id'])) {
			// get template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options('search_map', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		// get default values
		$sc_attrs = PL_Shortcode_CPT::get_shortcode_attrs('search_map');
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($vals['default'])) {
				$atts[$key] = $vals['default'];
			}
		}
		$atts = wp_parse_args($atts, array('context' => 'shortcode', 'type' => 'listings', 'sync_map_to_list' => false));

		// add template formatting
		$header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($atts['context'], 'search_map');
		if (!empty($template['before_widget']) && empty($_GET['embedded'])) {
			$header = $template['before_widget'].$header;
		}
		if (!empty($template['css'])) {
			$header = '<style type="text/css">'.$template['css'].'</style>'.$header;
		}
		if (!empty($template['after_widget']) && empty($_GET['embedded'])) {
			$footer .= $template['after_widget'];
		}

		$encoded_atts = array(
				'width' => $atts['width'],
				'height' => $atts['height'],
				'type' => $atts['type']
		);

		$encoded_atts = json_encode( $encoded_atts );

		ob_start();
		?>
		<script type="text/javascript">
		jQuery(document).ready(function( $ ) {

			var map = new Map ();
			var json_atts = jQuery.parseJSON(' <?php echo $encoded_atts; ?> ');

			// var filter = new Filters ();
			var listings = new Listings ({
				map: map,
				<?php if( $atts['sync_map_to_list'] ): ?>sync_map_to_list: true, <?php endif; ?>
				<?php // echo "property_ids: ['" . implode("','", $property_ids) . "'],"; ?>
				// filter: filter,
			});
			if(json_atts.type == 'lifestyle') {
				var lifestyle = new Lifestyle( {
					map: map
				});
			}
			if(json_atts.type == 'lifestyle_polygon' ) {
				var lifestyle_polygon = new Lifestyle_Polygon( {
					map: map
				});
			}

			// var status = new Status_Window ({map: map, listings:listings});

			// fill map init args
			var init_args = new Object();

			init_args.type = json_atts.type;
			init_args.listings = listings;
			init_args.status_window = status;

			if( json_atts.type == 'lifestyle' ) {
				init_args.lifestyle = lifestyle;
			}
			else if( json_atts.type == 'lifestyle_polygon' ) {
				init_args.lifestyle_polygon = lifestyle_polygon;
			}

			// init maps
			map.init( init_args );
			// type: 'neighborhood',
			// type: 'listings',

			listings.init();

		});
		</script>
		<?php
		$listings = null;
		echo PLS_Map::listings( $listings, array('width' => $atts['width'], 'height' => $atts['height']) );
		return $header.ob_get_clean().$footer;
	}

	/**
	 * Generate listing_slideshow shortcode output
	 */
	public static function listing_slideshow( $atts, $default_style = true ) {

		// fix attribute name case so js slideshow gets correct value names
		$sc_attrs = PL_Shortcode_CPT::get_shortcode_attrs('listing_slideshow');
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($atts[strtolower($key)])) {
				$atts[$key] = $atts[strtolower($key)];
				unset($atts[strtolower($key)]);
			}
		}

		if (!empty($atts['id'])) {
			// get template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options('listing_slideshow', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		// get default values
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($vals['default'])) {
				$atts[$key] = $vals['default'];
			}
		}
		$atts = wp_parse_args($atts, array(
				'startClockOnMouseOut' => true,			// if clock should start on MouseOut
				'startClockOnMouseOutAfter' => 1000,	// how long after MouseOut should the timer start again
				'directionalNav' => true,				// manual advancing directional navs
				'captions' => true,						// do you want captions?
				'captionAnimation' => 'fade',			// fade, slideOpen, none
				'captionAnimationSpeed' => 800,			// if so how quickly should they animate in
				'afterSlideChange' => 'function(){}',	// empty function
				'bullets' => 'false',
				'context' => 'shortcode',
				'featured_option_id' => 'slideshow-featured-listings',
				'listings' => 'limit=5&is_featured=true&sort_by=price'
		));

		// basic slideshow style
		$css = '';
		if ($default_style) {
			$css = '
				.orbit-wrapper .orbit-caption {
				z-index: 999999 !important;
				margin-top: -113px;
				position: absolute;
				right: 0;
				bottom: 0;
				width: 100%;
				}
				.orbit-caption {
				display: none;
				}';
		}
		// add template formatting
		$header = $footer = '';
		$template = PL_Shortcode_CPT::load_template($atts['context'], 'listing_slideshow');
		if (!empty($template['css'])) {
			$css .= $template['css'];
		}
		if ($css) {
			$header = '<style type="text/css">'.$css.'</style>';
		}
		if (!empty($template['before_widget']) && empty($_GET['embedded'])) {
			$header .= $template['before_widget'];
		}
		if (!empty($template['after_widget']) && empty($_GET['embedded'])) {
			$footer .= $template['after_widget'];
		}

		ob_start();
		echo PLS_Slideshow::slideshow($atts);
		return $header.ob_get_clean().$footer;
	}

	/**
	 * Fetch fields for formatting individual items in the listing_slideshow shortcode output
	 */
	public static function listing_slideshow_sub_entity( $atts, $content, $tag ) {
		if( empty( self::$listing ) ) {
			return '';
		}

		$listing = self::$listing;

		if( $tag === 'ls_index' ) {
			return self::$slideshow_caption_index;
		} else if( $tag === 'ls_url' ) {
			return $listing['cur_data']['url'];

		} else if( $tag === 'ls_address' ) {
			return $listing['location']['address'];

		} else if( $tag === 'ls_beds' ) {
			return $listing['cur_data']['beds'];

		} else if( $tag === 'ls_baths' ) {
			return $listing['cur_data']['baths'];
		}

		return '';
	}

	public static function neighborhood_sub_entity( $atts, $content, $tag ) {
		$val = '';

		// blank term - shouldn't happen
		if( empty( self::$neighborhood_term ) ) {
			return '';
		}

		$term = self::$neighborhood_term;
		$taxonomy_name = $term->taxonomy;

		if( $tag === 'nb_title' ) {
			$val = apply_filters( 'pls_neighborhood_title', $term->name );
		} else if( $tag === 'nb_description' ) {
			$val = apply_filters( 'pls_neighborhood_description', $term->description );
		} else if( $tag === 'nb_featured_image' ) {
			// take the first off the listing, otherwise - default
			$taxonomy_maps_name = self::translate_taxonomy_type( $term->taxonomy );
			$term_name = $term->name;

			$api_response = PLS_Plugin_API::get_listings_list(
						array( 'location[' . $taxonomy_maps_name . ']' => $term_name, 'limit' => 1 ) );

			$featured_image_src = PLS_IMG_URL . '/null/listing-300x180.jpg';

			if( ! empty( $api_response['listings'] ) &&
				! empty( $api_response['listings'][0] ) &&
				! empty( $api_response['listings'][0]['images'] )
			) {
				$featured_image_src = $api_response['listings'][0]['images'][0]['url'];
			}

			$val = "<img src='$featured_image_src'></img>";
		} else if( $tag === 'nb_link' ) {
			$term_link = get_term_link( $term );
			if( ! is_wp_error( $term_link ) ) {
				$val = $term_link;
			}
		} else if( $tag === 'nb_map' ) {
			ob_start();
			$taxonomy_maps_name = self::translate_taxonomy_type( $taxonomy_name );

			?>
			<script type="text/javascript">
			if (typeof bootloader !== 'object') {
				var bootloader;
			}

			jQuery(document).ready(function( $ ) {
				var map = new Map();
				var listings = new Listings({
					map: map
				});
				debugger;

				var neighborhood = new Neighborhood({
					map: map,
					type: '<?php echo $taxonomy_maps_name; ?>',
					name: '<?php echo $term->name; ?>',
					slug: '<?php echo $term->slug; ?>'
				});

				map.init({
					type: 'neighborhood',
					neighborhood: neighborhood,
					listings: listings
				});

				if (typeof bootloader !== 'object') {
					bootloader = new SearchLoader();
					bootloader.add_param({map: map});
				} else {
					bootloader.add_param({map: map});
				}

				listings.init();

			});
			</script>
			<?php
			echo PLS_Map::polygon( null, array(
					'width' => 629,
					'height' => 303,
					'zoom' => 16,
					'polygon_search' => $taxonomy_name,
					'polygon' => $term->slug,
					'loading_overlay' => '<div id="spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div></div>',
					'class' => 'polygon_search')
				);
			$val = ob_get_clean();
		}

		return $val;
	}

	/**
	 * Helper function for formatting individual listing fields.
	 * self::$listing should contain the listing values.
	 */
	public static function listing_sub_entity( $atts, $content, $tag ) {
		$listing_list = array();

		if( !empty( self::$listing ) ) {
			$listing_list = self::$listing;
		} else if ( !empty( PL_Shortcodes::$listing ) ) {
			$listing_list = PL_Shortcodes::$listing;
		} else {
			return;
		}

		if (array_key_exists($tag, $listing_list['cur_data'])) {
			$val = $listing_list['cur_data'][$tag];
		}else if (array_key_exists($tag, $listing_list['location'])) {
			$val = $listing_list['location'][$tag];
		}else if (array_key_exists($tag, $listing_list['contact'])) {
			$val = $listing_list['contact'][$tag];
		}else if (array_key_exists($tag, $listing_list['rets'])) {
			$val = $listing_list['rets'][$tag];
		}
		else {
			$val = '';
		}

		// This is an example of handling a specific tag in a different way
		// TODO: make this more elegant...
		switch ($tag) {
			case 'desc':
				$max_len = !empty($atts['maxlen']) ? (int)$atts['maxlen'] : 500;
				$val = substr($val, 0, $max_len);
				break;
			case 'image':
				$width = !empty($atts['width']) ? (int)$atts['width'] : 180;
				$height = !empty($atts['height']) ? (int)$atts['height'] : 120;
				$val = PLS_Image::load(!empty($listing_list['images'][0]['url']) ? $listing_list['images'][0]['url'] : '',
					array('resize' => array('w' => $width, 'h' => $height),
						'fancybox' => true,
						'as_html' => true,
						'html' => array('alt' => empty($listing_list['location']['full_address']) ? $listing_list['location']['address'] : $listing_list['location']['full_address'], 'itemprop' => 'image')));
				break;
			case 'gallery':
				ob_start();
				?>
				<div id="slideshow" class="clearfix theme-default left bottomborder">
					<div class="grid_8 alpha">
						<ul class="property-image-gallery grid_8 alpha">
							<?php foreach ($listing_list['images'] as $image): ?>
							<li><?php echo PLS_Image::load($image['url'],
									array('resize' => array('w' => 100, 'h' => 75),
										'fancybox' => true,
										'as_html' => false,
										'html' => array('itemprop' => 'image'))); ?>
							</li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
				<?php
				$val = ob_get_clean();
				break;
			case 'map':
				$width = !empty($atts['width']) ? (int)$atts['width'] : 590;
				$height = !empty($atts['height']) ? (int)$atts['height'] : 250;
				if (!empty($atts['type']) && $atts['type']=='lifestyle') {
					$val = PLS_Map::lifestyle($listing_list, array('width' => $width, 'height' => $height, 'zoom' => 16, 'life_style_search' => true,
					'show_lifestyle_controls' => true, 'show_lifestyle_checkboxes' => true,
					'lat' => $listing_list['location']['coords'][0], 'lng' => $listing_list['location']['coords'][1]));
				}
				else {
					$val = PLS_Map::dynamic($listing_list, array('width' => $width, 'height' => $height, 'zoom' => 16, 'life_style_search' => true,
					'show_lifestyle_controls' => false, 'show_lifestyle_checkboxes' => false,
					'lat' => $listing_list['location']['coords'][0], 'lng' => $listing_list['location']['coords'][1]));
				}
				break;
			case 'price':
				$val = PLS_Format::number($listing_list['cur_data']['price'], array('abbreviate' => false, 'add_currency_sign' => true));
				break;
			case 'listing_type':
				$val = PLS_Format::translate_property_type($listing_list);
				break;
			case 'amenities':
				$amenities = PLS_Format::amenities_but($listing_list, array('half_baths', 'beds', 'baths', 'url', 'sqft', 'avail_on', 'price', 'desc'));
				$amen_type = empty($atts['type']) ? 'list' : (string)$atts['type'];
				ob_start();
				?>
				<div class="amenities-section grid_8 alpha">
					<ul>
						<?php if (is_array($amenities[$amen_type])): ?>
						<?php $amenities[$amen_type] = PLS_Format::translate_amenities($amenities[$amen_type]); ?>
						<?php foreach ($amenities[$amen_type] as $amenity => $value): ?>
						<li><span><?php echo $amenity; ?> </span> <?php echo $value ?></li>
						<?php endforeach ?>
						<?php endif ?>
					</ul>
				</div>
				<?php
				$val = ob_get_clean();
				break;
			case 'compliance':
				ob_start();
				PLS_Listing_Helper::get_compliance(array('context' => 'inline_search',
					'agent_name' => $listing_list['rets']['aname'] ,
					'office_name' => $listing_list['rets']['oname'],
					'office_phone' => PLS_Format::phone($listing_list['contact']['phone'])));
				$val = ob_get_clean();
				break;
			case 'favorite_link_toggle':
				$val = PLS_Plugin_API::placester_favorite_link_toggle(array('property_id' => $listing_list['id']));
				break;
			case 'custom':
				// TODO: format based on data type
				if (!empty($atts['attribute'])) {
					if (empty($atts['group']) && isset($listing_list[$atts['attribute']])) {
						$val = $listing_list[$atts['attribute']];
					}
					elseif (!empty($atts['group']) && isset($listing_list[$atts['group']]) && isset($listing_list[$atts['group']][$atts['attribute']])) {
						$val = $listing_list[$atts['group']][$atts['attribute']];
					}
					if ($val == '' && !empty($atts['value'])) {
						$val = $atts['value'];
					}
					if (!empty($atts['type'])) {
						switch($atts['type']) {
							case 'list':
								$vals = array_map('trim', explode(',', $val));
								if (!empty($vals[0])) {
									$val = '<ul>';
									foreach($vals as $item) {
										$val .= '<li>'.$item.'</li>';
									}
									$val .= '</ul>';
								}
								break;
							case 'currency':
								$val = PLS_Format::number($val, array('abbreviate' => false, 'add_currency_sign' => true));
								break;
						}
					}
				}
				else {
					$val = '[custom]';
				}
			default:
				// print as is
		}

		return $val;
	}

	public static function pl_neighborhood_entity( $atts ) {
		ob_start();
		$taxonomy_type = 'state';
		$taxonomy = null;
		$term_slug = '';
		$term_name = '';
		$neighborhood_term = '';

		// Type of neighborhood is set as radio_type from the radio box in the admin
		// get key and value to test for neighborhood object
		if( ! isset( $atts['radio_type'] ) ) {
			return;
		}
		$key = $atts['radio_type'];
		if( ! isset( $atts['nb_select_' . $key] ) ) {
			return;
		}
		$value = $atts['nb_select_' . $key];

		// API searches for neighborhood by slug
		if( in_array( $key, array( 'state', 'city', 'neighborhood', 'zip', 'street' ) ) ) {
			$term = get_term_by('id', $value, $key);
			if( ! empty( $term ) ) {
				$taxonomy_type = $key;
				$taxonomy = get_taxonomy( $key );
				$atts[$key] = $term->slug;
				$term_slug = $term->slug;
				$term_name = $term->name;
				$neighborhood_term = $term;
			}
		}

		if( empty( $taxonomy ) ) {
			return;
		}

		$taxonomy_maps_type = self::translate_taxonomy_type( $taxonomy_type );

		$args = wp_parse_args($atts, array('state' => false, 'city' => false,
			'neighborhood' => false, 'zip' => false, 'street' => false, 'image_limit' => 20,
			'width' => 400, 'height' => 400, 'zoom' => 14, 'context' => false, 'context_var' => '' ));

		?>
		<script type="text/javascript">
			if (typeof bootloader !== 'object') {
				var bootloader;
			}

			jQuery(document).ready(function( $ ) {
				var taxonomy = jQuery.parseJSON(' <?php echo json_encode( $taxonomy ); ?> ');
				var map = new Map();
				var listings = new Listings({
					map: map
				});
				debugger;

				var neighborhood = new Neighborhood({
					map: map,
					type: '<?php echo $taxonomy_maps_type; ?>',
					name: '<?php echo $term_name; ?>',
					slug: '<?php echo $term_slug; ?>'
				});

				map.init({
					type: 'neighborhood',
					neighborhood: neighborhood,
					listings: listings
				});

				if (typeof bootloader !== 'object') {
					bootloader = new SearchLoader();
					bootloader.add_param({map: map});
				} else {
					bootloader.add_param({map: map});
				}

				listings.init();

			});
		</script>
		<?php

		echo PLS_Map::polygon( null, array(
					'width' => 629,
					'height' => 303,
					'zoom' => 16,
					'polygon_search' => $taxonomy->name,
					'polygon' => $taxonomy->rewrite['slug'],
					'loading_overlay' => '<div id="spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div></div>',
					'class' => 'polygon_search')
				);
		$neighborhood_html = ob_get_clean();

		$neighborhood_html = apply_filters( pls_get_merged_strings(
						array( 'pls_neighborhood_html', $args['context'] ), '_', 'pre', false ),
						$neighborhood_html, $neighborhood_term, $args['context'], $args['context_var'] );

		return $neighborhood_html;
	}

	/**
	 * Generate output for search_form shortcode
	 */
	public static function search_form_entity( $atts ) {

		if (!empty($atts['id'])) {
			// get template and other attributes
			$options = PL_Shortcode_CPT::get_shortcode_options('search_form', $atts['id']);
			if ($options!==false) {
				$atts = wp_parse_args($atts, $options);
			}
			else {
				unset($atts['id']);
			}
		}
		// get default values
		$sc_attrs = PL_Shortcode_CPT::get_shortcode_attrs('search_form');
		foreach ($sc_attrs['options'] as $key=>$vals) {
			if (empty($atts[$key]) && !empty($vals['default'])) {
				$atts[$key] = $vals['default'];
			}
		}
		$atts = wp_parse_args($atts, array('context' => 'shortcode'));

		// Setup form action
		$form_data = array('action'=>'');
		// Handle attributes using shortcode_atts...
		$form_action = esc_url( home_url( '/' ) ) . 'listings';
		if( isset( $atts['form_action_url'] ) ) {
			$form_data['action'] = $atts['form_action_url'];
		}
		// use the form action from the metabox if AJAX is disabled
		if( isset( $atts['ajax'] ) && $atts['ajax'] == 'true' && isset( $atts['formaction'] ) ) {
			$form_data['action'] = $atts['formaction'];
		}
		$atts['form_data'] = (object)$form_data;
		// add context and ajax support if missing
		if( isset( $atts['ajax'] ) ) {
			$atts['ajax'] = true;
			$atts['context_var']['header'] = '
			<script type="text/javascript" src="'.trailingslashit(PLS_JS_URL).'scripts/filters.js"></script>
			<script type="text/javascript">
				if (typeof bootloader !== \'object\') {
					var bootloader;
				}

				jQuery(document).ready(function( $ ) {
					if (typeof bootloader !== \'object\') {
						bootloader = new SearchLoader();
						bootloader.add_param({filter: {context: "'.$atts['context'].'"}});
					} else {
						bootloader.add_param({filter: {context: "'.$atts['context'].'"}});
					}
				});
			</script>
			';
		} else {
			$atts['ajax'] = false;
		}

		return PLS_Partials_Listing_Search_Form::init($atts);
	}

	/**
	 * Helpers
	 */

	private static function get_property_ids( $featured_listing_id ) {
		// if( ! is_int( $featured_listing_id ) ) { }
		$values = get_post_custom( $featured_listing_id );

		$property_ids = isset( $values['keatingbrokerage_meta'] ) ? @unserialize($values['keatingbrokerage_meta'][0]) : '';
		$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? @json_decode($values['pl_featured_listing_meta'][0], true) : '';
		// $pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? array('listings' => array()) : $pl_featured_listing_meta['featured-listings-type'];
		// $pl_featured_meta_value = empty( $pl_featured_listing_meta ) ? array('listings' => array()) : @json_decode($pl_featured_listing_meta[0], true);

		if( empty( $pl_featured_listing_meta ) ) {
			$pl_featured_listing_meta = isset( $values['pl_featured_listing_meta'] ) ? @unserialize($values['pl_featured_listing_meta'][0]) : '';
			if( empty( $pl_featured_listing_meta ) ) {
				return array( );
			}
		}

		// remove the top array key if any
		if( isset( $pl_featured_listing_meta['featured-listings-type'] ) ) {
			$pl_featured_listing_meta = $pl_featured_listing_meta['featured-listings-type'];
		}

		return $pl_featured_listing_meta;
	}

	private static function print_filters( $static_listing_filters, $context = 'listings_search' ) {

		wp_enqueue_script('filters-featured.js', trailingslashit(PLS_JS_URL) . 'scripts/filters.js', array('jquery'));
		?>
		<script type="text/javascript">

			jQuery(document).ready(function( $ ) {

				var list = new List ();
				var filter = new Filters ();
				var listings = new Listings ({
					filter: filter,
					<?php echo do_action('featured_filters_featured_ids'); ?>
					list: list
				});

				filter.init({
					dom_id : "#pls_search_form_listings",
					'class' : ".pls_search_form_listings",
					list : list,
					listings : listings
				});

				list.init({
					dom_id: '#placester_listings_list',
					filter : filter,
					'class': '.placester_listings_list',
					listings: listings,
					<?php echo do_action('listings_limit_default'); ?>
					context: '<?php echo $context; ?>'
				});


				<?php
				if( !empty( $static_listing_filters ) ) {
						echo $static_listing_filters;
				}
				?>
				listings.init();

			});

		</script>
		<?php
	}

	public static function partial_one( $listing, $featured_listing_id ) {

		$property_ids = PL_Component_Entity::get_property_ids( $featured_listing_id );
		$property_ids = array_flip( $property_ids );

		$api_response = PLS_Plugin_API::get_listings_details_list(array('property_ids' => $property_ids));
		//response is expected to be of fortmat api response
		//no addiitonal formatting needed.
		return $api_response;
	}

	public static function print_property_listing_args() {
		global $property_ids;
		echo "property_ids: ['" . implode("','", $property_ids) . "'],";
	}

	private static function convert_filters( $filters ) {
		ob_start();
		if( is_array( $filters ) ) {
			foreach( $filters as $top_key => $top_value ) {
				if( is_array( $top_value ) ) {
					if ($top_key == 'custom') {
						// we store custom data as custom but it uses filter name metadata
						$top_key = 'metadata';
					}
					foreach( $top_value as $key => $value ) {
						echo 'listings.default_filters.push( { "name": "' . $top_key . '[' .  $key . ']", "value" : "'. $value . '" } );';
					}
				} else {
					echo 'listings.default_filters.push( { "name": "'. $top_key . '", "value" : "'. $top_value . '" } );';
				}
			}
		}
		return ob_get_clean();
	}


	/**
	 * Templating functions for the shortcodes
	 */


	/**
	 * Format single featured listing
	 */
	public static function featured_listings_templates( $item_html, $listing, $context_var ) {
		$shortcode = 'featured_listings';
		self::$listing = $listing;

		// get the template attached as a context arg, 30 is the length of the filter prefix
		$template = substr(current_filter(), 30);

		$snippet_body = PL_Shortcodes::get_active_snippet_body( $shortcode, $template );
		if (empty($snippet_body)) {
			return $item_html;
		}
		return PL_Featured_Listings_CPT::do_templatetags($snippet_body, $listing);
	}

	/**
	 * Format the search form body using any template we might have.
	 * Called from PLS_Partials_Listing_Search_Form
	 */
	public static function search_form_inner_template($form, $form_html, $form_options, $section_title, $context_var) {
		$shortcode = 'search_form';
		self::$form_html = $form_html;
		PL_Shortcodes::$form_html = $form_html;

		// get the template attached as a context arg, 31 is the length of the filter prefix
		$template_id = substr(current_filter(), 31);

		$template = PL_Shortcode_CPT::load_template($template_id, $shortcode);
		if (empty($template['snippet_body'])) {
			return $form;
		}
		return PL_Form_CPT::do_templatetags($template['snippet_body'], $form_html);
	}

	/**
	 * Format and style the search form using any template we might have.
	 * Called from PLS_Partials_Listing_Search_Form
	 */
	public static function search_form_outer_template($form, $form_html, $form_options, $section_title, $form_data, $form_id, $context_var) {
		// get the template attached as a context arg, 31 is the length of the filter prefix
		$template_id = substr(current_filter(), 31);
		$template = PL_Shortcode_CPT::load_template($template_id, 'search_form');

		// form enclosure and add template formatting
		$header = $footer = '';
		if (!empty($context_var['header'])) {
			$header .= $context_var['header'];
		}
		if (!empty($template['css'])) {
			$header .= '<style type="text/css">'.$template['css'].'</style>';
		}
		if (!empty($template['before_widget'])) {
			$header .= $template['before_widget'];
		}
		if (!empty($template['after_widget'])) {
			$footer = $template['after_widget'];
		}
		return $header.$form.$footer;
	}

	/**
	 * Format single slideshow caption
	 */
	public static function listing_slideshow_templates( $caption_html, $listing, $context, $context_var, $index ) {
		$shortcode = 'listing_slideshow';
		self::$listing = $listing;
		self::$slideshow_caption_index = $index;

		$snippet_body = PL_Shortcodes::get_active_snippet_body( $shortcode, $context );
		if (empty($snippet_body)) {
			return $caption_html;
		}
		return do_shortcode($snippet_body);
	}

	// that would work fine for output styling, not caption-specific
	public static function listing_slideshow_templates3( $html, $data, $context, $context_var, $args ) {
		$shortcode = 'listing_slideshow';
		if( ! isset( $data['listing'] ) ) {
			return '';
		}
		self::$listing = $data['listing'];

		$snippet_body = PL_Shortcodes::get_active_snippet_body( $shortcode, $context );
		return do_shortcode($snippet_body . $html);
	}

	/**
	 * Generate individual items for search_listings, static_listings and featured_listings shortcodes
	 */
	public static function search_listings_templates( $item_html, $listing, $context_var ) {
		$shortcode = 'search_listings';

		// get the template attached as a context arg, 33 is the length of the filter prefix
		$template = substr(current_filter(), 33);

		if( false !== strpos($template, 'static_listings_' ) ) {
			$template = substr( $template, 16 );
			$shortcode = 'static_listings';
		} else if( false !== strpos( $template, 'search_listings_' ) ) {
			$template = substr( $template, 16 );
		}

		$snippet_body = PL_Shortcodes::get_active_snippet_body( $shortcode, $template );
		if (empty($snippet_body)) {
			return $item_html;
		}
		return PL_Search_Listing_CPT::do_templatetags($snippet_body, $listing);
	}

	/**
	 * Neighborhoods and their templates
	 */
	public static function neighborhood_templates( $neighborhood_html, $term, $context, $context_var ) {
		$shortcode = 'pl_neighborhood';
		self::$neighborhood_term = $term;

		// get the template attached as a context arg, 33 is the length of the filter prefix
		$template = $context;

		$snippet_body = PL_Shortcodes::get_active_snippet_body($shortcode, $template);
		return do_shortcode($snippet_body);
	}

	public static function compliance_entity( $atts ) {
		$content = '';
		if( !empty( self::$listing ) ) {
			$listing = self::$listing;
			ob_start();
			PLS_Listing_Helper::get_compliance(array(
					'context' => 'inline_search',
					'agent_name' => $listing['rets']['aname'],
					'office_name' => $listing['rets']['oname'],
					'office_phone' => PLS_Format::phone($listing['contact']['phone']),
					'agent_license' => ( isset( $listing['rets']['alicense'] ) ? $listing['rets']['alicense'] : false ),
					'co_agent_name' => ( isset( $listing['rets']['aconame'] ) ? $listing['rets']['aconame'] : false ),
					'co_office_name' => ( isset( $listing['rets']['oconame'] ) ? $listing['rets']['oconame'] : false )
				));

			// No compliance found
			if( ! isset( $_POST['compliance_message'] ) ) {
				return $content;
			}
			ob_clean();
			ob_start();

			$compliance_message = wp_kses_post($_POST['compliance_message']);
			$compliance_message = wp_parse_args($compliance_message, array(
					'agent_name' => false,
					'office_name' => false,
					'office_phone' => false,
					'img' => false,
					'disclaimer' => false,
					'agent_license' => false,
					'co_agent_name' => false,
					'co_office_name' => false
				));
			?>
			<div class="clear"></div>
			<div class="compliance-wrapper">
				<?php if ($compliance_message['img']): ?>
				<img src="<?php echo $compliance_message['img'] ?>" alt="">
				<?php endif ?>
				<?php if ($compliance_message['agent_name']): ?>
				<p class="inline-compliance">
					Listing Agent:
					<?php echo $compliance_message['agent_name'] ?>
				</p>
				<?php endif ?>
				<?php if (!empty($compliance_message['agent_license'])): ?>
				<p class="inline-compliance">
					DRE#:
					<?php echo $compliance_message['agent_license'] ?>
				</p>
				<?php endif ?>
				<?php if (!empty($compliance_message['office_name'])): ?>
				<p class="inline-compliance">
					Courtesy of:
					<?php echo $compliance_message['office_name'] ?>
				</p>
				<?php endif ?>
				<?php if (!empty($compliance_message['office_phone'])): ?>
				<p class="inline-compliance">
					Agent Phone:
					<?php echo $compliance_message['office_phone'] ?>
				</p>
				<?php endif ?>
				<?php if (!empty($compliance_message['co_agent_name'])): ?>
				<p class="inline-compliance">
					Co-Listing Agent:
					<?php echo $compliance_message['co_agent_name'] ?>
				</p>
				<?php endif ?>
				<?php if (!empty($compliance_message['co_office_name'])): ?>
				<p class="inline-compliance">
					Co-Listing Office:
					<?php echo $compliance_message['co_office_name'] ?>
				</p>
				<?php endif ?>
				<?php if ($compliance_message['disclaimer']): ?>
				<p class="disclaimer">
					<?php echo $compliance_message['disclaimer'] ?>
				</p>
				<?php endif ?>
			</div>
			<div class="clear"></div>
			<?php
			$content = ob_get_clean();
		}

		return $content;
	}

	/**
	 * Convert the Neighborhood taxonomy type to a Maps-accepted one
	 * @param string $taxonomy_type
	 */
	public static function translate_taxonomy_type( $taxonomy_type ) {
		switch( $taxonomy_type ) {
			case 'city': return 'locality';
			case 'zip': return 'postal';
			case 'state': return 'region';
			default: return $taxonomy_type;
		}
		return $taxonomy_type;
	}

	/**
	 * Helper, add CSS to template to hide dropdowns
	 */
	public static function hide_unnecessary_controls( $atts ) {

		$css = '<style type="text/css">';

		if( ! empty( $atts ) ) {

			if( ! empty( $atts['hide_sort_by'] ) && $atts['hide_sort_by'] == 'true' ) {
				$css .= '.sort_wrapper .sort_item:first-child { display: none; } ';
			}
			if( ! empty( $atts['hide_sort_direction'] ) && $atts['hide_sort_direction'] == 'true' ) {
				$css .= '.sort_wrapper .sort_item:last-child { display: none; } ';
			}
			if( ! empty( $atts['hide_num_results'] ) && $atts['hide_num_results'] == 'true' ) {
				$css .= '#placester_listings_list_length { display: none; } ';
			}
		}

		$css .= '</style>';

		echo $css;
	}
}
