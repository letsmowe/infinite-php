<?php
/**
 * Magma Scientific Connection Server v1.5.0 (http://getvilla.org/)
 * Copyright 2014-2015 Magma Fantastico
 * Licensed under MIT (https://github.com/noibe/villa/blob/master/LICENSE)
 */

Class Connection
{
	public $connection;
	public $msg;

	public function __construct() {
		$HOSTNAME = "localhost";
		$USER = "usuario";
		$PASSWD = "abc123ABC!@#";
		$SCHEMA = "infinite-php";

		$conn = new mysqli($HOSTNAME, $USER, $PASSWD, $SCHEMA);

		if (!$conn->connect_errno)
			$this->connection = $conn;
		else {
			echo "Failed to connect to MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
		}
	}

	public function getConnection() {
		$connec = $this->connection;
		mysqli_set_charset($connec,"utf8");
		
		return $connec;
	}
}