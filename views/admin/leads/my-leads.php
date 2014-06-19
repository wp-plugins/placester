<?php PL_Form::generate_form(PL_Config::PL_API_LEAD('get', 'args'), array('method' => "POST", 'title' => true, 'include_submit' => false, 'id' => 'pls_admin_my_leads', 'textarea_as_text' => true)); ?>
<div id="container">
  <table id="placester_leads_list" class="widefat post fixed placester_properties" cellspacing="0">
    <thead>
      <tr>
        <th><span>Date Created</span></th>
        <th><span>Name</span></th>
        <th><span>Email</span></th>
        <th><span>Phone</span></th>
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
<div style="display:none" id="delete_listing_confirm">
  <div id="delete_response_message"></div>
  <div>Are you sure you want to permanently delete <span id="delete_listing_address"></span>?</div>  
</div>
