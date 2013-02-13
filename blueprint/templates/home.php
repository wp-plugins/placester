<?php
/**
 * Home Template
 *
 * This is the home template.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */
?>
<script type="text/javascript">
    	jQuery(document).ready(function( $ ) {
    		
    		var listing_list = new List ();
    		var map = new Map (); 
    		// var filter = new Filters ();
    		var listings = new Listings ({
    			map: map,
    			// filter: filter,
    			list: listing_list
    		});
            // status.init({map: map, listings:listings});
            var neighborhood = new Neighborhood ({map: map,
             // slug: 'blackstone'
            });
            var status = new Status_Window ({map: map, listings:listings});
            
            
    		listing_list.init({
    			dom_id: '#placester_listings_list',
    			// filter : filter,
    			class: '.another',
    			listings: listings,
                map: map,
                limit_default: 25
    		});
            map.init({
                // type: 'lifestyle',
                // type: 'lifestyle_polygon',
                // type: 'neighborhood',
                type: 'listings',
                neighborhood: neighborhood,
                // lifestyle: lifestyle,
                listings: listings,
                // lifestyle_polygon: lifestyle_polygon,
                list: listing_list,
                status_window: status
            });

    		listings.init();
    		
    	});
    </script>
<div id="slideshow" class="clearfix theme-default left bottomborder grid_8 alpha">
    <?php //echo PLS_Map::lifestyle(array(), array('width' => 590, 'height' => 300, 'life_style_search' => true,'show_lifestyle_controls' => true, 'show_lifestyle_checkboxes' => true, 'show_submit' => true, 'lat' => '41.815594', 'lng' => '-71.413879' ) ); ?>
    <?php //echo PLS_Map::lifestyle_polygon(array(), array('width' => 590, 'height' => 300, 'life_style_search' => true,'show_lifestyle_controls' => true, 'show_lifestyle_checkboxes' => true, 'show_submit' => true, 'lat' => '41.815594', 'lng' => '-71.413879' ) ); ?>
    <?php echo PLS_Map::listings( null, array('width' => 950, 'height' => 400) ); ?>
    <?php //echo PLS_Map::polygon(null, array('width' => 590, 'height' => 250, 'zoom' => 16,'map_js_var' => 'test2', 'canvas_id' => 'another2', 'polygon_search' => 'neighborhood', 'search_class' => 'another')); ?>

    <?php echo PLS_Partials::get_listings_search_form( array(
	    	'context' => 'home_map',
	    	'search_id' => 'another',
	        'class' => 'another',
	        'bedrooms' => 1,
            'min_beds' => 0,
            'max_beds' => 0,
            'bathrooms' => 0,
            'min_baths' => 0,
            'max_baths' => 0,
            'price' => 0,
            'half_baths' => 0,
            'property_type' => 0,
            'listing_types'=> 0,
            'zoning_types' => 0,
            'purchase_types' => 0,
            'available_on' => 0,
            'cities' => 0,
            'states' => 0,
            'zips' => 0,
            'neighborhood' => 0,
            'county' => 0,
            'min_price' => 0,
            'max_price' => 0,
            'min_price_rental' => 0,
            'max_price_rental' => 0,
            'neighborhood_polygons' => 0,
    	)); ?>
	<?php echo PLS_Partials::get_listings_list_ajax(
		array(
			'context' => 'custom_listings_search',
			'table_id' => 'placester_listings_list',
			'map_js_var' => 'test2'
			)); ?> 
    <?php 
			// echo PLS_Slideshow::slideshow( array( 
			// 			'animation' => 'fade', 									// fade, horizontal-slide, vertical-slide, horizontal-push
			// 			'animationSpeed' => 800, 								// how fast animtions are
			// 			'timer' => true,												// true or false to have the timer
			// 			'pauseOnHover' => true,									// if you hover pauses the slider
			// 			'advanceSpeed' => 4000,									// if timer is enabled, time between transitions 
			// 			'startClockOnMouseOut' => true,					// if clock should start on MouseOut
			// 			'startClockOnMouseOutAfter' => 1000,		// how long after MouseOut should the timer start again
			// 			'directionalNav' => true, 							// manual advancing directional navs
			// 			'captions' => true, 										// do you want captions?
			// 			'captionAnimation' => 'fade', 					// fade, slideOpen, none
			// 			'captionAnimationSpeed' => 800, 				// if so how quickly should they animate in
			// 			'afterSlideChange' => 'function(){}',		// empty function
			// 			'width' => 620, 
			// 			'height' => 300,
			// 			'bullets' => true,
			// 			'context' => 'home',
			// 			'featured_option_id' => 'slideshow-featured-listings', 
			// 			'listings' => array('limit' => 5, 'sort_by' => 'price'),
			// 		)
			// ); 
		?>
    <?php 
			// echo PLS_Slideshow::slideshow( array( 
			// 			'animation' => 'fade', 									// fade, horizontal-slide, vertical-slide, horizontal-push
			// 			'animationSpeed' => 800, 								// how fast animtions are
			// 			'timer' => true,												// true or false to have the timer
			// 			'pauseOnHover' => true,									// if you hover pauses the slider
			// 			'advanceSpeed' => 4000,									// if timer is enabled, time between transitions 
			// 			'startClockOnMouseOut' => true,					// if clock should start on MouseOut
			// 			'startClockOnMouseOutAfter' => 1000,		// how long after MouseOut should the timer start again
			// 			'directionalNav' => true, 							// manual advancing directional navs
			// 			'captions' => true, 										// do you want captions?
			// 			'captionAnimation' => 'fade', 					// fade, slideOpen, none
			// 			'captionAnimationSpeed' => 800, 				// if so how quickly should they animate in
			// 			'afterSlideChange' => 'function(){}',		// empty function
			// 			'width' => 620, 
			// 			'height' => 300,
			// 			'bullets' => true,
			// 			'context' => 'home',
			// 			'featured_option_id' => 'slideshow-featured-listings', 
			// 			'listings' => array('limit' => 5, 'sort_by' => 'price'),
			// 			'allow_user_slides' => true,
			// 			'user_slides_header_id' => 'slideshow_1'
			// 		)
			// ); 
		?>
</div>

<div id="listing" class="grid_8 alpha">
    
    <?php //echo pls_get_listings( "limit=5&context=home" ) ?>
    <?php //echo pls_get_listings( "limit=5&featured_option_id=custom-featured-listings&context=home" ) ?>
    <?php //PLS_Listing_Helper::get_compliance(array('context' => 'listings', 'agent_name' => false, 'office_name' => false)); ?>
</div>
