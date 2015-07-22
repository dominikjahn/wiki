<?php
	namespace Wiki\Domain;
	
	use Wiki\Exception\NotAuthorizedToCreateNewUsersException;
	use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	
	/**
	 * @table user
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
			if(strtolower($this->password) == strtolower($password)) {
				return true;
			}
			
			return false;
		}
		
		public function Save() {
			if(!$this->ID && !self::$currentUser->HasPermission("CREATE_USERS")) {
				throw new NotAuthorizedToCreateNewUsersException();
			} else if($this->ID && self::$currentUser->ID != $this->ID && !self::$currentUser->HasPermission("EDIT_USER_ACCOUNTS")) {
				throw new NotAuthorizedToEditOtherUsersException();
			}
			
			if(($this->ID === 1 || $this->ID === 2) && $this->Status !== 100) {
				throw new \Exception("You cannot delete the 'guest' or 'admin' users");
			}
			
			return parent::Save();
		}
		
		public function Delete() {
			if(!self::$currentUser->HasPermission("DELETE_USERS")) {
				throw new NotAuthorizedToDeleteUsersException();
			}
			
			if($this->ID === 1 || $this->ID === 2) {
				throw new \Exception("You cannot delete the 'guest' or 'admin' users");
			}
			
			return parent::Delete();
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->loginname.$this->password);
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function HasPermission($permission) {
			// The user with the ID=2 is the admin (maybe this should be a setting)
			if($this->ID === 2) {
				return true;
			}
			
			if(is_null($this->permissions) || !count($this->permissions)) {
				return false;
			}
			
			foreach($this->permissions as $userpermission) {
				if($userpermission->Status === 100 && ($userpermission->Permission == $permission || $userpermission->Permission == "SUBADMIN")) {
					return true;
				}
			}
			
			return false;
		}
		
		public function RevokePermission($permission) {
			
			foreach($this->permissions as $userpermission) {
				if($userpermission->Permission == $permission) {
					return $userpermission->Delete();
				}
			}
			
			return false;
		}
		
		public function GrantPermission($permission) {
			
			$userpermission = new UserPermission();
			$userpermission->Status = 100;
			$userpermission->User = $this;
			$userpermission->Permission = strtoupper($permission);
			
			return $userpermission->Save();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function jsonSerialize() {
			return [
				"user_id" => $this->id,
				"loginname" => $this->loginname
			];
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
		
		/**
		 * A list of permissions that the user has
		 */
		protected $permissions;
		
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
		
		# Permissions
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetPermissions() {
			return $this->permissions;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetPermissions($value) {
			$this->permissions = $value;
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