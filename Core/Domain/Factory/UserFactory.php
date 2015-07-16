<?php
	namespace Wiki\Domain\Factory;
	
	use Wiki\Domain\Manager\LogManager;
	use Wiki\Domain\Manager\UserPermissionManager;
	use Wiki\Domain\Domain;
	use Wiki\Domain\Log;
	use Wiki\Database\DatabaseRow;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class UserFactory extends DomainFactory
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
			$logManager = LogManager::GetInstance();
			$userpermManager = UserPermissionManager::GetInstance();
			
			$object->ID = $row->user_id->Integer;
			$object->Status = $row->status->Integer;
			$object->Loginname = $row->loginname->String;
			$object->Password = $row->password->String;
			
			$object->LogCreated = $logManager->GetByObjectAndType($object, Log::TYPE_CREATE);
			$object->LogModified = $logManager->GetByObjectAndType($object, Log::TYPE_MODIFY);
			
			if(is_null($object->LogModified)) {
				$object->LogModified = $object->LogCreated;
			}
			
			if($object->Status === 0) {
			  $object->LogDeleted = $logManager->GetByObjectAndType($object, Log::TYPE_DELETE);
			}
			
			$permissions = $userpermManager->GetByUser($object);
			$object->Permissions = $permissions;
			
			$object->IsLoadedFromDatabase = true;
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
				self::$instance = new UserFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>