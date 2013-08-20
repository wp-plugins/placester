<?php 

PLS_Notifications::init();
class PLS_Notifications {

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
					<div style="margin-top: 10px; border: 1px solid #E6DB55;" class="update-nag">You are currently using a Placester theme without the <a href="http://wordpress.org/extend/plugins/placester/">Real Estate Website Builder</a> plugin. This theme wont work without it. Please <a class="button" href='<?php echo $adminURL; ?>plugin-install.php?tab=search&type=term&s="real+estate+website+builder"&plugin-search-input=Search+Plugins'>Install it Now</a></div>
				<?php
				break; 

			  case "no_api_key":
			    ?>
					<div style="margin-top: 10px; border: 1px solid #E6DB55;" class="update-nag">There is no API key associated with the Real Estate Website Builder plugin. Please <a class="button" href='<?php echo $adminURL; ?>admin.php?page=placester_settings'>Set a Valid API Key</a></div>
				<?php
				break; 
			}

			echo ob_get_clean();
		}
	}
}