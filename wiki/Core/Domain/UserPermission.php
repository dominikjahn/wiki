<?php
	namespace Wiki\Domain;
	
	use Wiki\Exception\AuthorizationMissingException;
	use Wiki\Exception\CannotRevokeAdminPermissionException;
	
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
				throw new AuthorizationMissingException("You are not permitted to grant permissions");
			}
			
			if($this->permission == "SUBADMIN" && !$currentUser->HasPermission("ADMIN")) {
				throw new AuthorizationMissingException("Only the admin can grant or revoke the SUBADMIN permission");
			}
			
			if($this->permission != "ADMIN") {
				return parent::Save();
			}
			
			// If the permission is "ADMIN", throw an exception! ADMIN is an implicit permission which is granted by the ID of the user
			throw new AuthorizationMissingException("You cannot grant nor revoke the implicit permission 'ADMIN'");
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Delete() {
			$currentUser = User::GetCurrentUser();
			
			if(!$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
				throw new AuthorizationMissingException("You are not permitted to revoke permissions");
			}
			
			if($this->permission == "SUBADMIN" && !$currentUser->HasPermission("ADMIN")) {
				throw new AuthorizationMissingException("Only the admin can grant or revoke the SUBADMIN permission");
			}
			
			if($this->permission != "ADMIN") {
				return parent::Delete();
			}
			
			// If the permission is "ADMIN", throw an exception! ADMIN is an implicit permission which is granted by the ID of the user
			throw new CannotRevokeAdminPermissionException("You cannot revoke the implicit permission 'ADMIN'");
		}
		
		public function jsonSerialize() {
			return [
				"user" => $this->user->loginname,
				"permission" => $this->permission
			];
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->user->ID.$this->permission);
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
		
		// List of core permissions
		const PERMISSION_CREATE_USERS = "CREATE_USERS";
		const PERMISSION_EDIT_USERS = "EDIT_USERS";
		const PERMISSION_DELETE_USERS = "DELETE_USERS";
		const PERMISSION_MANAGE_USERPERMISSIONS = "MANAGE_USERPERMISSIONS";
		
		const PERMISSION_CREATE_GROUPS = "CREATE_GROUPS";
		const PERMISSION_EDIT_GROUPS = "EDIT_GROUPS";
		const PERMISSION_DELETE_GROUPS = "DELETE_GROUPS";
		const PERMISSION_MANAGE_GROUPMEMBERS = "MANAGE_GROUPMEMBERS";
		
		const PERMISSION_CREATE_PAGES = "CREATE_PAGES";
		const PERMISSION_EDIT_PAGES = "EDIT_PAGES";
		const PERMISSION_DELETE_PAGES = "DELETE_PAGES";
		const PERMISSION_MANAGE_PAGE_VISIBILITY = "MANAGE_PAGE_VISIBILITY";
		const PERMISSION_MANAGE_PAGE_MANIPULATION = "MANAGE_PAGE_MANIPULATION";
		const PERMISSION_MANAGE_PAGE_OWNERSHIP = "MANAGE_PAGE_OWNERSHIP";
		const PERMISSION_SCRIPTING = "SCRIPTING";
		
		const PERMISSION_SUBADMIN = "SUBADMIN";
	}
?>