<?php
/* Add option page only if the user has the proper permissions */
add_action('init', 'placester_of_rolescheck' );
function placester_of_rolescheck () {
    global $theme_options;
    $theme_options = array();
    if ( current_user_can('edit_theme_options') ) {
        add_action('admin_menu', 'placester_of_add_page');
        add_action('admin_init', 'placester_of_init' );
    }
}

/* 
 * Creates the settings in the database by looping through the array
 * we supplied in options.php.  This is a neat way to do it since
 * we won't have to save settings for headers, descriptions, or arguments-
 * and it makes it a little easier to change and set up in my opinion.
 *
 * Read more about the Settings API in the WordPress codex:
 * http://codex.wordpress.org/Settings_API
 *
 */
function placester_of_init() {

    // Include the required files
    require_once dirname( __FILE__ ) . '/sanitize.php';
    require_once dirname( __FILE__ ) . '/interface.php';

    // Set the option defaults
    placester_of_setdefaults();

    $placester_of_settings = get_option('placester_theme_options');
    // Gets the unique id, returning a default if it isn't defined
    $option_name = $placester_of_settings['id'];

    // Registers the settings fields and callback
    register_setting('placester_theme_options', $option_name, 'placester_of_validate' );
}

/* 
 * Adds default options to the database if they aren't already present.
 * May update this later to load only on plugin activation, or theme
 * activation since most people won't be editing the options.php
 * on a regular basis.
 *
 * http://codex.wordpress.org/Function_Reference/add_option
 *
 */
function placester_of_setdefaults() {

    // Set the option name to the theme name
	$option_name = get_theme_data(STYLESHEETPATH . '/style.css');
	$option_name = $option_name['Name'];
	$option_name = preg_replace("/\W/", "_", strtolower($option_name) ) . "_to";
	
    // Get the unique option id
	$placester_of_settings = get_option('placester_theme_options');
	$placester_of_settings['id'] = $option_name;
	update_option('placester_theme_options', $placester_of_settings);

    /* 
     * Tracking all options lists added using this framework in the 
     * 'knownoptions' field of the framework option
     */
    if ( isset($placester_of_settings['knownoptions']) ) {
        $knownoptions =  $placester_of_settings['knownoptions'];
        if ( !in_array($option_name, $knownoptions) ) {
            array_push( $knownoptions, $option_name );
            $placester_of_settings['knownoptions'] = $knownoptions;
            update_option('placester_theme_options', $placester_of_settings);
        }
    } else {
        $newoptionname = array( $option_name );
        $placester_of_settings['knownoptions'] = $newoptionname;
        update_option('placester_theme_options', $placester_of_settings);
    }

    global $theme_options; // Gets the default options data from the array in options.php

    // If the options haven't been added to the database yet, they are added now
    foreach ($theme_options as $option) {

        if ( ($option['type'] != 'heading') && ($option['type'] != 'info') ) {
            $option_id = preg_replace('/\W/', '', strtolower($option['id']) );

            // wp_filter_post_kses for strings
            if (isset($option['std' ]) ) {
                if ( !is_array($option['std' ]) ) {
                    $values[$option_id] = wp_filter_post_kses($option['std']);
                } else {
                    foreach ($option['std' ] as $key => $value) {
                        $optionarray[$key] = wp_filter_post_kses($value);
                    }
                    $values[$option_id] = $optionarray;
                    unset($optionarray);
                }
            } else {
                $value = '';
            }
        }
    }

    if ( isset($values) ) {
        add_option($option_name, $values);
    }
}

/* Add a subpage called "Theme Options" to the appearance menu. */
if ( !function_exists( 'placester_of_add_page' ) ) {
    function placester_of_add_page() {
        $of_page = add_submenu_page('themes.php', 'Theme Options', 'Theme Options', 'edit_theme_options', 'options-framework', 'placester_of_page');

        // Adds actions to hook in the required css and javascript
        add_action("admin_print_styles-$of_page",'placester_of_load_styles');
        add_action("admin_print_scripts-$of_page", 'placester_of_load_scripts');

    }
}

/* Loads the CSS */
function placester_of_load_styles() {
    wp_enqueue_style('admin-style', OPTIONS_FRAMEWORK_DIRECTORY .'css/admin-style.css');
    wp_enqueue_style('color-picker', OPTIONS_FRAMEWORK_DIRECTORY .'css/colorpicker.css');
}	

/* Loads the javascript */
function placester_of_load_scripts() {
    // Inline scripts from options-interface.php
    // add_action('admin_head', 'of_admin_head');

    // Enqueued scripts
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('color-picker', OPTIONS_FRAMEWORK_DIRECTORY . 'js/colorpicker.js', array('jquery'));
    wp_enqueue_script('options-custom', OPTIONS_FRAMEWORK_DIRECTORY . 'js/options-custom.js', array('jquery'));
}

