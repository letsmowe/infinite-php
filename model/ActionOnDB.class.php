<?php

Class ActionOnDB
{
	/**
	 * @param Persistence $dataPersistence persistence = created class on index
	 * @param mysqli $conn
	*/
	public function insertPost ($dataPersistence, $conn)
	{
		$post_id = $dataPersistence->post->post_id;
		$app_id = $dataPersistence->app->_id;
		$timestamp = $dataPersistence->post->timestamp;
		$ip = $dataPersistence->post->ip;
		$useragent = $dataPersistence->post->useragent;
		$meta_info = $dataPersistence->post->postMeta;
		$files_info = $dataPersistence->post->postFiles;

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

	/**
	 *
	 *
	 * @param App $dataApp array com que contem o objeto app
	 * @param mysqli $conn
	*/
	public function insertApp ($dataApp, $conn) {
		$id = $dataApp->_id;
		$name = $dataApp->name;

		$sql = "INSERT INTO app (_id, name) VALUES ('" . $id . "','" . $name . "')";

		if ($conn->query($sql) !== TRUE) {
			echo "Erro: " . $conn->error;
		}
	}

	/**
	 * run through $meta_info array (a created array with fields from
	 * submitted form), and prepare its keys and values as a sql query
	 *
	 * @param array $meta_info POST metadata
	 * @param string $app_id generated id for app
	 * @param string $post_id generated id for post
	 * @param mysqli $conn database connection
	 */
	public function insertMeta ($meta_info, $app_id, $post_id, $conn)
	{

		$sql = "";

		foreach ($meta_info as $key => $meta) {
			$insert = "INSERT INTO meta (app_id, posts_id, name, value) VALUES ('$app_id','$post_id'";
			$values = "";

			foreach ($meta as $name => $value) {
				$values .= ",'$value'";
			}

			$final = ");";

			$sql .= $insert . $values . $final;
		}

		if ($conn->multi_query($sql) !== TRUE) {
			echo "Erro: " . $conn->error;
		}

	}

	/**
	 * run through $file_info array (a created array with metadata from
	 * uploaded file), and prepare its keys and values as a sql query
	 *
	 * @param array $files_info FILE metadata
	 * @param string $app_id generated id for app
	 * @param string $post_id generated id for post
	 * @param mysqli $conn database connection
	 */
	public function insertFile ($files_info, $app_id, $post_id, $conn) {

		$sql = "";

		foreach ($files_info as $key => $file) {

			$insert = "INSERT INTO files (app_id, posts_id, name, path, size, extension, type, width, height) VALUES ('$app_id','$post_id'";

			$values = "";
			foreach ($file as $attr => $value) {
				($attr == "size" || $attr == "width" || $attr == "height") ? $values .= ", $value" : $values .= ", '$value'";
			}

			$final = ");";

			$sql .= $insert . $values . $final;

		}

		if ($conn->multi_query($sql) !== TRUE) {
			echo "Erro: " . $conn->error;
		}
	}

	/**
	 * try to select app record from db
	 *  if find specified app, return its info
	 *  if it does not, then return NULL
	 *      PROBABLY will find the record, since it is called inside a
	 *      successful verifyId()
	 *
	 * @param string $id id string
	 * @param mysqli $conn
	 * 
	 * @return array $dados if able to select, set $dados array, else return NULL
	*/
	public function selectApp ($id, $conn) {

		$dados = array();

		$sql = "SELECT _id, name FROM apps WHERE _id='$id'";

		$result = $conn->query($sql);

		if($result->num_rows) {
			while ($row = mysqli_fetch_assoc($result)) {
				foreach ($row as $key => $value) {
					$dados[$key] = $value;
				}
			}
		} else {
			$dados = NULL;
		}

		return $dados;
	}

	public function insertRules() {
		
	}
	
	public function selectRules() {
		
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

}