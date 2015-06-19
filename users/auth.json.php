<?php
	//ini_set('display_errors', 1); error_reporting(E_ALL);
	include '../config/config.php';
	include 'login.php';
	$conn = mysqli_connect($dbhost , $dbuser, $dbpass, $dbname);
	if (!$conn) {
		json('dbfailure', '');
	}
	else{
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			login($_POST['user'], $_POST['password'], $conn);
		}
		else{
			login($_GET['user'], $_GET['password'], $conn);
		}
		$conn->close();
	}
?>