<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Category;
	use Wiki\Domain\Page;
	
	/**
	 * @table categorypage
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class CategoryPage extends Domain
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
				"categorypage_id" => $this->id,
				"category" => $this->category->ID,
				"page" => $this->page->ID
			];
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->category->ID.$this->page->ID);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field category_id
		 */
		protected $category;
		
		/**
		 * @field page_id
		 */
		protected $page;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Category
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetCategory() {
			return $this->category;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetCategory(Category $value) {
			$this->category = $value;
		}
		
		# Page
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetPage() {
			return $this->page;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetPage(Page $value) {
			$this->page = $value;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "categorypage";
	}
?>