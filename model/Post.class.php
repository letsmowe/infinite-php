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
	 * @param string $appId generated id for app
	 * @param mysqli $conn
	 */
	public function insert ($appId, $conn)
	{
		$post_id = $this->post_id;
		$app_id = $appId;
		$timestamp = $this->timestamp;
		$ip = $this->ip;
		$useragent = $this->useragent;

		$sql = "INSERT INTO posts (_id, app_id, timestamp, ip, useragent) VALUES ('$post_id','$app_id','$timestamp','$ip','$useragent')";

		if ($conn->query($sql) !== TRUE) {
			echo "Erro: " . $conn->error;
		}

		/*
		//override $sql because INSERT (command on $sql) was already executed
		$sql = $this->prepareMeta($meta_info, $post_id);
		$sql .= $this->prepareFile($files_info, $post_id);

		if ($conn->multi_query($sql) !== TRUE) {
			//git gud
			echo "Erro: " . $conn->error;
		}
		*/

	}
}