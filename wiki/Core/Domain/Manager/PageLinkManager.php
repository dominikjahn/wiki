<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\PageLinkFactory;
	use Wiki\Domain\Page;
	use Wiki\Domain\PageLink;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PageLinkManager extends DomainManager
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
			$fromCache = $this->GetFromCache($id);
		  
		    if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT pagelink_id, status, checksum, page_from_id, page_to_id, text FROM %PREFIX%pagelink WHERE pagelink_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = PageLinkFactory::GetInstance();
			
			$object = new PageLink();
			
			$this->AddToCache($object, $id);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByFromPage(Page $page) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT pagelink_id, status, checksum, page_from_id, page_to_id, text FROM %PREFIX%pagelink WHERE status = 100 AND page_from_id = :page";
			$stmObjects = $db->Prepare($sqlObjects);
			
			$resObjects = $stmObjects->Read(["page" => $page]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = PageLinkFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new PageLink();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				$objects[] = $object;
			}
		
			$stmObjects->Close();
			
			return $objects;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByToPage(Page $page) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT pagelink_id, status, checksum, page_from_id, page_to_id, text FROM %PREFIX%pagelink WHERE status = 100 AND page_to_id = :page";
			$stmObjects = $db->Prepare($sqlObjects);
			
			$resObjects = $stmObjects->Read(["page" => $page]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = PageLinkFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new PageLink();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				$objects[] = $object;
			}
		
			$stmObjects->Close();
			
			return $objects;
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
				self::$instance = new PageLinkManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>