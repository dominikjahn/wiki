<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class Version extends Domain
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
				"version_id" => $this->id,
				"page" => [
					"page_id" => $this->page->ID,
					"name" => $this->page->Name
					],
				"title" => $this->title,
				"content" => $this->content,
				"summary" => $this->summary,
				"minor_edit" => $this->minorEdit,
				"timestamp" => $this->logCreated->Timestamp->format("Y-m-d H:i:s"),
				"user" => $this->logCreated->User->Loginname
			];
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field page_id
		 */
		protected $page;
		
		/**
		 * @field title
		 */
		protected $title;
		
		/**
		 * @field content
		 */
		protected $content;
		
		/**
		 * @field summary
		 */
		protected $summary;
		
		/**
		 * @field minor_edit
		 */
		protected $minorEdit;
		
		  //
		 // GETTERS / SETTERS
		//
		
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
		
		# Content
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetContent() {
			return $this->content;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetContent($value) {
			$this->content = $value;
		}
		
		# Summary
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetSummary() {
			return $this->summary;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetSummary($value) {
			$this->summary = $value;
		}
		
		# MinorEdit
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetMinorEdit() {
			return $this->minorEdit;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetMinorEdit($value) {
			$this->minorEdit = $value;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "version";
	}
?>