<?php 

class PL_Custom_Attribute_Helper {

	private static $translations = null;

	public static function get_translations () {
		// Check for memoized version...
		if (!is_null(self::$translations)) {
			// error_log("Using memoized translations!!!");
			return self::$translations;
		}

		$api_dictionary = PL_Custom_Attributes::get();
		$dictionary = array();

		foreach ($api_dictionary as $item) {
			$dictionary[$item['key']] = $item['name'];
		}

		// Memoize translations...
		self::$translations = $dictionary;
		// error_log("Memoizing translations...");

		return $dictionary;
	}
}