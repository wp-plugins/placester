<?php

/**
 * Admin interface: Get Themes tab
 * Entry point
 */

include(ABSPATH . 'wp-admin/includes/theme-install.php');

?>
<div class="wrap">
  <?php 

  placester_admin_header('placester_themes');

  if (!isset($_POST['search']) && !isset($_GET['paged']))
      include('themes_intro.php'); 
  else
      include('themes_results.php'); 

  ?>
</div>
