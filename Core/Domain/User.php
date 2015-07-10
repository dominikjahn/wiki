<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class User extends Domain
	{
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __construct() {
			
		}
		
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function MatchPassword($password) {
			if($this->password == $password) {
				return true;
			}
			
			return false;
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field loginname
		 */
		protected $loginname;
		
		/**
		 * @field password
		 */
		protected $password;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Loginname
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetLoginname() {
			return $this->loginname;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetLoginname($value) {
			$this->loginname = $value;
		}
		
		# Password
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetPassword() {
			return $this->password;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetPassword($value) {
			$this->password = $value;
		}
		
		  //
		 // FUNCTIONS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public static function GetCurrentUser() {
			return self::$currentUser;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public static function SetCurrentUser(User $user) {
			self::$currentUser = $user;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $currentUser;
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "user";
	}
?>