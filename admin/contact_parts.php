<?php

/**
 * Admin interface: Contact tab
 * Utilities
 */

/**
 * Returns property value or null is it's not exists
 *
 * @param object $o
 * @param string $property
 * @return string
 */
function p($o, $property)
{
    if (!isset($o) || !isset($o->$property))
        return null;
    return $o->$property;
}



/**
 * Prints address fields in html <table> rows 
 * with possible validation error messages
 *
 * @param string $option_name_prefix
 * @param object $data
 * @param object $validation_data
 * @param string $property
 */
function row_address($option_name_prefix, $data, $validation_data, $property)
{
    $location_data = p($data, $property);
    $location_validation = p($validation_data, $property);
    if (is_array($location_validation))
        $location_validation = $location_validation[0];

    row_textbox('Address', $option_name_prefix . 'address', 
        $location_data, $location_validation, 'address');
    row_textbox('Unit', $option_name_prefix . 'unit', 
        $location_data, $location_validation, 'unit');
    row_textbox('City', $option_name_prefix . 'city', 
        $location_data, $location_validation, 'city');
    row_textbox('State', $option_name_prefix . 'state', 
        $location_data, $location_validation, 'state');
    row_textbox('Zip', $option_name_prefix . 'zip', 
        $location_data, $location_validation, 'zip');
}



/**
 * Prints image upload textbox in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param string $option_name
 * @param object $value_object, 
 * @param object $validation_objet
 * @param string $property
 */
function row_image($label, $option_name, $value_object, 
    $validation_objet, $property)
{
    $id = p($value_object, $property);
    $img = '';
    if (strlen($id) > 0)
    {
        $thumbnail = wp_get_attachment_image_src($id, 'thumbnail');
        $img = '<img src="' . $thumbnail[0] . '" />';
    }

    ?>
    <tr valign="top">
      <th scope="row"><label><?php echo $label ?></label></th>
      <td>
        <input type="file" name="file" id="<?php echo $option_name ?>_file" />
        <input type="hidden" name="<?php echo $option_name ?>" 
          id="<?php echo $option_name ?>" value="<?php echo $id ?>" />
      </td>
    </tr>
    <tr valign="top">
      <th></th>
      <td><div id="<?php echo $option_name ?>_thumbnail"><?php echo $img ?></div></td>
    </tr>
    <?php
}



/**
 * Prints textbox in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param string $option_name
 * @param object $value_object, 
 * @param object $validation_object
 * @param string $property
 */
function row_textbox($label, $option_name, $value_object, 
    $validation_object, $property)
{
    ?>
    <tr valign="top">
      <th scope="row"><label for="<?php echo $option_name ?>"><?php echo $label ?></label></th>
      <td>
        <input type="text" name="<?php echo $option_name ?>"
          value="<?php echo p($value_object, $property) ?>" 
          class="heading form-input-tip" 
          style="width:100%" />
        <?php 
        $validation_message = p($validation_object, $property);
        if (is_array($validation_message))
            $validation_message = $validation_message[0];

        if (isset($validation_message))
        {
            echo '<br/><div style="color:red">';
            echo $validation_message;
            echo '</div>';
        }
        ?>
      </td>
    </tr>
    <?php
}



/**
 * Prints textarea in html <table> row
 * with possible validation error messages
 *
 * @param string $label
 * @param string $option_name
 * @param object $value_object, 
 * @param object $validation_object
 * @param string $property
 */
function row_textarea($label, $option_name, $value_object, 
    $validation_object, $property)
{
    ?>
    <tr valign="top">
      <th scope="row"><label for="<?php echo $option_name ?>"><?php echo $label ?></label></th>
      <td>
        <textarea name="<?php echo $option_name ?>" rows="5" 
          class="heading form-input-tip" 
          style="width:100%"><?php echo p($value_object, $property) ?></textarea>
        <?php 
        $validation_message = p($validation_object, $property);
        if (isset($validation_message))
        {
            echo '<br/><div style="color:red">';
            echo $validation_message;
            echo '</div>';
        }
        ?>
      </td>
    </tr>
    <?php
}



/**
 * Combines company / user object with data posted by POST method
 * from client (as a result of data modification)
 *
 * @param object $company
 * @param object $user
 */
function details_compine_with_http(&$company, &$user)
{
    foreach ($_POST as $key => $value)
    {
        if (substr($key, 0, 14) == 'user_location_')
        {
            $subfield = substr($key, 14);
            $user->location->$subfield = $value;
        }
        else if (substr($key, 0, 5) == 'user_')
        {
            $field = substr($key, 5);
            $user->$field = $value;
        }
        if (substr($key, 0, 17) == 'company_location_')
        {
            $subfield = substr($key, 17);
            $company->location->$subfield = $value;
        }
        else if (substr($key, 0, 8) == 'company_')
        {
            $field = substr($key, 8);
            $company->$field = $value;
        }
    }
}



