<?php
	namespace Wiki\Domain\Factory;
	
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\LogManager;
	use Wiki\Domain\Domain;
	use Wiki\Domain\Log;
	use Wiki\Database\DatabaseRow;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class VersionFactory extends DomainFactory
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
			$pageManager = PageManager::GetInstance();
			$logManager = LogManager::GetInstance();
			
			$object->ID = $row->version_id->Integer;
			$object->Status = $row->status->Integer;
			$object->Page = $pageManager->GetByID($row->page_id->Integer);
			$object->Title = $row->title->String;
			$object->Content = $row->content->String;
			$object->Summary = $row->summary->String;
			$object->MinorEdit = $row->minor_edit->Boolean;
			
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
				self::$instance = new VersionFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>