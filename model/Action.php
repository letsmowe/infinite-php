<?php


class Action {
	
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