<?php
// When including/loading this script, make sure to set the $color variable

if ( isset($color) ) 
{
  ob_start();
  ?>
	header h1 a, header h1 a:visited {
		color: <?php echo $color; ?> !important;
	}

	header h1 a:hover {
		color: <?php echo $color; ?> !important;
	}

	.main-nav ul li a:hover {
		color: white !important;
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}

	.main-nav ul li.current_page_item {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}

	.main-nav {
		border: 1px default  !important;
	}

	#placester_listings_list .single-item .list-thumb-size .thumbs {
		border: 1px default  !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}

	body.single-property #property-details-main-image img {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}

	/* Start Custom CSS */

	.blue {
		color: <?php echo $color; ?> !important;
	}

	header .main-nav ul li:hover {
		background-color: <?php echo $color; ?> !important;
	}

	header nav ul li a:visited:hover, 
	header nav ul li a:link:hover, 
	header nav ul li a:hover {
		color: white !important;
	}

	header #nav-login a:hover {
	background-color: <?php echo $color; ?> !important;
	}

	header #nav-login a:hover, 
	header section.phone ul li a:hover, 
	header section.email ul li a:hover {
		color: #666 !important;
	}

	body.page-template-page-template-listings-php a.feat-title, 
	body.page-template-page-template-listings-php a.feat-title:visited, 
	body.page-template-page-template-client-php a.feat-title, 
	body.page-template-page-template-client-php a.feat-title:visited, 
	aside .widget-pls-agent .agent-phone strong  {
		color: black !important;
	}

	footer #footer-nav ul li a:hover , footer #footer-nav ul li a, footer #footer-base a:hover, footer #footer-base a:visited:hover , footer a{
		color: white !important;
	}

	body.page-template-page-template-listings-php a.feat-title:hover, 
	body.page-template-page-template-listings-php a.feat-title:visited:hover, 
	body.page-template-page-template-client-php a.feat-title:hover, 
	body.page-template-page-template-client-php a.feat-title:visited:hover {
		color: <?php echo $color; ?> !important;
	}

	header nav ul li a:visited:hover, 
	header nav ul li a:link:hover, 
	header nav ul li a:hover, 
	header nav ul li a:visited:hover, 
	header nav ul li a:link:hover, 
	header nav ul li a:hover {
		color: white !important;
	}

	#banner, 
	#home-banner {
		background: <?php echo $color; ?> !important;
	}

	footer, 
	footer #footer-base {
		/* background: black !important; */
	}

	footer #footer-base a, 
	footer #footer-base a:visited {
		color: #999 !important;
	}
	
	footer #footer-base a, 
	footer #footer-base a:visited, 
	footer #footer-nav ul li a, 
	footer #footer-base a:hover, 
	footer #footer-base a:visited:hover, 
	footer #footer-nav ul li a:hover {
		color: white;
	}

	aside .widget-pls-quick-search input#search, 
	body.page-template-page-template-listings-php a.seemore-btn-sml, 
	body.page-template-page-template-client-php a.seemore-btn-sml, 
	input[class="btn-biggest"], 
	#home-listings .home-listings-featured .home-listings-featured-info a.learnmore-btn-sml, 
	.button-primary {
		background: <?php echo $color; ?> !important;
		border-radius: 10px;
		border: 1px solid <?php echo $color; ?>;
		border-top: 1px solid <?php echo $color; ?>;
		border-left: 1px solid <?php echo $color; ?>;
		border-right: 1px solid <?php echo $color; ?>;
		border-bottom: 1px solid <?php echo $color; ?>;
	}

	body.single-property article.property-details #pl_add_remove_lead_favorites a, 
	body.single-property article.property-details #pl_add_remove_lead_favorites a:visited, 
	body.single-property article.property-details #pl_add_remove_lead_favorites a, 
	body.single-property article.property-details #pl_add_remove_lead_favorites a:visited {
		background: <?php echo $color; ?> !important;
		border-radius: 10px;
		border: 1px solid <?php echo $color; ?> !important;
	}
  <?php

  echo ob_get_clean();
}