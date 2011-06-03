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

/**
 * Shows the listing's number of bedrooms
 * @return int $data->bedrooms
 */
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
/** @} */

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