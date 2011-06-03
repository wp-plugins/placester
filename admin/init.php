<?php

/**
 * Admin interface, disptaching all the stuff.
 * @file /admin/init.php
 */

/**
 * Defines plugin admin menu
 */
function placester_admin_menu() {
    // Add separator
    global $menu;
    $menu['3a'] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

    // Add menu
    add_menu_page( 'Placester', 'Placester', 'edit_themes', 'placester', 
        'placester_admin_default_html', 
        plugins_url( '/images/logo_16.png', dirname( __FILE__ ) ), 
        '3b' /* position between 3 and 4 */ );

    // Avoid submenu to start with menu function
    global $submenu;
    $submenu['placester'] = array();

    add_submenu_page( 'placester', '', 
        'Dashboard', 'edit_themes', 'placester_dashboard', 
        'placester_admin_dashboard_html' );
    add_submenu_page( 'placester', '', 
        'My Listings', 'edit_themes', 'placester_properties', 
        'placester_admin_properties_html' );
    add_submenu_page( 'placester', '', 
        'Add Listing', 'edit_themes', 'placester_property_add', 
        'placester_admin_property_add_html' );
    add_submenu_page( 'placester', '', 
        'Contact', 'edit_themes', 'placester_contact', 
        'placester_admin_contact_html' );
    add_submenu_page( 'placester', '', 
        'Settings', 'edit_themes', 'placester_settings', 
        'placester_admin_settings_html' );
    add_submenu_page( 'placester', '', 
        'Get Themes', 'edit_themes', 'placester_themes', 
        'placester_admin_themes_html' );
    add_submenu_page( 'placester', '', 
        'Templates', 'edit_themes', 'placester_templates', 
        'placester_admin_templates_html' );
    add_submenu_page( 'placester', '', 
        'Support', 'edit_themes', 'placester_support', 
        'placester_admin_support_html' );
    add_submenu_page( 'placester', '', 
        'Update', 'edit_themes', 'placester_update', 
        'placester_admin_update_html' );

    // Styles, scripts
    wp_register_style( 'placester.admin', 
        plugins_url( '/css/admin.css', dirname( __FILE__ ) ) );
    wp_register_style( 'placester.admin.jquery-ui', 
        plugins_url( '/css/admin.jquery-ui.css', dirname( __FILE__ ) ) );

    wp_register_script( 'googlemaps_v3',
        'http://maps.google.com/maps/api/js?sensor=false&amp;v=3.3' );
    wp_register_script( 'jquery.datatables',
        plugins_url( '/js/jquery.dataTables.js', dirname( __FILE__ ) ) );
    wp_register_script( 'jquery.lightbox',
        plugins_url( '/js/jquery.lightbox-0.5.min.js', dirname( __FILE__ ) ) );
    wp_register_script( 'jquery.multifile',
        plugins_url( '/js/jquery.MultiFile.pack.js', dirname( __FILE__ ) ) );
    wp_register_script( 'jquery.upload',
        plugins_url( '/js/jquery.upload.js', dirname( __FILE__ ) ) );
    wp_register_script( 'jquery-ui.datepicker',
        plugins_url( '/js/jquery-ui.datepicker.min.js', dirname( __FILE__ ) ) );

    wp_register_script( 'placester.admin.property', 
        plugins_url( '/js/admin.property.js', dirname( __FILE__ ) ) );
}

add_action( 'admin_menu', 'placester_admin_menu' );



/**
 * Admin menu
 * @{
 */

/**
 * Admin menu - "dashboard" page
 */
function placester_admin_dashboard_html() {
    require( 'dashboard.php' );
}



/**
 * Admin menu - "dashboard" page, on-load handler
 */
function placester_admin_dashboard_onload() {
    if ( isset( $_REQUEST['ajax_action'] ) ) {
        require( 'dashboard_ajax.php' );
        exit();
    }

    wp_enqueue_script( 'dashboard' );
    wp_enqueue_style( 'dashboard' ); 
    wp_enqueue_style( 'wp-admin' );

    wp_enqueue_script( 'placester.widgets', 
        'http://dhiodphkum9p1.cloudfront.net/assets/api/v1.0/widgets.js' );
    wp_enqueue_script( 'placester.admin.widgets', 
        plugins_url( '/js/admin.widgets.js', dirname( __FILE__ ) ) );
    wp_enqueue_style( 'placester.admin.widgets', 
        plugins_url( '/css/admin.widgets.css', dirname( __FILE__ ) ) );
}

