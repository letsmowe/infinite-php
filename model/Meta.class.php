<?php

class Meta {
	
	public $metaName;
	public $metaValue;

	/**
	 * Receive fields name and value about post "metadata" (form fields)
	 * 
	 * @param string $name field name
	 * @param string $value field value
	 *
	 * @return Meta $this return meta object
	 */
	public function setMeta($name, $value)
	{
		$this->metaName = $name;
		$this->metaValue = $value;

		return $this;
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
	public function insert ($meta_info, $app_id, $post_id, $conn)
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
			echo "Erro @ metaInsert: " . $conn->error;
		}

	}
}