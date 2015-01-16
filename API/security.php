<?php	
	function generatePassword($password){
		$cost = 10;
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		
		// Hash the password with the salt
		$hash = crypt($password, $salt);
		
		return $hash;
	}
	
	//Generates a ApiKey
	function generateApiKey($username){
		return md5(rand(0,1000).$username);
	}
	
	function verify($ApiKey){
		global $db;
		
		$verify = $db->prepare("SELECT * FROM user WHERE ApiKey = :key");
		$verify->bindParam(":key", $ApiKey);
		$verify->execute();
		
		if($verify->rowCount() == 1){
			foreach($verify as $row) {
				switch($row['Admin']){
					case 0 : return "User";
					case 1 : return "Admin";
					default : return "User";
				}
			}
		}
		else{
			return 404;
		}
	}
	
	function verifyPersonalRequest($user, $ApiKey){
		global $db;
		
		if(is_numeric($user)){
			$verify = $db->prepare("SELECT * FROM user WHERE Id = :id AND ApiKey = :key");
		}
		else{
			$verify = $db->prepare("SELECT * FROM user WHERE Username = :id AND ApiKey = :key");
		}
		
		$verify->bindParam(":id", $user);
		$verify->bindParam(":key", $ApiKey);
		$verify->execute();
		
		if($verify->rowCount() == 1){
			return true;
		}
		else{
			return false;
		}	
	}
	
	//Removes charcthers that could cause troubles
	function removeSpecialCharacthers($string){
		$invalid_characters = array("$", "%", "#", "<", ">", "|");
		return str_replace($invalid_characters, "", $string);
	}
	
	function verifyAssigned($ApiKey, $survey){
		global $db;
		
		if(verify($ApiKey) == 404){
			//If not found...
			return false;
		}
		else{
			//If found, check if assigned to survey
			$getId = $db->prepare("SELECT * FROM user WHERE ApiKey = :key");
			$getId->bindParam(":key", $ApiKey);
			$getId->execute();
			
			if($getId->rowCount() == 1){
				foreach($getId as $row){
					$userId = $row['Id'];
				}
			}
			else{
				return false;
			}
			
			$check = $db->prepare("SELECT * FROM assignedsurvey WHERE UserId = :user AND SurveyId = :survey");
			$check->bindParam(":user", $userId);
			$check->bindParam(":survey", $survey);
			$check->execute();
			if($check->rowCount() == 1){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	function verifyAdministrator($ApiKey){
		$verify = verify($ApiKey);
		if($verify != "Admin"){
			return false;
		}else{
			return true;
		}
	}
?>