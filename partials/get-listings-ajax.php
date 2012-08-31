<?php 
PLS_Partials_Get_Listings_Ajax::init();
class PLS_Partials_Get_Listings_Ajax {

	/**
     * Returns the list of listings managed by ajax. It includes pagination and 
     * 'sort by' controls.
     * 
     * The defaults are as follows:
     *     'placeholder_img' - Defaults to placeholder image. The path to the 
     *          listing image that should be use if the listing has no images.
     *     'loading_img' - Defaults to the Wordpress spinner. Path to the 
     *          loader image.
     *     'image_width' - Defaults to 100. The with of the listing image.
     *     'crop_description' - Defaults to false. Wether the description 
     *     should be cropped or not.
     *     'context' - An execution context for the function. Used when the 
     *          filters are created.
     *     'context_var' - Any variable that needs to be passed to the filters 
     *          when function is executed.
     * Defines the following hooks:
     *      pls_listings_list_ajax_item_html[_context] - Filters html for each 
     *          item in the list
     *      pls_listings_list_ajax_no_results_html[_context] - Filters what 
     *          should be displayed when no results are found.
     *      pls_listings_list_ajax_html[_context] - Filters the html for the 
     *          whole list.
     *      pls_listings_list_ajax_sort_by_options[_context] - Filters the 
     *          options from the "Sort by" select box.
     *
     * @static
     * @param array $args Optional. Overrides defaults.
     * @return string The html and js.
     * @since 0.0.1
     */
    function init() {
        // Hook the callback for ajax requests
        add_action('wp_ajax_pls_listings_ajax', array(__CLASS__, 'get' ) );
        add_action('wp_ajax_nopriv_pls_listings_ajax', array(__CLASS__, 'get' ) );

        add_action( 'wp_ajax_pls_listings_fav_ajax', array(__CLASS__,'get_favorites'));
        add_action( 'wp_ajax_nopriv_pls_listings_fav_ajax', array(__CLASS__,'get_favorites'));
    }

    function get_favorites () {
      $favorite_ids = PLS_Plugin_API::get_listings_fav_ids();
      self::get(array('property_ids' => $favorite_ids, 'allow_id_empty' => true));
    }

    function load($args = array()) {    
        // * Set the options for the "Sort by" select. 
        $defaults = array(
            'placeholder_img' => PLS_IMG_URL . "/null/listing-300x180.jpg",
            'loading_img' => admin_url( 'images/wpspin_light.gif' ),
            'image_width' => 100,
            'sort_type' => 'desc',
            'crop_description' => 0,
            'listings_per_page' => get_option( 'posts_per_page' ),
            'context' => '',
            'context_var' => NULL,
            'append_to_map' => true,
            'search_query' => $_POST,
            'table_id' => 'placester_listings_list',
            'show_sort' => true
        );

        /** Extract the arguments after they merged with the defaults. */
        extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );
        
        $sort_by_options = array('images' => 'Images','location.address' => 'Address', 'location.locality' => 'City', 'location.region' => 'State', 'location.postal' => 'Zip', 'zoning_types' => 'Zoning', 'purchase_types' => 'Purchase Type', 'listing_types' => 'Listing Type', 'property_type' => 'Property Type', 'cur_data.beds' => 'Beds', 'cur_data.baths' => 'Baths', 'cur_data.price' => 'Price', 'cur_data.sqft' => 'Square Feet', 'cur_data.avail_on' => 'Date Available');;

        // /** Filter the "Sort by" options. */
        $sort_by_options = apply_filters("pls_listings_list_ajax_sort_by_options", $sort_by_options);

        ob_start();
        ?>
            <!-- Sort Dropdown -->
            <?php if ($show_sort): ?>
              <form class="sort_wrapper">
                  <div class="sort_item">
                    <label for="sort_by">Sort By</label>
                    <select name="sort_by" id="sort_by">
                        <?php foreach ($sort_by_options as $key => $value): ?>
                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>
                  <div class="sort_item">
                    <label for="sort_by">Sort Direction</label>
                    <select name="sort_type" id="sort_dir">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                  </div>
              </form>  
            <?php endif ?>
            

            <!-- Datatable -->
            <div class="clear"></div>

