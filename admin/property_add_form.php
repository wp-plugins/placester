<?php

/**
 * Admin interface: Add listing tab
 * "Add listing" form
 */

?>
<div class="wrap">
  <?php admin_header('placester_property_add') ?>

  <h3>Add Listing</h3>
  <form method="post" action="admin.php?page=placester_property_add"
    enctype="multipart/form-data">
    <?php
    if (strlen($error_message) > 0)
      placester_error_message($error_message);

    property_form($p, $v);
    row_images();
    ?>

    <p class="submit">
      <input type="submit" name="add_finish" class="button-primary" 
        value="Add Property" />
    </p>
  </form>
</div>