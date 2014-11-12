<?php

PLS_Style::add(array(
	"name" => "MLS Compliance",
	"type" => "heading"));

	PLS_Style::add(array(
		"name" => "Display Property MLS# and Listing Status",
		"desc" => "Your MLS may require that these fields be displayed in search results. Check this box to make them appear.",
		"id" => "pls-display-mlsid-status",
		"std" => false,
		"type" => "checkbox"));

	PLS_Style::add(array(
		"name" => "Display Zip Codes in Listings",
		"desc" => "Locality and region (city and state) are shown for each listing.  This option enables the inclusion of postal codes.",
		"id" => "pls-display-postal",
		"std" => "",
		"type" => "checkbox"));

	PLS_Style::add(array(
		"name" => "'MLS' Requires Trademark",
		"desc" => "If selected, the '&#174;' symbol will appear whenever the MLS acronym is used in listing data.",
		"id" => "pls-mls-trademarked",
		"std" => "",
		"type" => "checkbox"));

PLS_Style::add(array(
	"name" => "Disable Map Info for Properties",
	"desc" => "Check this box if your MLS disallows the display of listing data in the format used in the search maps.",
	"id" => "pls-disable-map-info",
	"std" => "",
	"type" => "checkbox"));

PLS_Style::add(array(
	"name" => "Disable Auto-Image Resizing",
	"desc" => "Placester makes sure your listing images aren't distorted by automatically resizing them. This option disables resizing.",
	"id" => "pls-disable-dragonfly",
	"std" => "",
	"type" => "checkbox"));
