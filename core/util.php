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

    if ($wp_rewrite->using_permalinks())
        return site_url() . '/' . $placester_post_slug . '/' . $id;
    
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