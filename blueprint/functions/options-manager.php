<?php

PLS_Options_Manager::init();
class PLS_Options_Manager {

	static $defs_dir;
	static $def_theme_opts_list = array();

	function init() {
		// add_action('wp_ajax_export_theme_options', array(__CLASS__, 'export_ajax'));
		add_action('admin_init', array(__CLASS__,'export_http'));
		add_action('wp_ajax_import_theme_options', array(__CLASS__, 'import_ajax'));
		add_action('wp_ajax_import_default_options', array(__CLASS__, 'import_default_ajax'));

		self::$defs_dir = trailingslashit( PLS_OP_DIR ) . 'defaults';
		self::build_default_list();
	}

	static function export_http() {
	  // error_log("Made it into export_http...");
	  if ( isset($_GET['page']) && ($_GET['page'] == 'pls-theme-options') && isset($_GET['export']) ) {	
		// error_log("In export_http...");

		$config = get_option( 'optionsframework' );
		$options = get_option( $config['id'], false );

		$theme_name = strtolower(get_current_theme());
		$filename = 'theme_options';
		if ( isset($_GET['filename']) && !empty($_GET['filename'])) {
			$filename = $_GET['filename'];
		}

		// ob_start();
		//   pls_dump($options);
		// $options_str = ob_get_clean();
		// error_log($options_str);

		// Set HTTP response headers...
		header("Content-type: text/plain");
        header("Content-Disposition: attachment; filename={$theme_name}_{$filename}.txt");
        header("Pragma: no-cache");
        header("Expires: 0");

		echo serialize($options);
        exit();
      }
	}

	static function import_ajax() {
		// error_log('Made it into import_ajax...');

		if ( isset($_POST['options_raw']) ) {
			$options_raw = stripslashes($_POST['options_raw']);
			$options = unserialize($options_raw);

			// ob_start();
			//   pls_dump($options);
			// $options_str = ob_get_clean();
			// error_log($options_str);  

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

	static function build_default_list() {
		$def_files = scandir(self::$defs_dir);
		foreach ($def_files as $file) { 
			if (stripos($file, '.txt')) { 
				// error_log($file); 
				self::$def_theme_opts_list[] = str_replace('.txt', '', $file); 
			} 
		}
	}

	static function import_default_ajax() {
    if ( isset($_POST['name']) ) {
      $filename = $_POST['name'] . '.txt';
      $fullpath = trailingslashit( self::$defs_dir ) . $filename;
      
      if ( file_exists($fullpath) ) {
        $options_raw = file_get_contents($fullpath);
        $options = unserialize( $options_raw );
      
        // This is VITAL to allowing the theme options field to actually get updated
        $_POST['update'] = 'true';
        
        $config = get_option( 'optionsframework' );
        $import_status = update_option( $config['id'], $options );
      }
    }
	  die();
	}
}

?>