add_action( 'load-placester_page_placester_dashboard', 
    'placester_admin_dashboard_onload' );



/**
 * Admin menu - defult page, really never called
 */
function placester_admin_default_html()
{}



/**
 * Admin menu - "Contact" page
 */
function placester_admin_contact_html() {
    require( 'contact.php' );
}



/**
 * Admin menu - "Contact" page, on-load handler
 */
function placester_admin_contact_onload() {
    if ( isset( $_REQUEST['ajax_action'] ) ) {
        require( 'contact_ajax.php' );
        exit();
    }

    wp_enqueue_style( 'placester.admin' );
    wp_enqueue_script( 'jquery.upload' );
    wp_enqueue_script( 'placester.admin.contact',
        plugins_url( '/js/admin.contact.js', dirname( __FILE__ ) ) );
}

add_action( 'load-placester_page_placester_contact', 
    'placester_admin_contact_onload' );



/**
 * Admin menu - "Properties" page
 */
function placester_admin_properties_html() {
    if ( isset( $_REQUEST['id'] ) )
        require( 'property_edit.php' );
    else
        require( 'properties.php' );
}



/**
 * Admin menu - "Properties" page, on-load handler
 */
function placester_admin_properties_onload() {
    if ( isset( $_REQUEST['popup'] ) ) {
        wp_enqueue_script( 'jquery.multifile' );

        require( 'property_edit_images_popup.php' );
        exit();
    }

    if ( isset( $_REQUEST['id'] ) ) {
        wp_enqueue_style( 'placester.admin.jquery-ui' );
        wp_enqueue_script( 'googlemaps_v3' );
        wp_enqueue_script( 'jquery.lightbox' );
        wp_enqueue_script( 'jquery.multifile' );
        wp_enqueue_script( 'jquery-ui.datepicker' );
        wp_enqueue_script( 'placester.admin.property' );
        wp_enqueue_script( 'placester.admin.property_edit',
            plugins_url( '/js/admin.property_edit.js', dirname( __FILE__ ) ) );
    }
    else {
        wp_enqueue_style( 'placester.admin' );
        wp_enqueue_script( 'jquery.datatables' );
        wp_enqueue_script('placester.admin.properties',
            plugins_url( '/js/admin.properties.js', dirname( __FILE__ ) ) );
    }
}

add_action( 'load-placester_page_placester_properties', 
    'placester_admin_properties_onload' );



/**
 * Admin menu - "Add Listing" page
 */
function placester_admin_property_add_html() {
    require( 'property_add.php' );
}



/**
 * Admin menu - "Add Listing" page, on-load handler
 */
function placester_admin_property_add_onload() {
    wp_enqueue_style( 'placester.admin.jquery-ui' );

    wp_enqueue_script( 'googlemaps_v3' );
    wp_enqueue_script( 'jquery.multifile' );
    wp_enqueue_script( 'jquery-ui.core' );
    wp_enqueue_script( 'jquery-ui.datepicker' );
    wp_enqueue_script( 'placester.admin.property' );
}

add_action( 'load-placester_page_placester_property_add', 
    'placester_admin_property_add_onload' );



/**
 * Admin menu - "Settings" page
 */
function placester_admin_settings_html() {
    require( 'settings.php' );
}



/**
 * Admin menu - "Settings" page, on-load handler
 */
function placester_admin_settings_onload() {
    if ( isset( $_REQUEST['ajax_action'] ) ) {
        require( 'settings_ajax.php' );
        exit();
    }

    wp_enqueue_style( 'placester.admin' );
    wp_enqueue_script( 'jquery.upload' );
    wp_enqueue_script( 'placester.admin.settings',
        plugins_url( '/js/admin.settings.js', dirname( __FILE__ ) ) );
}

