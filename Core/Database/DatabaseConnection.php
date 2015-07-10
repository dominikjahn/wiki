<?php
	class DatabaseConnection
	{
		  //
		 // ATTRIBUTES
		//
		
		private $connection;
		
		  //
		 // CONSTRUCTOR
		//
		
		private function __construct() {
			switch(Configuration::DATABASE_DRIVER) {
				case "MySQL":
					$this->connection = new \PDO("mysql:host=".Configuration::DATABASE_HOST.";dbname=".Configuration::DATABASE_NAME.";charset=".Configuration::DATABASE_CHARSET, Configuration::DATABASE_USER, Configuration::DATABASE_PASSWORD);
					break;
					
				default:
					throw new \Exception("Unsupported database driver");
			}
			
			$this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		}
		
		  //
		 // METHODS
		//
		
		public function Prepare($query) {
			
			$statement = $this->connection->prepare($query);
			return new DatabaseStatement($statement);
			
		}
		
		/*
		 * Shortcuts to skip $db->Prepare($query); $db->Execute/Read/ReadSingle($parameters)
		 */
		
		public function PrepareAndExecute($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->Execute($parameters);
		}
		
		public function PrepareAndRead($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->Read($parameters);
		}
		
		public function PrepareAndReadSingle($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->ReadSingle($parameters);
		}
		
		/*
		 * Transaction management
		 */
		
		public function BeginTransaction() {
			$this->connection->beginTransaction();
		}
		
		public function Commit() {
			$this->connection->commit();
		}
		
		public function Rollback() {
			$this->connection->rollBack();
		}
		
		/*
		 * Disconnect
		 */
		
		public function Close() {
			$this->connection = null;
			
			/*
			 * Setting instance to NULL makes sure that if the connection is still in use it can be opened up again
			 */
			self::$instance = null;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
		
		  //
		 // FUNCTIONS
		//
		
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new DatabaseConnection;
			}
			
			return self::$instance;
		}
	}
?>