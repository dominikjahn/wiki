<?php
	abstract class Domain
	{
		  //
		 // PROPERTIES
		//
		
		public function __get($field) {
			$getter = "Get".$field;
			
			if(!method_exists($this, $getter)) {
				throw new \Exception("Field '".get_class($this)."->".$field."' does not exist or is write-only");
			}
			
			return static::$getter();
		}
		
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
		
		protected function GetID() {
			return $this->id;
		}
		
		protected function SetID($value) {
			$this->id = $value;
		}
		
		# Status
		
		protected function GetStatus() {
			return $this->status;
		}
		
		protected function SetStatus($value) {
			$this->status = $value;
		}
	}
?>