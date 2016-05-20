<?php

include "MetaRule.class.php";
include "FileRule.class.php";

Class Rule {

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

}