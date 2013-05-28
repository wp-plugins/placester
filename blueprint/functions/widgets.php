<?php
/**
 * Sets up the core framework's widgets. 
 *
 * @package PlacesterBlueprint
 * @subpackage Functions
 */

/* Register Placester widgets. */
add_action( 'widgets_init', 'pls_register_widgets' );

/**
 * Registers the core frameworks widgets.  These widgets typically overwrite the equivalent default WordPress
 * widget by extending the available options of the widget.
 *
 * @since 0.0.1
 * @uses register_widget() Registers individual widgets with WordPress
 * @link http://codex.wordpress.org/Function_Reference/register_widget
 */
function pls_register_widgets() {

	/** Load the Placester Agent widget. */
	if ( current_theme_supports( 'pls-widget-agent' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'agent.php' );
		register_widget( 'PLS_Widget_Agent' );
	}
	/** Load the Placester Office widget. */
	if ( current_theme_supports( 'pls-widget-office' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'office.php' );
		register_widget( 'PLS_Widget_Office' );
	}
	
	/** Load the Placester Contact widget. */
	if( ! pls_has_plugin_error() && current_theme_supports( 'pls-widget-contact' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'contact.php' );
    	register_widget( 'Placester_Contact_Widget' );
	}

	/** Load the Placester Recent Posts widget. */
	if ( current_theme_supports( 'pls-widget-recent-posts' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'recent-posts.php' );
		register_widget( 'PLS_Widget_Recent_Posts' );
	}
	
	/** Load the Placester Quick Search widget. */
	if ( current_theme_supports( 'pls-widget-quick-search' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'quick-search.php' );
		register_widget( 'PLS_Quick_Search_Widget' );
	}
	
	/** Load the Placester Listings widget. */
	if ( current_theme_supports( 'pls-widget-listings' ) ) {
		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'listings.php' );
		register_widget( 'PLS_Widget_Listings' );
	}
	
  /** Load the Placester Mortgage Calculator widget. */
	if ( current_theme_supports( 'pls-widget-mortgage-calculator' ) ) {
  		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'mortgage-calculator.php' );
		register_widget( 'PLS_Widget_Mortgage_Calculator' );
	}

  /** Load the Placester Feedburner Subscribe Form widget. */
	if ( current_theme_supports( 'pls-widget-feedburner-form' ) ) {
  		require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'feedburner-subscribe-form.php' );
    	register_widget( 'PLS_Widget_Feedburner_Widget' );
	}

	/** Load the Testimonials widget. */
	if ( current_theme_supports( 'pls-widget-testimonials' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'testimonials.php' );
    	register_widget( 'PLS_Widget_Testimonials' );
  	}

  	/** Load the Agents widget. */
  	if ( current_theme_supports( 'pls-widget-agents' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'agents.php' );
    	register_widget( 'PLS_Widget_Agents' );
  	}

  	/** Load the Services widget. */
  	if ( current_theme_supports( 'pls-widget-services' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'services.php' );
    	register_widget( 'PLS_Widget_Services' );
  	}

  	/** Load the Twitter widget. */
  	if ( current_theme_supports( 'pls-widget-twitter' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'twitter.php' );
    	register_widget( 'PLS_Widget_Twitter' );
  	}

  	/** Load the Facebook widget. */
  	if ( current_theme_supports( 'pls-widget-facebook' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'facebook.php' );
    	register_widget( 'PLS_Widget_Facebook' );
  	}

  	/** Load the YouTube widget. */
  	if ( current_theme_supports( 'pls-widget-youtube' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'youtube.php' );
    	register_widget( 'PLS_Widget_YouTube' );
  	}

  	/** Load the Facebook Like Box widget. */
  	if ( current_theme_supports( 'pls-widget-facebook-like-box' ) ) {
    	require_once( trailingslashit( PLS_WIDGETS_DIR ) . 'facebook-like-box.php' );
    	register_widget( 'PLS_Widget_Facebook_Like_Box' );
  	}
}