/**
 * Prints contact details html form
 * with possible validation error messages
 *
 * @param object $company
 * @param object $user
 * @param object $error_validation_data, 
 */
function details($company, $user, $error_validation_data)
{
    $v_company = new StdClass;
    $v_user = new StdClass;
    if (property_exists($error_validation_data, 'company'))
        $v_company = $error_validation_data->company;
    if (property_exists($error_validation_data, 'user'))
        $v_user = $error_validation_data->user;
 
    ?>
    <h3 class="title">Basic Details</h3>
        <div style="width:910px">
            <div style="width:600px;float:left;clear:left;">
                <table class="form-table">
                    <?php row_textbox('Email', 'user_email', $user, $v_user, 'email'); ?>
                    <?php row_textbox('First Name', 'user_first_name', $user, $v_user, 'first_name'); ?>
                    <?php row_textbox('Last Name', 'user_last_name', $user, $v_user, 'last_name'); ?>
                </table>    
            </div>
            <div style="width:300px;float:left;font-size:11px;margin-left:10px">
                The email address you wish to be contacted at by leads.  Depending on your theme, this email address and name will be used to be displayed outwardly to the public.  Check your theme for specific details
            </div>
        </div>
        <div style="clear:both"></div>
        <p class="submit">
          <input type="submit" name="<?php echo CONTACT_SIGNUP_FORM ? 'signup_finish' : 'edit_finish' ?>" class="button-primary" 
            value="Save Changes" />
        </p>        
        <hr />

    <h3 class="title">Company</h3>
        <div style="width:910px">
            <div style="width:600px;float:left;clear:left;">
                <table class="form-table">
                <?php row_image('Logo', 'company_logo', $company, $v_company, 'logo'); ?>
                <?php row_textbox('Company Name', 'company_name', $company, $v_company, 'name'); ?>                
                <?php row_textbox('Phone', 'company_phone', $company, $v_company, 'phone'); ?>
                </table>
            </div>
            <div style="width:300px;float:left;font-size:11px;margin-left:10px">
                The logo and name of your company.  These will be used by your theme to display to the public.<br />Additionally, the phone number of your company.  Depending on your theme, this will be displayed publicly and thus will receive calls from time to time.
            </div>
            <div style="clear:both"></div>
            <div style="width:600px;float:left;clear:left;">
                <table class="form-table">
                      <?php row_address('company_location_', $company, $v_company, 'location'); ?>        
                </table>
            </div>
            <div style="width: 300px; float: left; font-size: 11px; margin-left: 10px">
                The address of your office.  This could be the location of your office, just a mailing address or both.  To see how it's used.
            </div>
            <div style="clear: both"></div>
            <div style="width: 600px; float: left; clear: left;">
                <table class="form-table">
                  <?php row_textarea('Description', 'company_description', $company, $v_company, 'description'); ?>
                </table>
            </div>
            <div style="width: 300px; float: left; font-size: 11px; margin-left: 10px">
                A description of your company.  Typically this will be used in the about us section and used to give potential clients a bit more information about your company.
            </div>
        </div>
        <div style="clear: both"></div>
    <p class="submit">
          <input type="submit" name="<?php echo CONTACT_SIGNUP_FORM ? 'signup_finish' : 'edit_finish' ?>" class="button-primary" 
        value="Save Changes" />
    </p>        
    <hr />
    
    <h3 class="title">Personal Details</h3>
    <div style="width: 910px">
        <div style="width: 600px; float: left; clear: left;">
            <table class="form-table">
                <?php row_image('Headshot', 'user_logo', $user, $v_user, 'logo'); ?>
                <?php row_textbox('Phone', 'user_phone', $user, $v_user, 'phone'); ?>
                <?php row_textbox('Website', 'user_website', $user, $v_user, 'website'); ?>
                </table>
            </div>
            <div style="width: 300px; float: left; font-size: 11px; margin-left: 10px">
                If your theme supports a headshot, upload one here.  Depending on your theme and it's settings, you may want to list your personal phone number for clients to call.
            </div>
            <div style="clear: both"></div>
            <div style="width: 600px; float: left; clear: left;">
                <table class="form-table">
                    <?php row_address('user_location_', $user, $v_user, 'location'); ?>
                </table>
            </div>
            <div style="width: 300px; float: left; font-size: 11px; margin-left: 10px">
                Depending on your needs, your theme and it's settings you may wish to display a personal or secondary address as well.  Enter that information here. 
            </div>
            <div style="clear: both"></div>
            <div style="width: 600px; float: left; clear: left;">
                <table class="form-table">
                    <?php row_textarea('Bio', 'user_description', $user, $v_user, 'description'); ?>
                </table>
            </div>
            <div style="width: 300px; float: left; font-size: 11px; margin-left: 10px">
                A short description of you.  Depending on your theme and it's settings this might be used throughout the site - typically on an about us or contact us page.
            </div>
            <div style="clear: both"></div>
    <?php
}