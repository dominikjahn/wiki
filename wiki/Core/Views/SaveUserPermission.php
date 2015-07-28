<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class SaveUserPermission extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
			
			$userID = (int) $request->Body["userID"];
			$permissions = $request->Body["permissions"];
		
			$user = UserManager::GetInstance()->GetByID($userID);
			
			if(!$user || $user->Status === 0) {
				$this->Status = 404;
				throw new UserNotFoundException();
			}
			
			$total_success = false;
			
			$db = DatabaseConnection::GetInstance();
			$db->BeginTransaction();
			$exception = null;
			
			foreach($permissions as $permissionName) {
				$total_success = true;
				
				try {
					if($request->Method == "PUT") {
						$success = $user->GrantPermission($permissionName);
					} else if($request->Method == "DELETE") {
						$success = $user->RevokePermission($permissionName);
					}
				} catch(\Exception $e) {
					$exception = $e;
				}
				
				if(!$success) {
					$total_success = false;
					$db->Rollback();
					break;
				}
			}
			
			if(!$total_success) {
				throw $exception;
			}
			
			$db->Commit();
			
			$this->Status = 200;
			$this->Message = "The permissions have successfully been ".($request->Method == "PUT" ? "granted" : "revoked");
		}
	}
?>