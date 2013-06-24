<?php

PL_Analytics::init();

class PL_Analytics {

	public static function init () {
		// Hook this event for general actions in the page's footer...
		add_action('wp_footer', array(__CLASS__, 'print_footer_scripts'));

		// AJAX endpoint for retrieving data/hash...
		add_action('wp_ajax_analytics_data', array(__CLASS__, 'get_data_ajax'));
		add_action('wp_ajax_nopriv_analytics_data', array(__CLASS__, 'get_data_ajax'));
	}

	public static function can_collect () {
		$can_collect;
		
		if (defined("HOSTED_PLUGIN_KEY")) {
			$can_collect = true;
		}
		else { // i.e., not on the hosted platform...
			$can_collect = PL_Option_Helper::get_log_errors();
		}

		return $can_collect;
	}

	private static function get_admin_info () {
		// Use for API key ID + web_secret
		$whoami = PL_Helper_User::whoami();

		// We need BOTH of these...
		if (empty($whoami["api_key_id"]) || empty($whoami["api_key_web_secret"])) {
			return false;
		}

		$info = array("api_key_id" => $whoami["api_key_id"], "web_secret" => $whoami["api_key_web_secret"]);
		return $info;
	}

	private static function hash_data ($data) {
		$info = self::get_admin_info();
		
		// Sanity check...
		if (empty($info)) { return null; }
		
		// Add the API key ID to the data array...
		$data["api_key_id"] = $info["api_key_id"];

		// Encode the data array as JSON...
		$data_json = json_encode($data);

		// Combine, hash and repeat as necessary...
		$hash = PL_Base64::strict((hash_hmac("sha256", $data_json, "{$info['api_key_id']}{$info['web_secret']}", true)));
		$output = PL_Base64::url_safe("{$hash}--{$data_json}");

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

		// Add the "time" arg + value...
		$data["time"] = time();

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
		// error_log(var_export($args, true));
		return self::produce_data("listing_search", $args);
	}

	public static function home_view () {
		return self::produce_data("home_view");
	}

	public static function page_view () {
		return self::produce_data("page_view");
	}

	public static function get_data_ajax () {
		$event = isset($_POST["event"]) ? $_POST["event"] : "";
		$response = array();

		switch ($event) {
			case "listing_view":
				if (isset($_POST["prop_id"])) 
					{ $response["hash"] = self::listing_view($_POST["prop_id"]); }
				else
					{ $response["error_msg"] = "Missing required event attributes"; }
				break;
			case "home_view":
				$response["hash"] = self::home_view();
				break;
			case "page_view":
				$response["hash"] = self::page_view();
				break;				
			default:
				$response["error_msg"] = "Invalid event type or none passed";
		}

		echo json_encode($response);
		die();
	}

	public static function log_snippet_js ($event, $attributes = array()) {
	  	ob_start();
	  	?>
	  		<script type="text/javascript">
	  			if (typeof PlacesterAnalytics !== 'undefined') {
	  				var data = {action: "analytics_data", event: "<?php echo $event; ?>"};

	  			  <?php foreach ($attributes as $key => $value): ?>
	  				data.<?php echo $key; ?> = "<?php echo $value; ?>";
	  			  <?php endforeach; ?>

					jQuery.post(info.ajaxurl, data, function (response) {
						if (response && response.hash) { 
							PlacesterAnalytics.log(response.hash);
							// console.log("Sent this hash: ", response.hash);
						}
					}, 'json');
	  			}
	  		</script>
	  	<?php

	  	return ob_get_clean();
	}

	public static function print_footer_scripts () {
		global $i_am_a_placester_theme;

		// If the site is using a Placester theme, add footer script...
		if ($i_am_a_placester_theme === true) {
			$event = ( is_home() ? "home_view" : "page_view" );
			echo self::log_snippet_js($event);
		}
	}
}

/* 
 * Implements non-standard base64 encoding techniques not present in PHP...
 */
class PL_Base64 {

	public static function strict ($str) {
		// Start with the standard base64 encoding...
		$base = base64_encode($str);

		// Make base64 encoding comply with 'strict' standards -- currently, that requires no extra work!
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