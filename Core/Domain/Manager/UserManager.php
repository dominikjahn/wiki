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
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
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