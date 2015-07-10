<?php
	class Page extends Domain
	{
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct() {
			
		}
		
		  //
		 // ATTRIBUTES
		//
		
		private $name;
		private $title;
		private $content;
		private $owner;
		private $visiblity;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# Name
		
		protected function GetName() {
			return $this->name;
		}
		
		protected function SetName($value) {
			$this->name = $value;
		}
		
		# Title
		
		protected function GetTitle() {
			return $this->title;
		}
		
		protected function SetTitle($value) {
			$this->title = $value;
		}
		
		# Content
		
		protected function GetContent() {
			return $this->content;
		}
		
		protected function SetContent($value) {
			$this->content = $value;
		}
		
		# Owner
		
		protected function GetOwner() {
			return $this->owner;
		}
		
		protected function SetOwner(User $value) {
			$this->owner = $value;
		}
		
		# Visibility
		
		protected function GetVisibility() {
			return $this->visiblity;
		}
		
		protected function SetVisibility($value) {
			$this->visiblity = $value;
		}
		
		  //
		 // FUNCTIONS
		//
		
		public static function GetCurrentPage() {
			return self::$currentPage;
		}
		
		public static function SetCurrentPage(Page $value) {
			self::$currentPage = $value;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $currentPage;
		
		  //
		 // CONSTANTS
		//
		
		const VIS_PUBLIC = "PUBLIC";
		const VIS_PROTECTED = "PROTECTED";
		const VIS_PRIVATE = "PRIVATE";
		const VIS_GROUPPRIVATE = "GROUPPRIVATE";
	}
?>