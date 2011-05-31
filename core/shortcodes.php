<?php

/**
 * Shortcodes for use in post content
 */

/**
 * Shortcodes for the listing
 */
function placester_listing_shortcode_info() {
    global $post;
    $data = json_decode(stripslashes($post->post_content));
    return $data;
}

function placester_bedrooms() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->bedrooms))
        return $data->bedrooms;
}

function placester_bathrooms() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->bathrooms))
        return $data->bathrooms;
 }

function placester_price() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->price))
        return $data->price;
}

function placester_available_on() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->available_on))
        return $data->available_on;
}

function placester_listing_address() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->address))
        return $data->location->address;
}    

function placester_listing_city() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->city))
        return $data->location->city;
}

function placester_listing_state() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->state))
        return $data->location->state;
}

function placester_listing_unit() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->unit))
        return $data->location->unit;
}

function placester_listing_zip() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->zip))
        return $data->location->zip;
}

function placester_listing_neighborhood() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->location->neighborhood))
        return $data->location->neighborhood;
}

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

function placester_listing_description() {
    $data = placester_listing_shortcode_info();
    if(!empty($data->description))
        return $data->description;
}

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
 */

function placester_user_logo() {
    $user = placester_get_user_details();
    if($user->logo_url)
        return '<p style="float: left; width: 110px; padding-right: 10px;"><img src="' . $user->logo_url . '" /></p>'; 
}

function placester_user_first_name() {
    $user = placester_get_user_details();
    if($user->first_name)
        return $user->first_name; 
}

function placester_user_last_name() {
    $user = placester_get_user_details();
    if($user->last_name)
        return $user->last_name; 
}

function placester_user_phone() {
    $user = placester_get_user_details();
    if($user->phone)
        return $user->phone; 
}

function placester_user_email() {
    $data = placester_listing_shortcode_info();
    if($data->contact->email)
        return '<a href="mailto:' . $data->contact->email . '?subject=feedback">Email me</a>';
}

function placester_user_address() {
    $user = placester_get_user_details();
    if($user->location->address)
        return $user->location->address; 
}

function placester_user_unit() {
    $user = placester_get_user_details();
    if($user->location->unit)
        return $user->location->unit; 
}

function placester_user_city() {
    $user = placester_get_user_details();
    if($user->location->city)
        return $user->location->city; 
}

function placester_user_state() {
    $user = placester_get_user_details();
    if($user->location->state)
        return $user->location->state; 
}

function placester_user_zip() {
    $user = placester_get_user_details();
    if($user->location->zip)
        return $user->location->zip; 
}

function placester_user_description() {
    $user = placester_get_user_details();
    if($user->description)
        return $user->description; 
}


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