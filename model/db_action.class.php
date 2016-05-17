<?php
require('db_connection.php');

Class Db_action {

	function insertPost ($insert, $dbname)
	{

		$conn = connect($dbname); // function from bd_connection returns $conn
		$timestamp = $insert->timestamp;
		$ip = $insert->ip;
		$useragent = $insert->useragent;
		$meta_info = $insert->post_meta;
		$files_info = $insert->post_files;

		$sql = "INSERT INTO posts (timestamp, ip, useragent) VALUES ('$timestamp','$ip','$useragent')";

		if ($conn->query($sql) === TRUE) {
			$post_id = $conn->insert_id; // get the insert id from $conn (id post)

			//override sql because INSERT (command on $sql) was already executed
			$sql = $this->prepareMeta($meta_info, $post_id);
			$sql .= $this->prepareFile($files_info, $post_id);

			if ($conn->multi_query($sql) !== TRUE) {
				//git gud
				echo "Erro: " . $conn->error;
			}
			
		} else {
			echo "Erro: " . $conn->error;
		}
		
		$conn->close();
	
	}
	
	function insertApp ($dados, $dbname) {
		$conn = connect($dbname);

		$sql = "INSERT INTO apps (_id, name) VALUES ('" . $dados['appId'] . "','" . $dados['name'] . "')";

		if ($conn->query($sql) !== TRUE) {
			echo "Erro: " . $conn->error;
		}

		$conn->close();
	}

	/**
	 * @param string $id id string
	 * @param string $dbname database name
	 * 
	 * @return array $dados if able to select, set $dados array, else return NULL
	*/
	function selectApp ($id, $dbname) {
		$dados = array();
		
		$conn = connect($dbname);

		$sql = "SELECT _id, name FROM apps WHERE _id='$id'";

		$rest = $conn->query($sql);
		var_dump($conn);

		if($rest->num_rows > 0) {
			echo "a chave existe\n";
			while ($row = $rest->fetch_array(MYSQLI_BOTH)) {
				$dados[$row["_id"]] = $row["name"];
			}
		} else {
			$dados = NULL;
		}

		return $dados;

	}

	function verifyId ($id, $dbname, $table) {
		$conn = connect($dbname);

		switch ($table) {
			case "apps" :
				$sql = "SELECT _id FROM apps WHERE _id='$id'";
				break;
			case "posts" :
				$sql = "SELECT _id FROM posts WHERE _id='$id'";
				break;
		}

		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			var_dump($result);
			$result->free_result();
			$conn->close();
			return true; //used
		} else {
			$result->free_result();
			$conn->close();
			return false;
		}

	}

	function prepareMeta ($meta_info, $post_id)
	{

		$sql = "";

		foreach ($meta_info as $key => $meta) {
			$insert = "INSERT INTO meta (posts_id, name, value) VALUES ($post_id";
			$values = "";

			foreach ($meta as $name => $value) {
				$values .= ",'$value'";
			}

			$final = ");";

			$sql .= $insert . $values . $final;
		}

		return $sql;

	}

	function prepareFile ($files_info, $post_id) {

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