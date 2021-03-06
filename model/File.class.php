<?php

Class File {
	public $name; // only the file name on server
	public $tempPath; // temp file path on temp folder
	public $path;
	public $size;
	public $origin; // name + extension on client machine
	public $extension;
	public $type;
	public $width;
	public $height;

	/**
	 * File "constructor"
	 *
	 * Receive a request as param from Persistence.handlePost
	 * and verify if the request is a file array (or contain elements
	 * like so) and get information about the specific file ($numfile param)
	 *
	 * @param array $reqs $_FILES object array
	 * @param int $numfile number of the file
	 *
	 * @return File $this return file object
	 */
	public function setFile($reqs, $numfile)
	{

		if(count($reqs['file'])) { // same as $_FILES['file']

			$this->handleFile($reqs, $numfile); // or $reqs['file'] as param, but suppressed for later clarity
			return $this;

		} else {
			// function to return information
		}

	}

	/**
	 * Get params from constructor, handle file information and store it on folder
	 *
	 * @param array $reqs $_FILES object array
	 * @param int $i number of the file
	 */
	public function handleFile ($reqs, $i)
	{

		// tmp_name, size, name and type are parsed from $_FILE object
		$this->tempPath = $reqs['file']['tmp_name'][$i]; // temp filename on server
		$this->size = $reqs['file']['size'][$i]; // file size in bytes
		$this->origin = $reqs['file']['name'][$i]; // original name on client machine
		$this->type = $reqs['file']['type'][$i]; // mime type of the file (ex. "image/jpeg")

		$updir = '/var/www/static/';
		$info = pathinfo($this->origin);
		$upext = $info['extension']; // extension is get through the temp uploaded file
		$this->extension = $upext;
		$upname = $this->generateSafeString(11); // create a safe string to name file on static folder

		try {

			/*
			 * Try to move uploaded file from tmp folder to a new static folder,
			 * if success, set other infos as path of the uploaded file
			 * on the static folder and its new created name
			 */
			if (move_uploaded_file($this->tempPath, $updir . $upname . "." . $upext)) {
				$this->name = $upname;
				$this->path = $updir . $upname . "." . $upext;
			}

		} catch (Exception $er_move) {
			echo $er_move->getMessage();
		}

		try {

			/*
			 * With path information set, check if the file is an image, if it is,
			 * get infos as width and height
			*/
			if (exif_imagetype($this->path)) {
				$dimension = getimagesize($this->path);
				$this->width = $dimension[0]; // getimagesize returns an array, 0 is width
				$this->height = $dimension[1]; // 1 is height
			} else {
				$this->width = $this->height = 0; //if not image, set both 0
			}

		} catch (Exception $er_type) {
			echo $er_type->getMessage();
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
	public function insert ($files_info, $app_id, $post_id, $conn) {

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
			echo "Erro @ fileInsert: " . $conn->error;
		}
	}

	/**
	 * create a safe string of said size and returns it
	 *
	 * Create an array of chars from other array of chars (defined),
	 *  and return implode the array ("glue" array elements like a string)
	 *
	 * @param int $sizeid size of string
	 * @return string random char-number string
	 */
	public function generateSafeString($sizeid)
	{

		$chars = "ABCDEFGHJKLMPQRSTUVWXYZ";
		$chars .= "abcdefghkmnpqrstuvwxyz";
		$chars .= "0123456789-_";

		$id = array();

		for ($i = 0; $i < $sizeid; $i++)
			$id[$i] = $chars[mt_rand(0 , strlen($chars) - 1)];

		return implode("",$id);

	}
}