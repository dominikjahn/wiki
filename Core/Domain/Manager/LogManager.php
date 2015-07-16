<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class LogManager extends DomainManager
	{
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByID($id) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT log_id, status, object_table, object_id, user_id, type, timestamp FROM %PREFIX%log WHERE status = 100 AND log_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = LogFactory::GetInstance();
			
			$object = new Log();
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByObjectAndType(Domain $object, $type) {
			$db = DatabaseConnection::GetInstance();
			
			$table = constant(get_class($object)."::DB_TABLE");
			$object = $object->ID;
			
			$sqlObject = "SELECT log_id, status, object_table, object_id, user_id, type, timestamp FROM %PREFIX%log WHERE status = 100 AND object_table = :table AND object_id = :object AND type = :type ORDER BY timestamp DESC";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["table" => $table, "object" => $object, "type" => $type]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = LogFactory::GetInstance();
			
			$object = new Log();
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
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
				self::$instance = new LogManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>