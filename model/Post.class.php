<?php

Class Post {

	public $post_id;
	public $timestamp;
	public $ip;
	public $useragent;
	public $postMeta; // array with request metadata
	public $postFiles; // array with (may be more than one) file
	
	public function __construct()
	{
		
		
		
	}

	/**
	 * set info about post metadata and file metadata
	 *
	 * Handle $_POST info to get metadata (form fields values)
	 *  and $_FILES info to get information about the file(s)
	 *
	 * @param array $reqs $_POST information
	 *
	 * @param mysqli $conn
	 */
	public function handlePost($reqs, $conn)
	{

		/*
		 * get info about request (if any);
		 * timestamp, ip and useragent are parsed from header request
		*/
		$this->timestamp = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->useragent = $_SERVER['HTTP_USER_AGENT'];

		/* Create an array for metadata fields and files data */
		$metadados = array();
		$datafile = array();

		/* Populate metadados array with info about form fields (VERTICAL) */
		foreach ($reqs as $name => $value)
		{
			if ($name != "appId") {
				$metadados[] = new Meta($name, $value);
			}
		}

		$this->postMeta = $metadados;

		/* Populate datafile array with info about files */
		$i = 0;
		while ($_FILES['file']['name'][$i] != NULL) {

			$datafile[] = new File($_FILES, $i); //['file']['name'][$i] (appends fields to an array, not to [file])
			$i++;

		}

		if(count($datafile)) {
			$this->postFiles = $datafile;
		} else {
			$this->postFiles = NULL;
		}

		//check if id is already used, if not, set generated id to post
		$action = new Action();
		$post_id = $this->generateSafeString(11);

		while($action->verifyId($post_id, "posts", $conn)) { //true = used, generate another
			$post_id = $this->generateSafeString(11);
		}

		$this->post_id = $post_id;

	}

	public function generateSafeString($sizeid)
	{

		$chars = "ABCDEFGHJKLMPQRSTUVWXYZ";
		$chars .= "abcdefghkmnpqrstuvwxyz";
		$chars .= "0123456789";

		$id = array();

		for ($i = 0; $i < $sizeid; $i++)
			$id[$i] = $chars[mt_rand(0 , strlen($chars) - 1)];

		return implode("",$id);

	}

	/**
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this);
	}
}