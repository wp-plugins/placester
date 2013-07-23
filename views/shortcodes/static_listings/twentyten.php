<?php

$template = array(

'css' => '
/*
This template has no html body because it uses the built in listing renderer.
It can be used as a guide for making a custom template that styles output created by the built in renderer. 
*/
.pl-tpl-stl-twentyten {
}
.pl-tpl-stl-twentyten form {
	clear: both;
	padding: 1em 0 0 0;
}
/* style each listing... */
.pl-tpl-stl-twentyten .listing-item {
	display: block !important;
	float: none !important;
	clear: both !important;
	margin: 0 !important;
	border: none !important;
	padding: 10px 0 5px 0 !important;
	background: none !important;
	font-weight: 300 !important;
	overflow: hidden !important;
	font-size: 14px;
}
/* thumbnail */
.pl-tpl-stl-twentyten .listing-thumbnail {
	float: left !important;
	margin: 0 20px 5px 0 !important;
	width: 180px !important;
}
.pl-tpl-stl-twentyten .listing-thumbnail img {
	display: block !important;
	margin: 0 !important;
	border: none !important;
	padding: 0 !important;
	width: 180px !important;
	height: 120px !important;
}
/* defaults for text */
.pl-tpl-stl-twentyten .listing-item-details p {
	display: block !important;
	float: none !important;
	border: none !important;
	margin: 0 0 .1em 0 !important;
	padding: 0 !important;
	background: none !important;
	line-height: 1.2em !important;
}
.pl-tpl-stl-twentyten .listing-item-details a {
	margin: 0 !important;
	padding: 0 !important;
	text-decoration: none !important;
}
/* info block */
.pl-tpl-stl-twentyten .listing-item-details {
	margin: 0 !important;
	padding: 0 !important;
}
/* heading */
.pl-tpl-stl-twentyten header {
	float: none !important;
	margin: 0 !important;
	padding: 0 !important;
}
.pl-tpl-stl-twentyten p.h4 {
	font-size: 18px !important;
}
.pl-tpl-stl-twentyten .h4 a {
	color: inherit;
}
.pl-tpl-stl-twentyten .basic-details ul {
	float: none !important;
	margin: .3em 0 !important;
	padding: 0 !important;
	width: auto !important;
	max-width: 370px !important;
	list-style-type: none !important;
	list-style-image: none !important;
	overflow: hidden !important;
}
.pl-tpl-stl-twentyten .basic-details li {
	list-style: square outside none !important;
	float: left !important;
	margin: 0 .8em 0.1em 0 !important;
	padding: 0 !important;
	list-style-type: none !important;
	list-style-image: none !important;
	line-height: 1.2em !important;
	font-size: 14px !important;
	font-weight: bold !important;
	font-family: Georgia,"Bitstream Charter",serif !important;
}
.pl-tpl-stl-twentyten p.listing-description {
	float: left !important;
	margin: 0 !important;
	max-height: 52px !important;
	max-width: 370px !important;
	line-height: 17px !important;
	font-size: 14px !important;
	font-family: Georgia,"Bitstream Charter",serif !important;
	overflow: hidden !important;
}
.pl-tpl-stl-twentyten .actions {
	float: right !important;
	clear: none !important;
	margin: 0 !important;
	padding: 0 !important;
	width: auto !important;
}
.pl-tpl-stl-twentyten a.more-link {
	float: right !important;
	margin-left: 1em !important;
}
.pl-tpl-stl-twentyten #pl_add_remove_lead_favorites,
.pl-tpl-stl-twentyten .pl_add_remove_lead_favorites {
	float: right !important;
}
		
/* controls */
.pl-tpl-stl-twentyten .sort_item {
	float: left;
	margin: 0 2em 0 0;
	padding: 0;
}
.pl-tpl-stl-twentyten .sort_item label {
	display: inline;
	padding: 0;
	line-height: 20px;
	font-size: 14px;
}
.pl-tpl-stl-twentyten .sort_item select {
	margin: 0;
}
.pl-tpl-stl-twentyten .dataTables_length {
	float: right;
	margin: 0 0 0 0;
	padding: 0;
}
.pl-tpl-stl-twentyten .dataTables_length label {
	line-height: 20px;
	font-size: 14px;
}
.pl-tpl-stl-twentyten .dataTables_paginate a {
	margin: 0 1em 0 0;
	font-weight: 500;
}
.pl-tpl-stl-twentyten .dataTables_paginate a.paginate_active {
	font-weight: 300;
}

/* table formatting */
.pl-tpl-stl-twentyten #container {
	width: 100% !important;
}
.pl-tpl-stl-twentyten table {
	margin: 0 !important;
	border: 0 !important;
	width: 100% !important;
}
.pl-tpl-stl-twentyten table tr {
	float: none !important;
	border: none !important;
	margin: 0 !important;
	background: transparent !important;
}
.pl-tpl-stl-twentyten table td {
	border: 1px solid #dfdfdf !important;
	border-width: 0 0 1px 0 !important;
	padding: 0 !important;
	background: transparent !important;
}
/* styling for alternate rows */
.pl-tpl-stl-twentyten table tr.odd td {
}
.pl-tpl-stl-twentyten table tr.even td {
}
',

'snippet_body' => '',

'before_widget' => '<div class="pl-tpl-stl-twentyten">',

'after_widget' => '</div>',

);
