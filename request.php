<?php
	require_once("API/globals.php");
	
	/*
		q = query --> Defines what we want to retrieve from that model
		r = request --> Defines what model we are looking for
	*/
	
	if(!isset($_POST['r']) || !isset($_POST['q'])){
		http_response_code(400);
	}
	else{
		$r = $_POST['r'];
		$q = $_POST['q'];
	
		switch($r){
			case "user" : 
				require_once("API/user.php");
				
				switch($q){
					//Gets all users
					case "all" : 
						if(isset($_POST['ApiKey'])){
							if(!verifyAdministrator($_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						echo getAllUsers();break;
					//Gets user based off ID or Username
					case "user" :
						if(isset($_POST['ApiKey'])){
							if(!verifyAdministrator($_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						if(isset($_POST['id'])){
							echo getUser($_POST['id']);break;
						}
						else if(isset($_POST['username'])){
							echo getUser($_POST['username']);break;
						}
						else{
							http_response_code(400);break;
						}
					//Creates all users
					case "create" :
						if(isset($_POST['ApiKey'])){
							if(!verifyAdministrator($_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						if($_POST['username'] == "" || $_POST['password'] == "" || $_POST['admin'] == "" || $_POST['subject'] == ""){
							http_response_code(400);break;
						}
						else{
							echo createUser($_POST['username'], $_POST['password'], $_POST['admin'], $_POST['subject']);break;
						}
					//Login user
					case "login" :
						if(!isset($_POST['username']) && !isset($_POST['password'])){
							http_response_code(400);break;
						}
						else{
							echo signin($_POST['username'], $_POST['password']);break;
						}
					default : http_response_code(400);
				}
				break;
			case "survey" : 
				require_once("API/survey.php");

				switch($q){
					//Gets all surveys
					case "all" :
						if(isset($_POST['ApiKey'])){
							if(!verifyAdministrator($_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						
						echo getAllSurveys();break;
					//Creates a Survey
					case "create" :
						if(isset($_POST['ApiKey'])){
							if(!verifyAdministrator($_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						
						if(!isset($_POST['survey_name']) || $_POST['survey_name'] == ""){
							http_response_code(400);break;
						}
						else{
							if(!isset($_POST['survey_description']) || $_POST['survey_description'] == "")
								$description = "";
							else
								$description = $_POST['survey_description'];
								
							if(!isset($_POST['survey_delay']) || $_POST['survey_delay'] == "")
								$delay = "";
							else
								$delay = $_POST['survey_delay'];
								
							echo createSurvey($_POST['survey_name'], $description, $delay);break;
						}
						//Gets user based off ID or Username
					case "user" :
						if(!isset($_POST['username']) && !isset($_POST['id']))
							return http_response_code(400);
						$user = isset($_POST['username']) ? $_POST['username'] : $_POST['id'];
						
						require_once("API/security.php");
						if(isset($_POST['ApiKey'])){
							if(!verifyPersonalRequest($user, $_POST['ApiKey']))
								return http_response_code(401);
						}
						else{
							return http_response_code(401);
						}
						getAllUserSurveys($user);
						break;
						//Assigns User(s) to surveys
					case "assign" : 
						echo "Implment shit bitch";
						break;
						default : http_response_code(400);
				}
				break;
			default : http_response_code(501);
		}
	}
	
	function verifyAdministrator($ApiKey){
		require_once("API/security.php");
		$verify = verify($ApiKey);
		if($verify != "Admin"){
			return false;
		}else{
			return true;
		}
	}
	
	function verifyUser($ApiKey){
		require_once("API/security.php");
		$verify = verify($ApiKey);
		if($verify != "Admin"){
			return false;
		}else{
			return true;
		}
	}
?>

