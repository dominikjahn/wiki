<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Manager\CategoryManager;
	use Wiki\Domain\Manager\CategoryPageManager;
	
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
		
		public function Save() {
			$duplicateName = self::NameTaken($this->Name);
			
			if($duplicateName && $duplicateName->ID != $this->ID) {
				throw new \Exception("The name is already taken");
			}

			return parent::Save();
		}
		
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
		
		/**
		 * A list of pages in this category
		 */
		protected $pages;
		
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
		
		# Pages
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetPages() {
			if(!$this->pages && $this->ID) {
				$this->pages = CategoryPageManager::GetInstance()->GetByCategory($this);
			}
			
			return $this->pages;
		}
		
		  //
		 // FUNCTIONS
		//
		
		  //
		 // FUNCTIONS
		//
		
		public static function NameTaken($name) {
			$catManager = CategoryManager::GetInstance();
			$category = $catManager->GetByName($name);
				
			if(!$category) {
				return false;
			}
				
			return $category;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "category";
	}
?>