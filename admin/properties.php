<?php

/**
 * Admin interface: My Listings tab
 * Entry point
 */

$base_url = WP_PLUGIN_URL . '/placester';

/**
 *      Commented in favor of the warning message approach so user doesn't think something has broken
 *         When the view doesn't load
 */
$locations = new StdClass;
$locations->city = array();
$locations->state = array();
$locations->zip = array();
try
{
    $locations = placester_location_list();
    sort($locations->city);
    sort($locations->state);
    sort($locations->zip);
}
catch (Exception $e)
{
    // placester_warning_message('You need to add your contact details before you can continue.  Navigate to the <a href="/wp-admin/admin.php?page=placester_contact">personal tab</a> and add an email address to start.');


}

?>
<script>

var placesterListLone_base_url = '<?php echo $base_url ?>';

</script>
<div class="wrap">
  <?php admin_header('placester_properties') ?>

  <table style="margin-top: 10px">
    <tr>
      <td>City:</td>
      <td>
        <select id='location_city'>
          <option></option>
          <?php 
          foreach ($locations->city as $city)
              echo '<option>' . $city . '</option>';
          ?>
        </select>
      </td>
      <td>State:</td>
      <td>
        <select id='location_state'>
          <option></option>
          <?php 
          foreach ($locations->state as $state)
              echo '<option>' . $state . '</option>';
          ?>
        </select>
      </td>
      <td>Zip:</td>
      <td>
        <select id='location_zip'>
          <option></option>
          <?php 
          foreach ($locations->zip as $zip)
              echo '<option>' . $zip . '</option>';
          ?>
        </select>
      </td>
      <td>Bathrooms:</td>
      <td>
           <select id='min_bathrooms'>
               <option>1</option>
               <option>2</option>
               <option>3</option>
               <option>4</option>
               <option>5</option>
               <option>6</option>
               <option>7</option>
               <option>8</option>
               <option>9</option>
               <option>10</option>
            </select>               
          <!-- <input id='min_bathrooms' /> -->
      </td>
      <td>Bedrooms:</td>
      <td>
          <select id='min_bedrooms'>
              <option>1</option>
              <option>2</option>
              <option>3</option>
              <option>4</option>
              <option>5</option>
              <option>6</option>
              <option>7</option>
              <option>8</option>
              <option>9</option>
              <option>10</option>
           </select>
          <!-- <input id='min_bedrooms' /> -->
      </td>
      <td></td>
      <td><p class="submit" style="margin: 0; padding: 0"><input id='filter_button' type=button value='Filter' /></p></td>
    </tr>
  </table>
  
  <div id="container">
    <table id="placester_listings_list" style="border: 1px solid gray; width: 100%">
      <thead>
        <tr>
          <th style="width: 50px"></th>
          <th style="width: 25px"></th>
          <th>Address</th>
          <th style="width: 50px">baths</th>
          <th style="width: 50px">beds</th>
          <th style="width: 50px">price</th>
          <th style="width: 100px">location.city</th>
          <th style="width: 50px">Url</th>
          <th style="width: 60px">new</th>
          <th style="width: 100px">featured</th>
          <th style="width: 60px">edit</th>
        </tr>
      </thead>
    </table>
  </div>
</div>