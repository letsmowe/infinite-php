<?php
Class Persistence {
	public $app;
	public $_id;
	public $timestamp;
	public $ip;
	public $useragent;
	public $post_meta; // array with request metadata
	public $post_files; // array with (may be more than one) file

	/**
	 * Persistence constructor
	 *
	 * Called to manipulate and store infos and files upload from form
	 *
	 * @param array $reqs $_POST info
	 */
	public function __construct($reqs)
	{

		if (count($_POST)) {

			$this->handleApp($reqs);
			//$this->handlePost($reqs);

		} else {

			// function to return information

		}

	}

	/**
	 * connect app to its meta fields (rules)
	 *
	 * Handle $_POST info to get app id that was set on form and...
	 *
	 * @param array $reqs $_POST information
	*/
	public function handleApp($reqs) {
		//check if id is already used, if it is, handle its rules to fields post
		//if id not used, create id with its info
		
		$appId = $reqs['appId'];

		//$this->app = $reqs['appId'];
		
		//check if app id is already used, if not, create id for app
		$action = new Db_action();
		$db = "infinite-php";

		if($action->verifyId($appId, $db, "apps") === TRUE) { //true == used, set rules for post meta
			//app rules from id
			echo "entra aqui\n";
			$this->app = $action->selectApp($appId,$db);
		} else {
			echo "não tem essa chave";
			//$action->insertApp($reqs, $db);
		}
	}

	/**
	 * set info about post metadata and file data
	 *
	 * Handle $_POST info to get metadata (form fields values)
	 *  and $_FILES info to get information about the file(s)
	 *
	 * @param array $reqs $_POST information
	 */
	public function handlePost($reqs)
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

		$this->post_meta = $metadados;

		/* Populate datafile array with info about files */
		$i = 0;
		while ($_FILES['file']['name'][$i] != null) {

			$datafile[] = new File($_FILES, $i); //['file']['name'][$i] (appends fields to an array, not to [file])
			$i++;

		}

		if(count($datafile)) {
			$this->post_files = $datafile;
		} else {
			$this->post_files = NULL;
		}

		//check if id is already used, if not, set generated id to post
		$action = new Db_action();
		$db = "infinite-php";
		$post_id = $this->generateSafeString(11);

		while($action->verifyId($post_id, $db, "posts")) { //true = used, generate another
			$post_id = $this->generateSafeString(11);
		}

		$this->_id = $post_id;

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