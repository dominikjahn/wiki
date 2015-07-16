<?php
	namespace Wiki\Domain;
	
	/**
	 * @table userpermission
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class UserPermission extends Domain
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
		
		public function jsonSerialize() {
			return [
				"user" => $this->user->loginname,
				"permission" => $this->permission
			];
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field user_id
		 */
		protected $user;
		
		/**
		 * @field permission
		 */
		protected $permission;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# User
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetUser() {
			return $this->user;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetUser(User $value) {
			$this->user = $value;
		}
		
		# Permission
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetPermission() {
			return $this->permission;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetPermission($value) {
			$this->permission = $value;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "userpermission";
	}
?>