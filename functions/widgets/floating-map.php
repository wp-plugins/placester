<?php
/**
 * The Agents widget gives users the ability to add agent information.
 *
 * @package PlacesterBlueprint
 * @subpackage Classes
 */

/**
 * Agent Widget Class
 *
 * @since 0.0.1
 */
class PLS_Floating_Map extends WP_Widget {

	/**
	 * Textdomain for the widget.
	 * @since 0.0.1
	 */
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.0.1
	 */
	function __construct() {

		/* Set the widget textdomain. */
		$this->textdomain = pls_get_textdomain();

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'pls-floating-map',
			'description' => esc_html__( 'A map that follows you as you scroll and will display properties on all ajax search pages.', $this->textdomain )
		);

		/* Create the widget. */
        parent::__construct( "pls-floating-map", esc_attr__( 'Placester: Floating Search Map', $this->textdomain ), $widget_options );
	}

	/**
	 * Outputs and filters the widget.
     * 
     * The widget connects to the plugin using the framework plugin api class. 
     * If the class returns false, this means that either the plugin is 
     * missing, either the it has no API key set.
     *
	 * @since 0.0.1
	 */
	function widget( $args, $instance ) {

		echo "<div id='pls-floating-map-widget' style='float:left; height: 1000px; position: absolute;'>";
		echo PLS_Map::dynamic(null, array('ajax' => true, 'zoom' => '16','width' => 282,'height' => 400, 'class' => ' '), array());
		echo "</div>";
        
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
     *
	 * @since 0.0.1
	 */
	function update( $new_instance, $old_instance ) {

	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
     *
	 * @since 0.0.1
	 */
	function form( $instance ) {

    }
} // end of class