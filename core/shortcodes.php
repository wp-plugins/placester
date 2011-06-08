<?php

/**
 * Shortcodes for use in post content
 * @file /core/shortcodes.php
 */

/**
 * Shortcodes for the listing
 * @defgroup listing_shortcodes
 * @{
 */
function placester_listing_shortcode_info() {
    global $post;
    $data = json_decode(stripslashes($post->post_content));
    return $data;
}

function placester_listings_map_shortcode( $atts ) {

    $defaults = array(
        'max_price' => 5000,
        'min_price' => 200,
    );

    $args = shortcode_atts( $defaults, $atts );

    $filter_query = '';

    if ( isset( $atts['available_on'] ) ) $filter_query .= '&available_on=' . date( 'd-m-Y', strtotime( '1st' . $atts['available_on'] ) );
    if ( isset( $atts['bathrooms'] ) ) $filter_query .= '&bathrooms=' . $atts['bathrooms'];
    if ( isset( $atts['bedrooms'] ) ) $filter_query .= '&bedrooms=' . $atts['bedrooms'];
    if ( isset( $atts['city'] ) ) $filter_query .= '&location[city]=' . $atts['city'];
    $filter_query .= '&max_price=' . $args['max_price'] . '&min_price=' . $args['min_price'];

?>
    <script type="text/javascript">
    jQuery("#placester_listings_map_map").ready(function() {
        filter_query = '<?php echo $filter_query; ?>';
        placesterMap_setFilter(filter_query);
    });
    </script>
<?php
    $return = '<section class="map">';
    try {
        $return .= placester_listings_map( array(), true );
    }
    catch (PlaceSterNoApiKeyException $e) {
        display_no_api_key_error();
    }
    $return .= '</section>'; 
    return $return;
}


