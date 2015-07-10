<?php
	class User extends Domain
	{
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct() {
			
		}
		
		  //
		 // ATTRIBUTES
		//
		
		private $loginname;
		private $password;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Loginname
		
		protected function GetLoginname() {
			return $this->loginname;
		}
		
		protected function SetLoginname($value) {
			$this->loginname = $value;
		}
		
		# Password
		
		protected function GetPassword() {
			return $this->password;
		}
		
		protected function SetPassword($value) {
			$this->password = $value;
		}
		
		  //
		 // FUNCTIONS
		//
		
		public static function GetCurrentUser() {
			return self::$currentUser;
		}
		
		public static function SetCurrentUser(User $user) {
			self::$currentUser = $user;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $currentUser;
	}
?>