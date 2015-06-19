<?php
	function login($username, $password, $conn){
		$searchUser = $conn->prepare("SELECT * FROM users WHERE username=?");
		$searchUser->bind_param("s", $username);
		$searchUser->execute();
		$userFind = $searchUser->get_result();
		$searchUser->close();
		if($userFind->num_rows === 0){
			//user does not exist
			json('nouser', '');
		}
		else{
			//user found
			$userResult = $userFind->fetch_assoc();
			$pwresult = $userResult['password'];
			//verify password
			if(password_verify($password , $pwresult)){
				//generate session key
				$session = [redacted];
				//store in database
				$updateKey = $conn->prepare("UPDATE users SET sessionkey=? WHERE username=?");
				$updateKey->bind_param("ss", $session, $username);
				$updatestatus = $updateKey->execute();
				echo $updateKey->error;
				$updateKey->close();
				//session created, we're done here.
				if($updatestatus === false){
					json('dbfailure', '');
				}
				else{
					json('success', $session);			
				}
				
			}
			else{
				//password is incorrect
				json('wrongpass', '');
			}
		}
		
	}
	function json($status, $session){
		$json['status'] = $status;
		$json['session'] = $session;
		echo json_encode($json);
	}
?>