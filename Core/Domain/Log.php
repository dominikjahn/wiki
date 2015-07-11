<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class Log extends Domain
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
				"user" => $this->user,
				"type" => $this->type,
				"timestamp" => $this->timestamp
			];
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field object_table
		 */
		protected $objectTable;
		
		/**
		 * @field object_id
		 */
		protected $object;
		
		/**
		 * @field user_id
		 */
		protected $user;
		
		/**
		 * @field type
		 */
		protected $type;
		
		/**
		 * @field timestamp
		 */
		protected $timestamp;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# ObjectTable
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetObjectTable() {
			return $this->objectTable;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetObjectTable($value) {
			$this->objectTable = $value;
		}
		
		# Object
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetObject() {
			return $this->{"object"};
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetObject($value) {
			$this->{"object"} = $value;
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
		
		# Type
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetType() {
			return $this->type;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetType($value) {
			$this->type = $value;
		}
		
		# Timestamp
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetTimestamp() {
			return $this->timestamp;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetTimestamp(\DateTime $value) {
			$this->timestamp = $value;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "log";
		
		const TYPE_CREATE = "CREATE";
		const TYPE_MODIFY = "MODIFY";
		const TYPE_DELETE = "DELETE";
	}
?>