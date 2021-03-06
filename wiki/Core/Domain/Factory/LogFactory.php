<?php
	namespace Wiki\Domain\Factory;
	
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Domain;
	use Wiki\Database\DatabaseRow;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class LogFactory extends DomainFactory
	{
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function FromDataRow(Domain $object, DatabaseRow $row) {
			$userManager = UserManager::GetInstance();
			
			$object->ID = $row->log_id->Integer;
			$object->Status = $row->status->Integer;
			$object->ObjectTable = $row->object_table->String;
			$object->{"Object"} = $row->object_id->Integer;
			$object->User = $userManager->GetByID($row->user_id->Integer);
			$object->Type = $row->type->String;
			$object->Timestamp = $row->timestamp->DateTime;
			
			$object->IsLoadedFromDatabase = true;
			
			$object->ValidateChecksum($row->checksum->String);
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
				self::$instance = new LogFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>