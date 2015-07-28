<?php
	namespace Wiki;
	
	abstract class Response
	{
		  //
		 // ATTRIBUTES
		//
		
		private $status;
		private $message;
		private $data;
		
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct() {
			$this->status = 500;
			$this->message = "An unknown error has occured";
			$this->data = [];
		}
		
		  //
		 // METHODS
		//
		
		public abstract function Run();
		
		public function __toString() {
			if(is_string($this->data)) {
				return $this->data;
			}
			
			$response = (object) ["status" => $this->status, "message" => $this->message];
			
			foreach($this->data as $key => $value) {
				$response->$key = $value;
			}
			
			return json_encode($response);
		}
		
		  //
		 // PROPERTIES
		//
		
		public function __get($field) {
			switch($field) {
				case "Status": return $this->GetStatus(); break;
				case "Message": return $this->GetMessage(); break;
				case "Data": return $this->GetData(); break;
			}
		}
		
		public function __set($field, $value) {
			switch($field) {
				case "Status": $this->SetStatus($value); break;
				case "Message": $this->SetMessage($value); break;
				case "Data": $this->SetData($value); break;
			}
		}
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Status
		
		private function GetStatus() {
			return $this->status;
		}
		
		private function SetStatus($value) {
			$this->status = $value;
		}
		
		# Message
		
		private function GetMessage() {
			return $this->message;
		}
		
		private function SetMessage($value) {
			$this->message = $value;
		}
		
		# Data
		
		private function GetData() {
			return $this->data;
		}
		
		private function SetData($value) {
			$this->data = $value;
		}
		
	}
?>