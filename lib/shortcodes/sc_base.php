<?php
/**
 * Base class for creating a custom post type based on a shortcode.
 * Subclass this for each shortcode to provide an admin suitable for that shortcode.
 */

abstract class PL_SC_Base {

	// subclass should use this to set its post type
	protected static $pl_post_type = '';
	// subclass should use this to set its shortcode
	protected static $shortcode = '';
	// subclass should use this for form/widget titles, etc
	protected static $title = '';
	// help text
	protected static $help = '';
	// subclass should use this for basic display options/shortcode arguments
	protected static $options = array(
		'context'			=> array( 'type' => 'select', 'label' => 'Template', 'default' => ''),		// these should always exist
		'width'				=> array( 'type' => 'numeric', 'label' => 'Width(px)', 'default' => 250 ),
		'height'			=> array( 'type' => 'numeric', 'label' => 'Height(px)', 'default' => 250 ),
		'widget_class'		=> array( 'type' => 'text', 'label' => 'Widget Class', 'default' => '' ),
	//	'<field_name>'		=> array(
	//			'type'		=> '[text|numeric|select|subgrp]'	// type of form control:
	//															// text:	text field
	//															// numeric:	integer field
	//															// select:	drop list
	//															// subgrp:	contains a subgroup of controls
	//			'label'		=> '<Pretty Form Name>',			// field label for use in a form
	//			'options'	=> array(							// present if control type is 'select'
	//				'<value>'	=> '<Pretty Form Name>',		// field label for use in a form
	//				...
	//			),
	//			'default'	=> '<default val>'					// default value - type should be appropriate to the control type
	//	),
	);
	// subclass should use this for a list of shortcode filter subcodes
	protected static $filters = array(
		//		'<field_name>'		=> array(
		//			'type'		=> '[text|select|subgrp]'		// type of form control
		//														// text:	text field
		//														// select:	drop list
		//														// subgrp:	contains a group of filters
		//			'label'		=> '<Pretty Form Name>',		// field label for use in a form
		//			'default'	=> '<default val>'				// default value - type should be appropriate to the control type
		//	),
	);
	// subclass should use this for a list of shortcode subcodes
	protected static $subcodes = array(
		//		'<subcode_name>'	=> array(
		//			'help'		=> '<help text>'				// description of what the subcode does
		//	),
	);
	// tags allowed inside text boxes
	protected static $allowable_tags = "<a><p><script><div><span><section><label><br><h1><h2><h3><h4><h5><h6><scr'+'ipt><style><article><ul><ol><li><strong><em><button><aside><blockquote><footer><header><form><nav><input><textarea><select>";
	// built in templates 
	// TODO: build dynamically
	protected static $default_tpls = array('twentyten', 'twentyeleven');
	// default layout for template
	protected static $template = array(							// defines template fields
		//		'snippet_body'	=> array(
		//		'type'		=> 'textarea',
		//		'label'		=> '<Pretty Form Name>',
		//		'css'		=> '<css_class mime_type>', 		// used for CodeMirror
		//		'default'	=> '',
		//	),
	);




	/**
	 * Create an instance and register it with the custom shortcode manager
	 */
	public static function init() {
		$class = get_called_class();
 		if (class_exists('PL_Shortcode_CPT')) {
 			PL_Shortcode_CPT::register_shortcode($class::$shortcode, new $class);
 		}
	}

	public function __construct() {
 		add_action( 'template_redirect', array( $this, 'post_type_templating' ) );
	}

	/**
	 * Return the parameters that describe this shortcode type
	 * @return multitype:
	 */
	public function get_args() {
		if (empty($this::$filters)) {
			$this::$filters = $this->_get_filters();
		}
		return array(
				'shortcode'		=> $this::$shortcode,
				'pl_post_type'	=> $this::$pl_post_type,
				'title'			=> $this::$title,
				'help'			=> $this::$help,
				'options'		=> $this::$options,
				'filters'		=> $this::$filters,
				'subcodes'		=> $this::$subcodes,
				'default_tpls'	=> $this::$default_tpls,
				'template'		=> $this::$template,
		);
	}


