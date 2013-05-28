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
    public static function init() {
        // Hook the callback for ajax requests
        add_action('wp_ajax_pls_listings_ajax', array(__CLASS__, 'get' ) );
        add_action('wp_ajax_nopriv_pls_listings_ajax', array(__CLASS__, 'get' ) );

        add_action( 'wp_ajax_pls_listings_fav_ajax', array(__CLASS__,'get_favorites'));
        add_action( 'wp_ajax_nopriv_pls_listings_fav_ajax', array(__CLASS__,'get_favorites'));
    }

    public static function get_favorites() {
        $favorite_ids = PLS_Plugin_API::get_listings_fav_ids();
        self::get(array('property_ids' => $favorite_ids, 'allow_id_empty' => true));
    }

    public static function load ($args = array()) {
      
        // Set "Sort By" (default to number of total images)
        $sort_by = ( isset($args['sort_by']) ? $args['sort_by'] : 'total_images' );
        
        // Respect the "Sort By" theme option if it's set..
        $sort_by_theme_option = pls_get_option('listings_default_sort_by');
        if (!empty($sort_by_theme_option)) { $sort_by = $sort_by_theme_option; }
      
        // Set sort order (default to number of desc)
        $sort_type = ( isset($args['sort_type']) ? $args['sort_type'] : 'desc' );

        // Respect sort type theme option if it's set...
        $sort_type_theme_option = pls_get_option('listings_default_sort_type');
        if (!empty($sort_type_theme_option)) { $sort_type = $sort_type_theme_option; }

        // Set default options...
        $defaults = array(
            'loading_img' => admin_url('images/wpspin_light.gif'),
            'image_width' => 100,
            'sort_type' => $sort_type,
            'sort_by' => $sort_by,
            'show_sort' => true,
            'crop_description' => 0,
            'listings_per_page' => pls_get_option('listings_default_list_length'),
            'context' => '',
            'context_var' => NULL,
            'append_to_map' => true,
            'search_query' => $_POST,
            'table_id' => 'placester_listings_list',
            'map_js_var' => 'pls_google_map',
            'search_class' => 'pls_search_form_listings',
            'query_limit' => pls_get_option('listings_default_list_limit')
        );

        // Extract the arguments after they merged with the defaults
        $args = wp_parse_args($args, $defaults);
        extract($args, EXTR_SKIP);

        // Now we need to pass the 'limit' parameter from defaults into the search_query to honor corresponding theme option...
        $query_limit = (int) $query_limit;
        // If limit is invalid or greater than API's upper limit, adjust accordingly... 
        $search_query['limit'] = ( (is_int($query_limit) && $query_limit <= 50) ? $query_limit : 50 );
        
        $sort_by_options = array(
            'total_images' => 'Total Images',
            'location.address' => 'Address',
            'location.locality' => 'City',
            'location.region' => 'State',
            'location.postal' => 'Zip',
            // 'zoning_types' => 'Zoning',
            // 'purchase_types' => 'Purchase Type',
            // 'listing_types' => 'Listing Type',
            // 'property_type' => 'Property Type',
            'cur_data.beds' => 'Beds',
            'cur_data.baths' => 'Baths',
            'cur_data.price' => 'Price',
            'cur_data.sqft' => 'Square Feet',
            // 'cur_data.avail_on' => 'Date Available'
        );
        $sort_type_options = array('desc' => 'Descending','asc' => 'Ascending');

        // /** Filter the "Sort by"  and sort type options. */
        $sort_by_options = apply_filters("pls_listings_list_ajax_sort_by_options", $sort_by_options);
        $sort_type_options = apply_filters("pls_listings_list_ajax_sort_type_options", $sort_type_options);

	    // need to do this assuming sort_by and sort_type might not exist! -pek
        if (!isset($_POST['sort_by'])) {
            // sort_by was not specified, set our theme options default
        	$_POST['sort_by'] = $sort_by;
        }
        
        if (!isset($_POST['sort_type'])) {
        	// not specified, set our theme options default
        	$_POST['sort_type'] = $sort_type;
        }

        ob_start();
        ?>
            <script type="text/javascript" src="<?php echo trailingslashit(PLS_JS_URL) ?>scripts/listing-list.js"></script>
            
            <!-- Sort Dropdown -->
            <?php if ($show_sort): ?>
              <form class="sort_wrapper">
                  <div class="sort_item">
                    <label for="sort_by">Sort By</label>
                    <select name="sort_by" id="sort_by">
                        <?php foreach ($sort_by_options as $key => $value): ?>
                        	<?php if ($_POST['sort_by'] == $key): ?>
                                <option value="<?php echo $key ?>" selected="selected"><?php echo $value ?></option>
                            <?php else: ?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="sort_item">
                    <label for="sort_type">Sort Direction</label>
                    <select name="sort_type" id="sort_dir">
                    	<?php foreach ($sort_type_options as $key => $value): ?>
                        	<?php if ($_POST['sort_type'] == $key): ?>
                            	<option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
                        	<?php else: ?>
                        	   <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                        	<?php endif; ?>
                        <?php endforeach; ?>
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

  	public static function get ($args = array()) {
        // Saved Search init...
  		$saved_user_search = false;
        if (isset($_POST['saved_search_lookup']) ) {
            if (strpos($_POST['saved_search_lookup'], 'pl_ss_') !== false) {
            	$user_lookup = $_POST['saved_search_lookup'];
            	$user_lookup = trim($user_lookup, '/');
            	
                if (is_user_logged_in()) {
            		// Try to retrieve user's saved searches...
                    $logged_user_id = get_current_user_id();
            		$saved_searches = get_user_meta($logged_user_id, 'pls_saved_searches', true);
            		
                    // If not empty and a valid array,
                    if ( !empty($saved_searches) && is_array($saved_searches) && isset($saved_searches[$user_lookup]) ) {
        				$stored_user_search = $saved_searches[$user_lookup];
        				$values = json_decode($stored_user_search, true);
        				if (!empty($values) && is_array($values)) { $_POST = array_merge($values, $_POST); }
        				$saved_user_search = true;
            		}
            	}
      	    }
            elseif ($result = PLS_Saved_Search::check($_POST['saved_search_lookup'])) {
                $sEcho = $_POST['sEcho'];
                unset($result['sEcho']);
                $_POST = $result;
                $_POST['sEcho'] = $sEcho;
            }
        }
              
        // Pagination
        // If length is not set for number of listings to return, set it to our Theme Options default
        if ( !isset( $_POST['iDisplayLength']) ) {
            $_POST['iDisplayLength'] = pls_get_option( 'listings_default_list_length' );
        }
        $_POST['limit'] = @$_POST['iDisplayLength'];
        $_POST['offset'] = @$_POST['iDisplayStart'];     

        // Define the default argument array
        $defaults = array(
            'loading_img' => admin_url('images/wpspin_light.gif'),
            'image_width' => 100,
            'crop_description' => 0,
            'sort_type' => pls_get_option('listings_default_sort_type'),
            'listings_per_page' => pls_get_option( 'listings_default_list_length' ),
            'context' => isset($_POST['context']) ? $_POST['context'] : '',
            'context_var' => NULL,
            'append_to_map' => true,
            'search_query' => $_POST,
            'property_ids' => isset($_POST['property_ids']) ? $_POST['property_ids'] : '',
            'allow_id_empty' => false
        );
      
        if (isset($defaults['search_query']['sEcho'])) { unset($defaults['search_query']['sEcho']); }

        $cache = new PLS_Cache('list');
        if ($transient = $cache->get($defaults)) {
            $transient['sEcho'] = $_POST['sEcho'];
            echo json_encode($transient);
            die();
        }

        // Extract the arguments after they merged with the defaults
        extract( wp_parse_args( $args, $defaults ), EXTR_SKIP );

        // Start off with a placeholder in case the plugin is not active or there is no API key...
        $api_response = PLS_Listing_Helper::$default_listing;

        // If plugin is active, grab listings intelligently...
        if (!pls_has_plugin_error()) {
            // Get the listings list markup and JS
            if (!empty($property_ids) || $allow_id_empty) {

                // Sometimes property_ids are passed in as a flat screen from the JS post object
                if (is_string($property_ids)) {
                    $property_ids = explode(',', $property_ids);
                }

                $api_response = PLS_Plugin_API::get_listings_details_list(array('property_ids' => $property_ids, 'limit' => $_POST['limit'], 'offset' => $_POST['offset']));
            } 
            elseif (isset($search_query['neighborhood_polygons']) && !empty($search_query['neighborhood_polygons']) ) {
                $api_response = PLS_Plugin_API::get_polygon_listings($search_query);
            }
            else {
                $api_args = ( $saved_user_search ? $_POST : $search_query );
                $api_response = PLS_Plugin_API::get_listings_list($api_args);
            }
        }

        $response = array();        
      
        // Build response for datatables.js
        $listings = array();
        $listings_cache = new PLS_Cache('Listing Thumbnail');

        foreach ($api_response['listings'] as $key => $listing) {
            // Check for cached listing thumbnail...
            $cache_id = array('context' => $context, 'listing_id' => $listing['id']);
            if (!($item_html = $listings_cache->get($cache_id))) {
                // Handle case of zero listing images...
                if (empty($listing['images'])) {
                    $listing['images'][0]['url'] = '';
                }

                ob_start();
                ?>
                    <div class="listing-item grid_8 alpha" itemscope itemtype="http://schema.org/Offer" data-listing="<?php echo $listing['id'] ?>">

                      <div class="listing-thumbnail grid_3 alpha">
                        <?php 
                          $property_images = ( is_array($listing['images']) ? $listing['images'] : array() );
                          usort($property_images, array(__CLASS__, 'order_images_ajax'));
                        ?>
                          
                         <a href="<?php echo @$listing['cur_data']['url']; ?>" itemprop="url">
                          <?php echo PLS_Image::load($property_images[0]['url'], array('resize' => array('w' => 210, 'h' => 140), 'fancybox' => true, 'as_html' => true, 'html' => array('alt' => $listing['location']['full_address'], 'itemprop' => 'image', 'placeholder' => PLS_IMG_URL . "/null/listing-300x180.jpg"))); ?>
                        </a>
                      </div>

                      <div class="listing-item-details grid_5 omega">
                        <header>
                          <p class="listing-item-address h4" itemprop="name">
                            <a href="<?php echo PLS_Plugin_API::get_property_url($listing['id']); ?>" rel="bookmark" title="<?php echo $listing['location']['address'] ?>" itemprop="url">
                              <?php echo $listing['location']['address'] . ', ' . $listing['location']['locality'] . ' ' . $listing['location']['region'] . ' ' . $listing['location']['postal']  ?>
                            </a>
                          </p>
                        </header>

                        <div class="basic-details">
                          <ul>
                          	<?php if (!empty($listing['cur_data']['beds'])): ?>
                          		<li class="basic-details-beds p1"><span>Beds:</span> <?php echo @$listing['cur_data']['beds']; ?></li>
                          	<?php endif; ?>

                          	<?php if (!empty($listing['cur_data']['baths'])): ?>
                          		<li class="basic-details-baths p1"><span>Baths:</span> <?php echo @$listing['cur_data']['baths']; ?></li>
                          	<?php endif; ?>

                          	<?php if (!empty($listing['cur_data']['half_baths'])): ?>
                          		<li class="basic-details-half-baths p1"><span>Half Baths:</span> <?php echo @$listing['cur_data']['half_baths']; ?></li>
                          	<?php endif; ?>

                          	<?php if (!empty($listing['cur_data']['price'])): ?>
                          		<li class="basic-details-price p1" itemprop="price"><span>Price:</span> <?php echo PLS_Format::number($listing['cur_data']['price'], array('abbreviate' => false, 'add_currency_sign' => true)); ?></li>
                          	<?php endif; ?>

                          	<?php if (!empty($listing['cur_data']['avail_on'])): ?>
                          		<li class="basic-details-sqft p1"><span>Sqft:</span> <?php echo PLS_Format::number($listing['cur_data']['sqft'], array('abbreviate' => false, 'add_currency_sign' => false)); ?></li>
                          	<?php endif; ?>

                            <?php if (!empty($listing['rets']['mls_id'])): ?>
                              <li class="basic-details-mls p1"><span>MLS ID:</span> <?php echo @$listing['rets']['mls_id']; ?></li>
                            <?php endif; ?>
                          </ul>
                        </div>

                        <p class="listing-description p4" itemprop="description">
                          <?php echo substr($listing['cur_data']['desc'], 0, 300); ?>
                        </p>

                      </div>

                      <div class="actions">
                        <a class="more-link" href="<?php echo PLS_Plugin_API::get_property_url($listing['id']); ?>" itemprop="url">View Property Details</a>
                        <?php echo PLS_Plugin_API::placester_favorite_link_toggle(array('property_id' => $listing['id'])); ?>
                      </div>
                        
                      <?php PLS_Listing_Helper::get_compliance(array('context' => 'inline_search', 'agent_name' => @$listing['rets']['aname'] , 'office_name' => @$listing['rets']['oname'])); ?>

                    </div>
                <?php
          
                $item_html = ob_get_clean();
                $item_html = apply_filters( pls_get_merged_strings( array("pls_listings_list_ajax_item_html", $context), '_', 'pre', false ), htmlspecialchars_decode($item_html), $listing, $context_var);
                $listings_cache->save($item_html);
            }

            $listings[$key][] = $item_html;
            $listings[$key][] = $listing;
        }

        // Required for datatables.js to function properly.
        $response['sFirst'] = 'Previous';
        $response['sPrevious'] = 'Next';
      
        $response['sEcho'] = @$_POST['sEcho'];
        $response['aaData'] = $listings; 
        $api_total = isset( $api_response['total'] ) ? $api_response['total'] : 0; 
        $response['iTotalRecords'] = $api_total;
        $response['iTotalDisplayRecords'] = $api_total;

        $cache->save($response);

        ob_start("ob_gzhandler");
        echo json_encode($response);

        // wordpress echos out a 0 randomly. die prevents it.
        die();
  	}

    private static function order_images_ajax ($a, $b) {
        if ($a['order'] == $b['order']) { return 0; }
        return ($a['order'] < $b['order']) ? -1 : 1;
    }
  
}
// end of class
?>