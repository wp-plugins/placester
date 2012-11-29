<?php
// When including/loading this script, make sure to set the $color variable

if ( isset($color) ) 
{
  ob_start();
  ?>
	body {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	#inner {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	header h1 a {
		color: <?php echo $color; ?> !important;
	}
	
	body.page-template-page-template-listings-php #full-search h3, body.home #main #listing h3 {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default <?php echo $color; ?> !important;
	}
	
	#main #listing h3, body.page-template-page-template-listings-php #main h3 {
		background-repeat: repeat !important;
		background-position: top center !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}
	
	.orbit-caption p.address {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}
	
	header .primary, header .primary ul ul, header nav {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	.primary ul ul.sub-menu li a {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	header nav li.current_page_item > a {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	header nav li a:hover, header nav li a:focus {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	#listing .lu-left p, #placester_listings_list .lu-left p {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: repeat !important;
		background-position: top center !important;
		background-attachment: scroll !important;
	}
	
	#listing .lu-right .lu-links a.info, #placester_listings_list .lu-right a.info {
		color: <?php echo $color; ?> !important;
	}
	
	.lu-right a.details, .lu-right a.details {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: repeat !important;
		background-position: top center !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}
	
	body.single-property .neighborhood-widget h3 {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}
	
	body.single-property #map-widget h3 {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}
	
	body.single-property #gallery-widget h3 {
		border: 1px default  !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default  !important;
	}
	
	body.page-template-page-template-blog-php #inner #main .blog-post .post-top {
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
	}

	.widget h3, aside #floating-box #map h3, aside .placester_contact h3 {
		background: <?php echo $color; ?> !important;
		filter: none !important;
		background-repeat: no-repeat !important;
		background-position: top left !important;
		background-attachment: scroll !important;
		border: 1px default <?php echo $color; ?> !important;
	}

	/* Start Custom CSS */

	aside section.placester_contact form[name="widget_contact"] input[type="submit"]:hover, 
	aside section.placester_contact form[name="widget_contact"] input[type="submit"],  
	.widget-pls-quick-search h3, 
	.widget-pls-recent-posts h3, 
	body.home #main #listing h3, 
	aside section.placester_contact h3, 
	section.placester_contact form[name="widget_contact"] input[type="submit"]:hover, 
	.side-ctnr.placester_contact input[type="submit"]:active, 
	.side-ctnr.placester_contact input[type="submit"]:hover, 
	body.single-property aside .sidebar-add-to-favorites-link a, 
	body.single-property aside .sidebar-add-to-favorites-link a:visited, 
	body.single-property aside .sidebar-add-to-favorites-link a:hover, 
	body.single-property aside .sidebar-add-to-favorites-link a:visited:hover, 
	div#full-form #search-button, 
	#search-widget input#search, 
	#search-form-area #full-search h3 { 
		background: <?php echo $color; ?>; 
		border: 1px solid <?php echo $color; ?>; 
	} 

	div#full-form #search-button:hover, #search-widget input#search:hover, #comments-template form p input#submit, #comments-template form p input#submit:hover { 
		background: <?php echo $color; ?>; 
		border: 1px solid <?php echo $color; ?>; 
	}

	div.lu-right a.details { 
		background: <?php echo $color; ?>; 
		border: 1px solid <?php echo $color; ?>; 
	}

	div.main-widget.amenities-widget h3, .widget-pls-agent h3, .widget-pls-listings h3 { 
		background: <?php echo $color; ?>; 
		border: 1px solid <?php echo $color; ?>; 
	}

	body.page-template-page-template-listings-php #floating-box #map h3 { 
		border: 1px solid <?php echo $color; ?>; 
	}

	.orbit-wrapper .orbit-caption p.address, section#location-widget h3, #featured-widget h3 { 
		background: <?php echo $color; ?>; 
		border: 1px solid <?php echo $color; ?>; 
	}

	section.pls-listing-get_listings_widget div.featured-slot span, section#fold .fold-r p.price  {
		color: <?php echo $color; ?>; 
	}

	a:hover , a:visited , a , div.lu-right #pl_add_remove_lead_favorites a, #agent-widget span.email a, #agent-widget span.phone a {
		color: <?php echo $color; ?>;
	}

	a.read-more , body.single-post article a:visited, body.single-post article a, a:post-edit-link {
		color: <?php echo $color; ?>;
	}
  <?php

  echo ob_get_clean();
}
