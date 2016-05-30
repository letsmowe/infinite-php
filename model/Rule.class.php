<?php

include 'MetaRule.class.php';
include 'FileRule.class.php';

class Rule {

	public $restricted;
	public $metaRule;
	public $fileRule;

	/**
	 * Rules constructor
	 *
	 * Called to define rules information from parsed json
	 *
	 * @param boolean $restricted set if app record should follow rules strictly or not
	 * @param array $metaRule array with rules for meta fields
	 * @param array $fileRule array with rules for files
	 */
	public function __construct($restricted, $metaRule, $fileRule)
	{
		$this->restricted = $restricted;
		$this->setMetaRule($metaRule);
		$this->setFileRule($fileRule);
	}

	/**
	 * manipulate data about meta rules for app
	 *
	 * @param array $metaRule
	*/
	public function setMetaRule ($metaRule) {

		$rulesMeta = array();

		if (!empty($metaRule['rules'])) {

			foreach ($metaRule["rules"] as $ruleNum => $rule) {
				$rulesMeta[$ruleNum] = new MetaRule($rule);
			}

		} else {
			$rulesMeta = NULL;
		}

		$this->metaRule = $rulesMeta;
	}

	/**
	 * manipulate data about file rules for app
	 *
	 * @param array $fileRule
	 */
	public function setFileRule ($fileRule) {

		$rulesFile = array();

		if (!empty($fileRule['rules'])) {

			foreach ($fileRule['rules'] as $ruleNum => $rule) {
				$rulesFile[$ruleNum] = new FileRule($rule);
			}

		} else {
			$rulesFile = NULL;
		}

		$this->fileRule = $rulesFile;
	}

	/**
	 * insert function for meta_rules on db
	 *
	 * @param int $lastRuleId rule from rule insert on db
	 * @param mysqli $conn
	*/
	public function insertMetaRules($lastRuleId, $conn) {
		$metaRules = $this->metaRule;

		foreach ($metaRules as $obj => $rule) {
			$sql = "";
			$sqlattr = "";
			$sqlinsert = "INSERT INTO meta_rules (rules_id" . $sqlattr;
			$sqlvalue = ") VALUES ($lastRuleId";
			$sqlvalues = "";
			foreach ($rule as $attr => $value) {
				$sqlattr .= ", $attr";
				$sqlvalues .= ", '$value'";
			}
			$sqlfinal = ");";
			$sql .= $sqlinsert . $sqlattr . $sqlvalue . $sqlvalues . $sqlfinal;

			if ($conn->query($sql) !== TRUE) {
				echo "Erro @ metaRuleInsert " . $conn->error;
			}
		}
	}

	/**
	 * insert function for files_rules on db
	 *
	 * @param int $lastRuleId
	 * @param mysqli $conn
	 */
	public function insertFileRules($lastRuleId, $conn) {
		$fileRules = $this->fileRule;

		foreach ($fileRules as $obj => $rule) {
			$sql = "";
			$sqlattr = "";
			$sqlinsert = "INSERT INTO files_rules (rules_id" . $sqlattr;
			$sqlvalue = ") VALUES ($lastRuleId";
			$sqlvalues = "";
			foreach ($rule as $attr => $value) {
				$sqlattr .= ", $attr";
				($attr == "maxSize") ? $sqlvalues .= ", $value" : $sqlvalues .= ", '$value'";
			}
			$sqlfinal = ");";
			$sql .= $sqlinsert . $sqlattr . $sqlvalue . $sqlvalues . $sqlfinal;

			if ($conn->query($sql) !== TRUE) {
				echo "Erro @ metaRuleInsert " . $conn->error;
			}
		}
	}

	/**
	 * insert function for rules on db
	 *
	 * Returns the last_id (id of the inserted rule) to use on the relationship
	 * with its child-like rules (1-n relationship between rule and metaRule/fileRule)
	 *
	 * @param string $appId
	 * @param mysqli $conn
	 *
	 * @return int $last_id
	*/
	public function insert ($appId, $conn) {
		$restrc = $this->restricted;
		$last_id = NULL;

		$sql = "INSERT INTO rules (app_id, restricted) VALUES ('$appId','$restrc')";

		if ($conn->query($sql) !== TRUE) {
			echo "Erro @ ruleInsert " . $conn->error;
		} else {
			$last_id = $conn->insert_id;
		}

		return $last_id;
	}
}