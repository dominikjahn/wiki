<?php
	namespace Wiki\Domain;
	
	use Wiki\Exception\NotAuthorizedToCreateOrEditGroupsException;
	use Wiki\Domain\Manager\GroupMemberManager;
	
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
			return false;
		}
		
		public function Save() {
			$currentUser = User::GetCurrentUser();
			
			if(!$this->ID && !$currentUser->HasPermission("CREATE_GROUPS")) {
				throw new NotAuthorizedToCreateOrEditGroupsException();
			} else if($this->ID && $currentUser->HasPermission("EDIT_GROUPS")) {
				throw new NotAuthorizedToCreateOrEditGroupsException();
			}
			
			return parent::Save();
		}
		
		public function Delete() {
			$currentUser = User::GetCurrentUser();
			
			if(!$currentUser->HasPermission("DELETE_GROUPS")) {
				throw new NotAuthorizedToCreateOrEditGroupsException();
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
		 // CONSTANTS
		//
		
		const DB_TABLE = "group";
	}
?>