<?php

Class Action
{
	/**
	 * @param Persistence $insert
	 * @param mysqli $conn
	*/
	public function insertPost ($insert, $conn)
	{
		$id = $insert->post_id;
		$post_id = $insert->post_id;
		$app_id = $insert->app["_id"];
		$timestamp = $insert->timestamp;
		$ip = $insert->ip;
		$useragent = $insert->useragent;
		$meta_info = $insert->post_meta;
		$files_info = $insert->post_files;

		$sql = "INSERT INTO posts (_id, apps_id, timestamp, ip, useragent) VALUES ('$id','$app_id','$timestamp','$ip','$useragent')";

		if ($conn->query($sql) === TRUE) {

			//override $sql because INSERT (command on $sql) was already executed
			$sql = $this->prepareMeta($meta_info, $post_id);
			$sql .= $this->prepareFile($files_info, $post_id);

			if ($conn->multi_query($sql) !== TRUE) {
				//git gud
				echo "Erro: " . $conn->error;
			}
			
		} else {
			echo "Erro: " . $conn->error;
		}
	
	}

	/**
	 *
	 *
	 * @param array $dados
	 * @param mysqli $conn
	*/
	public function insertApp ($dados, $conn) {

		$sql = "INSERT INTO apps (_id, name) VALUES ('" . $dados['appId'] . "','" . $dados['name'] . "')";

		if ($conn->query($sql) !== TRUE) {
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
	 * run through $meta_info array (a created array with fields from
	 * submitted form), and prepare its keys and values as a sql query
	 *
	 * @param array $meta_info POST metadata
	 * @param string $post_id generated id for post
	 *
	 * @return string $sql return sql query with post metadata (fields names)
	*/
	public function prepareMeta ($meta_info, $post_id)
	{

		$sql = "";

		foreach ($meta_info as $key => $meta) {
			$insert = "INSERT INTO meta (posts_id, name, value) VALUES ('$post_id'";
			$values = "";

			foreach ($meta as $name => $value) {
				$values .= ",'$value'";
			}

			$final = ");";

			$sql .= $insert . $values . $final;
		}

		return $sql;

	}

	/**
	 * run through $file_info array (a created array with metadata from
	 * uploaded file), and prepare its keys and values as a sql query
	 *
	 * @param array $files_info FILE metadata
	 * @param string $post_id generated id for file
	 *
	 * @return string $sql return sql query with file metadata
	 */
	public function prepareFile ($files_info, $post_id) {

		$sql = "";

		foreach ($files_info as $key => $file) {

			$insert = "INSERT INTO files (posts_id, name, temp_path, path, size, origin, extension, type, width, height) VALUES ($post_id";

			$values = "";
			foreach ($file as $attr => $value) {
				($attr == "size" || $attr == "width" || $attr == "height") ? $values .= ", $value" : $values .= ", '$value'";
			}

			$final = ");";

			$sql .= $insert . $values . $final;

		}

		return $sql;
	}

}