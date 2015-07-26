<?php
	namespace Wiki\Domain;
	
	use Wiki\Exception\NotAuthorizedToCreateNewUsersException;
	use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Manager\UserPermissionManager;
	use Wiki\Domain\Manager\GroupMemberManager;
	use Wiki\Domain\Manager\UserManager;
	
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
		
		public function IsInGroup($group) {
			if(!($group instanceof Group)) {
				$groupManager = GroupManager::GetInstance();
				
				$groupname = $group;
				$group = $groupManager->GetByName($groupname);
				
				if(!$group || $group->Status === 0) {
					throw new GroupNotFoundException($groupname);
				}
			}
			
			return $group->HasUser($this);
		}
		
		public function AddToGroup($group) {
			if(!($group instanceof Group)) {
				$groupManager = GroupManager::GetInstance();
				
				$groupname = $group;
				$group = $groupManager->GetByName($groupname);
				
				if(!$group || $group->Status === 0) {
					throw new GroupNotFoundException($groupname);
				}
			}
			
			$groupmember = new GroupMember();
			$groupmember->Status = 100;
			$groupmember->Group = $group;
			$groupmember->User = $this;
			return $groupmember->Save();
		}
		
		public function RemoveFromGroup($group) {
			if(!($group instanceof Group)) {
				$groupManager = GroupManager::GetInstance();
				
				$groupname = $group;
				$group = $groupManager->GetByName($groupname);
				
				if(!$group || $group->Status === 0) {
					throw new GroupNotFoundException($groupname);
				}
			}
			
			$this->GetGroups();
			
			foreach($this->groups as $groupmember) {
				if($groupmember->Group->ID == $group->ID) {
					return $groupmember->Delete();
				}
			}
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
			//var_dump(preg_match("#^([a-z0-9]{3,20})$#", $this->loginname));
			// Check that the name is valid
			if(!preg_match("#^([a-z0-9]{3,20})$#", $this->loginname)) {
				throw new \Exception("The name contains characters which are not allowed for a login name. Three to twenty characters, only lower-cased letters and numbers.");
			}
			
			$duplicateLoginname = self::LoginnameTaken($this->Loginname);
			
			if($duplicateLoginname && $duplicateLoginname->ID != $this->ID) {
				throw new \Exception("The loginname is already taken");
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
			// Load permissions
			$this->GetPermissions();
			
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
			// Load permissions
			$this->GetPermissions();
			
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
		
		/**
		 * A list of groups the user is in
		 */
		protected $groups;
		
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
			if(!$this->permissions && $this->ID) {
				$this->permissions = UserPermissionManager::GetInstance()->GetByUser($this);
			}
			
			return $this->permissions;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		//protected function SetPermissions($value) {
		//	$this->permissions = $value;
		//}
		
		# Groups
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetGroups() {
			if(!$this->groups && $this->ID) {
				$this->groups = GroupMemberManager::GetInstance()->GetByUser($this);
			}
			
			return $this->groups;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		//protected function SetGroups($value) {
		//	$this->groups = $value;
		//}
		
		  //
		 // FUNCTIONS
		//
		
		public static function LoginnameTaken($name) {
			$userManager = UserManager::GetInstance();
			$user = $userManager->GetByLoginname($name);
			
			if(!$user) {
				return false;
			}
			
			return $user;
		}
		
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