<?php 

$lead_details = PL_Lead_Helper::get_lead_details_by_id($_GET['id']);
// error_log(var_export($lead_details, true));

 ?>
<input id="lead_id" type="hidden" value="<?php echo $_GET['id'] ?>" >
<h2 class="person-name-contact">
  <span class="name"><?php echo $lead_details['full_name'] ?></span>  
  <a href="<?php echo ADMIN_MENU_URL ?>?page=placester_my_leads&id=<?php echo $_GET['id'] ?>&edit=1" class="add-new-h2">Edit</a>
  <a href="#" id="delete_lead" class="add-new-h2">Delete</a>
</h2>
<div class="person-details">
  <span class="phone"><?php echo $lead_details['phone'] ?></span>
  <span class="email"><a mailto="<?php echo $lead_details['email'] ?>"><?php echo $lead_details['email'] ?></a></span>
  <span class="created">(Created on: <?php echo $lead_details['created'] ?>)</span>  
</div>


<div class="both"></div>

<div id="container" class="saved-searches">
  <p class="saved-searches-title">Saved Searches (<?php echo $lead_details['saved_searches'] ?>) <a href="<?php echo ADMIN_MENU_URL ?>?page=placester_my_leads&id=<?php echo $_GET['id'] ?>&new_search=1" class="add-new-h2">Add New Search</a></p>
  <table id="placester_saved_search_list" class="widefat post fixed placester_properties" cellspacing="0">
    <thead>
      <tr>
        <th><span>Date Created</span></th>
        <th><span>Search Name</span></th>
        <th><span>Fields Saved</span></th>
        <th><span>Last Updated</span></th>
        <th><span># of Saved Searches</span></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>

<div class="both"></div>

<div id="container" class="favorite-listings">
  <p class="saved-searches-title">Favorite Listings (<?php echo $lead_details['favorited_listings']  ?>) <a href="#" id="add_favorite" class="add-new-h2">Add New Favorite Listing</a></p>
  <table id="placester_favorite_listings_list" class="widefat post fixed placester_properties" cellspacing="0">
    <thead>
      <tr>
        <th><span></span></th>
        <th><span>Address</span></th>
        <th><span>Beds</span></th>
        <th><span>Baths</span></th>
        <th><span>Price</span></th>
        <th><span>Sqft</span></th>
        <th><span>MLS ID</span></th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>


<!-- <div style="display:none" id="delete_listing_confirm">
  <div id="delete_response_message"></div>
  <div>Are you sure you want to permanently delete <span id="delete_listing_address"></span>?</div>  
</div> -->
