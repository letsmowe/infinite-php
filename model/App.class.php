<?php

include 'Rule.class.php';

class App {
	
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
	 * @param array $rulesData rules
	 * @param mysqli $conn database connection
	 */
	public function __construct($id, $name, $rulesData, $conn)
	{
		$this->_id = $id;
		$this->name = $name;
		$rules = $this->setRules($rulesData);
		$this->insert($conn);
		
		$ruleId = $rules->insert($this->_id, $conn);
		$rules->insertMetaRules($ruleId, $conn);
		$rules->insertFileRules($ruleId, $conn);
	}

	/**
	 * set rules for app
	 *
	 * Receive rules from parsed json and set rules for app
	 *
	 * @param array $rulesData array with rules from both meta and files
	 *
	 * @return Rule $rules
	*/
	public function setRules ($rulesData) {
		$rules = new Rule($rulesData["restricted"], $rulesData["meta"], $rulesData["files"]);
		$this->rule = $rules;

		return $rules;
	}

	/**
	 *
	 *
	 * @param mysqli $conn database connection
	 *
	 * @return int $last_id
	 */
	public function insert ($conn) {
		$id = $this->_id;
		$name = $this->name;

		$sql = "INSERT INTO app (_id, name) VALUES ('$id','$name')";

		if ($conn->query($sql) !== TRUE) {
			echo "Erro @ appInsert: " . $conn->error;
		}
	}
}