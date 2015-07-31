<?php
	namespace Wiki\Domain;
	
	/**
	 * @table categorypage
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PageLink extends Domain
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
				"from" => $this->from,
				"to" => $this->to,
				"text" => $this->text
			];
		}
		
		protected function CalculateChecksum() {
			return md5($this->Status.$this->from->ID.$this->to->ID.$this->text);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * @field page_from_id
		 */
		protected $from;
		
		/**
		 * @field page_to_id
		 */
		protected $to;
		
		/**
		 * @field text
		 */
		protected $text;
		
		  //
		 // GETTERS / SETTERS
		//
		
		# From
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetFrom() {
			return $this->from;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetFrom(Page $value) {
			$this->from = $value;
		}
		
		# To
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetTo() {
			return $this->to;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetTo(Page $value) {
			$this->to = $value;
		}
		
		# Text
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetText() {
			return $this->text;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetText($value) {
			$this->text = $value;
		}
		
		  //
		 // CONSTANTS
		//
		
		const DB_TABLE = "pagelink";
	}
?>