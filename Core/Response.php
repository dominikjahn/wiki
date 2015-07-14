<?php
	abstract class Response
	{
		  //
		 // ATTRIBUTES
		//
		
		private $status;
		private $message;
		private $details;
		private $httpcode;
		
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct() {
			
		}
		
		  //
		 // METHODS
		//
		
		public function __toString() {
			$response = (object) ["status" => $this->status, "message" => $this->message];
			
			if($this->details) {
				$response->details = $this->details;
			}
			
			return json_encode($response);
		}
	}
?>