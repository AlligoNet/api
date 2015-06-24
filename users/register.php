<?php
	ini_set('display_errors', 1); error_reporting(E_ALL);
	include 'dbInit.php';
	function action($conn){
		$username = $_POST['user'];
		$password = $_POST['password']; 
		$email = $_POST['email'];
		$searchUser = $conn->prepare("SELECT * FROM users WHERE username=? OR useremail=?");
		$searchUser->bind_param("ss", $username, $useremail);
		$searchUser->execute();
		$userFind = $searchUser->get_result();
		$searchUser->close();
		if($userFind->num_rows > 0){
			//user already exists or email already taken
			$userResult = $userFind->fetch_assoc();
			if($userResult['username'] === $username){
				$json['status'] = 'userexists';
			}
			else{
				$json['status'] = 'emailexists';
			}
			echo json_encode($json);
		}
		else{
			//user and email not found, continue
			
			//check that username and email are valid
			if($username !== htmlspecialchars(stripslashes(trim($username))) || empty($username)){
				$json['status'] = 'invalidname';
				echo json_encode($json);
			}
			elseif($email !== htmlspecialchars(stripslashes(trim($email)))  || !filter_var($email, FILTER_VALIDATE_EMAIL)){
				$json['status'] = 'invalidemail';
				echo json_encode($json);
			}
			else{
				//generate password hash
				$pwhash = password_hash($password, PASSWORD_BCRYPT);
				//generate session key
				$session = md5(($username . time()));
				//insert into user and email tables
				$insertUser = $conn->prepare('INSERT INTO users (username, password, useremail, sessionkey) VALUES (?, ?, ?, ?)');
				$insertUser->bind_param("ssss", $username, $pwhash, $email, $session);
				$insertStatus = $insertUser->execute();
				echo $insertUser->error;
				echo $conn->error;
				$insertUser->close();
				 
				if($insertStatus === false){
					//something went wrong
					$json['status'] = 'dbfailure';
					echo json_encode($json);
				}
				else{
					//it worked; we're done here.
					$json['status'] = 'success';
					$json['session'] = $session;
					echo json_encode($json);	
				}
			}
		}		
	}
?>