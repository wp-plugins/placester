<?php

/**
 * Common plugin utilities
 */

/**
 * Returns slug of property posts
 *
 * @return string
 */
function placester_post_slug()
{
    $url_slug = get_option('placester_url_slug');
    if (strlen($url_slug) <= 0)
        $url_slug = 'listing';
    return $url_slug;
}



/*
 * "No API Key" exception
 */
class PlaceSterNoApiKeyException extends Exception
{
    /*
     * Constructor
     *
     * @param string $message
     */
    function __construct($message)
    {
        parent::__construct($message);
    }
}



/**
 * Returns API key. throws exception if not set
 *
 * @return string
 */
function placester_get_api_key()
{
    $api_key = get_option('placester_api_key');
    if (strlen($api_key) <= 0)
        throw new PlaceSterNoApiKeyException('API key not specified on settings page');
    return $api_key;
}



/**
 * Returns if API key is specified
 *
 * @return bool
 */
function placester_is_api_key_specified()
{
    try
    {
        placester_get_api_key();
        return true;
    }
    catch (PlaceSterNoApiKeyException $e) 
    {}
  
    return false;
}



function placester_get_post_id( $property_id ) {
    global $wpdb;

    $sql = $wpdb->prepare(
        'SELECT ID, post_modified ' .
        'FROM ' . $wpdb->prefix . 'posts ' .
        "WHERE post_type = 'property' AND post_name = %s " .
        'LIMIT 0, 1', $property_id);

    $row = $wpdb->get_row($sql);
    $post_id = 0;
    if ($row) {
        $post_id = $row->ID;
        $modified_timestamp = strtotime($row->post_modified);
        if ($modified_timestamp > time() - 3600 * 48)
            return $post_id;
    }

    try {
        $data = placester_property_get($property_id);
        $post = array(
             'post_type'   => 'property',
             'post_title'  => $property_id,
             'post_name'   => $property_id,
             'post_status' => 'publish',
             'post_author' => 1,
             'post_content'=> json_encode($data),
             'filter'      => 'db'
          );

        if ($post_id <= 0)
            $post_id = wp_insert_post($post);
        else
        {
            $post['ID'] = $post_id;
            $post_id = wp_update_post($post);
        }
    }
    catch (Exception $e) {
    }

    return $post_id;
}



/**
 * Returns property of object (1 level)
 *
 * @param object $o
 * @param string $property
 * @return string
 */
function placester_p1($o, $property)
{
    if (!property_exists($o, $property))
        return '';
    return $o->$property;
}



/**
 * Returns property of object (via 2 levels)
 *
 * @param object $o
 * @param string $property1
 * @param string $property2
 * @return string
 */
function placester_p2($o, $property1, $property2)
{
    if (!property_exists($o, $property1))
        return '';
    $o = $o->$property1;
    if (!property_exists($o, $property2))
        return '';
    return $o->$property2;
}



/**
 * Returns property of object (via 3 levels)
 *
 * @param object $o
 * @param string $property1
 * @param string $property2
 * @param string $property3
 * @return string
 */
function placester_p3($o, $property1, $property2, $property3)
{
    if (!property_exists($o, $property1))
        return '';
    $o = $o->$property1;
    if (!property_exists($o, $property2))
        return '';
    $o = $o->$property2;
    if (!property_exists($o, $property3))
        return '';
    return $o->$property3;
}



/**
 * Expands template with parameter values.
 * Each [field_name] replaced with value of that field
 *
 * @param string $template
 * @param object $i
 * @return string
 */