add_action( 'load-placester_page_placester_settings', 
    'placester_admin_settings_onload' );



/**
 * Admin menu - "Support" page
 */
function placester_admin_support_html() {
    require( 'support.php' );
}



/**
 * Admin menu - "Support" page, on-load handler
 */
function placester_admin_support_onload() {
    if ( isset( $_REQUEST['ajax_action'] ) ) {
        require( 'support_ajax.php' );
        exit();
    }

    wp_enqueue_style( 'placester.admin' );
    wp_enqueue_script( 'placester.admin.support',
        plugins_url( '/js/admin.support.js', dirname( __FILE__ ) ) );
}

add_action( 'load-placester_page_placester_support', 
    'placester_admin_support_onload' );



/**
 * Admin menu - "Get Themes" page
 */
function placester_admin_themes_html() {
    // not used now
    require( dirname( __FILE__ ) . '/themes.php' );
}



/**
 * Admin menu - "Get Themes" page, on-load handler
 */
function placester_admin_themes_onload() {
    wp_redirect( 'theme-install.php?tab=search&type=tag&s=placester&search=Search' );

    wp_enqueue_style( 'theme-install' );
    wp_enqueue_script( 'theme-install' );
    add_thickbox();
    wp_enqueue_script( 'theme-preview' );
}

add_action( 'load-placester_page_placester_themes', 
    'placester_admin_themes_onload' );



/**
 * Admin menu - "Templates" page, on-load handler
 */
function placester_admin_templates_onload() {
    if ( isset( $_REQUEST['template_iframe'] ) ) {
        require( dirname(__FILE__) . '/template-iframe.php' );
        exit();
    }

    wp_enqueue_style( 'placester.admin' );
    wp_enqueue_script( 'placester.admin.templates',
        plugins_url( '/js/admin.templates.js', dirname( __FILE__ ) ) );
}

add_action( 'load-placester_page_placester_templates', 
    'placester_admin_templates_onload' );



/**
 * Admin menu - "Templates" page
 */
function placester_admin_templates_html() {
    require( dirname( __FILE__ ) . '/templates.php' );
}



/**
 * Admin menu - "Update" page
 */
function placester_admin_update_html() {
    require( dirname( __FILE__ ) . '/update.php' );
}
/** @} */


/**
 * Admin utilities
 * @{
 */

/**
 * Prints error message
 *
 * @param string $message
 */
function placester_error_message( $message ) {
    ?>
    <div class="error inline">
      <p><?php echo $message ?></p>
    </div>
    <?php
}



/**
 * Prints warning message
 *
 * @param string $message
 * @param string $id
 */
function placester_warning_message( $message, $id = '' ) {
    ?>
    <div id="<?php echo $id ?>" class="updated inline">
      <p><?php echo $message ?></p>
    </div>
    <?php
}



/**
 * Prints info message
 *
 * @param object $e
 */
function placester_info_message( $e ) {
    ?>
    <div class="updated inline">
      <p><?php echo $e->getMessage(); ?></p>
    </div>
    <?php
}



/**
 * Prints success message
 *
 * @param string $message
 */
function placester_success_message( $message ) {
    ?>
    <div class="updated">
      <p><?php echo $message; ?></p>
    </div>
    <?php
}


/**
 * Header of all admin pages - shows tabs-like list
 *
 * @param string $current_page
 * @param string $title_postfix
 */