function placester_listings_search( $atts ) {

    $defaults = array(
        'max_price' => 5000,
        'min_price' => 200,
        'rows_per_page' => 5,
        'sort_by' => 'bathrooms',
        'show_sort' => 1,
    );

    $args = wp_parse_args( $atts, $defaults );

    $filter_query = '';

    if ( isset( $args['available_on'] ) ) $filter_query .= '&available_on=' . date( 'd-m-Y', strtotime( '1st' . $args['available_on'] ) );
    if ( isset( $args['bathrooms'] ) ) $filter_query .= '&bathrooms=' . $args['bathrooms'];
    if ( isset( $args['bedrooms'] ) ) $filter_query .= '&bedrooms=' . $args['bedrooms'];
    if ( isset( $args['city'] ) ) $filter_query .= '&location[city]=' . $args['city'];
    if ( isset( $args['state'] ) ) $filter_query .= '&location[state]=' . $args['state'];
    if ( isset( $args['zip'] ) ) $filter_query .= '&location[zip]=' . $args['zip'];
    $filter_query .= '&max_price=' . $args['max_price'] . '&min_price=' . $args['min_price'];

    // See if snippet format is defined
    $snippet_layout = get_option('placester_snippet_layout');

?>
    <script type="text/javascript">
    function placesterListLone_createRowHtml(row)
    {
        var null_image =  '<?php echo WP_PLUGIN_URL . '/placester/images/null/property3-73-37.png' ?>';
        if (row.images.length > 0) {
            var images_array = ('' + row.images).split(',');
            var image = '';

            if (images_array.length > 0 && images_array[0].length > 0)
            {
                image = '<img src="' + images_array[0] + '" width=100 />';
            }        
        } else {
            var image = '<img src="' + null_image + '" width=100 />';
        };
        <?php if ( $snippet_layout != '' ) { ?>
        s = '<?php echo $snippet_layout; ?>';
        s = s.replace("\[bedrooms\]", row.bedrooms);
        s = s.replace("\[bathrooms\]", row.bathrooms);
        s = s.replace("\[price\]", row.price);
        s = s.replace("\[available_on\]", row.available_on);
        s = s.replace("\[listing_address\]", row.location.address);
        s = s.replace("\[listing_city\]", row.location.city);
        s = s.replace("\[listing_state\]", row.location.state);
        s = s.replace("\[listing_zip\]", row.location.zip);
        s = s.replace("\[listing_description\]", row.description);
        s = s.replace("\[listing_image\]", image);
        
        /* s = s.replace("\[listing_unit\]", row.location.unit);  
        /* s = s.replace("\[listing_neighborhood\]", row.); 
        /* s = s.replace("\[listing_map\]", <?php do_shortcode("[listing_map]"); ?>); */
        /* s = s.replace("\[listing_images\]", row.bedrooms); */     
        
        <?php } else { ?>
        s = '  <li class="single-item clearfix">' +
            '  <div class="thumbs"><a href="' + row.url + '" >' +
            image + 
            '  </a></div>' +
            '  <div class="item-details">' +
            '  <a href="'+ row.url +'" class="feat-title">' + row.location.address + ', ' + row.location.city + ', ' + row.location.state + '</a>' +
            '  <ul class="item-details clearfix">' +
            '  <li>Bedrooms: ' + row.bedrooms + '</li>' +
            '  <li>Available: ' + row.available_on+'</li>' +
            '  <li>Bathrooms: ' + row.bathrooms + '</li>' +
            '  <li>Price: ' + row.price + '</li>' +
            '  </ul>' +
            '  <a href="' + row.url + '" class="seemore">See More Details</a>' +
            '  </div>' +
            '  </li>';
        <?php } ?>
        return s;
    }

    function custom_empty_listings_loader (dom_object) {
        var empty_property_search = '<div><h5>No results</h5><p>Sorry, no listings match that search. Maybe try something a bit broader? Or just give us a call and we\'ll personally help you find the right place.</p></div>'
            dom_object.html(empty_property_search);
    }

    jQuery("#placester_listings_list").ready(function() {
        filter_query = '<?php echo $filter_query; ?>';
        placesterListLone_setFilter(filter_query);

        jQuery('#sort_list').change(function() {
            var v = $('#sort_list').val();
            a = v.split(' ');
            placesterListLone_setSorting(a[0], a[1]);
        });
    });
    </script>
<?php
    $list_args = array(
        'table_type' => 'html',
        'sort_by' => 'bathrooms',
        'js_row_renderer' => 'placesterListLone_createRowHtml',
        'loading' => array(
            'render_in_dom_element' => 'my_loader_div'
        ),
        'attributes' => array(
            'bathrooms',
            'price',
            'images',
            'description',
            'url',
            'location.city',
            'location.state',
            'location.address',
            'location.zip',
            'bedrooms',
            'id',
            'available_on'
        )
    );

    $pagination = '';
    if ( $args['rows_per_page'] ) {
        $list_args['pager'] = array(
            'render_in_dom_element' => 'pagination_loads_here',
            'rows_per_page' => $args['rows_per_page'],
            'css_current_button' => 'prev-btn-passive',
            'css_not_current_button' => 'next-btn-active',
            'first_page' => array( 'visible' => false, 'label' => 'First'), 
            'previous_page' => array( 'visible' => true, 'label' => 'Prev' ),
            'numeric_links' => array(
                'visible' => false, 
                'max_count' => 10,
                'more_label' => '..more..',
                'css_outer' => 'pager_numberic_block'
            ),
            'next_page' => array(
                'visible' => true,
                'label' => 'Next',
            ),
            'last_page' => array(
                'visible' => false,
                'label' => 'Last'
            )
        );

        $pagination = 
            '<section id="pagination_loads_here" class="pagination">' . "\n" .
            '   <a href="#" class="prev-btn-passive">Prev</a>' . "\n" .
            '   <a href="#" class="next-btn-active">Next</a>' . "\n" .
            '   <div class="clr"></div>' . "\n" .
            '</section>';
    }
    $sort_widget = '';
    if ( $args['show_sort'] ) {
        $sort_widget = 
            '<div>' . "\n" .  
            '	<div class="selLabel2 sort-by">Sort by</div>' . "\n" .
            '    <div class="cselect2">' . "\n" .
            '        <select id="sort_list" class="sparkbox-custom">' . "\n" .
            '          <option value="bathrooms asc">bathrooms</option>' . "\n";
        $sort_widget .= ( $args['sort_by'] == 'price' ) ? '<option  selected="selected" value="price asc">price</option>' . "\n" : '<option value="price asc">price</option>' . "\n"; 
        $sort_widget .= 
            '        </select>' . "\n" .
            '    </div>' . "\n" .                    
            '</div>';
    } 

    return '<div class="placester_search_results">' . $sort_widget . '<ul class="item-list">' . placester_listings_list( $list_args, true ) . '</ul>' . $pagination . '</div>';
}

