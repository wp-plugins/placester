<?php

PL_Analytics::init();

class PL_Analytics {

	public static function init() {
		// Nothing yet...
	}

	public static function can_collect() {
		$can_collect;
		
		if (defined('HOSTED_PLUGIN_KEY')) {
			$can_collect = true;
		}
		else { // i.e., not on the hosted platform...
			$can_collect = PL_Option_Helper::get_log_errors();
		}

		return $can_collect;
	}

	private static function get_admin_info() {
		// Use for API key + web_secret
		$whoami = PL_Helper_User::whoami();

		// We need BOTH of these...
		if (empty($whoami["api_key_id"]) || empty($whoami["api_key_web_secret"])) {
			return false;
		}

		$info = array("api_key" => $whoami["api_key_id"], "web_secret" => $whoami["api_key_web_secret"]);
		return $info;
	}

	private static function hash_data ($data) {
		$info = self::get_admin_info();
		
		// Sanity check...
		if (empty($info)) { return null; }
		
		// Merge $data with $info to include the API key and web_secret, then encode the result as JSON...
		$data_json = json_encode(array_merge($data, $info));

		// Combine, hash and repeat as necessary...
		$hash = PL_Base64::strict((hash_hmac("sha256", $data_json, "{$info['api_key']}{$info['web_secret']}", true)));
		$output = PL_Base64::url_safe("{$hash}--{$data}");

		return $output;
	}

	private static function produce_data ($type, $args = array()) {
		global $PL_ANALYTICS_CONFIG;

		// Get config for particular event type...
		$type_config;
		if (isset($PL_ANALYTICS_CONFIG[$type])) {
			$type_config = $PL_ANALYTICS_CONFIG[$type];
		}
		else {
			// Unknown event type -- exit prematurely by returning null...
			return null;
		}	
		
		// Construct event data array, initially storing the event category..
		$data = array("category" => $type_config["category"]);

		// Only include values from the $args array whose keys are specified by the given event type's config...
		foreach ($type_config["allowed_params"] as $param) {
			if (isset($args[$param])) {
				$data[$param] = $args[$param];
			}	
		}
		error_log(var_export($data, true));
		$output = self::hash_data($data);
		return $output;
	}

	public static function contact_submission ($args = array()) {
		return self::produce_data("contact_submission", $args);
	}

	public static function listing_view ($property_id) {
		// Map this to the key the gatherer uses...
		return self::produce_data("listing_view", array("page_id" => $property_id));
	}

	public static function listing_search ($args = array()) {
		return self::produce_data("listing_search", $args);
	}

	public static function log_snippet_js ($data) {
	  	ob_start();
	  	?>
	  		<script type="text/javascript">
	  			if (PlacesterAds) {
	    			PlacesterAds.log(<?php echo $data; ?>);  
	  			}
	  		</script>
	  	<?php

	  	return ob_get_clean();
	}
}

/* 
 * Implements non-standard base64 encoding techniques not present in PHP...
 */
class PL_Base64 {

	public static function strict ($str) {
		// Start with the standard base64 encoding...
		$base = base64_encode($str);

		// Make base64 encoding comply with 'strict' standards...
		$strict = $base;
		
		return $strict;
	}

	public static function url_safe ($str) {
		// Start with the strict base64 encoding...
		$base = self::strict($str);	
		
		// Apply the necessary character transformations to make encoding URL safe...
		// (specifically, '+' => '-', and '/' => '_')
		$url_safe = strtr($base, "+/", "-_");

		return $url_safe;
	}
}

?>