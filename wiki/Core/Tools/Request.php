<?php
	namespace Wiki\Tools;
	
	use Wiki\Configuration;
	
	class Request
	{
	
		  //
		 // CONSTRUCTOR
		//
		
		private function __construct() {
			$headers = getallheaders();
			
			$this->method = $_SERVER["REQUEST_METHOD"];
			$this->body = file_get_contents("php://input");
			
			if(array_key_exists("Content-Type", $headers) && strpos($headers["Content-Type"],"application/x-www-form-urlencoded") !== false) {
				$body = [];
				parse_str($this->body, $body);
				$this->body = $body;
			}
			
			$this->useragent = $headers["User-Agent"];
			$this->accept = $headers["Accept"];
			$this->acceptlanguage = $headers["Accept-Language"];
			$this->requesturi = str_replace(Configuration::DOC_ROOT, null, $_SERVER["REQUEST_URI"]);
			
		}
		
		  //
		 // METHODS
		//
		
		  //
		 // ATTRIBUTES
		//
		
		private $method;
		private $body;
		private $useragent;
		private $accept;
		private $acceptlanguage;
		private $requesturi;
		
		  //
		 // PROPERTIES
		//
		
		public function __get($field) {
			switch($field) {
				case "Method": return $this->GetMethod(); break;
				case "Body": return $this->GetBody(); break;
				case "UserAgent": return $this->GetUserAgent(); break;
			}
		}
		
		  //
		 // GETTERS / SETTERS
		//
		
		protected function GetMethod() {
			return $this->method;
		}
		
		protected function GetBody() {
			return $this->body;
		}
		
		protected function GetUserAgent() {
			return $this->useragent;
		}
		
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