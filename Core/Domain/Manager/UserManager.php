<?php
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
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT user_id, status, loginname, password FROM user WHERE status = 100 AND user_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$userFactory->FromDataRow($user, $rowObject);
		
			$stmObject->Close();
			
			return $user;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByLoginname($loginname) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT user_id, status, loginname, password FROM user WHERE status = 100 AND loginname = :loginname";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["loginname" => $loginname]);
			
			if(!$rowObject) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$userFactory->FromDataRow($user, $rowObject);
		
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
			
			$sqlObjects = "SELECT user_id, status, loginname, password FROM user WHERE status = 100 ORDER BY loginname";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read();
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = UserFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new User();
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