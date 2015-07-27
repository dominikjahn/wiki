<?php
	namespace Wiki\Domain;
	
	use Wiki\Exception\AuthorizationMissingException;
	use Wiki\Exception\GroupNameContainsIllegalCharactersException;
	use Wiki\Exception\GroupNameAlreadyTaken;
	use Wiki\Domain\Manager\GroupMemberManager;
	use Wiki\Domain\Manager\GroupManager;
	
	/**
	 * @table group
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class Group extends Domain
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
		
		public function HasUser(User $user) {
			$this->GetUsers();
			
			foreach($this->users as $groupmember) {
				if($groupmember->User->ID = $user->ID) {
					return true;
				}
			}
			
			return false;
		}
		
		public function Save() {
			$currentUser = User::GetCurrentUser();
			
			if(!$this->ID && !$currentUser->HasPermission("CREATE_GROUPS")) {
				throw new AuthorizationMissingException("You are not permitted to create groups");
			} else if($this->ID && !$currentUser->HasPermission("EDIT_GROUPS")) {
				throw new AuthorizationMissingException("You are not permitted to edit groups");
			}
			
			// Check that the name is valid
			if(!preg_match("#^([a-z0-9]{3,20})$#", $this->name)) {
				throw new GroupNameContainsIllegalCharactersException();
			}
			
			$duplicateName = self::NameTaken($this->Name);
				
			if($duplicateName && $duplicateName->ID != $this->ID) {
				throw new GroupNameAlreadyTaken();
			}
			
			return parent::Save();
		}
		
		public function Delete() {
			$currentUser = User::GetCurrentUser();
			
			if(!$currentUser->HasPermission("DELETE_GROUPS")) {
				throw new AuthorizationMissingException("You are not permitted to delete groups");
			}
			
			return parent::Delete();
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->name);
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function jsonSerialize() {
			return [
				"group_id" => $this->id,
				"name" => $this->name
			];
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field name
		 */
		protected $name;
		
		/**
		 * A list of users in this group
		 */
		protected $users;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Name
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetName() {
			return $this->name;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetName($value) {
			$this->name = $value;
		}
		
		# Users
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetUsers() {
			if(!$this->users && $this->ID) {
				$this->users = GroupMemberManager::GetInstance()->GetByGroup($this);
			}
			
			return $this->users;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		//protected function SetUsers($value) {
		//	$this->users = $value;
		//}
		
		  //
		 // FUNCTIONS
		//
		
		public static function NameTaken($name) {
			$groupManager = GroupManager::GetInstance();
			$group = $groupManager->GetByName($name);
				
			if(!$group) {
				return false;
			}
				
			return $group;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "group";
	}
?>