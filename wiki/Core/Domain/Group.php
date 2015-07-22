<?php
	namespace Wiki\Domain;
	
	//use Wiki\Exception\NotAuthorizedToCreateNewGroupsException;
	//use Wiki\Exception\NotAuthorizedToEditGroupsException;
	
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
		
		public function Save() {
			if(!$this->ID && !self::$currentUser->HasPermission("CREATE_GROUPS")) {
				throw new NotAuthorizedToCreateNewGroupsException();
			} else if($this->ID && !self::$currentUser->HasPermission("EDIT_GROUPS")) {
				throw new NotAuthorizedToEditGroupsException();
			}
			
			return parent::Save();
		}
		
		public function Delete() {
			if(!self::$currentUser->HasPermission("DELETE_GROUPS")) {
				throw new NotAuthorizedToDeleteGroupsException();
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
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "group";
	}
?>