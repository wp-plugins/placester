<?php
/**
 * Post type/Shortcode to display neighbourhood search form
 *
 */

class PL_Neighborhood_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_neighborhood';

	protected static $shortcode = 'search_neighborhood';

	protected static $title = 'Neighborhood';

	protected static $help = 
		'<p>
		You can add a neighborhood area via the [pl_neighborhood] shortcode. 
		The neighborhood could list an area with polygons for a given region, such as:
		</p> 
		<ul>
			<li>Neighborhood</li>
			<li>City</li>
			<li>Zip code</li>
			<li>State</li>
		</ul>';

	protected static $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),
		'width'				=> array( 'type' => 'text', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'text', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	);

	protected static $subcodes = array(
		'nb_title'			=> array('help' => ''),
		'nb_featured_image'	=> array('help' => ''),
		'nb_description'	=> array('help' => ''),
		'nb_link'			=> array('help' => ''),
		'nb_map'			=> array('help' => '')
	);

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'HTML', 'css' => 'mime_html', 'default' => 'Put subcodes here to build your form...' ),
		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS', 'css' => 'mime_css', 'default' => '' ),
		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the template', 'css' => 'mime_html', 'default' => '' ),
		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the template', 'css' => 'mime_html', 'default' => '' ),
	);
}

PL_Neighborhood_CPT::init(__CLASS__);
