<?php
/**
 *  Enqueue the scripts only if the PLS_LOAD_SCRIPTS constant is not set to 
 *  false. This allows developers to stop framework script loading by defining 
 *  it in 'functions.php'
 *
 *  Scripts are enqueued in the footer
 *
 *  TIP: Wordpress 3.3 will allow the usage of wp_enqueue_script to add scripts 
 *  to any part of a template after the page has been loaded.
 *  See http://core.trac.wordpress.org/ticket/9346 for more details.
 */
if ( !defined( 'PLS_LOAD_SCRIPTS' ) || ( defined( 'PLS_LOAD_SCRIPTS' ) && ( PLS_DO_NOT_LOAD_SCRIPTS === true ) ) ) {

    /**
     * Registers and enqueues scripts 
     * 
     * All scripts should be added in the footer except for modernizr which is 
     * added to the top of the page. This is accomplished by setting the last 
     * parameter in wp_register_script to true.
     * See: http://codex.wordpress.org/Function_Reference/wp_register_script
     *
     * @since 0.0.1
     */
    add_action( 'init', 'pls_scripts' );
    function pls_scripts() {

        
        if (is_admin()) {
            return;
        }

        /** Register Modernizr. Will be enqueued using 'wp_print_scripts'. */
        wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.0.6' );

        /**
         *  Reregister the latest jQuery from the Google CDN. Will be enqueued 
         *  using 'wp_print_scripts' along with its fallback.
         */
        wp_deregister_script( 'jquery' );
        wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', array(), NULL );
        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
        wp_localize_script( 'jquery', 'info', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        
        /**
         *  If the plugin is inactive, register the script that deals with adding 
         *  notification about needing the plugin. Localize the notification 
         *  message. Accompanied by plugin-nags.css.
         */

        wp_register_script( 'get-listings-ajax', trailingslashit( PLS_JS_URL ) . 'scripts/get-listings-ajax.js' , NULL, NULL, true );
        wp_enqueue_script('get-listings-ajax');

        wp_register_script( 'get-listings-fav-ajax', trailingslashit( PLS_JS_URL ) . 'scripts/get-listings-fav-ajax.js' , NULL, NULL, true );
        wp_enqueue_script('get-listings-fav-ajax');

        wp_register_script( 'contact-widget', trailingslashit( PLS_JS_URL ) . 'scripts/contact.widget.ajax.js' , NULL, NULL, true );
        wp_enqueue_script('contact-widget');



        wp_register_script( 'client-edit-profile', trailingslashit( PLS_JS_URL ) . 'scripts/client-edit-profile.js' , NULL, NULL, true );
        wp_enqueue_script('client-edit-profile');

        if ( pls_has_plugin_error() ) {
            /** Register the nag script. */
            // wp_register_script( 'pls-plugin-nags', trailingslashit( PLS_JS_URL ) . 'scripts/plugin-nags.js' , array( 'jquery' ), NULL, true );

            /** Enqueue the nag script. */
            // wp_enqueue_script( 'pls-plugin-nags' );

            /** Localize the script. Send the correct notification. */
            $l10n = array();
            if ( pls_has_plugin_error() == 'no_api_key' ) 
                $l10n['no_api_key'] = __( 'You need to add a valid API Key to the <a href="' . admin_url( 'admin.php?page=placester_settings' ) . '">Placester Real Estate Pro plugin settings page</a>.', pls_get_textdomain() );
            elseif ( pls_has_plugin_error() == 'no_plugin' )
                $l10n['no_plugin'] = __( 'This theme needs the <a href="http://wordpress.org/extend/plugins/placester/" target="_blank">Placester Real Estate Pro plugin</a> to work.', pls_get_textdomain() );
            wp_localize_script( 'pls-plugin-nags', 'messages', $l10n );
        } 

        /** Get theme-supported js modules. */
        $js = get_theme_support( 'pls-js' );

        /** If there is no support, return. */
        if ( ! is_array( $js[0] ) )
            return;

        /**
         * The "Chosen" script.
         * Deal with it only theme support has been added.
         * {@link: http://harvesthq.github.com/chosen/}
         */
        if ( array_key_exists( 'chosen', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'chosen', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen.jquery.min.js' , array( 'jquery' ), NULL, true );
            wp_register_script( 'chosen-custom', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen-custom.js' , array( 'jquery' ), NULL, true );
            wp_register_style( 'chosen', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen.css' );
            /** Enqueue scrip and styles only if supported. */
            if ( is_array( $js[0]['chosen'] ) ) {
                if ( in_array( 'script', $js[0]['chosen'] ) ) {
                    wp_enqueue_script( 'chosen' );
                    wp_enqueue_script( 'chosen-custom' );
                }
                /** Enqueue the chosen style */
                if ( in_array( 'style', $js[0]['chosen'] ) ) {
                    wp_enqueue_style( 'chosen' );
                }
            }
        }

        if ( array_key_exists( 'floating', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'floating', trailingslashit( PLS_JS_URL ) . 'scripts/floating.js' , array( 'jquery'), NULL, true );
            /** Enqueue scrip and styles only if supported. */
            if ( is_array( $js[0]['floating'] ) ) {
                if ( in_array( 'script', $js[0]['floating'] ) ) {
                    wp_enqueue_script( 'floating' );
                }
            }
        }   

        if ( array_key_exists( 'datatable', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
            /** Enqueue scrip and styles only if supported. */
            if ( is_array( $js[0]['datatable'] ) ) {
                if ( in_array( 'script', $js[0]['datatable'] ) ) {
                    wp_enqueue_script( 'datatable' );
                }
            }
        }

        if ( array_key_exists( 'jquery-ui', $js[0] ) ) {
            wp_register_script( 'jquery-ui', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/js/jquery-ui-1.8.17.custom.min.js' , array( 'jquery'), NULL, true );
            wp_register_style( 'jquery-ui', trailingslashit( PLS_JS_URL ) . 'libs//jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css' );
            if ( is_array( $js[0]['jquery-ui'] ) ) {
                if ( in_array( 'script', $js[0]['jquery-ui'] ) ) {
                    wp_enqueue_script( 'jquery-ui' );
                }
                if ( in_array( 'style', $js[0]['jquery-ui'] ) ) {
                    wp_enqueue_style( 'jquery-ui' );
                }
            }
        }

    }


    /**
     * Enqueues scripts before the ones added with 'wp_enqueue_script'
     *
     * @since 0.0.1
     */
    add_action( 'wp_head', 'pls_print_header_scripts', 8 );
    function pls_print_header_scripts() {

        /** Load Google CDN jQuery and its fallback before everything else */
        wp_print_scripts( 'jquery' );
        echo '<script type="text/javascript">window.jQuery || document.write(\'<script src="wp-includes/js/jquery/jquery.js"><\/script>\')</script>' . "\n";

        /** Load Modernizr */
        wp_print_scripts( 'modernizr' );
    }
}

