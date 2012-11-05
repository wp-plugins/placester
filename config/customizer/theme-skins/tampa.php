<?php
// When including/loading this script, make sure to set the $color variable

if ( isset($color) ) 
{
  ob_start();
  ?>
  	

  <?php

  echo ob_get_clean();
}