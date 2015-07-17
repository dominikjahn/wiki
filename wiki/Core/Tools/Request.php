<?php
	namespace Wiki\Tools;
	
	class Request
	{
	
		  //
		 // CONSTRUCTOR
		//
		
		private function __construct() {
			$this->method = $_SERVER["REQUEST_METHOD"];
			$this->body = file_get_contents("php://stdin");
			
			/*
			Content-Type"]=>
  string(33) "application/x-www-form-urlencoded"
  
    ["Content-Type"]=>
  string(24) "text/plain;charset=UTF-8"
  
  ["Content-Type"]=>
  string(68) "multipart/form-data; boundary=----WebKitFormBoundaryV5gCXfCjsAHGGapA"
  */
  
  
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