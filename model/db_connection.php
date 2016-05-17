<?php

function connect($dbname) {
	// Create connection
	$server = "localhost";
	$user = "usuario";
	$pass = "abc123ABC!@#";

	static $conn;

	if ($conn === NULL)
		//$conn = new mysqli($server, $user, $pass, $dbname);
		$conn = mysqli_connect($server, $user, $pass, $dbname);

	// Check connection
	if (!$conn) {
		die("Falha na conexão: " . $conn->connect_error . "<br>");
	}

	// Change charset to utf8
	mysqli_set_charset($conn,"utf8");

	return $conn;
}