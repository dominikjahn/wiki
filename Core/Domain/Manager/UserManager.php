<?php
	class UserManager extends DomainManager
	{
	
		  //
		 // METHODS
		//
		
		public function GetByID($id) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlUser = "SELECT user_id, status, loginname, password FROM user WHERE status = 100 AND user_id = :id";
			$stmUser = $db->Prepare($sqlUser);
			$rowUser = $stmUser->ReadSingle(["id" => $id]);
			
			if(!$rowUser) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$userFactory->FromDataRow($user, $rowUser);
		
			$stmUser->Close();
			
			return $user;
		}
		
		public function GetByLoginname($loginname) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlUser = "SELECT user_id, status, loginname, password FROM user WHERE status = 100 AND loginname = :loginname";
			$stmUser = $db->Prepare($sqlUser);
			$rowUser = $stmUser->ReadSingle(["loginname" => $loginname]);
			
			if(!$rowUser) {
				return null;
			}
			
			$userFactory = UserFactory::GetInstance();
			
			$user = new User();
			
			$userFactory->FromDataRow($user, $rowUser);
		
			$stmUser->Close();
			
			return $user;
		}
		
		  //
		 // FUNCTIONS
		//
		
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