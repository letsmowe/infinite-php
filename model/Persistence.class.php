<?php

include 'App.class.php';
include 'Post.class.php';
include 'Meta.class.php';
include 'File.class.php';

class Persistence {
	public $post;
	public $app;

	/**
	 * Persistence constructor
	 *
	 * Called to manipulate and store apps definitions and rules
	 *
	 * @param array $dados decoded json
	 * @param mysqli $conn db connection already set, connection is set on Action
	 */
	public function __construct($dados, $conn)
	{
		if (!empty($dados)) {

			$this->handleApp($dados, $conn);
			$this->handlePost($dados, $conn);

		} else {
			// function to return information
		}

	}

	/**
	 * define app and its rules for meta fields and files
	 *
	 * Handle json decoded array to get info
	 *
	 * @param array $dados json decoded array
	 * @param mysqli $conn (already a connection, not the object reference)
	 */
	public function handleApp($dados, $conn) {

		//create id for app
		//check if id is already used
		//if id not used, set the rules

		$appId = $this->generateSafeString(11);

		while ($this->verifyId($appId, "apps", $conn)) { //true == used, generate new id
			$appId = $this->generateSafeString(11);
		}

		$app = new App($appId, $dados["name"], $dados["rules"], $conn);
		$this->app = $app;
	}

	/**
	 * set info about post metadata and file metadata (if there is one)
	 *
	 * Handle $_POST info to get metadata (form fields values)
	 *  and $_FILES info to get information about the file(s)
	 *
	 * @param array $reqs $_POST information (when not form, json parsed array
	 * @param mysqli $conn
	 */
	public function handlePost($reqs, $conn)
	{

		/*
		 * get info about request (if any);
		 * timestamp, ip and useragent are parsed from header request
		*/

		$timestamp = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		$ip = $_SERVER['REMOTE_ADDR'];
		$useragent = $_SERVER['HTTP_USER_AGENT'];

		// Create an array for metadata fields and files data
		$datameta = array();
		$datafile = array();

		$meta = new Meta();
		$file = new File();

		if ($reqs['appId']) {

			// Populate metadados array with info about form fields (VERTICAL)
			foreach ($reqs as $name => $value) {

				if ($name != "appId") {
					array_push($datameta, $meta->setMeta($name, $value));
				}

			}

			// Populate datafile array with info about files
			$i = 0;
			while ($_FILES['file']['name'][$i] != NULL) {

				array_push($datafile, $file->setFile($_FILES, $i)); //['file']['name'][$i] (appends fields to an array, not to [file])
				$i++;

			}
		}

		//check if id is already used, if not, set generated id to post
		$post_id = $this->generateSafeString(11);

		while($this->verifyId($post_id, "posts", $conn)) { //true = used, generate another
			$post_id = $this->generateSafeString(11);
		}

		(count($datameta)) ? $postMeta = $datameta : $postMeta = NULL;
		(count($datafile)) ? $postFiles = $datafile : $postFiles = NULL;

		$post = new Post($post_id, $timestamp, $ip, $useragent, $postMeta, $postFiles);
		$this->post = $post;

		$post->insert($this->app->_id, $conn);
		
		if ($postMeta != NULL)
			$meta->insert($postMeta, $this->app->_id, $this->post->post_id, $conn);

		if ($postFiles != NULL)
			$file->insert($postFiles, $this->app->_id, $this->post->post_id, $conn);
	}

	/**
	 * create a safe string of said size and returns it
	 *
	 * Create an array of chars from other array of chars (defined),
	 *  and return implode the array ("glue" array elements like a string)
	 *
	 * @param int $sizeid size of string
	 * @return string
	 */
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
	 * get string id and table name, and try to select on db
	 *  if query parse any info (probably only one row - its unique anyway), then id is already used
	 *  if query does not return any info, then id is available
	 *
	 * @param string $id id from target element
	 * @param string $table table name from target element
	 * @param mysqli $conn
	 *
	 * @return boolean
	 */
	public function verifyId ($id, $table, $conn) {

		switch ($table) {
			case "apps" :
				$sql = "SELECT _id FROM apps WHERE _id='$id'";
				break;
			case "posts" :
				$sql = "SELECT _id FROM posts WHERE _id='$id'";
				break;
		}

		$result = $conn->query($sql);

		if ($result->num_rows) {
			return true; //used
		} else {
			return false;
		}

	}

	/**
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this);
	}

}