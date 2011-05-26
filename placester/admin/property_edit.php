<?php

/**
 * Admin interface: Edit listing form
 * Entry point
 */

include_once(dirname(__FILE__) . '/../core/const.php');
include_once('property_parts.php');

$p = new StdClass;
$v = new StdClass;

$error = false;
$error_message = '';
$view_success = false;

$property_id = $_REQUEST['id'];

//
// Create property
//
if (isset($_POST['edit_finish']))
{
    $p = http_property_data();

    try
    {
        $p->url = placester_get_property_url($property_id);
        $r = placester_property_set($property_id, $p);
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
        $view_success = true;
}

$p = placester_property_get($property_id);
$combined = '';
if (isset($p->listing_types) && is_array($p->listing_types) && 
    count($p->listing_types) > 0)
    $combined .= $p->listing_types[0];
$combined .= ',';
if (isset($p->zoning_types) && is_array($p->zoning_types) && 
    count($p->zoning_types) > 0)
    $combined .= $p->zoning_types[0];
$combined .= ',';
if (isset($p->purchase_types) && is_array($p->purchase_types) && 
    count($p->purchase_types) > 0)
    $combined .= $p->purchase_types;

if (!isset($multi_types[$combined]))
    $combined = 'other,,';
$p->combined_type = $combined;



if ($view_success)
    include('property_edit_ok.php');
else
    include('property_edit_form.php');
