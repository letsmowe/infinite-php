<?php

include 'Action.class.php';
include 'App.class.php';
include 'Post.class.php';
include 'Meta.class.php';
include 'File.class.php';

Class Persistence {
	public $post;
	public $app;

	/**
	 * Persistence constructor
	 *
	 * Called to manipulate and store apps definitions and rules
	 *
	 * @param array $dados decoded json
	 * @param object $c db connection already set, but need to get through function
	 */
	public function __construct($dados, $c)
	{

		if (!empty($dados)) {

			$conn = $c->getConnection();
			$this->handleApp($dados, $conn);
			$this->handlePost($dados, $conn);

		} else {

			// function to return information

		}

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
		$metadados = array();
		$datafile = array();

		if ($reqs['appId']) {
			// Populate metadados array with info about form fields (VERTICAL)
			foreach ($reqs as $name => $value) {
				if ($name != "appId") {
					$metadados[] = new Meta($name, $value);
				}
			}

			// Populate datafile array with info about files
			$i = 0;
			while ($_FILES['file']['name'][$i] != NULL) {

				$datafile[] = new File($_FILES, $i); //['file']['name'][$i] (appends fields to an array, not to [file])
				$i++;

			}
		}

		//check if id is already used, if not, set generated id to post
		$action = new Action();
		$post_id = $this->generateSafeString(11);

		while($action->verifyId($post_id, "posts", $conn)) { //true = used, generate another
			$post_id = $this->generateSafeString(11);
		}

		if(count($metadados)) {
			$postMeta = $metadados;
		} else {
			$postMeta = NULL;
		}

		if(count($datafile)) {
			$postFiles = $datafile;
		} else {
			$postFiles = NULL;
		}

		$post = new Post($post_id, $timestamp, $ip, $useragent, $postMeta, $postFiles);
		$this->post = $post;
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
		
		$action = new Action();

		$appId = $this->generateSafeString(11);
		while ($action->verifyId($appId, "apps", $conn)) { //true == used, generate new id
			$appId = $this->generateSafeString(11);
		}
		
		$app = new App($appId, $dados["name"], $dados["rules"]);
		$this->app = $app;
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
	 * @return string
	 */
	public function toJSON()
	{
		return json_encode($this);
	}
}