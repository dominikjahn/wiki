<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\UserFactory;
	use Wiki\Domain\User;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class UserManager extends DomainManager
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
			
			$sqlObject = "SELECT user_id, status, loginname, password FROM %PREFIX%user WHERE status = 100 AND user_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$this->AddToCache($user, $id);
			
			$userFactory->FromDataRow($user, $rowObject);
		
			$stmObject->Close();
			
			$this->AddToCache($user, "loginname", $user->Loginname);
			
			return $user;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByLoginname($loginname) {
			 $fromCache = $this->GetFromCache($loginname, "loginname");
		  
		  if($fromCache) return $fromCache;
		  
		  $db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT user_id, status, loginname, password FROM %PREFIX%user WHERE status = 100 AND loginname = :loginname";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["loginname" => $loginname]);
			
			if(!$rowObject) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$this->AddToCache($user, $loginname, "loginname");
			
			$userFactory->FromDataRow($user, $rowObject);
			
			$this->AddToCache($user);
		
			$stmObject->Close();
			
			return $user;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetAll() {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT user_id, status, loginname, password FROM %PREFIX%user WHERE status = 100 ORDER BY loginname";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read();
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = UserFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = $this->GetFromCache($rowObject->user_id->Integer);
				
				if(!$object) {
  				$object = new User();
  				
  				$this->AddToCache($object, $rowObject->user_id->Integer);
  				
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