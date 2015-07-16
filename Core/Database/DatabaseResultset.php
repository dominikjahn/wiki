<?php
	namespace Wiki\Database;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DatabaseResultset
	{
		  //
		 // ATTRIBUTES
		//
		
		private $statement;
		
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __construct(\PDOStatement $statement) {
			$this->statement = $statement;
		}
		
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function NextRow() {
			$row = $this->statement->fetch(\PDO::FETCH_ASSOC);
			
			if(!$row) {
				return null;
			}
			
			return new DatabaseRow($row);
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Close() {
			$this->statement->closeCursor();
		}
	}
?>