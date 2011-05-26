<?php

/**
 * Admin interface: Add listing tab
 * Entry point
 */

include_once(dirname(__FILE__) . '/../core/const.php');
include_once('property_parts.php');

$p = new StdClass;
$v = new StdClass;

$error = false;
$error_message = '';
$view_success = false;
$property_id = '';

//
// create property
//
if (isset($_POST['add_finish']))
{
    $p = http_property_data();

    try
    {
        $r = placester_property_add($p);
        $property_id = $r->id;

        // set_url
        $p = new StdClass;
        $p->url = placester_get_property_url($property_id);
        placester_property_set($property_id, $p);

    }
    catch (ValidationException $e) 
    {
        $error_message = $e->getMessage();
        $v = $e->validation_data;
        $error = true;
    }
    catch (Exception $e) 
    {
        $error_message = $e->getMessage();
        $error = true;
    }

    if (!$error)
    {
        // if we get error from now - can redirect to "property edit" page
        // and allow to finish with images
        try
        {
            for ($n = 0; $n < count($_FILES['images']['name']); $n++)
            {
                if (strlen($_FILES['images']['name'][$n]) <= 0)
                    continue;

                placester_property_image_add($property_id, 
                    $_FILES['images']['name'][$n],
                    $_FILES['images']['type'][$n],
                    $_FILES['images']['tmp_name'][$n]);
            }
        }
        catch (Exception $e) 
        {
            $error_message = 'Property was created, but got problem with images: ' . $e->getMessage();
            $error = true;
        }
    }

    if (!$error)
        $view_success = true;
}

if ($view_success)
    include('property_add_ok.php');
else
    include('property_add_form.php');
