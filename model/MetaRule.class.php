<?php

Class MetaRule {
	
	public $name;
	public $contentType;

	/**
	 * @param array $rule single meta rule from Rule.class
	*/
	public function __construct($rule)
	{
		$this->name = $rule['name'];
		$this->contentType = $rule['contentType'];
	}

}