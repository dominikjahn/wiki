<?php
	namespace Wiki\Tools;
	
	class Request
	{
	
		  //
		 // CONSTRUCTOR
		//
		
		private function __construct() {
			$headers = getallheaders();
			
			$this->method = $_SERVER["REQUEST_METHOD"];
			$this->body = file_get_contents("php://stdin");
			
			if($headers["Content-Type"] == "application/x-www-form-urlencoded") {
				$body = [];
				parse_str($this->body, $body);
				$this->body = $body;
			}
  
		}
		
		  //
		 // METHODS
		//
		
		  //
		 // ATTRIBUTES
		//
		
		private $method;
		private $body;
		
		  //
		 // FUNCTIONS
		//
		
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new Request;
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>