<?php

/*****************************************************/
/* Initialize the Heavily Modified Theme Customizer */
/*****************************************************/

PL_Customizer_Helper::init();
class PL_Customizer_Helper 
{
	public static function init() {
		add_action ('admin_menu', array(__CLASS__, 'themedemo_admin') );
		add_action( 'customize_register', array(__CLASS__, 'PL_customize_register'), 1 );
		add_action( 'customize_controls_print_footer_scripts', array(__CLASS__, 'load_partials') );

		add_action( 'wp_ajax_load_custom_styles', array(__CLASS__, 'load_custom_styles') );
		add_action( 'wp_ajax_load_theme_info', array(__CLASS__, 'load_theme_info') );
		add_action( 'wp_ajax_change_theme', array(__CLASS__, 'change_theme') );
	}

	public static function is_onboarding() 
	{
		return ( defined('HOSTED_PLUGIN_KEY') && isset($_GET['onboard']) && strtolower($_GET['onboard']) == 'true' );
	}

	public static function themedemo_admin() 
	{
	    // add the Customize link to the admin menu
	    add_theme_page( 'Customize', 'Customize', 'edit_theme_options', 'customize.php' );
	}

	public static function PL_customize_register( $wp_customize ) 
	{
		// A simple check to ensure function was called properly...
		if ( !isset($wp_customize) ) { return; }

		// Check to see if the current active theme is present in the list of those supported by our customizer..
		global $PL_CUSTOMIZER_THEME_LIST;
		$theme_supported = in_array( get_stylesheet(), $PL_CUSTOMIZER_THEME_LIST );

		// Check to see if the plugin is running on the hosted environment...
		$on_hosted = defined('HOSTED_PLUGIN_KEY');

		// error_log('On Hosted: ' . $on_hosted);
		// error_log('Current theme supported: ' . $theme_supported);

		if ( $on_hosted || $theme_supported ) {
			// This is a global function, as PHP does not allow nested class declaration...
			define_custom_controls();

			// If NOT in hosted environment (but theme is supported), do not render the 'Theme Selection' pane...
			$excluded_opts = null;
			if ( !$on_hosted ) {
				$excluded_opts = array( 'Theme Selection', 'theme-select' );
			}

			// Load the customizer with necessary flags...
			PL_Customizer::register_components( $wp_customize, $excluded_opts );

			// Prevent default control from being created
			remove_action( 'customize_register', array(  $wp_customize, 'register_controls' ) );

			// Register function to inject script to make postMessage settings work properly
			if ( $wp_customize->is_preview() && ! is_admin() ) { 
				add_action( 'wp_footer', array(__CLASS__, 'inject_postMessage_hooks'), 21); 
			}
		}
		else {
			// If the user is neither on hosted, nor using a placester theme, make sure NOT to register
			// the accompanying scripts and stylesheets...
			remove_action( 'customize_controls_enqueue_scripts', array( 'PL_Js_Helper', 'customizer') );
			remove_action( 'customize_controls_enqueue_scripts', array( 'PL_Css_Helper', 'customizer' ) );
			remove_action( 'customize_controls_print_footer_scripts', array(__CLASS__, 'load_partials') );
		}

		// No infobar in theme previews, regardless of which customizer is used...
		remove_action( 'wp_head', 'placester_info_bar' );
	}

	public static function inject_postMessage_hooks() 
	{
	  global $wp_customize;
	  global $PL_CUSTOMIZER_THEME_INFO;

	  // Gets the theme that the customizer is currently set to display/preview...
	  $theme_opts_key = $wp_customize->get_stylesheet();
	  // error_log($theme_opts_key);
	  $postMessage_settings = isset($PL_CUSTOMIZER_THEME_INFO[$theme_opts_key]) ? $PL_CUSTOMIZER_THEME_INFO[$theme_opts_key] : array();
	  // error_log(serialize($PL_CUSTOMIZER_THEME_INFO));

	  ?>
	    <script type="text/javascript">
	    ( function( $ ){
	    <?php foreach ($postMessage_settings as $id => $selector): ?>
	      wp.customize('<?php echo "{$theme_opts_key}[{$id}]"; ?>', function( value ) {
	        value.bind(function(to) {
	          //if (to) {	
	            $('<?php echo $selector; ?>').text(to);
	          //}
	        });
	      });
	    <?php endforeach; ?>  
	    } )( jQuery )
	    </script>
	  <?php
	}

	public static function load_partials() {
	  ?>
	    <!-- Spinner for Theme Preview overlay -->
	    <img id="preview_load_spinner" src="<?php echo plugins_url('/placester/images/preview_load_spin.gif'); ?>" alt="Theme Preview is Loading..." />
	  
	    <?php echo PL_Logging::mixpanel_inline_js(); ?>

		<?php if ( self::is_onboarding() ): ?>
		    <!-- Tooltip box -->
		    <div id="tooltip" class="tip">
		      <a class="close" href="#"></a>    
		    	<h4>Welcome!</h4>
		      <p class="desc">Great!  You're making all the right moves.  We're going to take you into the main admin panel now so you can further customize your web site.<br />
		      <br />You can always return to this customization wizard by clicking Appearance in the main menu, then clicking "Customize."</p>
		      <p class="link"><a href="#">Let's Get Started</a></p>
		    </div>
		 <?php endif; ?>

	  <?php
	}

	public static function load_custom_styles() {
		if ( isset($_POST['color']) )  {
		  	// This needs to be defined (ref'd by the template file we're about to load...)
		  	$color = $_POST['color'];

		  	$curr_theme = wp_get_theme()->Template;
		  	$skin_path = ( trailingslashit(PL_THEME_SKIN_DIR) . trailingslashit($curr_theme) . "{$color}.css" );

		  	// Read in CSS file contents as a sting...
		  	$styles = file_get_contents($skin_path);

			echo json_encode( array( 'styles' => $styles ) );
		}

		die();
	}

	public static function load_theme_info() {
		if ( isset($_POST['theme']) ) {
			$theme_name = $_POST['theme'];
			// switch_theme( $theme_name, $theme_name);

			$theme_obj = wp_get_theme( $theme_name );

			ob_start();
			?>
	            <div class="theme-screenshot">
	              <img src="<?php echo esc_url( $theme_obj->get_screenshot() ); ?>" />
	      	    </div>

	            <h2>Theme Description</h2>
	            <p><?php echo $theme_obj->display('Description'); ?></p>
	        <?php
	        $new_html = ob_get_clean();
	       	    
			echo json_encode(array('theme_info' => $new_html));
		}

		die();
	}

	public static function change_theme() {
		if ( isset ($_POST['new_theme']) ) {
			$new_theme = $_POST['new_theme'];

			// Assume stylesheet and template name are the same for now...
			switch_theme( $new_theme, $new_theme );

			echo json_encode(array('success' => 'true'));
		}

		die();
	}

}

?>
