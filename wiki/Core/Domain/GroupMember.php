<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Category;
	use Wiki\Domain\Page;
	
	/**
	 * @table groupmember
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GroupMember extends Domain
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
		
		public function jsonSerialize() {
			return [
				"group" => $this->group,
				"user" => $this->user
			];
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->group->ID.$this->user->ID);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field group_id
		 */
		protected $group;
		
		/**
		 * @field user_id
		 */
		protected $user;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Group
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetGroup() {
			return $this->group;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetGroup(Group $value) {
			$this->group = $value;
		}
		
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
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "groupmember";
	}
?>