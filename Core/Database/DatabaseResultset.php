<?php
	class DatabaseResultset
	{
		  //
		 // ATTRIBUTES
		//
		
		private $statement;
		
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct(PDOStatement $statement) {
			$this->statement = $statement;
		}
		
		  //
		 // METHODS
		//
		
		public function NextRow() {
			$row = $this->statement->fetch(\PDO::FETCH_ASSOC);
			
			if(!$row) {
				return null;
			}
			
			return new DatabaseRow($row);
		}
		
		public function Close() {
			$this->statement->closeCursor();
		}
	}
?>