function placester_expand_template($template, $i)
{
    $field_names =
        array
        (
            "/\\[available_on\\]/",
            "/\\[bathrooms\\]/",
            "/\\[bedrooms\\]/",
            "/\\[contact.email\\]/",
            "/\\[contact.phone\\]/",
            "/\\[description\\]/",
            "/\\[half_baths\\]/",
            "/\\[id\\]/",
            "/\\[location.address\\]/",
            "/\\[location.city\\]/",
            "/\\[location.coords.latitude\\]/",
            "/\\[location.coords.longitude\\]/",
            "/\\[location.state\\]/",
            "/\\[location.unit\\]/",
            "/\\[location.zip\\]/",
            "/\\[price\\]/",
            "/\\[url\\]/"
        );
    $field_values =
        array
        (
            placester_p1($i, 'available_on'),
            placester_p1($i, 'bathrooms'),
            placester_p1($i, 'bedrooms'),
            placester_p2($i, 'contact', 'email'),
            placester_p2($i, 'contact', 'phone'),
            placester_p1($i, 'description'),
            placester_p1($i, 'half_baths'),
            placester_p1($i, 'id'),
            placester_p2($i, 'location', 'address'),
            placester_p2($i, 'location', 'city'),
            placester_p3($i, 'location', 'coords', 'latitude'),
            placester_p3($i, 'location', 'coords', 'longitude'),
            placester_p2($i, 'location', 'state'),
            placester_p2($i, 'location', 'unit'),
            placester_p2($i, 'location', 'zip'),
            placester_p1($i, 'price'),
            placester_get_property_url(placester_p1($i, 'id'))
        );                   

    $values = array();
    foreach ($field_values as $v) 
        $values[count($values)] = preg_replace("/[$]/", "\\\\$", $v);

    $output = preg_replace($field_names, $values, $template);

    return $output;
}



global $placester_warn_on_api_key;



/**
 * Prints warning message if API key not set
 */
function placester_warn_on_api_key()
{
    if (!placester_is_api_key_specified())
    {
        global $placester_warn_on_api_key;
        if (!$placester_warn_on_api_key)
        {
            $placester_warn_on_api_key = true;
            echo '<div style="color: red; border: 1px solid red; padding: 10px">';
            echo 'API key not specified';
            echo '</div>';
        }

        return true;
    }

    return false;
}



/**
 * Returns URL of property page
 *
 * @return string
 */
function placester_get_property_url($id)
{
    global $placester_post_slug;
    global $wp_rewrite;

    if ($wp_rewrite->using_permalinks()) {
        return site_url() . '/' . $placester_post_slug . '/' . $id;
    }


    return site_url() . '/?post_type=property&property=' . $id;
}



/**
 * Adds filters to property list request specified in admin section
 */
function placester_add_admin_filters(&$filter)
{
    $property_types = get_option('placester_display_property_types');
    if (is_array($property_types))
        $filter['property_types'] = $property_types;

    $listing_types = get_option('placester_display_listing_types');
    if (is_array($listing_types))
        $filter['listing_types'] = $listing_types;

    $zoning_types = get_option('placester_display_zoning_types');
    if (is_array($zoning_types))
        $filter['zoning_types'] = $zoning_types;

    $purchase_types = get_option('placester_display_purchase_types');
    if (is_array($purchase_types))
        $filter['purchase_types'] = $purchase_types;
}



/**
 * Returns IDs of properties marked as "New"
 *
 * @return array
 */
function placester_properties_new_ids()
{
    $new_ids = get_option('placester_properties_new_ids');
    if (!is_array($new_ids))
        $new_ids = array();
    return $new_ids;
}


/**
 * Returns IDs of properties marked as "Featured"
 *
 * @return array
 */
function placester_properties_featured_ids()
{
    $featured_ids = get_option('placester_properties_featured_ids');
    if (!is_array($featured_ids))
        $featured_ids = array();

    return $featured_ids;
}



/*
 * Resets all featured/new settings
 */
function unset_all_featured_new_properties() {

        $v = array();
        update_option('placester_properties_featured_ids', $v);
        update_option('placester_properties_new_ids', $v);    
}


/**
 * Returns value of property, when property can be "property1.property2"
 * meaning $o->property1->property2 value
 *
 * @return string
 */
function placester_get_property_value($o, $property_name)
{
    $parts = explode('/', $property_name);
    for ($n = 0; $n < count($parts) - 1; $n++)
    {
        $p = $parts[$n];
        if (!isset($o->$p))
            return null;

        $o = $o->$p;
    }

    $p = $parts[count($parts) - 1];
    if (!isset($o->$p))
        return null;

    return $o->$p;
}



