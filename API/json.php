<?php
	class json {
		private $status;	//HTTP Status Code
		private $type;		//True = Success Message, False = Error Message
		private $message;	//Custom Message of Status code
		
		//For custom messages please just use the constructor
		function __construct($status = 200, $type = true, $message = "Success"){
			$this->status = $status;
			$this->type = $type;
			$this->message = $message;
		}
		
		private function printJson(){
			if($this->type)
				$msg = "Success";
			else
				$msg = "Error";
				
			http_response_code($this->status);
			
			echo '{ "Status" : '. $this->status . ', "'.$msg.'" : "'.$this->message.'" }';
		}
		
		//Please keep the list in number order by status #
		public function success(){
			$this->status = 200;
			$this->type = true;
			$this->message = "Success";
			
			$this->printJson();
		}
		
		public function addedSuccessfully($object){
			$this->status = 200;
			$this->type = true;
			$this->message = "Successfully " . $object;
			
			$this->printJson();
		}
		
		public function created($object){
			$this->status = 201;
			$this->type = true;
			$this->message = $object . " created";
			
			$this->printJson();
		}
		
		public function invalidRequest(){
			$this->status = 400;
			$this->type = false;
			$this->message = "Invalid request parameters";
			
			$this->printJson();
		}
		
		public function invalidRequestId(){
			$this->status = 400;
			$this->type = false;
			$this->message = "Invalid ID provided";
			
			$this->printJson();
		}	
		
		public function invalidMinimumRequirements($object){
			$this->status = 400;
			$this->type = false;
			$this->message = $object . " does not meet minimum requirements";
			
			$this->printJson();
		}
		
		public function sliderStartEnd(){
			$this->status = 400;
			$this->type = false;
			$this->message = "Start value must be lower then end value";
			
			$this->printJson();
		}
		
		public function unauthorizedAdmin(){
			$this->status = 401;
			$this->type = false;
			$this->message = "Requires administrator access";
			
			$this->printJson();
		}
		
		public function unauthorized(){
			$this->status = 401;
			$this->type = false;
			$this->message = "Requires user access";
			
			$this->printJson();
		}
		
		public function unauthorizedInvalidPassword(){
			$this->status = 401;
			$this->type = false;
			$this->message = "Invalid password provided";
			
			$this->printJson();
		}
		
		public function notFound($object){
			$this->status = 404;
			$this->type = false;
			$this->message = $object . " not found";
			
			$this->printJson();
		}
		
		public function exists($object){
			$this->status = 409;
			$this->type = false;
			$this->message = $object . " already exists";
			
			$this->printJson();
		}
		
		public function alreadyAssigned($object){
			$this->status = 409;
			$this->type = false;
			$this->message = $object . " already assigned";
			
			$this->printJson();
		}
		
		public function server(){
			$this->status = 500;
			$this->type = false;
			$this->message = "Unknown server error has occurred";
			
			$this->printJson();
		}
		
		public function notImplemented(){
			$this->status = 501;
			$this->type = false;
			$this->message = "Method not implemented";
			
			$this->printJson();
		}
	}

?>