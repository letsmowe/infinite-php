<?php

include 'model/Persistence.class.php';
require_once ('var/connection.php');

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

if (isset($_SERVER['HTTP_ORIGIN'])) {
	header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Credentials: true');
	header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
		header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

	exit(0);
}

try {

	if ($_GET['callback']) {

		echo $_GET['callback'] . '(' . json_encode($_GET) . ')'; //jsonp only get requests

	} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
		//if json_decode get true parameter, it creates an associative array
		$dados = json_decode($_POST['app'], true);
		
		$c = new Connection();
		$manipulation = new Persistence($dados, $c->getConnection());

		echo $manipulation->toJSON();

	} else {

		// this else may be == GET

	}

} catch (Exception $e) {
	echo "Erro: " . $e->getMessage();
}