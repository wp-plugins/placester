<?php

/**
 * Admin interface: Settings tab
 */

include_once(dirname(__FILE__) . '/../core/const.php');
include_once('settings_parts.php');

//
// Save values
//
if (array_key_exists('set_default', $_POST))
{
    foreach ($_POST as $key => $value)
    {
        if (substr($key, 0, 33) == 'placester_display_property_types_' ||
            substr($key, 0, 32) == 'placester_display_listing_types_' ||
            substr($key, 0, 31) == 'placester_display_zoning_types_' ||
            substr($key, 0, 33) == 'placester_display_purchase_types_')
        {}
        elseif (substr($key, 0, 10) == 'placester_')
            update_option($key, '');
    }

    update_option('placester_display_property_types', array());
    update_option('placester_display_listing_types', array());
    update_option('placester_display_zoning_types', array());
    update_option('placester_display_purchase_types', array());
}
if (array_key_exists('remove', $_POST))
{
  placester_remove_listings();
}
if (array_key_exists('apply', $_POST))
{
    // Flag for showing success message
    $show_success_message = TRUE;
    
    if (isset($_POST['placester_api_key']) && $_POST['placester_api_key'] != get_option('placester_api_key') ) {
        placester_clear_cache();
        unset_all_featured_new_properties();
    }
    
    update_option('placester_list_searchable', '');   // clear value

    $placester_display_property_types = array();
    $placester_display_listing_types = array();
    $placester_display_zoning_types = array();
    $placester_display_purchase_types = array();

    foreach ($_POST as $key => $value)
    {
        if (substr($key, 0, 33) == 'placester_display_property_types_')
            array_push($placester_display_property_types, substr($key, 33));
        elseif (substr($key, 0, 32) == 'placester_display_listing_types_')
            array_push($placester_display_listing_types, substr($key, 32));
        elseif (substr($key, 0, 31) == 'placester_display_zoning_types_')
            array_push($placester_display_zoning_types, substr($key, 31));
        elseif (substr($key, 0, 33) == 'placester_display_purchase_types_')
            array_push($placester_display_purchase_types, substr($key, 33));
        elseif (substr($key, 0, 10) == 'placester_')
            update_option($key, $value);
    }

    placester_admin_actualize_company_user();
        
    cut_if_fullset($placester_display_property_types, $placester_const_property_types);
    cut_if_fullset($placester_display_listing_types, $placester_const_listing_types);
    cut_if_fullset($placester_display_zoning_types, $placester_const_zoning_types);
    cut_if_fullset($placester_display_purchase_types, $placester_const_purchase_types);

    update_option('placester_display_property_types', $placester_display_property_types);
    update_option('placester_display_listing_types', $placester_display_listing_types);
    update_option('placester_display_zoning_types', $placester_display_zoning_types);
    update_option('placester_display_purchase_types', $placester_display_purchase_types);

    // Update property urls
    if (!empty($api_key))
    {
        $url = placester_get_property_url('{id}');
        $filter = array();
        placester_add_admin_filters($filter);
        placester_property_seturl_bulk($url, $filter);
    }
}

?>

<?php

    function myplugin_addbuttons() {
        add_filter('mce_external_plugins', "tinyplugin_register");
    }

    function tinyplugin_register($plugin_array)
    {
        $plugin_array["tinyplugin"] = 
            plugins_url('/js/admin.settings.tinymce.js', dirname(__FILE__));
        return $plugin_array;
    }

    myplugin_addbuttons();

    if (function_exists('wp_tiny_mce')) {

     wp_tiny_mce(false, array(
        "editor_selector" => "form-input-tip",
        'width' => '100%',
        'theme_advanced_buttons1' => 'tinyplugin,formatselect,bold, italic, underline, separator, bullist, numlist,justifyleft, justifycenter, justifyright, link, unlink',
        'theme_advanced_buttons2' => 'bedrooms, bathrooms, price, available_on, address, city, state, zip',
        'theme_advanced_buttons3' => '',
        'theme_advanced_buttons4' => '',

     ));
    }


