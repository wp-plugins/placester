<?php 


class PL_Options {
	
	public static function get ($option, $default = false) {
		return get_option($option, $default);
	}

	public static function set ($option, $value, $autoload = true) {
		// Translate autoload boolean to expected WP string value...
		$autoload = ($autoload ? 'yes' : 'no');

		// Initially, try to add the option...
		$outcome = add_option($option, $value, '', $autoload);

		// If add_option fails, it almost always indicates that an option with the provided key 
		// already exists, so attempt to update the existing option's value...
		if ($outcome === false) {
			$outcome = update_option($option, $value);
		}

 		return $outcome;
	}

	public static function delete ($option) {
		return delete_option($option);
	}
}