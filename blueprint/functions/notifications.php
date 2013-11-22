<?php 

PLS_Notifications::init();
class PLS_Notifications {

	public static $messages = array(
		'no_plugin' => 'This theme is designed for use with the <a href="%s">Real Estate Website Builder</a> plugin. To activate additional features <a class="button" href="%s">Install it Now</a>.',
		'no_api_key' => 'There is no API key associated with the Real Estate Website Builder plugin. Please <a class="button" href="%s">Set a Valid API Key</a>'
	);

	public static $wp_org_plugin_url = 'http://wordpress.org/extend/plugins/placester/';
	// this doesn't give search results on a network install, but it still loads the plugin page
	public static $plugin_install_path = 'plugin-install.php?tab=search&s=real+estate+website+builder&plugin-search-input=Search+Plugins';
	public static $plugin_settings_path = 'admin.php?page=placester_settings';

	public static function init () {
		add_action('admin_notices', array(__CLASS__, 'no_plugin'));
	}

	public static function no_plugin () {
		$error_msg = pls_has_plugin_error();

		$adminURL = ( defined('ADMIN_URL') ? ADMIN_URL : trailingslashit( admin_url() ) );

		if ($error_msg) {
			ob_start();

			switch ($error_msg) {
				case "no_plugin":
				?>
					<div style="margin-top: 10px; border: 1px solid #E6DB55;" class="update-nag"><?php printf( self::$messages['no_plugin'], self::$wp_org_plugin_url, $adminURL . self::$plugin_install_path ); ?></div>
				<?php
				break; 

				case "no_api_key":
					?>

					<div style="margin-top: 10px; border: 1px solid #E6DB55;" class="update-nag"><?php printf( self::$messages['no_api_key'], self::$plugin_settings_path ); ?></div>
				<?php
				break; 
			}

			echo ob_get_clean();
		}
	}
}