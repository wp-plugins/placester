<?php

/**
 * Functions related to extraction of plugin settings
 */

/**
 * Returns object containing user details
 *
 * @return object
 */
function get_user_details()
{ 
    $user = get_option('placester_user');

    $user->logo_url = plugins_url('/images/blank.gif', dirname(__FILE__));
    if (property_exists($user, 'logo'))
    {
        $id = $user->logo;
        if (strlen($id) > 0)
        {
            $urls = wp_get_attachment_image_src($id, 'full');
            $user->logo_url = $urls[0];
        }
    }

    if (!property_exists($user, 'first_name'))
        $user->first_name = '';
    if (!property_exists($user, 'last_name'))
        $user->last_name = '';
    if (!property_exists($user, 'location'))
        $user->location = new StdClass;
    if (!property_exists($user, 'phone'))
        $user->phone = '';
    if (!property_exists($user, 'email'))
        $user->email = '';
        
        

    return $user;
}



/**
 * Returns object containing company details
 *
 * @return object
 */
function get_company_details()
{
    $company = get_option('placester_company');

    $company->logo_url = plugins_url('/images/blank.gif', dirname(__FILE__));
    if (property_exists($company, 'logo'))
    {
        $id = $company->logo;
        if (strlen($id) > 0)
        {
            $urls = wp_get_attachment_image_src($id, 'full');
            $company->logo_url = $urls[0];
        }
    }

    if (!property_exists($company, 'location'))
        $company->location = new StdClass;
    if (!property_exists($company, 'phone'))
        $company->phone = '';
    if (!property_exists($company, 'description'))
        $company->description = '';

    return $company;
}
