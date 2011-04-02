<?php

/**
 * Admin interface: Settings tab
 */

include_once(dirname(__FILE__) . '/../core/const.php');
include_once('settings_parts.php');

//
// Save values
//
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
        'width' => '300px',
        'theme_advanced_buttons1' => 'tinyplugin,formatselect,bold, italic, underline, separator, bullist, numlist,justifyleft, justifycenter, justifyright, link, unlink',
        'theme_advanced_buttons2' => 'bedrooms, bathrooms, price, available_on, address, city, state, zip',
        'theme_advanced_buttons3' => '',
        'theme_advanced_buttons4' => '',

     ));
    }


?>
<div class="wrap">
  <?php admin_header('placester_settings') ?>
  <?php if (isset($show_success_message)) {     placester_success_message("You're settings have been successfully saved"); } ?>
  <form method="post" action="admin.php?page=placester_settings">

  	<h3 class="title">API Key</h3>
    <div style="width:900px">
        <table class="form-table" >
            <tr valign="top">
                <?php row_textbox('API Key', 'placester_api_key'); ?>
            </tr>
            <tr>
            <th></th>
            <td>This is your api key from <a href="http://placester.com">Placester</a> a company dedicated to making real estate marketing painless.  
                If you have an api key (found in the <a href="http://placester.com/company/distribution/">distribution</a> tab) copy and paste it in above.
                If you don't have an api key, don't worry.  Just fill out the fields in the <a href="admin.php?page=placester_personal">contact</a> tab
                and one will be generated automatically (and for free).
            </td>
          </tr>
        </table>
    </div>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Changes" />
    </p>
    <hr />


    <h3 class="title" style="float:left;clear:both;">Map Style Settings</h3>
    <div style="width:900px">
        <table class="form-table">
            <tr valign="top">
                <?php row_textarea('Info window html', 'placester_map_info_template'); ?>
                <td>
                    <img style="float: left; margin-right: 10px" src="<?php echo plugins_url(); ?>/placester/images/settings-infowindow-help.png" />
                    The info window html setting allows you to customize what appears in the box that appears when a marker is clicked on.  Use the beds, baths, etc.. buttons to indicate where that listing specific information should be displayed.  For example, if you say: Beds:[bedrooms].  Placester will automatically insert the correct number of bedrooms no matter which listing is clicked on.
                </td>
            </tr>
          <?php row_image('Marker icon', 'placester_map_marker_image','<img style="float: left; margin-right: 10px" src="' .  plugins_url() . '/placester/images/sample-marker.png" />
            The image used to display each of your listings on the map. Default is a red google maps marker.', '<strong>Note</strong>: <em>Some themes may override your custom marker to preserve their look and feel.  Check your specific theme for details.</em>'); ?>
            <tr>
                <th></th>
                <td>

                </td>
            </tr>
          <?php row_image('Marker hover icon', 'placester_map_marker_hover_image',   '<img style="float: left; margin-right: 10px" src="' .  plugins_url() . '/placester/images/sample-hover-marker.png" />
            The image displayed when a mouse cursor is "over" one of your listings.  A yellow google maps icon is the default', '<strong>Note</strong>: <em>Some themes may override your custom marker to preserve their look and feel.  Check your specific theme for details.</em>'); ?>
          <?php row_image('Tile loading icon', 'placester_map_tile_loading_image',           '<img style="float: left; margin-right: 10px" src="' .  plugins_url() . '/placester/images/sample-loading.png" />
            An image, or animated "gif". Displayed over each section of the map as listings are being loaded.', '<strong>Note</strong>: <em>Some themes may override your loading icon to preserve their look and feel.  Check your specific theme for details.</em>'); ?>
        </table>		
	</div>

    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Changes" />
    </p>        
    <hr />

    <h3 class="title">List</h3>
    <div style="width: 900px">
        <table class="form-table">
            <tr valign="top">
                <?php row_textarea('Details row html', 'placester_list_details_template'); ?>
                <td>
                    <img style="float: left; margin-right: 10px" src="<?php echo plugins_url(); ?>/placester/images/settings-infowindow-help.png" />
                    This html will be displayed when a user "clicks" on a row in a datatable.  Use the beds, baths, etc.. icons to specify where listing specific information should be inserted.  <strong>Note:</strong> <em>Not every theme will use a datatable, and some themes might override your choices.  See your specific theme for details.</em>
                </td>
            </tr>
          <?php row_checkbox('Searchable table', 'placester_list_searchable'); ?>
        </table>
    </div>
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Changes" />
    </p>        
    <hr />

    <h3 class="title">Display only</h3>
    <div style="width: 900px">
        <div style="clear: left; width: 550px">
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
        <div style="float:left;margin: 0 0 0 160px;font-size:11px;width:200px">
            Display only settings will "pre-filter" all your listings so only those meeting the criteria set on the left are returned.  This is helpful if you'd like to create multiple sites for different types of listings. Often, you'll want to customize the look and feel of a site to be more appealing to prospective clients who are interested certain type of listing.
        </div>
    </div>
         <div style="clear: both">
    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Changes" />
    </p>        
    <hr />

  	<h3 class="title">General</h3>
    <table class="form-table">
        <tr valign="top">
          <?php row_textbox('URL slug', 'placester_url_slug'); ?>
          <td>
              The url slug forces a keyword or a series of keywords into the url of each property details page.  The structure of the url is important for indicating what a specific page does. <br /><strong>Tip:</strong><em> If your unsure what to use, try "listing" or "property".</em>
          </td>
        </tr>
      <tr>
        <th></th>
        <td>Depending on your <a href="options-permalink.php">permalink settings</a>:<br />
            <?php echo get_bloginfo('url') . '/<span id="url_target" style="font-weight: bold;"></span>/4d6e805aabe10f0f1500004c' ?>
        </td>
      </tr>
    </table>

    <p class="submit">
      <input type="submit" name="apply" class="button-primary" 
        value="Save Changes" />
    </p>
  </form>
</div>
