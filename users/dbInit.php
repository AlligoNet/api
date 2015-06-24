<?php
	include '../config/config.php';
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$conn = mysqli_connect($dbhost , $dbuser, $dbpass, $dbname);
		if (!$conn) {
			$json['status'] = 'dbfailure';
			echo json_encode($json);
		}
		else{
			action($conn);
			$conn->close();
		}
	}
	else{
		$json['status'] = 'wrongmethod';
		echo json_encode($json);
	}
?>