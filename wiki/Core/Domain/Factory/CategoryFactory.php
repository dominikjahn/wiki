<?php
	namespace Wiki\Domain\Factory;
	
	use Wiki\Domain\Manager\LogManager;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Domain;
	use Wiki\Domain\Log;
	use Wiki\Database\DatabaseRow;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class CategoryFactory extends DomainFactory
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
			$logManager = LogManager::GetInstance();
			
			$object->ID = $row->category_id->Integer;
			$object->Status = $row->status->Integer;
			$object->Name = $row->name->String;
			$object->Title = $row->title->String;
			
			$object->LogCreated = $logManager->GetByObjectAndType($object, Log::TYPE_CREATE);
			$object->LogModified = $logManager->GetByObjectAndType($object, Log::TYPE_MODIFY);
			
			if(is_null($object->LogModified)) {
				$object->LogModified = $object->LogCreated;
			}
			
			if($object->Status === 0) {
			  $object->LogDeleted = $logManager->GetByObjectAndType($object, Log::TYPE_DELETE);
			}
			
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
				self::$instance = new CategoryFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>