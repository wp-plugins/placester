<?php

/**
 * Admin interface: Edit listing / Add listing utilities
 */

/**
 * @var array $multi_types - list of property types for use from UI
 */
global $multi_types;
$multi_types = array();
$multi_types['housing,commercial,rental'] = 'Commercial Rental';
$multi_types['housing,commercial,sale'] = 'Commercial Sale';
$multi_types['land,,'] = 'Land';
$multi_types['other,,'] = 'Other';
$multi_types['parking,,rental'] = 'Parking';
$multi_types['housing,residential,rental'] = 'Residential Rental';
$multi_types['housing,residential,sale'] = 'Residential Sale';
$multi_types['sublet,residential'] = 'Residential Sublet';
$multi_types['sublet,commercial'] = 'Commercial Sublet';
$multi_types['vacation,,rental'] = 'Vacation Rental';



/**
 * Returns property value, or null if not exists.
 * Returns value of $o->p1->p2 when $property = 'p1/p2'
 *
 * @param object $o
 * @param string $property
 * @return string or null
 */
function p($o, $property)
{
    if (!isset($o))
        return null;

    $parts = explode('/', $property);
    for ($n = 0; $n < count($parts); $n++)
    {
        $p = $parts[$n];
        if (!isset($o->$p))
            return null;

        $o = $o->$p;
        if (is_array($o))
            $o = $o[0];
    }

    return $o;
}



/**
 * Prints dropdown with possible selected value $value.
 *
 * @param string $property_name
 * @param array $possible_values
 * @param string $value
 * @param string $width
 */
function control_dropdown($property_name, $possible_values, $value, 
    $width = '')
{
    ?>
    <select class="heading form-input-tip" 
      name="<?php echo $property_name ?>" 
      id="<?php echo $property_name ?>" 
      style="width: <?php echo $width ?>" 
      class="heading form-input-tip">
    <?php

    foreach ($possible_values as $key => $name)
    {
        if (!is_array($value))
            $is_selected = ($key == $value);
        else
        {
            $is_selected = false;
            foreach ($value as $v)
            {
                if ($key == $v)
                {
                    $is_selected = true;
                    break;
                }
            }
        }

        echo '<option value="' . $key . '"' . ($is_selected ? ' selected' : '') . 
            '>' . $name . '</option>';
    }

    echo '</select>';
}



/**
 * Prints dropdown inside html <table> column
 * with possible validation error messages
 *
 * @param string $label
 * @param array $possible_values
 * @param string $property_name
 * @param string $value
 * @param object $validation_object
 * @param string $width
 * @param string $colspan
 */
function column_dropdown($label, $possible_values, $property_name, 
    $value, $validation_object, $width = '', $colspan = '1')
{
    $value = placester_get_property_value($value, $property_name);
    $validation_message = p($validation_object, $property_name);

    ?>
    <th scope="row"><label for="<?php echo $property_name ?>"><?php echo $label ?></label></th>
    <td colspan="<?php echo $colspan ?>">
      <?php 
      control_dropdown($property_name, $possible_values, $value, $width);
      if (isset($validation_message))
      {
          echo '<br/><div style="color:red">';
          echo $validation_message;
          echo '</div>';
      }
      ?>
    </td>
    <?php
}



/**
 * Prints dropdown in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param array $possible_values
 * @param string $property_name
 * @param string $value
 * @param object $validation_object
 */
function row_dropdown($label, $possible_values, $property_name, 
    $value, $validation_object)
{
    ?>
    <tr valign="top">
      <?php 
      column_dropdown($label, $possible_values, $property_name, 
          $value, $validation_object, '', 3);
      ?>
    </tr>
    <?php
}



/**
 * Prints textbox inside html <table> column
 * with possible validation error messages
 *
 * @param string $label
 * @param string $property_name
 * @param string $value
 * @param object $validation_object
 * @param string $colspan
 */
function column_textbox($label, $property_name, $value, $validation_object, 
    $colspan = '1')
{
    $value = placester_get_property_value($value, $property_name);
    $validation_message = p($validation_object, $property_name);
    $id = str_replace('/', '_', $property_name);

    ?>
    <th scope="row"><label for="<?php echo $id ?>"><?php echo $label ?></label></th>
    <td colspan="<?php echo $colspan ?>">
      <input type="text" name="<?php echo $property_name ?>"
        id="<?php echo $id ?>"
        value="<?php echo htmlspecialchars($value) ?>" 
        class="heading form-input-tip" 
        style="width:100%" />
      <?php 
      if (isset($validation_message))
      {
          echo '<br/><div style="color:red">';
          echo $validation_message;
          echo '</div>';
      }
      ?>
    </td>
    <?php
}



/**
 * Prints textbox in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param string $property_name
 * @param string $value
 * @param object $validation_object
 */
function row_textbox($label, $property_name, $value, $validation_object)
{
    ?>
    <tr valign="top">
      <?php 
      column_textbox($label, $property_name, $value, $validation_object, 3);
      ?>
    </tr>
    <?php

}



/**
 * Prints textarea in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param string $property_name
 * @param string $value
 * @param object $validation_object
 */
