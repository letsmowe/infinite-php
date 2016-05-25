<?php

class FileRule {

	public $name;
	public $contentType;
	public $maxSize;
	public $isRequired;

	/**
	 * @param array $rule single file rule from Rule.class
	*/
	public function __construct($rule)
	{
		$this->name = $rule['name'];
		$this->contentType = $rule['contentType'];
		$this->maxSize = $rule['maxSize'];
		$this->isRequired = $rule['isRequired'];
	}
}