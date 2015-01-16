<?php	
	require_once("globals.php");

	class survey{
		
		public $id;
		public $name;
		public $description;
		public $delay;
		
		function __construct($id = 0, $name = null, $description = null, $delay = null){
			require_once("security.php");
			
			$this->id = $id;
			$this->name = removeSpecialCharacthers($name);
			$this->description = removeSpecialCharacthers($description);
			$this->delay = $delay;
		}
		
		function printSurvey(){
			return '{ "Survey" : { "Id" : '.$this->id.', "Name" : "'.$this->name.'", "Description" : "'.$this->description.'", "Delay" : "'.$this->delay.'", ' . $this->getQuestions() . '} }';				
		}
		//Creates user and does some preliminary value checking
		function createSurvey(){
			global $db;
			global $json;

			//Check if Survey name meets minimum requirements
			if($this->name == "" || $this->name == null || strlen($this->name) < 6){
				$json->invalidMinimumRequirements("Survey Name");
				return;
			}
			else{
			//Check if survey name already exists
				if($db->query('SELECT * FROM survey WHERE Name = "'.$this->name.'"')->rowCount() > 0){
					$json->exists("Survey Name");
					return;
				}
			}
			
			if($this->delay == "" || !is_numeric($this->delay))
				$this->delay = null;
			if($this->delay == "")
				$this->delay = null;
				
			$insert = $db->prepare('INSERT INTO survey VALUES (DEFAULT, :name, :description, :delay)');
			$insert->bindParam(':name', $this->name);
			$insert->bindParam(':description', $this->description);
			$insert->bindParam(':delay', $this->delay);

			if($insert->execute()){
				$json->created("Survey");
				return;
			}
			else{
				$json->server();
				return;
			}
		}
		
		function getSurvey(){
			global $db;
			global $json;
			
			if($this->id != 0){
				$query = $db->prepare('SELECT * FROM survey WHERE Id= :id');
				$query->bindParam(':id', $this->id);
			}
			else if($this->name != null){
				$query = $db->prepare('SELECT * FROM survey WHERE Name= :id');
				$query->bindParam(':id', $this->name);
			}
			else{
				$json->invalidRequest();
				return false;
			}

			//Gets Survey info
			$query->execute();
				
			if($query->rowCount() > 0){
				
				foreach($query as $row) {
					$result = '{ "Survey" : { "Id" : '.$row['Id'].', "Name" : "'.$row['Name'].'", "Description" : "'.$row['Description'].'", "Delay" : "'.$row['Delay'].'", ' . $this->getQuestions() . '} }';				
				}
					
				return $result;
			}
			else{
				$json->notFound("Survey");
				return false;
			}
		}
		
		function getQuestions(){
			global $db;
			
			$_POST['r'] = "require_once";
			require_once("question.php");
			
			$getQuestions = $db->prepare("SELECT * from assignedquestion WHERE SurveyId = :id ORDER BY OrderNum");
			$getQuestions->bindParam(':id', $this->id);
			$getQuestions->execute();
			
			$result = '"Questions" : [';
			if($getQuestions->rowCount() > 0){
				
				foreach($getQuestions as $row){
					$question = new question($row['QuestionId'], null, null);
					$questionRes = $question->getQuestion();
					
					$questionRes = rtrim($questionRes, '} }');
					
					$questionRes = $questionRes . ', "Page" : '. $row['PageNum'] . '} }';
					$result = $result . $questionRes . ',';
				}
				
				$result = rtrim($result, ',');
				return $result . "]";
			}
			else{
				return $result . ']';
			}
		}
	}
	
	//NON-USER CLASS FUNCTIONS

	//Gets ALL surveyys
	function getAllSurveys(){
		global $db;
		
		$query = $db->query('SELECT * FROM survey');
		$json = '{';
		

		if ($query->rowCount() > 0) {
			$json = $json . ' "Surveys" : [';

		  	foreach($query as $row) {
				if($row['Delay'] == "")
					$row['Delay'] = "0";
				
				$survey = new survey($row['Id'], $row['Name'], $row['Description'], $row['Delay']);
				
				$json = $json . $survey->printSurvey() . ",";
				
				//$json = $json . '{ "Id" : ' . $row['Id'] . ', "Name" : "' . $row['Name'] . '", "Description" : "'.$row['Description'].'", "Delay" : ' . $row['Delay'] . ', ';
			}
			
			$json = rtrim($json, ',');
			$json = $json . " ] }";
			

			return $json;
		} else {
		   return $json . ' "Message" : "No surveys found" }';
		}
	}
	
	function getAllUserSurveys($user){
		//Do this later, need to be able to assign surveys first...DUMBASS....
		echo "You're dumb";
	}
	
	//AJAX REQUEST HANDLING BELOW
	header('Content-type: application/json');
	
	if(!isset($_POST['r']) && ((!isset($_GET['id']) || !isset($_GET['username'])) && !isset($_GET['ApiKey'])) && (!isset($_GET['all']) || !isset($_GET['ApiKey']))){
		$json->invalidRequest();
		die();
	}
	else{
		require_once("security.php");
		
		if(isset($_POST['r']) && $_POST['r'] == "require_once")
			return;
		
		//Set request / Clean it
		if(isset($_GET['id'])){
			if(is_numeric($_GET['id'])){	
				if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey'])) || !verifyAssigned(removeSpecialCharacthers($_GET['ApiKey']), $_GET['id'])){
					$json->unauthorizedAdmin();
					return;
				}
				//Return specific user based off id
				$survey = new survey($_GET['id'], null, null, null);
				echo $survey->getSurvey();
				return;
			}
			else{
				$json->invalidRequestId();
				return;
			}
		}
		else if(isset($_GET['name'])){
			if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
				$json->unauthorizedAdmin();
				return;
			}

			$r = removeSpecialCharacthers($_GET['name']);
			$survey = new survey(0, $r, null, null);
			echo $survey->getSurvey();
			return;
		}
		else if(isset($_GET['all'])){
			if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
				$json->unauthorizedAdmin();
				return;
			}
			
			echo getAllSurveys();
			return;
		}
		else if(isset($_POST['r'])){
			$r = removeSpecialCharacthers($_POST['r']);
			
			//Handle Request
			switch($r){
				case "create" :
					if(!isset($_POST['name'])){
						$json->invalidRequest();
						break;;
					}
					else{
						if(!verifyAdministrator(removeSpecialCharacthers($_POST['ApiKey']))){
							$json->unauthorizedAdmin();
							break;
						}
						
						$survey = new survey(0, removeSpecialCharacthers($_POST['name']), removeSpecialCharacthers($_POST['description']), $_POST['delay']);
						echo $survey->createSurvey();
					}
					break;
				case "require_once":
					break;
				/* END OF CREATE */
				default:
					$json->notImplemented();
					break;
			}
		}
		if($r != "require_once")
			die();
	}
?>