	/*******************************************
	 * Override the following as necessary
	 *******************************************/


	/**
	 * Called when the post is being formatted for display by an embedded js tag for example
	 * Make the shortcode and render it. The template will already have been rendered by the embedded js.
	 * @param object $single	: post object
	 * @param bool $skipdb
	 */
	public function post_type_templating( $single, $skipdb = false ) {
		global $post;

		if( !empty($post) && $post->post_type == 'pl_general_widget') {
			$sc_str = $post->post_content;
			$sc_options = PL_Shortcode_CPT::load_shortcode($post->ID);
			include(PL_VIEWS_DIR . 'shortcode-embedded.php');
			die;			
		}
	}

	/**
	 * Return array of filters used to configure this shortcode.
	 */
	protected function _get_filters() {return array();}

	/**
	 * Return array of filters used to configure this custom shortcode
	 * @param $id int	: id of custom shortcode record
	 * @return array
	 */
	public static function get_filters($id) {
		$class = get_called_class();
		if ($post = get_post($id, ARRAY_A, array('post_type'=>'pl_general_widget'))) {
			$postmeta = get_post_meta($id);
			if (!empty($postmeta['pl_filters'])) {
				$filters = maybe_unserialize($postmeta['pl_filters'][0]);
				return $filters;
			}
		}
		return array();
	}
		
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
			foreach($class::$options as $attr=>$vals) {
				if ($attr=='context') {
					$key = 'pl_cpt_template';
				}
				else {
					$key = $attr;
				}
				if (isset($postmeta[$key])) {
					$options[$attr] = maybe_unserialize($postmeta[$key][0]);
				}
			}
			return $options;
		}
		return false;
	}
		
	/**
	 * Generate a shortcode for this shortcode type from arguments
	 * Used by shortcode edit page, template edit page for the preview pane
	 * @param array $args				: set of key value pairs
	 * @return string					: returned shortcode
	 */
	public static function generate_shortcode_str($args) {
		
		$class = get_called_class();

		// prepare args
		$sc_args = '';
		$class_options = $class::$options;
		foreach($args as $option => $value) {
			if (!empty($value)) {
				// only output options that are valid for this type
				if (!empty($class_options[$option])
					&& $class_options[$option]['type'] != 'featured_listing_meta'
					) {
					$sc_args .= ' '.$option."='".$value."'";
				}
			}
		}

		$shortcode = '[' . $class::$shortcode . $sc_args;

		// prepare filters
		$subcodes = '';
		$class_filters = $class::$filters;
		foreach($class_filters as $f_id => $f_atts) {
			if (!empty($args[$f_id])) {
				if(count($f_atts) && empty($f_atts['type'])) {
					// probably group filter
					if (is_array($args[$f_id])) {
						foreach( $f_atts as $key => $value ) {
							if (!empty($args[$f_id][$key]) && $args[$f_id][$key]!='false') { 
								$subcodes .= " [pl_filter group='" . $f_id. "' filter='" . $key . "' value='" . $args[$f_id][$key] . "'] ";
							}
						}
					}
				} 
				else {
					if (!empty($f_atts['type']) && $f_atts['type']=='multiselect') {
						if (is_array($args[$f_id])) {
							$subcodes .= " [pl_filter filter='" . $f_id . "' value='". implode(',', $args[$f_id]) . "'] ";
						}
					}
					else {
						if (!is_array($args[$f_id]) && $args[$f_id]!='false') {
							$subcodes .= " [pl_filter filter='" . $f_id . "' value='". $args[$f_id] . "'] ";
						}
					}
				}
			}
		}

		// build the shortcode
		if ($subcodes) {
			$shortcode = $shortcode . ']'.$subcodes."[/".$class::$shortcode."]";
		}
		else {
			$shortcode .= ']';
		}

		return $shortcode;
	}
}
