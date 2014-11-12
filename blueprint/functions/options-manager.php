<?php

PLS_Options_Manager::init();
class PLS_Options_Manager {

	public static function init() {
		// add_action('wp_ajax_export_theme_options', array(__CLASS__, 'export_ajax'));
		add_action('admin_init', array(__CLASS__,'export_http'));
		add_action('wp_ajax_import_theme_options', array(__CLASS__, 'import_ajax'));
	}

	public static function export_http() {
		// error_log("Made it into export_http...");
		if ( isset($_GET['page']) && ($_GET['page'] == 'pls-theme-options') && isset($_GET['export']) ) {	
			// error_log("In export_http...");

			$config = get_option('optionsframework');
			$options = get_option( $config['id'], false );

			$theme_name = strtolower(wp_get_theme()->template);
			$filename = 'theme_options';
			if ( isset($_GET['filename']) && !empty($_GET['filename'])) {
				$filename = $_GET['filename'];
			}

			// Set HTTP response headers...
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename={$theme_name}_{$filename}.txt");
			header("Pragma: no-cache");
			header("Expires: 0");

			echo serialize($options);
			exit;
		}
	}

	public static function import_ajax() {
		// error_log('Made it into import_ajax...');
		if ( isset($_POST['options_raw']) ) {
			$options_raw = stripslashes($_POST['options_raw']);
			$options = unserialize($options_raw);

			$config = get_option( 'optionsframework' );
			// error_log($config['id']);			

			// This is VITAL to allowing the theme options field to actually get updated
			$_POST['update'] = 'true';

			$config = get_option( 'optionsframework' );
			$import_status = update_option( $config['id'], $options );

			// error_log('Theme options imported successfully: ' . ($import_status ? 'true' : 'false'));
			// error_log($import_status);
		}

		die();
	}
}

?>