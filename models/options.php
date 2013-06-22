<?php 


class PL_Options {
	
	public static function get ($option, $default = false) {
		return get_option($option, $default);
	}

	public static function set ($option, $value) {
		if (get_option($option, null) !== null) {
			return update_option($option, $value);
		} else {
			return add_option($option, $value);
		}
		
	}

}