/**
 * Sets value of property, when property can be "property1.property2"
 * meaning $o->property1->property2 value
 *
 * @return string
 */
function placester_set_property_value($o, $property_name, $value)
{
    $parts = explode('/', $property_name);
    $my = $o;

    for ($n = 0; $n < count($parts) - 1; $n++)
    {
        $p = $parts[$n];
        if (!isset($my->$p))
            $my->$p = new StdClass;
        $my = $my->$p;
    }

    $p = $parts[count($parts) - 1];
    $my->$p = $value;
}



/**
 * Cuts empty entries of array
 */
function placester_cut_empty_fields(&$request)
{
    foreach ($request as $key => $value)
    {
        if (empty($request[$key]))
            unset($request[$key]);
    }
}

/**
 * Checks if user is registered
 *
 * @return bool
 */
 function is_user_registered ()
 {
     $api_key = get_option('placester_api_key');
     
    if (strlen($api_key) <= 0) {
         return FALSE;
     } else {
         return TRUE;
     }
     
 }

 
 /**
  *     Checks to see if the user has a provider
  */
function placester_provider_check()
{
    $api_key = get_option('placester_api_key');

    if (strlen($api_key) > 0) {
        $api_key_info = placester_apikey_info($api_key);
        // var_dump($api_key_info);
        if (!empty($api_key_info) && isset($api_key_info->provider)) {
            return array("name" => $api_key_info->provider->name, "url" => $api_key_info->provider->url);
        } else {
            return false;
        }
        
    }
}

/**
 *      Checks to see if the company is_verified
 *      Displays a warning message if not.
 */
function placester_verified_check()
{
    $api_key = get_option('placester_api_key');

    if (strlen($api_key) > 0) {
        $api_key_info = placester_apikey_info($api_key);
        // var_dump($api_key_info);
        if (!empty($api_key_info) && !$api_key_info->is_verified) {
            echo '<div class="updated inline"><p>You don\'t have enough contact information in your account to distribute your listings around the web. Placester requires you to verify your email address and enter a phone number so leads have accurate contact information.  Enter that information here: <input type="button" class="button " value="Contact Settings" onclick="document.location.href = \'/wp-admin/admin.php?page=placester_contact\';">. If you\'ve already entered that information, your account will be verified in 24 hours.</p></div>';        
        } 
    }
}

/**
 *      Checks to make sure a placester theme is active
 *      so the user doesn't have a negative experience
 *      with the plugin because a paired theme isn't used.
 */
 
function placester_theme_compatibility () 
{
    global $i_am_a_placester_theme;
    if (!$i_am_a_placester_theme) {
        echo '<div class="updated inline"><p>You are currently running the Placester plugin, but not with a Placester theme. You\'ll likely have a better experience with a compatible theme.  Download one here: <input type="button" class="button " value="Show a List of Placester Themes" onclick="document.location.href = \'/wp-admin/theme-install.php?tab=search&type=term&s=placester&search=Search\';"></p></div>';        
    }
}
add_action('admin_notices', 'placester_theme_compatibility',2);


function placester_check_theme ()
{
    $path = pathinfo(get_bloginfo('template_directory'));
    
    $all_files = recursive_directory_search("../wp-content/themes/" . $path['filename']);
    
    $theme->hash = @md5(implode($all_files, ' '), 0 );
    $theme->domain = $_SERVER['HTTP_HOST'];
    $theme->name = pathinfo(get_bloginfo('template_directory'));

    
    placester_theme_check($theme);
    
}
add_action("switch_theme", 'placester_check_theme', 1);

function recursive_directory_search( $path = '.')
{ 
    $files = array();
    if ($dh = opendir($path)) {
        while( false !== ( $file = readdir( $dh ) ) ){ 
            if($file !== "." && $file !== ".." && is_dir($path . "/" . $file) ) {
                $files[] = recursive_directory_search($path . "/" . $file); 
            } else { 
                if ($file !== "." && $file !== "..") {
                    $files[] = $file . ' ' . filemtime($path . "/" . $file); 
                }         
            }
        } 
        closedir( $dh ); 
    }
    return $files;
}