function placester_admin_header( $current_page, $title_postfix = '' ) {
    $api_key = get_option( 'placester_api_key' );
    if ( empty( $api_key ) )
        placester_warning_message(
            'You need to add your contact details before you can continue. ' .
            ' Navigate to the <a href="admin.php?page=placester_contact">' .
            'personal tab</a> and add an email address to start.',
            'warning_no_api_key');

    global $wp_rewrite;

    if ( ! $wp_rewrite->using_permalinks() ) {
        placester_warning_message(
            'For best performance <input type="button" class="button " value="Enable Fancy Permalinks" onclick="document.location.href = \'/wp-admin/options-permalink.php\';">' .
            'following the directions appropriate for your ' .
            '<a href="http://codex.wordpress.org/Using_Permalinks#Choosing_your_permalink_structure">' .
            'WordPress ' . get_bloginfo( 'version' ) .
            '</a>');
    }
    
    /**
     *      Check to see if the agency is verified.
     */
    placester_verified_check()
            

    ?>
    <div id="icon-options-general" class="icon32 placester_icon"><br /></div>
    <h2 style="border-bottom: #ccc 1px solid; padding-bottom: 0px">
      <?php
      $current_title = '';
      $v = '';

      global $submenu;
      foreach ( $submenu['placester'] as $i ) {
          $title = $i[0];
          $slug = $i[2];
          $style = '';
          if ( $slug == $current_page ) {
              $style = 'nav-tab-active';
              $current_title = $title;
          }

          $v .= "<a href='admin.php?page=$slug' style='font-size: 15px' class='nav-tab $style'>$title</a>";
      }

      echo $current_title;
      echo $title_postfix;
      echo '&nbsp;&nbsp;&nbsp;';
      echo $v;
      ?>
    </h2>
    <?php
}



/**
 * Reloads company / user data from webservice to plugin local data storage
 */
function placester_admin_actualize_company_user() {
    $api_key = get_option( 'placester_api_key' );
    if ( strlen( $api_key ) <= 0 ) {
        update_option( 'placester_user_id', '' );
        update_option( 'placester_company_id', '' );
        update_option( 'placester_user', new StdClass );
        update_option( 'placester_company', new StdClass );
    } else {
        $r = placester_apikey_info( $api_key );
        
        if (isset($r) && isset($r->api_key_id)) {
            update_option( 'placester_api_id', $r->api_key_id);
        }
        
        $company = $r;
        if ( isset( $company ) )
            $old_company = get_company_details();
        if ( isset( $old_company->logo ) )
            $company->logo = $old_company->logo;

        if (isset($r->id) && isset($r->user->id)) {
            $user = placester_user_get( $r->id, $r->user->id );
            update_option( 'placester_user_id', $r->user->id );
            update_option( 'placester_company_id', $r->id );
        }
        $user = $r->user;
        if ( isset($user) ) {
            update_option( 'placester_user', $user );
        }

        $old_user = placester_get_user_details();
        if ( isset( $old_user->logo ) )
            $user->logo = $old_user->logo;




        update_option( 'placester_company', $company );
    }
}



/**
 * Create a postbox widget.
 * @{
 */
/**
 * Container header
 */
function placester_postbox_container_header( $styles = 'width: 100%' ) {
    ?>
    <div class="postbox-container" style="<?php echo $styles; ?>">
        <div class="metabox-holder">	
            <div class="meta-box-sortables ui-sortable">
    	
    <?php
}


/**
 * Container footer
 */
function placester_postbox_container_footer() {
	?>
   			</div>
   		</div>
   	</div>
		
	<?php
}


/**
 * Main content
 */
function placester_postbox( $id, $title, $content ) {
?>
	<div id="<?php echo $id; ?>" class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php echo $title; ?></span></h3>
		<div class="inside">
			<?php echo $content; ?>
		</div>
	</div>
<?php
}



/**
 * Postbox header
 */
function placester_postbox_header( $title, $id = '' ) {
    ?>
    <div id="<?php echo $id; ?>" class="postbox">
    	<div class="handlediv" title="Click to toggle"><br /></div>
    	<h3 class="hndle"><span><?php echo $title; ?></span></h3>
    	<div class="inside">
    <?php
}


/**
 * Postbox footer
 */
function placester_postbox_footer() {
    ?>
		</div>
	</div>
    <?php
}
/** @} */


/**
 * Checks is specified template is active according to current theme
 *
 * @param string $name
 */
function placester_is_template_active($name) {
    if ( substr( $name, 0, 5) == 'user_' )
        return true;

    $themes = get_allowed_themes();
    $theme_names = array();
    foreach ($themes as $t)
        $theme_names[] = $t['Stylesheet'];

    foreach ($theme_names as $theme) {
        if ( substr( $name, 0, strlen( $theme ) + 1) == $theme . '_' )
            return true;
    }

    return false;
}
/** @} */
