<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DatabaseStatement
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
		public function Execute($parameters = []) {
			
			foreach($parameters as $name => $value) {
				$type = \PDO::PARAM_STR;
				
				if(is_int($value)) {
					$type = \PDO::PARAM_INT;
				} else if(is_bool($value)) {
					$type = \PDO::PARAM_BOOL;
				} else if(is_null($value)) {
					$type = \PDO::PARAM_NULL;
				}
				
				$this->statement->bindValue(":".$name, $value, $type);
			}
			
			return $this->statement->execute();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Read($parameters = []) {
			
			$this->Execute($parameters);
			$resultset = new DatabaseResultset($this->statement);
			
			return $resultset;
			
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function ReadSingle($parameters = []) {
			
			$resultset = $this->Read($parameters);
			$row = $resultset->NextRow();
			
			return $row;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Close() {
			$this->statement->closeCursor();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetAffectedRows() {
			return $this->statement->rowCount();
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
			switch($field) {
				case "AffectedRows": return $this->GetAffectedRows(); break;
				default: throw new \Exception("No such field '".$field."'");
			}
		}
	}
?>