function control_textarea($label, $property_name, $value, $validation_object)
{
    $value = placester_get_property_value($value, $property_name);
    $validation_message = p($validation_object, $property_name);

    ?>
    <textarea 
      name="<?php echo $property_name ?>" 
      id="<?php echo $property_name ?>" 
      rows="5" 
      class="heading form-input-tip" 
      style="width:100%"><?php echo htmlspecialchars($value) ?></textarea>
    <?php 
    if (isset($validation_message))
    {
        echo '<br/><div style="color:red">';
        echo $validation_message;
        echo '</div>';
    }
}



/**
 * Prints images upload control in html <table> row
 * with possible validation error messages
 *
 * @param string $validation_message
 */
function box_images($validation_message = '')
{
    placester_postbox_header('Images');

    ?>
    <input type="file" name="images[]" class="multi" />
    <?php 

    if (isset($validation_message))
    {
        echo '<br/><div style="color:red">';
        echo $validation_message;
        echo '</div>';
    }
    placester_postbox_footer();
}



/**
 * Returns property object from HTTP POST data
 *
 * @return object
 */
function http_property_data()
{
    $p = new StdClass;
    foreach ($_POST as $key => $value)
        placester_set_property_value($p, $key, $value);

    $a = explode(',', $p->combined_type);

    $p->listing_types = array();
    if (count($a) >= 1)
        $p->listing_types[] = $a[0];

    $p->zoning_types = array();
    if (count($a) >= 2)
        if (strlen($a[1]) > 0)
            $p->zoning_types[] = $a[1];

    $p->purchase_types = array();
    if (count($a) >= 3)
        if (strlen($a[2]) > 0)
            $p->purchase_types[] = $a[2];

    return $p;
}



/**
 * Prints property add / edit form
 *
 * @param object $p
 * @param object $v
 */
function property_form($p, $v)
{
    global $placester_const_property_types;
    global $multi_types;

    $company = get_option('placester_company');

    if (! placester_is_api_key_specified())
    {
        ?>
        <input id="property_readonly" type="hidden" value="warning_no_api_key" />
        <?php
    }
    else if ($company instanceof StdClass &&
        isset($company->provider) && isset($company->provider->name))
    {
        $name = 'aa'; //$company->provider->name;
        placester_warning_message("We're automatically pulling " .
            'in your listings from ' . $name . '. ' .
            'Add or edit your listings with ' . $name, 'provider_warning');
        ?>
        <input id="property_readonly" type="hidden" value="provider_warning" />
        <?php
    }

    $array_1_9 = array();
    for ($n = 1; $n <= 9; $n++)
        $array_1_9[$n] = $n . '&nbsp;&nbsp;';

    ?>

    <?php placester_postbox_header('Type'); ?>
    <table class="form-table">
      <?php
      row_dropdown('Listing Type', $multi_types, 'combined_type', $p, $v);
      ?>
    </table>
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Basics'); ?>
    <table class="form-table">
      <tr>
        <?php
        column_dropdown('Beds', $array_1_9, 'bedrooms', $p, $v);
        column_textbox('Available_on', 'available_on', $p, $v);
        ?>
      </tr>
      <tr>
        <?php
        column_dropdown('Baths', $array_1_9, 'bathrooms', $p, $v);
        column_dropdown('Property Type', $placester_const_property_types, 
            'property_type', $p, $v);
        ?>
      </tr>
      <tr>
        <?php
        column_dropdown('Half baths', $array_1_9, 'half_baths', $p, $v);
        column_textbox('Price', 'price', $p, $v);
        ?>
      </tr>
    </table>
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Address'); ?>
    <table class="form-table">
      <tr>
        <?php
        column_textbox('Address', 'location/address', $p, $v);
        column_textbox('Zip', 'location/zip', $p, $v);
        ?>
      </tr>
      <tr>
        <?php
        column_textbox('City', 'location/city', $p, $v);
        column_textbox('Unit', 'location/unit', $p, $v);
        ?>
      </tr>
      <tr>
        <?php
        column_textbox('State', 'location/state', $p, $v);
        column_textbox('Neighborhood', 'location/neighborhood', $p, $v);
        ?>
      </tr>
      <tr>
        <?php
        column_textbox('Latitude', 'location/coords/latitude', $p, $v);
        ?>
        <td colspan="2" rowspan="3" style="text-align: right">
          <div>
            <input type=button value="&nbsp;&nbsp;Locate by Address&nbsp;&nbsp;"
              onclick="map_geocoded_address = ''; map_geocode_address();" />
          </div>
          <div id="map" style="width: 300px; height: 150px; float: right; clear: both"></div>
        </td>
      </tr>
      <tr>
        <?php
        column_textbox('Longitude', 'location/coords/longitude', $p, $v);
        ?>
      </tr>
      <tr>
        <td colspan="2">
          <br />
          <br />
          <br />
          <br />
        </td>
      </tr>
    </table>
    <?php placester_postbox_footer(); ?>

    <?php placester_postbox_header('Description'); ?>
      <div style="padding: 5px">
        <?php control_textarea('Description', 'description', $p, $v); ?>
      </div>
    <?php placester_postbox_footer(); ?>
    <?php
}
