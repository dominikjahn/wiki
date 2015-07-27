<?php
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
	
	$request = Request::GetInstance();
	
	$groupID = (int) $request->Body["groupID"];
	$userIDs = $request->Body["userIDs"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$group = GroupManager::GetInstance()->GetByID($groupID);
		
		if(!$group || $group->Status === 0) {
			$data->status = 404;
			throw new GroupNotFoundException();
		}
		
		$total_success = false;
		
		foreach($userIDs as $userID) {
			$total_success = true;
			
			$user = UserManager::GetInstance()->GetByID($userID);
			
			if(!$user || $user->Status === 0) {
				$data->status = 404;
				throw new UserNotFoundException();
			} else 
			
			$success = false;
			
			if($request->Method == "PUT") {
				$success = $user->AddToGroup($group);
			} else if($request->Method == "DELETE") {
				$success = $user->RemoveFromGroup($group);
			}
			
			if(!$success) {
				$total_success = false;
				// rollback
				throw new \Exception("Something failed");
				break;
			}
		}
		
		$data->status = ($total_success ? 200 : 0);
		$data->message = "The user has successfully been ".($request->Method == "PUT" ? "added" : "removed");
		
	}/* catch(NotAuthorizedToManageUserPermissionsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>