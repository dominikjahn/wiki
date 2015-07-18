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
		
		public function Save() {
			$currentUser = User::GetCurrentUser();
			
			if(!$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
				throw new \Exception("You are not permitted to grant permissions");
			}
			
			if($this->permission == "SUBADMIN" && !$currentUser->HasPermission("ADMIN")) {
				throw new \Exception("Only the admin can grant or revoke the SUBADMIN permission");
			}
			
			if($this->permission != "ADMIN") {
				return parent::Save();
			}
			
			// If the permission is "ADMIN", throw an exception! ADMIN is an implicit permission which is granted by the ID of the user
			throw new \Exception("You cannot grant nor revoke the implicit permission 'ADMIN'");
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Delete() {
			$currentUser = User::GetCurrentUser();
			
			if(!$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
				throw new \Exception("You are not permitted to revoke permissions");
			}
			
			if($this->permission == "SUBADMIN" && !$currentUser->HasPermission("ADMIN")) {
				throw new \Exception("Only the admin can grant or revoke the SUBADMIN permission");
			}
			
			if($this->permission != "ADMIN") {
				return parent::Delete();
			}
			
			// If the permission is "ADMIN", throw an exception! ADMIN is an implicit permission which is granted by the ID of the user
			throw new \Exception("You cannot revoke the implicit permission 'ADMIN'");
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
			$this->permission = strtoupper($value);
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "userpermission";
	}
?>