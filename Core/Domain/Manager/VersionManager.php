<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class VersionManager extends DomainManager
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
			
			$sqlObject = "SELECT version_id, status, page_id, title, content, summary, minor_edit FROM version WHERE status = 100 AND version_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = VersionFactory::GetInstance();
			
			$object = new Version();
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			$this->AddToCache($object);
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByPage(Page $page) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT version_id, status, page_id, title, content, summary, minor_edit FROM version WHERE status = 100 AND page_id = :page ORDER BY version_id DESC";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read(["page" => $page]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = VersionFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new Version();
				$objectFactory->FromDataRow($object, $rowObject);
				
				$this->AddToCache($object);
				
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
				self::$instance = new VersionManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>