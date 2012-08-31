<?php 

PLS_Notifications::init();
class PLS_Notifications {

	function init () {
		add_action('admin_notices', array(__CLASS__, 'no_plugin'));
	}

	function no_plugin () {
		if (pls_has_plugin_error()) {
			ob_start();
			?>
				<div style="margin-top: 10px; border: 1px solid #E6DB55;" class="update-nag">You are currently using a Placester theme without the <a href="http://wordpress.org/extend/plugins/placester/">Real Estate Website Builder</a> plugin. This theme wont work without it. Please <a class="button" href="/wp-admin/plugin-install.php?tab=search&type=term&s=real+estate+website+builder&plugin-search-input=Search+Plugins">Install it Now</a></div>
			<?php 
			echo ob_get_clean();
		}
	}
}