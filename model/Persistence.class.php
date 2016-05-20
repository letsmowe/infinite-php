<?php

include 'Action.class.php';
include 'App.class.php';

Class Persistence {
	public $app;
	//public $post;

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