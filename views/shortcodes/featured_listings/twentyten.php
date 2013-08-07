<?php

$template = array(

'css' => '
/*
This template has no html body because it uses the built in listing renderer.
It can be used as a guide for making a custom template that styles output created by the built in renderer.
*/
.pl-tpl-fl-twentyten {
}

/* style each listing... */
.pl-tpl-fl-twentyten .listing-item {
	display: block !important;
	float: none !important;
	clear: both !important;
	margin: 0 !important;
	border: 1px solid #dfdfdf !important;
	border-width: 0 0 1px 0 !important;
	padding: 10px 0 25px 0 !important;
	background: none !important;
	font-weight: 300 !important;
	overflow: hidden !important;
	font-size: 14px;
	position: relative;
}
/* thumbnail */
.pl-tpl-fl-twentyten .listing-thumbnail {
	float: left !important;
	margin: 0 20px 5px 0 !important;
	width: 180px !important;
}
.pl-tpl-fl-twentyten .listing-thumbnail img {
	display: block !important;
	margin: 0 !important;
	border: none !important;
	padding: 0 !important;
	width: 180px !important;
	height: 120px !important;
}
/* defaults for text */
.pl-tpl-fl-twentyten .listing-item-details p {
	display: block !important;
	float: none !important;
	border: none !important;
	margin: 0 0 .1em 0 !important;
	padding: 0 !important;
	background: none !important;
	line-height: 1.2em !important;
}
.pl-tpl-fl-twentyten .listing-item-details a {
	margin: 0 !important;
	padding: 0 !important;
	text-decoration: none !important;
}
/* info block */
.pl-tpl-fl-twentyten .listing-item-details {
	margin: 0 !important;
	padding: 0 !important;
}
/* heading */
.pl-tpl-fl-twentyten header {
	float: none !important;
	margin: 0 !important;
	padding: 0 !important;
}
.pl-tpl-fl-twentyten p.h4 {
	font-size: 18px !important;
}
.pl-tpl-fl-twentyten .h4 a {
	color: inherit;
}
.pl-tpl-fl-twentyten .basic-details ul {
	float: none !important;
	margin: .3em 0 !important;
	padding: 0 !important;
	width: auto !important;
	max-width: 370px !important;
	list-style-type: none !important;
	list-style-image: none !important;
	overflow: hidden !important;
}
.pl-tpl-fl-twentyten .basic-details li {
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
.pl-tpl-fl-twentyten p.listing-description,
.pl-tpl-fl-twentyten .compliance-wrapper p {	
	float: left !important;
	margin: 0 0 .2em 0 !important;
	max-height: 52px !important;
	max-width: 370px !important;
	line-height: 17px !important;
	font-size: 14px !important;
	font-family: Georgia,"Bitstream Charter",serif !important;
	overflow: hidden !important;
}
.pl-tpl-fl-twentyten .compliance-wrapper {
	float: right;
}
.pl-tpl-fl-twentyten .compliance-wrapper p {
	font-size: .8em;	
}
.pl-tpl-fl-twentyten .actions {
	float: none !important;
	position: absolute;
	bottom: 0;
	right: 0;
	margin: 0 !important;
	padding: 0 !important;
}
.pl-tpl-fl-twentyten a.more-link {
	float: right !important;
	margin-left: 1em !important;
}
.pl-tpl-fl-twentyten #pl_add_remove_lead_favorites,
.pl-tpl-fl-twentyten .pl_add_remove_lead_favorites {
	float: right !important;
}
',

'snippet_body' => '',

'before_widget' => '<div class="pl-tpl-fl-twentyten">',

'after_widget' => '</div>',

);
