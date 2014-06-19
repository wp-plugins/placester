<?php

PL_CRM_Followupboss::init();

class PL_CRM_Followupboss extends PL_CRM_Base {
	
	private static $apiOptionKey = "pl_followupboss_api_key";
	private static $apiURL = "https://api.followupboss.com";
	private static $version = "v1";

	private static $contactFieldMeta = array();

	public static function init () {
		// Register this CRM implementation with the controller...
		if (class_exists("PL_CRM_Controller")) {
			$crm_info = array(
				"id" => "followupboss",
				"class" => "PL_CRM_Followupboss",
				"display_name" => "Follow Up Boss",
				"referral_url" => "https://app.followupboss.com/signup?p=placester",
				"cred_lookup_url" => "https://app.followupboss.com/settings/user",
				"logo_img" => "follow-up-boss-color.png"
			);

			PL_CRM_Controller::registerCRM($crm_info);
		}

		// Initialize contact field -- NOTE: Specific to this CRM's API!!!
		self::$contactFieldMeta = array(
			"id" => array(
				"label" => "ID",
				"data_format" => "string",
				"searchable" => false
			),
			"firstName" => array(
				"label" => "First Name",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"lastName" => array(
				"label" => "Last Name",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"emails" => array(
				"label" => "E-mail(s)",
				"data_format" => "array",
				"searchable" => false
			),
			"phones" => array(
				"label" => "Phone(s)",
				"data_format" => "array",
				"searchable" => false
			),
			"stage" => array(
				"label" => "Stage",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"source" => array(
				"label" => "Source",
				"data_format" => "string",
				"searchable" => true,
				"group" => "Search",
				"type" => "text"
			),
			"lastActivity" => array(
				"label" => "Last Activity",
				"data_format" => "datetime",
				"searchable" => false
			),
			"contacted" => array(
				"label" => "Contacted",
				"data_format" => "boolean",
				"searchable" => true,
				"group" => "Search",
				"type" => "checkbox"
			)
		);
	}

	public function __construct () {
		// Nothing yet...
	}

	protected function getAPIOptionKey () {
		return self::$apiOptionKey;
	}

	protected function setCredentials (&$handle, &$args) {
		$api_key = $this->getAPIKey();

		// HTTP authentication using the API key as user name with no password...
		curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($handle, CURLOPT_USERPWD, $api_key . ":");
	}

	public function constructURL ($endpoint) {
		$url = self::$apiURL;
		$version = self::$version;

		return "{$url}/{$version}/{$endpoint}";
	}

	/*
	 * Contacts
	 */

	public function contactFieldMeta () {
		return self::$contactFieldMeta;
	}  

	public function contactFieldLabels () {
		$labels = array();

		foreach (self::$contactFieldMeta as $field => $meta) {
			$labels[] = $meta["label"]; 
		}

		return $labels;
	}

	public function formatContactData ($value, $format) {
		$newVal = $value;
		
		switch($format) {
			case "boolean":
				$newVal = empty($value) ? "No" : "Yes";
				break;
			case "datetime":
				$date = new DateTime($value);
				$newVal = $date->format("m/d/Y");
				break;
			case "array":
				$newVal = "";
				if (is_array($value)) {
					foreach ($value as $item) {
						$type = empty($item["type"]) ? "" : "(<i>{$item['type']}</i>)<br/>";
						$val = empty($item["value"]) ? "" : "{$item['value']} ";
						$newVal .= "{$val}{$type}";
					}
					// $newVal = rtrim($newVal, ", ");
				}
				break;
			case "string":
				$newVal = trim($value);
				break;
			default:
				// Do nothing...
				break;
		}

		return $newVal;
	}

	public function getContacts ($filters = array()) {
		// This is a GET request, so mark all filters as query string params...
		$args = array("query_params" => $filters);

		// Make API Call...
		$response = $this->callAPI("people", "GET", $args);

		// error_log(var_export($response, true));

		// Translate API specific response into standard contacts collection...
		$data = array();
		$data["total"] = empty($response["_metadata"]["total"]) ? 0 : $response["_metadata"]["total"];
		$data["contacts"] = (empty($response["people"]) || !is_array($response["people"])) ? array() : $response["people"];

		return $data;
	}

	public function getContact ($id) {
		// Make API Call...
		$response = $this->callAPI("people/{$id}", "GET");
		// error_log(var_export($response, true));

		$details = array();
		$field_meta = $this->contactFieldMeta();

		if (!empty($response) && is_array($response)) {
			foreach ($response as $key => $value) {
				// Format value with CRM specific method...
				if (!empty($field_meta[$key]["data_format"])) {
					$details[$field_meta[$key]["label"]] = $this->formatContactData($value, $field_meta[$key]["data_format"]);
				}
				else {
					$details[$key] = $value;
				}
			}
		}
		
		$contact = array("name" => "", "details" => array());

		// Translate CRM-specific name field to top-level and remove from details...
		if (!empty($details["name"])) {
			$contact["name"] = $details["name"];
			unset($details["name"]);
		}

		$contact["details"] = $details;

		return $contact;
	}

	public function createContact ($args) {
		//
	}

	public function pushEvent ($event) {
		// NOTE: Use events endpoint for this!!!
		// event data
		$event = array(
		    "source" => "MyAwesomeWebsite.com",
		    "type" => "Property Inquiry",
		    "message" => "I would like to receive more information about 1234 High Oak St, Rochester, WA 98579.",
		    "person" => array(
		        "firstName" => "John",
		        "lastName" => "Smith",
		        "emails" => array(array("value" => "john.smith@gmail.com", "type" => "home")),
		        "phones" => array(array("value" => "555-555-5555", "type" => "home")),
		        "tags" => "Buyer, South"
		    ),
		    "property" => array(
		        "street" => "1234 High Oak St",
		        "city" => "Rochester",
		        "state" => "WA",
		        "code" => "98579",
		        "mlsNumber" => "1234567",
		        "price" => 449000,
		        "forRent" => false,
		        "url" => "http://www.myawesomewebsite.com/property/1234567-1234-high-oak-st-rochester-wa-98579/",
		        "type" => "Single-Family Home",
		        "bedrooms" => 3,
		        "bathrooms" => 2,
		        "area" => 2888,
		        "lot" => 0.98
		    )
		);

		// Set field the caller is expecting to set request payload...
		$args["body"] = $event;

		// Make API Call...
		$response = $this->callAPI("events", "GET", $args);
	}
}

?>