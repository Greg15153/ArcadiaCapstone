<?php	
	require_once("globals.php");

	//USER CLASS
	class user{
		
		public $id;
		public $username;
		private $password;
		public $subject;
		public $admin;
		public $key;
		
		//Initializes User
		function __construct($id = 0, $username = null, $password = null, $subject = 0, $admin = 0, $key = null){
			require_once("security.php");
			
			$this->id = $id;
			$this->username = removeSpecialCharacthers($username);
			$this->password = $password;
			$this->subject = $subject;
			$this->admin = $admin;
			$this->key = $key;
		}
		
		//Creates user and does some preliminary value checking
		function createUser(){
			global $db;
			global $json;
			
			//Check if username meets minimum requirements
			if($this->username == "" || $this->username == null || strlen($this->username) < 6){
				$json->invalidMinimumRequirements("Username");
				return;
			}
			else{
			//Check if username already exists
				if($db->query('SELECT * FROM user WHERE Username = "'.$this->username.'"')->rowCount() > 0){
					$json->exists("Username");
					return;
				}
			}
			
			//Check if password meets minimum requirements
			if($this->password == "" || $this->password == null || strlen($this->password) < 6){
				$json->invalidMinimumRequirements("Password");
				return;
			}
			else{
				$passHash = generatePassword($this->password);
				$this->password = $passHash;
				$apiHash = generateApiKey($this->username."".date('Y-m-d'));
			}
			
			//Check if admin is empty or invalid, if so make user non-admin
			if($this->admin == "" || $this->admin == null || $this->admin < 0)
				$this->admin = 0;
			
			if($this->subject == "" || $this->subject == null || $this->subject < 0)
				$this->subject = 0;
				
			$insert = $db->prepare('INSERT INTO user VALUES (DEFAULT, :username, :password, :subject, :admin, :api)');
			$insert->bindParam(':username', $this->username);
			$insert->bindParam(':password', $this->password);
			$insert->bindParam(':subject', $this->subject);
			$insert->bindParam(':admin', $this->admin);
			$insert->bindParam(':api', $apiHash);
			
			if($insert->execute()){
				$json->created("User");
				return;
			}
			else{
				$json->server();
				return;
			}
		}
		
		//Signs in user and returns user information if valid
		function signIn(){
			global $db;
			global $json;

			include_once("security.php");
			
			if($this->username == "" || $this->password == null){
				$json->invalidRequest();
				return;
			}
			else{
				$userInfo = $db->prepare('SELECT * FROM user WHERE Username = :username');
				$userInfo->bindParam(':username', $this->username);
				$userInfo->execute();
				
				//Check if user exists
				if($userInfo->rowCount() == 0){
					$json->notFound("User");
					return;
				}
				else{
				//If exists, pull Password hash and verify against inserted password
					foreach($userInfo as $row) {
						if($row['Password'] === crypt($this->password, $row['Password'])){
							//correct username & password combination
							echo '{ "User" : { "Id" : '.$row['Id'].', "Username" : "'.$row['Username'].'", "Subject" : '.$row['Subject'].', "Admin" : '.$row['Admin'].', "ApiKey" : "'.$row['ApiKey'].'" } }';
							return;
						}
						else{
							$json->unauthorizedInvalidPassword();
							return;
						}
					}
				}
			}
		}
		
		//Gets user information by ID || username
		function getUser(){
			global $db;
			global $json;

			if($this->id != 0){
				$query = $db->prepare('SELECT * FROM user WHERE Id= :id');
				$query->bindParam(':id', $this->id);
			}
			else if($this->username != null){
				$query = $db->prepare('SELECT * FROM user WHERE Username= :id');
				$query->bindParam(':id', $this->username);
			}
			else{
				$json->invalidRequest();
				return false;
			}
			
			$query->execute();
				
			
			if($query->rowCount() > 0){
			
				foreach($query as $row) {
					$this->id = $row['Id'];
					$this->username = $row['Username'];
					$this->subject = $row['Subject'];
					$this->admin = $row['Admin'];
					$this->key = $row['ApiKey'];
					
					$result = '{ "User" : { "Id" : ' . $row['Id'] . ', "Username" : "' . $row['Username'] . '", "Subject" : '.$row['Subject'].', "Admin" : ' . $row['Admin'] . ', "ApiKey" : "' . $row['ApiKey'] . '", '; 
				
					$result = $result . "" . $this->getAssignedSurveys() . "} }";
				}
				
				return $result;
			}
			else{
				$json->notFound("User");
				return false;
			}
		}
		
		function assignSurvey($survey){
			global $db;
			global $json;
			
			//Find Survey
			$_POST['r'] = "require_once"; //We set it to this from stopping it from killing the page on requireing
			require_once("survey.php");
			$survey = new survey($survey, null, null, null);
			$getSurv = $survey->getSurvey();
			
			if($getSurv){
				
				//Check if user is already assigned this survey...
				$check = $db->prepare('SELECT * FROM assignedsurvey WHERE UserId=:user AND SurveyId=:survey');
				$check->bindParam(':user', $this->id);
				$check->bindParam(':survey', $survey->id);
				$check->execute();
				
				if($check->fetchColumn() > 0){
					$json->alreadyAssigned("Survey");
					return;
				}
				else{
					$insert = $db->prepare('INSERT INTO assignedsurvey VALUES (DEFAULT, :user, :survey)');
					$insert->bindParam(':user', $this->id);
					$insert->bindParam(':survey', $survey->id);

					if($insert->execute()){
						$json->addedSuccessfully("assigned survey to user");
						return;
					}
					else{
						$json->server();
						return;
					}
				}
			}
		}
		
		function getAssignedSurveys(){
			global $db;
			
			$query = $db->prepare('SELECT * FROM assignedsurvey WHERE UserId = :user');
			$query->bindParam(':user', $this->id);
			$query->execute();
			
			$json = ' "Surveys" : [';

			if ($query->rowCount() > 0) {
				foreach($query as $result){
					$survey = $db->prepare('SELECT * FROM survey WHERE Id = :id');
					$survey->bindParam(":id", $result['SurveyId']);
					$survey->execute();
					
					foreach($survey as $row) {
						$_POST['r'] = "require_once";
						require_once("survey.php");
											
						if($row['Delay'] == "")
							$row['Delay'] = "0";
							
						$survey = new survey($row['Id'], $row['Name'], $row['Description'], $row['Delay']);
						
						$json = $json . $survey->printSurvey() . ",";
					}
				}
				
				$json = rtrim($json, ',');
				$json = $json . " ]";
				return $json;
			} else {
			   return $json . ']';
			}
		}
	}
	
	//NON-USER CLASS FUNCTIONS
		
		//Gets all user information in entire database
		function getAllUsers(){
			global $db;
			
			$query = $db->query('SELECT * FROM user');
			$json = '{';
			

			if ($query->rowCount() > 0) {
				$json = $json . ' "Users" : [';

				foreach($query as $row) {
					$user = new user($row['Id'], null, null, 0, 0, null);
					
					$json = $json . $user->getUser() . ",";
				}
				
				$json = rtrim($json, ',');
				$json = $json . " ] }";
				

				return $json;
			} else {
			   return $json . ' "Message" : "No users found" }';
			}
		}
		
	//AJAX REQUEST HANDLING BELOW
	header('Content-type: application/json');
		
	if(!isset($_POST['r']) && ((!isset($_GET['id']) || !isset($_GET['username'])) && !isset($_GET['ApiKey'])) && (!isset($_GET['all']) || !isset($_GET['ApiKey']))){
		$json->invalidRequest();
		die();
	}
	else{
		require_once("security.php");
				
		//Set request / Clean it
		if(isset($_GET['id'])){
			if(is_numeric($_GET['id'])){	
				if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
					//Check if User is getting their own info...
					if(!verifyPersonalRequest($_GET['id'], removeSpecialCharacthers($_GET['ApiKey']))){
						$json->unauthorized();
						return;
					}
				}

				//Return specific user based off id
				$user = new user($_GET['id'], null, null, 0, 0, null);
				echo $user->getUser();
				return;
			}
			else{
				$json->invalidRequestId();
				return;
			}
		}
		else if(isset($_GET['username'])){
			if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
					//Check if User is getting their own info...
					if(!verifyPersonalRequest($_GET['username'], removeSpecialCharacthers($_GET['ApiKey']))){
						$json->unauthorized();
						return;
					}
				}
			$r = removeSpecialCharacthers($_GET['username']);
			$user= new user(0, removeSpecialCharacthers($_GET['username']), null, null, 0, 0, null);
			echo $user->getUser();
			return;
		}
		else if(isset($_GET['all'])){
			if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
				$json->unauthorizedAdmin();
				return;
			}
			
			echo getAllUsers();
		}
		else if(isset($_POST['r'])){
			$r = removeSpecialCharacthers($_POST['r']);
				
			//Handle Request
			switch($r){
				case "create" :	//Creates a user
					if(isset($_POST['ApiKey']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['admin']) && is_numeric($_POST['admin']) && isset($_POST['subject']) && is_numeric($_POST['subject'])){
						if(!verifyAdministrator($_POST['ApiKey'])){
							$json->unauthorizedAdmin();
							break;
						}
					}
					else{
						$json->invalidRequest();
						break;
					}
				
					$user = new user(0, removeSpecialCharacthers($_POST['username']), $_POST['password'], $_POST['admin'], $_POST['subject'], null);
					$user->createUser();
					break;
				/* END CREATE USER */
				case "login" :	//Logs a user in
					if(!isset($_POST['username']) && !isset($_POST['password'])){
						$json->invalidRequest();
						break;
					}
					else{
						$user = new user(null, removeSpecialCharacthers($_POST['username']), $_POST['password'], 0, 0, null);
						echo $user->signIn();
					}
					break;
				case "assign" : //Assigns survey to user
					if(!isset($_GET['user']) || !is_numeric($_GET['user']) || !isset($_POST['survey']) || !is_numeric($_POST['survey'])){
						$json->invalidRequest();
						break;
					}
					else{
						if(!verifyAdministrator($_POST['ApiKey'])){
							$json->unauthorizedAdmin();
							break;
						}
						else{
							//ASSIGN HERE
							$user = new user($_GET['user'], null, null, 0, 0, null);
							$err = $user->getUser();
							
							if($err){
								ob_end_clean();
								$user->assignSurvey($_POST['survey']);
							}
						
							//$user->assignSurvey($_POST['survey']);
						}
					}
					break;
				/* END LOGIN USER */
				default:
					$json->notImplemented();
					break;
			}
		}
		die();
	}
	
?>