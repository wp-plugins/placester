<?php
/**
 * Post type/Shortcode for displaying the slideshow
 *
 */

class PL_Listing_Slideshow_CPT extends PL_SC_Base {

	protected static $pl_post_type = 'pl_slideshow';

	protected static $shortcode = 'listing_slideshow';

	protected static $title = 'Slideshow';

	protected static $help = 
		'<p>
        You can create a slideshow for your Featured Listings by using the 
        [listing_slideshow post_id="<em>slideshowid</em>"] shortcode. 
		</p>';

	protected static $options = array(
		'context'		=> array( 'type' => 'select', 'label' => 'Template', 'default' => '' ),
		'width'			=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 610 ),
		'height'		=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 320 ),
		'widget_class'	=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
		'animation' 	=> array( 'type' => 'select', 'label' => 'Animation', 'options' => array(
				'fade' => 'fade',
				'horizontal-slide' => 'horizontal-slide',
				'vertical-slide' => 'vertical-slide',
				'horizontal-push' => 'horizontal-push',
			),
			'default' => 'fade' ),
		'animationSpeed'	=> array( 'type' => 'numeric', 'label' => 'Animation Speed(ms)', 'default' => 800 ),	// how fast animtions are
		'advanceSpeed'		=> array( 'type' => 'numeric', 'label' => 'Advance Speed(ms)', 'default' => 5000 ),		// if timer is enabled, time between transitions
		'timer'				=> array( 'type' => 'checkbox', 'label' => 'Timer', 'default' => true ),				// true or false to have the timer
		'pauseOnHover'		=> array( 'type' => 'checkbox', 'label' => 'Pause on hover', 'default' => true ),		// if you hover pauses the slider
		'pl_featured_listing_meta' => array( 'type' => 'featured_listing_meta', 'default' => '' ),
	);

	protected static $subcodes = array(
		'ls_index'		=> array('help' => 'Index of the listing in the slideshow, starting with 1.'),
		'ls_url'		=> array('help' => 'The url to view the listing.'),
		'ls_address'	=> array('help' => 'The street address of the listing.'),
		'ls_beds'		=> array('help' => 'The number of bedrooms.'),
		'ls_baths'		=> array('help' => 'The number of bathrooms.'),
	);

	protected static $template = array(
		'snippet_body'	=> array( 'type' => 'textarea', 'label' => 'Caption text for each slideshow image', 'css' => 'mime_html', 'default'	=> 
			'
<div id="caption-[ls_index]" class="orbit-caption">
	<p class="caption-title"><a href="[ls_url]">[ls_address]</a></p>
	<p class="caption-subtitle"><span class="price">[ls_beds] beds</span>, <span class="baths">[ls_baths] baths</span></p>
	<a class="button details" href="[ls_url]"><span></span></a>
</div>',
			'description'	=> '
You can use any valid HTML in this field to format the subcodes, but you must ensure that it is contained in a block similar to:
<div id="caption-[ls_index]" class="orbit-caption">...</div>.'),

		'css'			=> array( 'type' => 'textarea', 'label' => 'CSS to style your slideshow', 'css' => 'mime_css', 'default' => '
/* sample div used to wrap the slideshow plus any addiitonal html */
.my-slideshow {
	overflow: hidden;;
}
/* sample div used to wrap the slideshow */
.my-slideshow-wrapper {
	float: left;
	border: 1px solid #7f7f7f;
	padding: 5px;
}
/* controls background of caption area */
.orbit-wrapper .orbit-caption {
	background: none repeat scroll 0 0 rgba(0, 0, 0, 0.6);
	color: #fff;
}
/* controls general layout of caption items */
.orbit-wrapper .orbit-caption p {
	margin: 0;
	padding: 10px 20px 0;
}
/* caption title */
.orbit-wrapper .orbit-caption .caption-title {
	font-weight: bold;
	font-size: 1.8em;
}
/* caption sub-title */
.orbit-wrapper .orbit-caption .caption-subtitle {
	padding-top: 0;
	padding-bottom: 10px;
	font-size: 1.2em;
}
/* make sure caption links are visible! */
#main .my-slideshow .orbit-wrapper .orbit-caption a {
	color: #fff;
	text-decoration: none;
}
#main .my-slideshow .orbit-wrapper .orbit-caption a:hover {
	color: #fff;
	text-decoration: underline;
}',
			'description'	=> '
You can use any valid CSS in this field to customize the caption, which will also inherit the CSS from the theme.' ),

		'before_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content before the slideshow', 'css' => 'mime_html', 'default' => '
<div class="my-slideshow">
	<div class="my-slideshow-wrapper">',
			'description'	=> '
You can use any valid HTML in this field and it will appear before the slideshow images. 
For example, you can wrap the whole slideshow with a <div> element to apply borders, etc, by placing the opening <div> tag in this field and the closing </div> tag in the following field.' ),

		'after_widget'	=> array( 'type' => 'textarea', 'label' => 'Add content after the slideshow', 'css' => 'mime_html', 'default' => '
	</div>
</div>',
			'description'	=> '
You can use any valid HTML in this field and it will appear after the slideshow images.' ),
	);


	/**
	 * Return array of options used to configure this custom shortcode
	 * @param $id int		: id of custom shortcode record
	 * @return array/bool	: array of results/false if id invalid/trashed
	 */
	public static function get_options($id) {
		$class = get_called_class();
		$options = array();
		if (($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) && $post['post_status']=='publish') {
			$postmeta = get_post_meta($id);
			if (!empty($postmeta['shortcode'])) {
				foreach($class::$options as $attr=>$vals) {
					if ($attr=='context') {
						$key = 'pl_cpt_template';
					}
					elseif ($attr=='pl_featured_listing_meta') {
						// we have featured listings
						$options['post_id'] = $id;
						$options['post_meta_key'] = 'pl_featured_listing_meta';
						continue;
					}
					else {
						$key = $attr;
					}
					if (isset($postmeta[$key])) {
						$options[$attr] = maybe_unserialize($postmeta[$key][0]);
					}
				}
			}
			return $options;
		}
		return false;
	}
		
	/**
	 * Generate a shortcode for this shortcode type from arguments
	 * @param string $shortcode_type	: shortcode type we will be generating
	 * @param array $args				: shortcode post type record including postmeta values
	 * @return string					: returned shortcode
	 */
	public static function generate_shortcode_str($args) {
		
		// prepare args
		$sc_args = '';
		$class_options = self::$options;
		foreach($args as $option => $value) {
			if (!empty($value)) {
				// only output options that are valid for this type
				if (!empty($class_options[$option])) {
					if ($class_options[$option]['type'] == 'featured_listing_meta' && !empty($args['id'])) {
						$sc_args .= " post_id='".$args['id']."' post_meta_key='pl_featured_listing_meta' ";
					}
					elseif (!is_array($value)) {
						$sc_args .= ' '.$option."='".$value."'";
					}
				}
			}
		}

		$shortcode = '[' . self::$shortcode . $sc_args . ']';

		return $shortcode;
	}
}

PL_Listing_Slideshow_CPT::init();
