<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Exception\GroupNotFoundException;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class SaveGroupMember extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
		
			$groupID = (int) $request->Body["groupID"];
			$userIDs = $request->Body["userIDs"];
		
			$group = GroupManager::GetInstance()->GetByID($groupID);
			
			if(!$group || $group->Status === 0) {
				$this->Status = 404;
				throw new GroupNotFoundException();
			}
			
			$total_success = false;
			
			$db = DatabaseConnection::GetInstance();
			$db->BeginTransaction();
			$exception = null;
			
			foreach($userIDs as $userID) {
				$total_success = true;
				
					try {
					$user = UserManager::GetInstance()->GetByID($userID);
					
					if(!$user || $user->Status === 0) {
						$this->Status = 404;
						throw new UserNotFoundException();
					} else 
					
					$success = false;
					
					if($request->Method == "PUT") {
						$success = $user->AddToGroup($group);
					} else if($request->Method == "DELETE") {
						$success = $user->RemoveFromGroup($group);
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
			$this->Message = "The users have successfully been ".($request->Method == "PUT" ? "added" : "removed");
		}
	}
?>