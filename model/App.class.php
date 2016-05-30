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
		// App insert
		$this->insert($conn);
		
		// Object that receive Rule object created (able to access its functions)
		$rules = $this->setRules($rulesData);
		
		// $ruleId get the last insert id (or the inserted id for rules)
		$ruleId = $rules->insert($this->_id, $conn);
		// Use the last insert id to insert metaRules/fileRules
		$rules->insertMetaRules($ruleId, $conn);
		$rules->insertFileRules($ruleId, $conn);
	}

	/**
	 * set rules for app
	 *
	 * Receive rules from parsed json and set rules for app,
	 * returns the created Rule object
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
	 * insert function for app on db
	 *
	 * @param mysqli $conn database connection
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