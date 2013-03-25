<?php

/**
 *  Include all widgets from the widgets directory
 */

add_action( 'widgets_init', 'pl_register_widgets' );

function pl_register_widgets() {
  // widget seems unfinished and requested to not show up re:
  // https://app.asana.com/0/1618131686665/2329684547950
  // "Issue - PL Widget Removal"
	// include PL_PARENT_DIR . "/lib/pl_widget.php";
	/*
	foreach (glob( PL_PARENT_DIR . "/lib/widgets/*widget.php") as $filename) {
		include_once $filename;
	}
	*/
}
