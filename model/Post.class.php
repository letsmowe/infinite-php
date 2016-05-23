<?php

Class Post {

	public $post_id;
	public $timestamp;
	public $ip;
	public $useragent;
	public $postMeta; // array with request metadata
	public $postFiles; // array with (may be more than one) file
	
	public function __construct($id, $timestamp, $ip, $useragent, $postMeta, $postFiles)
	{
		$this->post_id = $id;
		$this->timestamp = $timestamp;
		$this->ip = $ip;
		$this->useragent = $useragent;
		$this->postMeta = $postMeta;
		$this->postFiles = $postFiles;
	}

	/**
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this);
	}
}