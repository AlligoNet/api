<?php
	ini_set('display_errors', 1); error_reporting(E_ALL);
	include 'dbInit.php';
	function action($conn){
		$username = $_POST['user'];
		$password = $_POST['password']; 
		$searchUser = $conn->prepare("SELECT * FROM users WHERE username=?");
		$searchUser->bind_param("s", $username);
		$searchUser->execute();
		$userFind = $searchUser->get_result();
		$searchUser->close();
		if($userFind->num_rows === 0){
			//user does not exist
			$json['status'] = 'nouser';
			echo json_encode($json);
		}
		else{
			//user found
			$userResult = $userFind->fetch_assoc();
			$pwresult = $userResult['password'];
			//verify password
			if(password_verify($password , $pwresult)){
				//generate session key
				$session = md5(($username . time()));
				//store in database
				$updateKey = $conn->prepare("UPDATE users SET sessionkey=? WHERE username=?");
				$updateKey->bind_param("ss", $session, $username);
				$updatestatus = $updateKey->execute();
				echo $updateKey->error;
				$updateKey->close();
				//session created, we're done here.
				if($updatestatus === false){
					$json['status'] = 'dbfailure';
					echo json_encode($json);
				}
				else{
					$json['status'] = 'success';
					$json['session'] = $session;
					echo json_encode($json);	
				}
				
			}
			else{
				//password is incorrect
				$json['status'] = 'wrongpass';
				echo json_encode($json);
			}
		}
		
	}

?>