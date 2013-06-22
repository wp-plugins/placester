<?php

abstract class PL_CRM_Base {
	
	public static function init () {

	}

	abstract function getAPIOptionKey ();

	public function getAPIkey ();
	public function setAPIkey ($key);

	abstract public function callAPI ($args);

	abstract public function createContact ($args);

	abstract public function updateContact ($args);

	abstract public function deleteContact ($args);

}

?>