            <div id="container" style="width: 99%">
              <div id="context" class="<?php echo $context ?>"></div>
              <table id="<?php echo $table_id ?>" class="widefat post fixed placester_properties" cellspacing="0">
                <thead>
                  <tr>
                    <th><span></span></th>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
        <?php
        echo ob_get_clean();
    }

	function get ($args = array()) {
	
		    // Pagination
        $_POST['limit'] = $_POST['iDisplayLength'];
        $_POST['offset'] = $_POST['iDisplayStart'];     

        /** Define the default argument array. */
        $defaults = array(
            'placeholder_img' => PLS_IMG_URL . "/null/listing-300x180.jpg",
            'loading_img' => admin_url( 'images/wpspin_light.gif' ),
            'image_width' => 100,
            'sort_type' => 'desc',
            'crop_description' => 0,
            'listings_per_page' => get_option( 'posts_per_page' ),
            'context' => $_POST['context'],
            'context_var' => NULL,
            'append_to_map' => true,
            'search_query' => $_POST,
            'property_ids' => array(),
            'allow_id_empty' => false
        );
        

        /** Extract the arguments after they merged with the defaults. */
        extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

        /** Display a placeholder if the plugin is not active or there is no API key. */
        if ( pls_has_plugin_error() && current_user_can( 'administrator' ) ) {
            global $PLS_API_DEFAULT_LISTING;
            $api_response = $PLS_API_DEFAULT_LISTING;
        } elseif (pls_has_plugin_error()) {
            global $PLS_API_DEFAULT_LISTING;
            $api_response = $PLS_API_DEFAULT_LISTING;
        } else {
            /** Get the listings list markup and javascript. */
            if (!empty($property_ids) || $allow_id_empty) {
              $api_response = PLS_Plugin_API::get_listings_details_list(array('property_ids' => $property_ids, 'limit' => $_POST['limit'], 'offset' => $_POST['offset']));
            } else {
              $api_response = PLS_Plugin_API::get_listings_list($search_query);
            }
        }

        $response = array();        
        
        // build response for datatables.js
        $listings = array();
        foreach ($api_response['listings'] as $key => $listing) {
            if (empty($listing['images'])) {
                $listing['images'][0] = array('url' => $placeholder_img);
            }
            ob_start();
            // pls_dump($listing);
            ?>

            <div class="listing-item grid_8 alpha" id="post-<?php the_ID(); ?>">
                <header class="grid_8 alpha">
                    <p><a href="<?php echo PLS_Plugin_API::get_property_url($listing['id']); ?>" rel="bookmark" title="<?php echo $listing['location']['address'] ?>"><?php echo $listing['location']['address'] . ', ' . $listing['location']['locality'] . ' ' . $listing['location']['region'] . ' ' . $listing['location']['postal']  ?></a></p>
                </header>
                <div class="listing-item-content grid_8 alpha">
                    <div class="grid_8 alpha">
                        <!-- If we have a picture, show it -->
                            <div class="listing-thumbnail">
                                <div class="outline">
                                   <a href="<?php echo @$listing['cur_data']['url']; ?>"><?php echo PLS_Image::load($listing['images'][0]['url'], array('resize' => array('w' => 250, 'h' => 150), 'fancybox' => true, 'as_html' => true, 'html' => array('alt' => $listing['location']['full_address']))); ?></a>
                                </div>
                            </div>

                        <div class="basic-details">
													<?php if (!empty($listing['cur_data']['beds'])) { ?>
														<p>Beds: <?php echo @$listing['cur_data']['beds']; ?></p>
													<?php } ?>

													<?php if (!empty($listing['cur_data']['baths'])) { ?>
														<p>Baths: <?php echo @$listing['cur_data']['baths']; ?></p>
													<?php } ?>

													<?php if (!empty($listing['cur_data']['half_baths'])) { ?>
														<p>Half Baths: <?php echo @$listing['cur_data']['half_baths']; ?></p>
													<?php } ?>
                            
													<?php if (!empty($listing['cur_data']['price'])) { ?>
														<p>Price: <?php echo @$listing['cur_data']['price']; ?></p>
													<?php } ?>

													<?php if (!empty($listing['cur_data']['avail_on'])) { ?>
														<p>Available On: <?php echo @$listing['cur_data']['avail_on']; ?></p>
													<?php } ?>

                          <?php if (!empty($listing['rets']['mls_id'])) { ?>
                            <p>MLS ID: <?php echo @$listing['rets']['mls_id']; ?></p>
                          <?php } ?>
                        </div>

                        <p class="listing-description">
                        	<?php echo substr($listing['cur_data']['desc'], 0, 300); ?>
                        </p>
                        <div class="actions">
                            <a class="more-link" href="<?php echo PLS_Plugin_API::get_property_url($listing['id']); ?>">View Property Details</a>
                            <?php echo PLS_Plugin_API::placester_favorite_link_toggle(array('property_id' => $listing['id'])); ?>
                        </div>
                        <?php PLS_Listing_Helper::get_compliance(array('context' => 'inline_search', 'agent_name' => $listing['rets']['aname'] , 'office_name' => $listing['rets']['oname'])); ?>
                    </div>
                </div>
            </div>
            <?php
            $item_html = ob_get_clean();
            $listings[$key][] = apply_filters( pls_get_merged_strings( array( "pls_listings_list_ajax_item_html", $context ), '_', 'pre', false ), htmlspecialchars_decode( $item_html ), $listing, $context_var);
            $listings[$key][] = $listing;
        }

        // Required for datatables.js to function properly.
        $response['sEcho'] = $_POST['sEcho'];
        $response['aaData'] = $listings;
        $response['iTotalRecords'] = $api_response['total'];
        $response['iTotalDisplayRecords'] = $api_response['total'];
        echo json_encode($response);

        //wordpress echos out a 0 randomly. die prevents it.
        die();
	}

}//end of class