<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	abstract class Domain
	{
		  //
		 // PROPERTIES
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __get($field) {
			$getter = "Get".$field;
			
			if(!method_exists($this, $getter)) {
				throw new \Exception("Field '".get_class($this)."->".$field."' does not exist or is write-only");
			}
			
			return static::$getter();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __set($field, $value) {
			$setter = "Set".$field;
			
			if(!method_exists($this, $setter)) {
				throw new \Exception("Field '".get_class($this)."->".$field."' does not exist or is read-only");
			}
			
			return static::$setter($value);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * The id of the object
		 */
		private $id;
		
		/**
		 * The status of the object (0 - 100)
		 */
		private $status;
		
		  //
		 // GETTERS/SETTERS
		//
		
		# ID
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetID() {
			return $this->id;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetID($value) {
			$this->id = $value;
		}
		
		# Status
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetStatus() {
			return $this->status;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetStatus($value) {
			$this->status = $value;
		}
	}
?>