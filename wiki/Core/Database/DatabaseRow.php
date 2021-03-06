<?php
	namespace Wiki\Database;
	
	use Wiki\Exception\NoSuchPropertyException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DatabaseRow
	{
		  //
		 // ATTRIBUTES
		//
		
		private $row;
		
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __construct($row) {
			$this->row = $row;
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
			if($field == "Columns") {
				return array_keys($this->row);	
			}
			
			if(!array_key_exists($field, $this->row)) {
				throw new NoSuchPropertyException($field); //\Exception("No such field '".$field."' in row (only ".print_r($this->row,1).")");
			}
			
			return new DatabaseColumn($field, $this->row[$field]);
		}
	}
?>