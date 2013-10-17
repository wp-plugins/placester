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
     * added to the top of the page. The 5th param of wp_enqueue_scripts is $in_footer
     * which defaults to false, but can be set to true.
     * See: http://codex.wordpress.org/Function_Reference/wp_register_script
     *
     * @since 0.0.1
     */
    add_action( 'wp_enqueue_scripts', 'pls_scripts' );
    function pls_scripts() {
        if (is_admin()) {
            return;
        }

        /** Register Modernizr. Will be enqueued using 'wp_print_scripts'. */
        wp_register_script( 'modernizr', trailingslashit( PLS_JS_URL ) . 'libs/modernizr/modernizr.min.js' , array(), '2.6.1');

        // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
//         wp_localize_script( 'jquery', 'info', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		add_action( 'wp_print_scripts', 'pls_print_info_var' );
        
        /**
         *  If the plugin is inactive, register the script that deals with adding 
         *  notification about needing the plugin. Localize the notification 
         *  message. Accompanied by plugin-nags.css.
         */
		wp_register_script( 'jquery-placeholder', trailingslashit( PLS_JS_URL ) . 'libs/jquery-placeholder/jquery.placeholder.min.js' , array( 'jquery' ), '1.0.1', true );
		wp_enqueue_script( 'jquery-placeholder' );
		
        wp_register_script( 'listings-object', trailingslashit( PLS_JS_URL ) . 'scripts/listings.js' , array( 'jquery' ), '1.0.1', true );
        wp_enqueue_script('listings-object');

        wp_register_script( 'get-listings-fav-ajax', trailingslashit( PLS_JS_URL ) . 'scripts/get-listings-fav-ajax.js' , array( 'jquery' ), NULL, true );
        wp_enqueue_script('get-listings-fav-ajax');

        wp_register_script( 'contact-widget', trailingslashit( PLS_JS_URL ) . 'scripts/contact.widget.ajax.js' , array( 'jquery' ), NULL, true );
        wp_enqueue_script('contact-widget');

        wp_register_script( 'client-edit-profile', trailingslashit( PLS_JS_URL ) . 'scripts/client-edit-profile.js' , array( 'jquery' ), NULL, true );
        wp_enqueue_script('client-edit-profile');

        wp_register_script( 'script-history', trailingslashit( PLS_JS_URL ) . 'libs/history/jquery.address.js' , array( 'jquery' ), NULL, true );
        wp_enqueue_script('script-history');

        wp_register_script( 'search-bootloader', trailingslashit( PLS_JS_URL ) . 'scripts/search-loader.js' , array( 'jquery' ), NULL, true );
        wp_enqueue_script('search-bootloader');
        
        wp_enqueue_script( 'underscore' );

        if ( pls_has_plugin_error() ) {
            /** Localize the script. Send the correct notification. */
            $l10n = array();
            if ( pls_has_plugin_error() == 'no_api_key' ) 
                $l10n['no_api_key'] = 'You need to add a valid API Key to the <a href="' . admin_url( 'admin.php?page=placester_settings' ) . '">Placester Real Estate Pro plugin settings page</a>.';
            elseif ( pls_has_plugin_error() == 'no_plugin' )
                $l10n['no_plugin'] = 'This theme needs the <a href="http://wordpress.org/extend/plugins/placester/" target="_blank">Placester Real Estate Pro plugin</a> to work.';
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
            wp_register_script( 'chosen', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen.jquery.min.js' , array( 'jquery' ), NULL, false );
            wp_register_script( 'chosen-custom', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen-custom.js' , array( 'jquery' ), NULL, false );
            wp_register_style( 'chosen', trailingslashit( PLS_JS_URL ) . 'libs/chosen/chosen.css' );
            /** Enqueue script and styles only if supported. */
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

        if ( array_key_exists( 'spinner', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'spinner', trailingslashit( PLS_JS_URL ) . 'libs/spinner/spinner.js' , array( 'jquery'), NULL, true );
            wp_register_style( 'spinner', trailingslashit( PLS_JS_URL ) . 'libs/spinner/spinner.css' );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['spinner'] ) ) {
                if ( in_array( 'script', $js[0]['spinner'] ) ) {
                    wp_enqueue_script( 'spinner' );
                    wp_enqueue_style( 'spinner' );
                }
            }
        }
        
        if ( array_key_exists( 'picturefill', $js[0] ) ) {
        	/** Register the script and style. */
        	wp_register_script( 'picturefill', trailingslashit( PLS_JS_URL ) . 'libs/picturefill/picturefill.js' , array( 'jquery'), NULL, true );
        	
        	/** Enqueue script and styles only if supported. */
        	if ( is_array( $js[0]['picturefill'] ) ) {
        		if ( in_array( 'script', $js[0]['picturefill'] ) ) {
        			wp_enqueue_script( 'picturefill' );
        		}
        	}
        }
        
        if ( array_key_exists( 'masonry', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'masonry', trailingslashit( PLS_JS_URL ) . 'scripts/masonry.js' , array( 'jquery'), NULL, true );

            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['masonry'] ) ) {
                if ( in_array( 'script', $js[0]['masonry'] ) ) {
                    wp_enqueue_script( 'masonry' );
                }
            }
        }

        if ( array_key_exists( 'datatable', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'datatable', trailingslashit( PLS_JS_URL ) . 'libs/datatables/jquery.dataTables.js' , array( 'jquery'), NULL, true );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['datatable'] ) ) {
                if ( in_array( 'script', $js[0]['datatable'] ) ) {
                    wp_enqueue_script( 'datatable' );
                }
            }
        }

        if ( array_key_exists( 'jquery-ui', $js[0] ) ) {            
            wp_register_style( 'jquery-ui', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/css/smoothness/jquery-ui-1.8.17.custom.css' );
            if ( is_array( $js[0]['jquery-ui'] ) ) {
                if ( in_array( 'script', $js[0]['jquery-ui'] ) ) {
                    wp_enqueue_script( 'jquery-ui-core' );
                    if (isset( $GLOBALS['wp_scripts']->registered['jquery-ui-datepicker']) )  {
                      wp_enqueue_script( 'jquery-ui-datepicker' );
                    } else {
                      wp_register_script( 'jquery-ui-datepicker', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/js/jquery.ui.datepicker.js' , array( 'jquery'), NULL, true );
                      wp_enqueue_script( 'jquery-ui-datepicker' );
                    }
                    if (isset( $GLOBALS['wp_scripts']->registered['jquery-ui-dialog']) )  {
                      wp_enqueue_script( 'jquery-ui-dialog' );
                    } else {
                      // wp_register_script( 'jquery-ui-dialog', trailingslashit( PLS_JS_URL ) . 'libs/jquery-ui/js/jquery.ui.dialog.js' , array( 'jquery'), NULL, true );
                      // wp_enqueue_script( 'jquery-ui-dialog' );
                    }
                }

                if ( in_array( 'style', $js[0]['jquery-ui'] ) ) {
                    wp_enqueue_style( 'jquery-ui' );
                }
            }
        }
        
        if ( array_key_exists( 'jquery-tools', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'tabs', trailingslashit( PLS_JS_URL ) . 'libs/jquery-tools/tabs.js' , array( 'jquery'), NULL, true );
            wp_register_script( 'rangeinput', trailingslashit( PLS_JS_URL ) . 'libs/jquery-tools/rangeinput.js' , array( 'jquery'), NULL, true );
            wp_register_script( 'validator', trailingslashit( PLS_JS_URL ) . 'libs/jquery-tools/validator.js' , array( 'jquery'), NULL, true );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['jquery-tools'] ) ) {
                if ( in_array( 'script', $js[0]['jquery-tools'] ) ) {
                    wp_enqueue_script( 'tabs' );
                    wp_enqueue_script( 'rangeinput' );
                    wp_enqueue_script( 'validator' );
                }
            }
        }

        if ( array_key_exists( 'form', $js[0] ) ) {
            /** Register the script and style. */
        	wp_enqueue_script( 'modernizr' );
        	wp_register_script( 'form', trailingslashit( PLS_JS_URL ) . 'scripts/form.js' , array('jquery', 'modernizr'), NULL, true );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['form'] ) ) {
                if ( in_array( 'script', $js[0]['form'] ) ) {
                    wp_enqueue_script( 'form' );
                }
            }
        }
        
        /**
         * The "Cookies" script.
         * Deal with it only theme support has been added.
         * {@link: http://code.google.com/p/cookies/wiki/License}
         */
        if ( array_key_exists( 'cookies', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'cookies', trailingslashit( PLS_JS_URL ) . 'libs/cookies/cookies.jquery.js' , array( 'jquery' ), NULL, false );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['cookies'] ) ) {
                if ( in_array( 'script', $js[0]['cookies'] ) ) {
                  wp_enqueue_script( 'cookies' );
                }
            }
        }

        if ( array_key_exists( 'lead-capture', $js[0] ) ) {
            /** Register the script and style. */
            wp_register_script( 'lead-capture', trailingslashit( PLS_JS_URL ) . 'scripts/lead-capture.js' , array( 'jquery' ), NULL, true );
            /** Enqueue script and styles only if supported. */
            if ( is_array( $js[0]['lead-capture'] ) ) {
                if ( in_array( 'script', $js[0]['lead-capture'] ) ) {
                  wp_enqueue_script( 'lead-capture' );
                }
            }
        }
        
    }


    /**
     * Enqueues scripts before the ones added with 'wp_enqueue_script'
     *
     * @since 0.0.1
     */
    add_action( 'wp_enqueue_scripts', 'pls_print_header_scripts', 8 );
    
    function pls_print_header_scripts() {    
        /** Load Google CDN jQuery and its fallback before everything else */
        wp_enqueue_script( 'jquery' );
    }
    
    function pls_print_info_var( ) {
    	ob_start();
    	?>
    	<script type="text/javascript">//<![CDATA[
			var info = {"ajaxurl": "<?php echo admin_url( 'admin-ajax.php' ); ?>"};
    	//]]>
    	</script>
    	<?php 
    	echo ob_get_clean();
    }

    add_action( 'wp_enqueue_scripts', 'add_mixpanel' );

    function add_mixpanel() {
      // get theme option for mixpanel ID
      $mixpanel_id = pls_get_option('pls-mixpanel-id');
      if ( isset($mixpanel_id) && !empty($mixpanel_id) ) {
        ob_start(); ?>
          <!-- start Mixpanel -->
          <script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f);b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==
          typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);
          b._i.push([a,e,d])};b.__SV=1.2}})(document,window.mixpanel||[]);
          mixpanel.init("<?php echo $mixpanel_id; ?>");</script>
          <!-- end Mixpanel -->
          <script type="text/javascript">
            window.onload = function () {
              mixpanel.track_pageview();
            }
          </script>
        <?php
        echo ob_get_clean();
      }
    }
}
