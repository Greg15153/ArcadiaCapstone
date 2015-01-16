<?php	
	require_once("globals.php");

	//USER CLASS
	class question{
		
		public $id;
		public $type;
		public $question;
		
		//Initializes User
		function __construct($id = 0, $type = null, $question = null){
			require_once("security.php");
			
			$this->id = $id;
			$this->type = removeSpecialCharacthers($type);
			$this->question = removeSpecialCharacthers($question);
		}
		
		//Creates question and does some preliminary value checking
		function createQuestion(){
			global $db;
			global $json;
						
			//Check if type meets minimum requirements
			if($this->type == "" || $this->type == null){
				$json->invalidMinimumRequirements("Type");
				return;
			}
			else{
				//Checks if type meets our supported types
				switch($this->type){
					case "multipleChoice":break;
					case "slider":break;
					default : $json->invalidRequest(); return;
				}
			}
			
			//Check if question meets minimum requirements
			if($this->question == "" || $this->question == null || strlen($this->question) < 6 || strlen($this->question) > 500){
				$json->invalidMinimumRequirements("Question");
				return false;
			}
				
			$insert = $db->prepare('INSERT INTO question VALUES (DEFAULT, :type, :question)');
			$insert->bindParam(':type', $this->type);
			$insert->bindParam(':question', $this->question);
			
			if($insert->execute()){
				$json->created("Question");
				return true;
			}
			else{
				$json->server();
				return false;
			}
		}		
		
		//Gets question information by ID
		function getQuestion(){
			global $db;
			global $json;

			if($this->id != 0){
				$query = $db->prepare('SELECT * FROM question WHERE Id= :id');
				$query->bindParam(':id', $this->id);
			}
			else{
				$json->invalidRequest();
				return false;
			}
			
			$query->execute();
				
			
			if($query->rowCount() > 0){
			
				foreach($query as $row) {
					$this->id = $row['Id'];
					$this->type = $row['Type'];
					$this->question = $row['Question'];
					
					$result = '{ "Question" : { "Id" : ' . $row['Id'] . ', "Type" : "' . $row['Type'] . '", "Question" : "'.$row['Question'].'"';

					switch($row['Type']){
						case "multipleChoice": 
							//Get choices...
							$choices = new multipleChoice($row['Id'], $row['Type'], $row['Question'], null);
							
							$result = $result . "," . $choices->getChoices();
							break;
						case "slider":
							$slider = new slider($row['Id'], $row['Type'], $row['Question'], null, null);
							$slider->getSliderOptions();
							
							$result = $result . ', "Start" : ' . $slider->start . ', "End" : ' . $slider->end;
							break;
					}
				}
				
				return $result . " } }";
			}
			else{
				$json->notFound("Question");
				return false;
			}
		}
		
		//Assign question to survey
		function assign($surveyId, $order, $page){
			global $db;
			global $json;
			
			//Question already checked, Make sure survey exists
			$_POST['r'] = "require_once";
			require_once("survey.php");
			
			$survey = new survey($surveyId, null, null, null);
			$checkSurvey = $survey->getSurvey();
			
			//Make sure survey exists...
			if(!$checkSurvey){
				echo $checkSurvey;
				return;
			}
			
			//If exists, make sure it doesn't already have this question assigned
			$survey_json = json_decode($checkSurvey);
			$questionCheck = $survey_json->Survey->Questions;
			
			if($questionCheck > 0){
				//Check the questions
				foreach($questionCheck as $question){
					if($question->Question->Id == $this->id){
						$json->alreadyAssigned("Question");
						return;
					}
				}
			}
			
			//Assign the question to the survey
			$insert = $db->prepare('INSERT INTO assignedquestion VALUES (DEFAULT, :survey, :question, :page, :order)');
			$insert->bindParam(':survey', $surveyId);
			$insert->bindParam(':question', $this->id);
			$insert->bindParam(':page', $page);
			$insert->bindParam(':order', $order);
			
			if($insert->execute()){
				$json->addedSuccessfully("assigned question to survey");
				return;
			}
			else{
				$json->server();
				return;
			}
		}
	}
	
	//Multiple Choice Question
	class multipleChoice extends question {
		//Multiple choice requires: # of options, Order, THIS WILL BE THE LAYER TO PUT IN ALL OPTIONS?
		
		public $id;
		public $choices;
		
		function __construct($id = 0, $type = "multipleChoice", $question = null, $choices = null){
			require_once("security.php");
		
			$this->id = $id;
			$this->type = $type;
			$this->question = $question;
			$this->choices = $choices;
		}
		
		function addChoices(){
			global $db;
			
			$createQuestion = $this->createQuestion(); //Create question to get ID of question...
			
			if(!$createQuestion){
				echo $createQuestion;
				return false;
			}
			
			$questionId = $db->lastInsertId();
			$count = 0;
			
			foreach($this->choices as $choice){
				//Clean each choice, Add to database linking to specific question. Mark order as order passed in.				
				$insert = $db->prepare('INSERT INTO questionoptions (QuestionId, Choice, `Order`) VALUES (:id, :choice, :order)');
				$insert->bindParam(':id', $questionId);
				$insert->bindParam(':choice', removeSpecialCharacthers($choice));
				$insert->bindParam(':order', $count);
				
				$insert->execute();
				$count++;
			}
			
		}
		
		function getChoices(){
			global $db; 
			
			$getChoices = $db->prepare("SELECT * FROM questionoptions WHERE QuestionId = :id ORDER BY 'Order'");
			$getChoices->bindParam(':id', $this->id);
			$getChoices->execute();
			
			$result = '"Choices" : [';

			if($getChoices->rowCount() > 0){
				
				$this->choices = array();
				
				foreach($getChoices as $row) {
					array_push($this->choices, $row['Choice']);

					$result = $result . '"'.$row['Choice'].'",';
				}
				$result = rtrim($result, ',');
				return $result . "]";
			}
			else{
				return $result . ']';
			}
		}
		
	}
	
	
	class slider extends question {
		//Slider requires: Start, End
		
		public $id;
		public $start;
		public $end;
		
		function __construct($id = 0, $type = "slider", $question = null, $start = 0, $end = 100){
			require_once("security.php");
		
			$this->id = $id;
			$this->type = $type;
			$this->question = $question;
			$this->start = $start;
			$this->end = $end;
		}
		
		function addSlider(){
			global $db;
			global $json;
			
			if($this->start > $this->end){
				$json->sliderStartEnd();
				return;
			}
			
			$createQuestion = $this->createQuestion(); //Create question to get ID of question...
			
			if(!$createQuestion){
				echo $createQuestion;
				return false;
			}
			
			$questionId = $db->lastInsertId();
			
			$insertStart = $db->prepare('INSERT INTO questionoptions (QuestionId, Start) VALUES (:id, :start)');
			$insertStart->bindParam(':id', $questionId);
			$insertStart->bindParam(':start', $this->start);
			$insertStart->execute();
			
			$insertEnd = $db->prepare('INSERT INTO questionoptions (QuestionId, End) VALUES (:id, :end)');
			$insertEnd->bindParam(':id', $questionId);
			$insertEnd->bindParam(':end', $this->end);
			$insertEnd->execute();
		}
		
		function getSliderOptions(){
			global $db; 
			
			$getStart = $db->prepare("SELECT * FROM questionoptions WHERE QuestionId = :id AND Start != \"\" ");
			$getStart->bindParam(':id', $this->id);
			$getStart->execute();
			
			foreach($getStart as $row) {
				$start = $row['Start'];
			}
			
			$getEnd = $db->prepare("SELECT * FROM questionoptions WHERE QuestionId = :id AND End != \"\"");
			$getEnd->bindParam(':id', $this->id);
			$getEnd->execute();
			
			foreach($getEnd as $row) {
				$end = $row['End'];
			}
			
			$this->start = $start;
			$this->end = $end;
		}
	}
		
	//NON-QUESTION CLASS FUNCTIONS
		
		//Gets all user information in entire database
		function getAllQuestions(){
			global $db;
			
			$query = $db->query('SELECT * FROM question');
			$json = '{';
			

			if ($query->rowCount() > 0) {
				$json = $json . ' "Questions" : [';

				foreach($query as $row) {
					$question = new question($row['Id'], null, null);
					
					$json = $json . $question->getQuestion() . ",";
				}
				
				$json = rtrim($json, ',');
				$json = $json . " ] }";
				

				return $json;
			} else {
			   return $json . ' "Message" : "No questions found" }';
			}
		}
	//AJAX REQUEST HANDLING BELOW
	header('Content-type: application/json');
	
	if(!isset($_POST['r']) && (!isset($_GET['all']) || !isset($_GET['ApiKey'])) && (!isset($_GET['id']) || !isset($_GET['ApiKey']))){
		$json->invalidRequest();
		die();
	}
	else{
		require_once("security.php");
		
		if(isset($_POST['r']) && $_POST['r'] == "require_once")
			return;
			
		if(isset($_GET['all'])){
			//Get all questions
			if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
				$json->unauthorizedAdmin();
				return;
			}
			
			echo getAllQuestions();
		}
		else if(isset($_GET['id'])){
			//Get question by ID
			if(is_numeric($_GET['id'])){
				if(!verifyAdministrator(removeSpecialCharacthers($_GET['ApiKey']))){
					$json->unauthorizedAdmin();
					return;
				}
				
				$question = new question($_GET['id'], null, null);
				echo $question->getQuestion();
			}
			else{
				$json->invalidRequestId();
				return;
			}
		}
		//Set request / Clean it
		else if(isset($_POST['r'])){
			$r = removeSpecialCharacthers($_POST['r']);
				
			//Handle Request
			switch($r){
				case "create" :	//Creates a quest
					if(isset($_POST['ApiKey']) && isset($_POST['type']) && isset($_POST['question'])){
						if(!verifyAdministrator($_POST['ApiKey'])){
							$json->unauthorizedAdmin();
							break;
						}
					}
					else{
						$json->invalidRequest();
						break;
					}
					
					//Create question... Get Type -> Determine if it has everything it needs, Creat create create
					$type = removeSpecialCharacthers($_POST['type']);
					switch($type){
						case "multipleChoice" :
							//If type is Multiple Choice... Make sure it meets all guidelines...
							
							//Check if choices were sent...and as array
							if(!isset($_POST['choices']) || !is_array($_POST['choices'])){
								$json->invalidMinimumRequirements("Choices"); 
								die();
							}

							//GENERATE QUESTION, GET ID...
							$multipleChoice = new multipleChoice(null, $type, removeSpecialCharacthers($_POST['question']), $_POST['choices']);
							$multipleChoice->addChoices();

							break;
						
						case "slider" :
							if(!isset($_POST['start']) || !is_numeric($_POST['start']) || !isset($_POST['end']) || !is_numeric($_POST['end'])){
								$json->invalidMinimumRequirements("Start and End");
								die();
							}
							
							$slider = new slider(null, $type, removeSpecialCharacthers($_POST['question']), $_POST['start'], $_POST['end']);
							$slider->addSlider();

							break;
						default : $json->invalidMinimumRequirements("Type"); die();
					}
					break;
				/* END CREATE QUESTION */
				/* START ASSIGN QUESTION */
				case "assign" :
					if(isset($_POST['ApiKey']) && isset($_POST['survey']) && is_numeric($_POST['survey']) && isset($_POST['question']) && is_numeric($_POST['question'])){
						if(!verifyAdministrator($_POST['ApiKey'])){
							$json->unauthorizedAdmin();
							break;
						}
						
						$order = 0;
						$page = 0;
						
						//check if Order and Page are set and make sure they are numbers
						if(isset($_POST['order']) && is_numeric($_POST['order'])){
							$order = $_POST['order'];
						}
						
						if(isset($_POST['order']) && is_numeric($_POST['order'])){
							$page = $_POST['page'];
						}
						
						//Get question to make sure it exists
						$question = new question($_POST['question'], null, null);
						$getQuestion = $question->getQuestion();		
						
						//If question not found, report it and die
						if(!$getQuestion){
							echo $getQuestion;
							die();
						}
						
						//Perform assigning procedure
						$question->assign($_POST['survey'], $order, $page);
					}
					else{
						$json->invalidRequest();
						break;
					}
					break;
				/* END ASSIGN QUESTION */
				default:
					$json->notImplemented();
					break;
			}
		}
		die();
	}
	