<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\GroupFactory;
	use Wiki\Domain\Group;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GroupManager extends DomainManager
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
			
			$sqlObject = "SELECT group_id, status, checksum, name FROM %PREFIX%group WHERE group_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = GroupFactory::GetInstance();
			
			$object = new Group();
			
			$this->AddToCache($object, $id);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			$this->AddToCache($object, "name", $object->Name);
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByName($name) {
			 $fromCache = $this->GetFromCache($name, "name");
		  
			if($fromCache) return $fromCache;

			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT group_id, status, checksum, name FROM %PREFIX%group WHERE status = 100 AND name = :name";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["name" => $name]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = GroupFactory::GetInstance();
			
			$object = new Group();
			
			$this->AddToCache($object, $rowObject->group_id->Integer);
			$this->AddToCache($object, $name, "name");
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetAll() {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT group_id, status, checksum, name FROM %PREFIX%group WHERE status = 100 ORDER BY name";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read();
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = GroupFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = $this->GetFromCache($rowObject->group_id->Integer);
				
				if(!$object) {
					$object = new Group();
					
					$this->AddToCache($object, $rowObject->group_id->Integer);
					
					$objectFactory->FromDataRow($object, $rowObject);
				}
				
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
				self::$instance = new UserManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>