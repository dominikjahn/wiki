<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Manager\CategoryManager;
	
	/**
	 * @table category
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class Category extends Domain
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
				"category_id" => $this->id,
				"name" => $this->name,
				"title" => $this->title
			];
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->name.$this->title);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field name
		 */
		protected $name;
		
		/**
		 * @field title
		 */
		protected $title;
		
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
		
		# Title
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetTitle() {
			return $this->title;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetTitle($value) {
			$this->title = $value;
		}
		
		  //
		 // FUNCTIONS
		//
		
		public static function CheckForDuplicateName($name) {
			$origName = $name;
			
			$attempt = 0;
			
			$catManager = CategoryManager::GetInstance();
			
			while(true) {
				$cat = $catManager->GetByName($name);
				
				if(!$cat) {
					break;
				} else {
					$attempt++;
					
					$name = $origName."-".$attempt;
				}
			}
			
			return $name;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "category";
	}
?>