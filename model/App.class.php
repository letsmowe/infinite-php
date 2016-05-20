<?php

include "Rule.class.php";

Class App {
	
	public $_id;
	public $name;
	public $rule;

	/**
	 * App constructor
	 *
	 * Called to define app information from parsed json
	 *
	 * @param string $id generated and verified unique string id
	 * @param string $name app name
	 * @param array $rules array with rules from both meta and files
	 */
	public function __construct($id, $name, $rules)
	{
		$this->_id = $id;
		$this->name = $name;
		$this->setRules($rules);
	}

	/**
	 * set rules for app
	 *
	 * Receive rules from parsed json and set rules for app
	 *
	 * @param array $rules
	*/
	public function setRules ($rules) {
		$rules = new Rule($rules["restricted"], $rules["meta"], $rules["files"]);
		$this->rule = $rules;
	}
}