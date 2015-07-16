<?php
	namespace Wiki\Tools;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PHPDocComment
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
		 // PROPERTIES
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __get($field) {
			switch($field) {
				case "Field": return $this->GetField(); break;
				case "Description": return $this->GetDescription(); break;
			}
		}
		
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __set($field, $value) {
			switch($field) {
				case "Field": $this->SetField($value); break;
				case "Description": $this->SetDescription($value); break;
			}
		}
		
		  //
		 // ATTRIBUTES
		//
		
		private $class;
		private $name;
		private $description;
		private $version;
		private $since;
		private $field;
		private $author;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Field
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetField() {
			return $this->field;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetField($value) {
			$this->field = $value;
		}
		
		# Description
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetDescription() {
			return $this->description;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetDescription($value) {
			$this->description = $value;
		}
		
	}
?>