?>
<div class="wrap">
  <?php placester_admin_header('placester_settings') ?>
  <?php if (isset($show_success_message)) {     placester_success_message("You're settings have been successfully saved"); } ?>
  <form method="post" action="admin.php?page=placester_settings" id="placester_form">
    <?php placester_postbox_container_header(); ?>

    <?php placester_postbox_header('API Key'); ?>
    <table class="form-table">
      <?php 
      row_textbox('API Key', 'placester_api_key',
          'This is your api key from <a href="http://placester.com">Placester</a> ' .
          'a company dedicated to making real estate marketing painless.' .
          'If you have an api key (found in the ' .
          '<a href="http://placester.com/company/distribution/">distribution</a>' .
          ' tab) copy and paste it in above.' .
          'If you don\'t have an api key, don\'t worry. Just fill out the ' .
          'fields in the <a href="admin.php?page=placester_personal">contact</a> tab '.
          'and one will be generated automatically (and for free).'); 
      ?>
    </table>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save All Changes" />
    </p>
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Regenerate All Listings'); ?>
    <table class="form-table">
      <?php 
      row_hidden('Regenerate listings', 'placester_remove_listings',
          'If you would like to remove and regenerate all listings click "Regenerate Listings" below. ' . 
          'These listings will regenerate as they come up again in the search results.'); 
      ?>
    </table>
    <p class="submit">
      <input type="submit" name="remove" class="button-primary" 
        value="Remove All Listings" />
    </p>
    <?php placester_postbox_footer(); ?>


    <?php placester_postbox_header('Layout Style Settings'); ?>
    <table class="form-table">
      <?php 
      row_textarea('Property snippet layout settings', 'placester_snippet_layout',
          'The property snippet layout settings allow you to customize what ' .
          'appears in the search results or on the home page.  You may use the shortcodes listed below.<br/>' .
          '<strong>Note:</strong> Not every theme will use these settings, ' .
          'and some themes might override your choices.  See your specific ' .
          'theme for details.'); 
      ?>
      <?php 
      row_textarea('Property listing page layout', 'placester_listing_layout',
          'The property listing page layout settings allow you to customize what ' .
          'appears on each individual property listing page.  You may use the shortcodes listed below.<br/>' .
          '<strong>Note:</strong> Not every theme will use these settings, ' .
          'and some themes might override your choices.  See your specific ' .
          'theme for details.'); 
      ?>

    </table>
    <div class="clear"></div>
      <div style="padding-left:200px">
      <p>
            Available shortcodes:<br />
            <div style="float: left; margin-right: 20px">
            For property listings: <br/><br/>
                [bedrooms]<br />
                [bathrooms]<br />
                [price]<br />
                [available_on]<br />
                [listing_address]<br />
                [listing_city]<br />
                [listing_state]<br />
                [listing_unit]<br />
                [listing_zip]<br />
                [listing_neighborhood]<br />
                [listing_map]<br/>
                [listing_description]<br />
                [listing_image]<br />
                [listing_images]<br />

            </div>
            <div style="float: left; margin-right: 20px">
            For listing agent information: <br/><br/>
                [logo]<br />
                [first_name]<br />
                [last_name]<br />
                [phone]<br />
                [email]<br />
                [user_address]<br />
                [user_unit]<br />
                [user_city]<br />
                [user_state]<br />
                [user_zip]<br />
                [user_description]<br />

            </div>
            <div class="clear"></div>
            </p>
        </div>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Layout Style Settings" />
    </p>
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Map Style Settings'); ?>
    <table class="form-table">
      <?php 
      row_textarea('Info window html', 'placester_map_info_template',
          '<img style="float: left; margin-right: 10px" src="' . 
          plugins_url('/images/settings-infowindow-help.png', dirname(__FILE__)) .
          '" />The info window html setting allows you to customize what ' .
          'appears in the box that appears when a marker is clicked on. ' .
          'Use the beds, baths, etc.. buttons to indicate where that listing ' .
          'specific information should be displayed. For example, if you say: ' .
          'Beds:[bedrooms].  Placester will automatically insert the correct ' .
          'number of bedrooms no matter which listing is clicked on.'); 
      ?>
      <?php 
      row_image('Marker icon', 'placester_map_marker_image',
          '<img style="float: left; margin-right: 10px" src="' . 
          plugins_url('/images/sample-marker.png', dirname(__FILE__)) . 
          '" />The image used to display each of your listings on the map. ' .
          'Default is a red google maps marker. <br />' .
          '<strong>Note</strong>: Some themes may override your custom ' .
          'marker to preserve their look and feel. ' .
          'Check your specific theme for details.'); 
      ?>
      <?php 
      row_image('Marker hover icon', 'placester_map_marker_hover_image', 
          '<img style="float: left; margin-right: 10px" src="' .  
          plugins_url('/images/sample-hover-marker.png', dirname(__FILE__)) . 
          '" /> The image displayed when a mouse cursor is "over" one of your ' .
          'listings.  A yellow google maps icon is the default.<br />' .
          '<strong>Note</strong>: Some themes may override your custom ' .
          'marker to preserve their look and feel. Check your specific theme ' .
          'for details.'); ?>
      <?php 
      row_image('Tile loading icon', 'placester_map_tile_loading_image',
          '<img style="float: left; margin-right: 10px" src="' .  
          plugins_url('/images/sample-loading.png', dirname(__FILE__)) . 
          '" /> An image, or animated "gif". Displayed over each section of the ' .
          'map as listings are being loaded.<br />' .
          '<strong>Note</strong>: Some themes may override your loading icon to ' .
          'preserve their look and feel.  Check your specific theme for details.'); 
      ?>
    </table>		
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save All Changes" />
    </p>        
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('List'); ?>
    <table class="form-table">
      <?php 
      row_textarea('Details row html', 'placester_list_details_template',
          '<img style="float: left; margin-right: 10px" src="' .
          plugins_url('/images/settings-infowindow-help.png', dirname(__FILE__)) . 
          '" /> This html will be displayed when a user "clicks" on a row in ' .
          'a datatable. Use the beds, baths, etc.. icons to specify where ' .
          'listing specific information should be inserted.<br />' .
          '<strong>Note:</strong> Not every theme will use a datatable, ' .
          'and some themes might override your choices.  See your specific ' .
          'theme for details.'); 
      row_checkbox('Searchable table', 'placester_list_searchable'); 
      ?>
    </table>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save All Changes" />
    </p>        
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Display only'); ?>
    <div>
      <?php
      row_checkboxes('Property Types', $placester_const_property_types, 
        'placester_display_property_types');
      row_checkboxes('Listing Types', $placester_const_listing_types, 
        'placester_display_listing_types');
      row_checkboxes('Zonings', $placester_const_zoning_types, 
        'placester_display_zoning_types');
      row_checkboxes('Purchase Types', $placester_const_purchase_types, 
        'placester_display_purchase_types');
      ?>
    </div>
    <table class="form-table">
      <tr>
        <td>
          <span class="description">
            Display only settings will "pre-filter" all your listings so only those 
            meeting the criteria set on the left are returned.  This is helpful if you'd 
            like to create multiple sites for different types of listings. 
            Often, you'll want to customize the look and feel of a site to be 
            more appealing to prospective clients who are interested certain type 
            of listing.
          </span>
        </td>
      </tr>
    </table>
    <div style="clear: both"></div>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save All Changes" />
    </p>        
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('General'); ?>
    <table class="form-table">
      <?php 
      row_textbox('URL slug', 'placester_url_slug',
          'The url slug forces a keyword or a series of keywords into the url ' .
          'of each property details page. The structure of the url is ' .
          'important for indicating what a specific page does. <br />' .
          '<strong>Tip:</strong> If your unsure what to use, try "listing" ' .
          'or "property".<br /><br />'.
          'Depending on your <a href="options-permalink.php">permalink settings</a>:' .
          '<br />' . get_bloginfo('url') . '/<span id="url_target" ' .
          'style="font-weight: bold;"></span>/4d6e805aabe10f0f1500004c');
      ?>
    </table>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save All Changes" />
    </p>
    <?php placester_postbox_footer(); ?>

    <p class="submit">
      <input type="submit" name="set_default" class="button" 
        value="Revert all settings to defaults" />
    </p>

    <?php placester_postbox_container_footer(); ?>
  </form>
</div>
