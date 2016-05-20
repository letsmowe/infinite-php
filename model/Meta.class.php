<?php
Class Meta {
	
	public $metaName;
	public $metaValue;

	/**
	 * Receive fields name and value about post "metadata" (form fields)
	 * @param string $name field name
	 * @param string $value field value
	 */
	public function __construct($name, $value)
	{
		$this->metaName = $name;
		$this->metaValue = $value;
	}

	/**
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this);
	}
}