function placester_bedrooms() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->bedrooms))
        return $data->bedrooms;
}

/**
 * Shows the listing's number of bathrooms
 * @return int $data->bathrooms
 */
function placester_bathrooms() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->bathrooms))
        return $data->bathrooms;
 }

/**
 * Shows the listing's price
 * @return int $data->price
 */
function placester_price() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->price))
        return $data->price;
}

/**
 * Shows the listing's available date
 * @return int $data->available_on
 */
function placester_available_on() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->available_on))
        return $data->available_on;
}

/**
 * Shows the listing's street address
 * @return string $data->location->address
 */
function placester_listing_address() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->address))
        return $data->location->address;
}    

/**
 * Shows the listing's city
 * @return string $data->location->city
 */
function placester_listing_city() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->city))
        return $data->location->city;
}

/**
 * Shows the listing's state
 * @return string $data->location->state
 */
function placester_listing_state() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->state))
        return $data->location->state;
}

/**
 * Shows the listing's unit number
 * @return string $data->location->unit
 */
function placester_listing_unit() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->unit))
        return $data->location->unit;
}

/**
 * Shows the listing's zip code
 * @return int $data->location->zip
 */
function placester_listing_zip() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->zip))
        return $data->location->zip;
}

/**
 * Shows the listing's neighborhood
 * @return string $data->location->neighborhood
 */
function placester_listing_neighborhood() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->neighborhood))
        return $data->location->neighborhood;
}

/**
 * Shows the listing's google map.
 * @return string $post_content
 */
function placester_listing_map() {
    $data = placester_listing_shortcode_info();
    if (isset($data->location->coords->latitude) && !empty($data->location->coords->latitude)): 
    $post_content  = '<div id="property_details_map" style="height: 200px; width: 340px"></div>';
    $post_content .= '
    <script type="text/javascript" charset="utf-8" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript" charset="utf-8">
        var latLng = new google.maps.LatLng(' . $data->location->coords->latitude.','. $data->location->coords->longitude . ')
        window.map = new google.maps.Map(document.getElementById("property_details_map"),
          {
            zoom: 14,
            center: latLng,
            disableDefaultUI: true,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
          });
    
    
        setTimeout(function () {
            var marker = new google.maps.Marker({
                position: latLng,
                map: window.map,
            });
                        
        }, 500)   
    </script>';
    return $post_content;
    endif;
}

/**
 * @return string $data->description
 */
function placester_listing_description() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->description))
        return $data->description;
}

/**
 * Shows all listing images
 * @return string
 */
function placester_listing_images() {
    $data = placester_listing_shortcode_info();
    if (!empty($data->images)) {
        $post_image = '';
        foreach($data->images as $image) {
            $post_image .= '<a class="placester_fancybox" href="' . $image->url . '"><img src="' . $image->url . '" alt width="150" height="150" /></a>';
        }
    return '<p class="images">' . $post_image . '</p>';
    }
}

/**
 * Shows a single image of the listing.
 * Will be updated to show the featured image.
 * @return string $image
 */
function placester_listing_single_image() {
    $data = placester_listing_shortcode_info();
    $base_url = WP_PLUGIN_URL . '/placester';
    if (!empty($data->images)) {
        $image = '<a class="" href="' . get_permalink() . '"><img src="' . $data->images[0]->url . '" alt width="150" height="150" /></a>';
    } else {
        $image = '<a class="" href="' . get_permalink() . '"><img src="' . $base_url . '/images/null/property3-73-37.png" alt width="150" height="150" /></a>'; ;
    }

    return $image;
}
 
