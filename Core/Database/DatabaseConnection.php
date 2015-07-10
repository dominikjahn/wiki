<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DatabaseConnection
	{
		  //
		 // ATTRIBUTES
		//
		
		private $connection;
		
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
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
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Prepare($query) {
			
			$statement = $this->connection->prepare($query);
			return new DatabaseStatement($statement);
			
		}
		
		/*
		 * Shortcuts to skip $db->Prepare($query); $db->Execute/Read/ReadSingle($parameters)
		 */
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function PrepareAndExecute($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->Execute($parameters);
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function PrepareAndRead($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->Read($parameters);
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function PrepareAndReadSingle($query, $parameters = []) {
			$statement = $this->Prepare($query);
			return $statement->ReadSingle($parameters);
		}
		
		/*
		 * Transaction management
		 */
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function BeginTransaction() {
			$this->connection->beginTransaction();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Commit() {
			$this->connection->commit();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function Rollback() {
			$this->connection->rollBack();
		}
		
		/*
		 * Disconnect
		 */
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
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
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new DatabaseConnection;
			}
			
			return self::$instance;
		}
	}
?>