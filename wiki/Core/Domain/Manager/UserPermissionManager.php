<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\UserPermissionFactory;
	use Wiki\Domain\UserPermission;
	use Wiki\Domain\User;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class UserPermissionManager extends DomainManager
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
			
			$sqlObject = "SELECT userpermission_id, status, user_id, permission FROM %PREFIX%userpermission WHERE status = 100 AND userpermission_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = UserPermissionFactory::GetInstance();
			
			$object = new UserPermission();
			
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
		public function GetByUserAndName(User $user, $name) {
			$fromCache = $this->GetFromCache($user->loginname."-".$name);
		  
			if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT userpermission_id, status, user_id, permission FROM %PREFIX%userpermission WHERE status = 100 AND user_id = :user AND permission = :name";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["user" => $user, "name" => $name]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = UserPermissionFactory::GetInstance();
			
			$object = new UserPermission();
			
			
			$objectFactory->FromDataRow($object, $rowObject);
			$this->AddToCache($object);
			
			$stmObject->Close();
			
			return $object;
		}
		
		public function GetByUser(User $user) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT userpermission_id, status, user_id, permission FROM %PREFIX%userpermission WHERE status = 100 AND user_id = :user";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read(["user" => $user]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = UserPermissionFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new UserPermission();
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
				self::$instance = new UserPermissionManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>