<?php
	class DatabaseRow
	{
		  //
		 // ATTRIBUTES
		//
		
		private $row;
		
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct($row) {
			$this->row = $row;
		}
		
		  //
		 // PROPERTIES
		//
		
		public function __get($field) {
			if(!array_key_exists($field, $this->row)) {
				throw new \Exception("No such field '".$field."' in row");
			}
			
			return new DatabaseColumn($this->row[$field]);
		}
	}
?>