function of_admin_head() {

    // Hook to add custom scripts
    do_action( 'placester_of_custom_scripts' );
}

/* 
 * Builds out the options panel.
 *
 * If we were using the Settings API as it was likely intended we would use
 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
 * we'll call our own custom placester_of_fields.  See options-interface.php
 * for specifics on how each individual field is generated.
 *
 * Nonces are provided using the settings_fields()
 *
 */
if ( !function_exists( 'placester_of_page' ) ) {
    function placester_of_page() {

        // Get the theme name so we can display it up top
        $themename = get_theme_data(STYLESHEETPATH . '/style.css');
        $themename = $themename['Name'];

        settings_errors();
?>

    <div class="wrap">
    <?php screen_icon( 'themes' ); ?>
    <h2><?php esc_html_e( 'Theme Options' ); ?></h2>

    <div id="of_container">
       <form action="options.php" method="post">
      <?php settings_fields('placester_theme_options'); ?>

        <div id="header">
          <div class="logo">
            <h2><?php esc_html_e( $themename ); ?></h2>
          </div>
          <div class="clear"></div>
        </div>
        <div id="main">
        <?php $return = placester_of_fields(); ?>
          <div id="of-nav">
            <ul>
              <?php echo $return[1]; ?>
            </ul>
          </div>
          <div id="content">
            <?php echo $return[0]; /* Settings */ ?>
          </div>
          <div class="clear"></div>
        </div>
        <div class="of_admin_bar">
            <input type="submit" class="button-primary" name="update" value="<?php esc_attr_e( 'Save Options' ); ?>" />
            <input type="submit" class="reset-button button-secondary" name="reset" value="<?php esc_attr_e( 'Restore Defaults' ); ?>" onclick="return confirm( '<?php print esc_js( __( 'Click OK to reset. Any theme settings will be lost!' ) ); ?>' );" />
        </div>
<div class="clear"></div>
    </form>
</div> <!-- / #container -->  
</div> <!-- / .wrap -->

<?php
    }
}

/* 
 * Data sanitization!
 *
 * This runs after the submit/reset button has been clicked and
 * validates the inputs.
 *
 */
function placester_of_validate($input) {

    $placester_of_settings = get_option('placester_theme_options');

    // Gets the unique option id
    $option_name = $placester_of_settings['id'];

    // If the reset button was clicked
    if (!empty($_POST['reset'])) {
        // If options are deleted sucessfully update the error message
        if (delete_option($option_name) ) {
            add_settings_error('options-framework', 'restore_defaults', __('Default options restored.'), 'updated fade');
        }
    } else {
        if (!empty($_POST['update'])) {

            $clean = array();

            global $theme_options; // Gets the default options data from the array in options.php

            foreach ($theme_options as $option) {

                // Verify that the option has an id
                if ( isset ($option['id']) ) {

                    // Keep all ids lowercase with no spaces
                    $id = preg_replace( '/\W/', '', strtolower( $option['id'] ) );

                    // Set checkbox to false if it wasn't sent in the $_POST
                    if ( 'checkbox' == $option['type'] && ! isset( $input[$id] ) ) {
                        $input[$id] = "0";
                    }

                    // Set each item in the multicheck to false if it wasn't sent in the $_POST
                    if ( 'multicheck' == $option['type'] && ! isset( $input[$id] ) ) {
                        foreach ( $option['options'] as $key => $value ) {
                            $input[$id][$key] = "0";
                        } 
                    }

                    // For a value to be submitted to database it must pass through a sanitization filter
                    if ( isset ( $input[$id] ) && has_filter('of_sanitize_' . $option['type']) ) {
                        $clean[$id] = apply_filters( 'of_sanitize_' . $option['type'], $input[$id], $option );
                    }

                } // end isset $input

            } // end isset $id

        } // end foreach

        if ( isset($clean) ) {
            add_settings_error('options-framework', 'save_options', __('Options saved.'), 'updated fade');
            return $clean; // Return validated input
        }

    } // end $_POST['update']

}

/* 
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 */
if ( !function_exists( 'placester_option_getter' ) ) {
    function placester_option_getter($name, $default = false) {

        $placester_of_settings = get_option('placester_theme_options');

        // Gets the unique option id
        $option_name = $placester_of_settings['id'];

        if ( get_option($option_name) ) {
            $options = get_option($option_name);
        }

        if ( !empty($options[$name]) ) {
            return $options[$name];
        } else {
            return $default;
        }
    }
}

/* 
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 */
if ( !function_exists( 'placester_option_setter' ) ) {
    function placester_option_setter($arg_array) {
        global $theme_options;
        array_push( $theme_options, $arg_array );
    }
}
