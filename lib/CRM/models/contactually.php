<?php

PL_CRM_Contactually::init();

class PL_CRM_Contactually extends PL_CRM_Base {
	
	private static $apiOptionKey = "pl_contactually_api_key";
	private static $apiURL = "https://www.contactually.com/api";
	private static $version = "v1";

	private static $contactFieldMeta = array();

	public static function init () {
		// Register this CRM implementation with the controller...
		if (class_exists("PL_CRM_Controller")) {
			$crm_info = array(
				"id" => "contactually",
				"class" => "PL_CRM_Contactually",
				"display_name" => "Contactually",
				"referral_url" => "https://www.contactually.com/invite/placester",
				"cred_lookup_url" => "https://www.contactually.com/settings/integrations",
				"logo_img" => "contactually-logo.png"
			);

			PL_CRM_Controller::registerCRM($crm_info);
		}

		// Initialize contact field -- NOTE: Specific to this CRM's API!!!
		self::$contactFieldMeta = array(
			"id" => array(
				"label" => "ID",
				"data_format" => "string",
				"searchable" => true,
				"type" => "text"
			),
			"first_name" => array(
				"label" => "First Name",
				"data_format" => "string",
				"searchable" => true,
				"type" => "text"
			),
			"last_name" => array(
				"label" => "Last Name",
				"data_format" => "string",
				"searchable" => true,
				"type" => "text"
			),
			"email_addresses" => array(
				"label" => "E-mail(s)",
				"data_format" => "array",
				"searchable" => true,
				"type" => "text"
			),
			"phone_numbers" => array(
				"label" => "Phone(s)",
				"data_format" => "array",
				"searchable" => true,
				"type" => "text"
			),
			"company" => array(
				"label" => "Company",
				"data_format" => "string",
				"searchable" => true,
				"type" => "text"
			),
			"user_bucket" => array(
				"label" => "User Bucket",
				"data_format" => "object",
				"searchable" => true,
				"type" => "text"
			),
			"tags" => array(
				"label" => "Tags",
				"data_format" => "array",
				"searchable" => true,
				"type" => "text"
			),
			"last_contacted" => array(
				"label" => "Last Contacted",
				"data_format" => "datetime",
				"searchable" => false,
				"type" => "text"
			),
			"hits" => array(
				"label" => "Hits",
				"data_format" => "string",
				"searchable" => false,
				"type" => "text"
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
		// Attach the API as the first query arg for authentication purposes...
		if (!empty($args["query_params"]) && is_array($args["query_params"])) {
			$args["query_params"]["api_key"] = $this->getAPIKey();
		}
		else {
			$args["query_params"] = array("api_key" => $this->getAPIKey());
		}
	}

	protected function constructURL ($endpoint) {
		$url = self::$apiURL;
		$version = self::$version;

		return "{$url}/{$version}/{$endpoint}.json";
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

	public function generateContactSearchForm () {
		// $form_args = array("method" => "POST", "title" => true, "include_submit" => false, "id" => "contacts_grid_search")
		// PL_Form::generate_form($this->contactFieldMeta(), $form_args);
		return "";
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
						$newVal .= "{$item}, ";
					}
					$newVal = rtrim($newVal, ", ");
				}
				break;
			case "object":
				$newVal = empty($value["name"]) ? "" : $value["name"];
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
		// Need to set these as this API does enforce sane defaults..
		$filters["limit"] = ( empty($filters["limit"]) || !is_numeric($filters["limit"]) ? 10 : $filters["limit"] );
		$filters["page"] = ( empty($filters["page"]) || !is_numeric($filters["page"]) ? 1 : $filters["page"] );

		// Translate traditional "offset" field into a valid page number...
		if (isset($filters["offset"])) {
			$limit = $filters["limit"];
			$offset = $filters["offset"];

			// Pages are indexed from 1, so an offset of 0 must translate to the first page and so on...
			$filters["page"] = round(($offset + $limit)/$limit);
		}

		// This is a GET request, so mark all filters as query string params...
		$args = array("query_params" => $filters);

		// Make API Call...
		$response = $this->callAPI("contacts", "GET", $args);

		// error_log(var_export($response, true));

		// Translate API specific response into standard contacts collection...
		$data = array();
		$data["total"] = empty($response["total_count"]) ? 0 : $response["total_count"];
		$data["contacts"] = (empty($response["contacts"]) || !is_array($response["contacts"])) ? array() : $response["contacts"];

		return $data;
	}

	public function getContact ($id) {
		// Make API Call...
		$response = $this->callAPI("contacts/{$id}", "GET");
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
		if (!empty($details["full_name"])) {
			$contact["name"] = $details["full_name"];
			unset($details["full_name"]);
		}

		// Handle CRM-specific image field...
		if (!empty($details["avatar"])) {
			$contact["img_url"] = $details["avatar"];
			unset($details["avatar"]);
		}

		$contact["details"] = $details;

		return $contact;
	}

	public function createContact ($args) {
		//
	}

	public function pushEvent ($event) {
		//
	}
}

?>