add_shortcode('listings_search', 'placester_listings_search');
add_shortcode('listings_map', 'placester_listings_map_shortcode');
add_shortcode('bedrooms', 'placester_bedrooms');
add_shortcode('bathrooms', 'placester_bathrooms');
add_shortcode('price', 'placester_price');
add_shortcode('available_on', 'placester_available_on');
add_shortcode('listing_address', 'placester_listing_address');
add_shortcode('listing_city', 'placester_listing_city');
add_shortcode('listing_state', 'placester_listing_state');
add_shortcode('listing_unit', 'placester_listing_unit');
add_shortcode('listing_zip', 'placester_listing_zip');
add_shortcode('listing_neighborhood', 'placester_listing_neighborhood');
add_shortcode('listing_map', 'placester_listing_map');
add_shortcode('listing_description', 'placester_listing_description');
add_shortcode('listing_images', 'placester_listing_images');
add_shortcode('listing_image', 'placester_listing_single_image');

/**
 * Shortcodes for the user
 * @defgroup user_shortcodes
 * @{
 */

/**
 * Shows the user's logo
 * @return string
 */
function placester_user_logo() {
    $user = placester_get_user_details();
    if($user->logo_url)
        return '<p style="float: left; width: 110px; padding-right: 10px;"><img src="' . $user->logo_url . '" /></p>'; 
}

/**
 * Shows the user's first name.
 * @return string $user->first_name
 */
function placester_user_first_name() {
    $user = placester_get_user_details();
    if($user->first_name)
        return $user->first_name; 
}

/**
 * Shows the user's last name.
 * @return string $user->last_name
 */
function placester_user_last_name() {
    $user = placester_get_user_details();
    if($user->last_name)
        return $user->last_name; 
}

/**
 * Shows the user's phone number
 * @return string $user->phone
 */
function placester_user_phone() {
    $user = placester_get_user_details();
    if($user->phone)
        return $user->phone; 
}

/**
 * Shows the listing's email address
 * @return string
 */
function placester_user_email() {
    $data = placester_listing_shortcode_info();
    if($data->contact->email)
        return '<a href="mailto:' . $data->contact->email . '?subject=feedback">Email me</a>';
}

/**
 * Shows the user's street address
 * @return string $user->location->address
 */
function placester_user_address() {
    $user = placester_get_user_details();
    if($user->location->address)
        return $user->location->address; 
}

/**
 * Shows the user's unit number
 * @return string $user->location->unit
 */
function placester_user_unit() {
    $user = placester_get_user_details();
    if($user->location->unit)
        return $user->location->unit; 
}

/**
 * Shows the user's city
 * @return string $user->location->city
 */
function placester_user_city() {
    $user = placester_get_user_details();
    if($user->location->city)
        return $user->location->city; 
}

/**
 * Shows the user's state
 * @return string $user->location->state
 */
function placester_user_state() {
    $user = placester_get_user_details();
    if($user->location->state)
        return $user->location->state; 
}

/**
 * Shows the user's zip code
 * @return int $user->location->zip
 */
function placester_user_zip() {
    $user = placester_get_user_details();
    if($user->location->zip)
        return $user->location->zip; 
}

/**
 * Shows the user's description
 * @return string $user->description
 */
function placester_user_description() {
    $user = placester_get_user_details();
    if($user->description)
        return $user->description; 
}

/** @} */

add_shortcode('logo', 'placester_user_logo');
add_shortcode('first_name', 'placester_user_first_name');
add_shortcode('last_name', 'placester_user_last_name');
add_shortcode('phone', 'placester_user_phone');
add_shortcode('email', 'placester_user_email');
add_shortcode('user_address', 'placester_user_address');
add_shortcode('user_unit', 'placester_user_unit');
add_shortcode('user_city', 'placester_user_city');
add_shortcode('user_state', 'placester_user_state');
add_shortcode('user_zip', 'placester_user_zip');
add_shortcode('user_description', 'placester_user_description');
