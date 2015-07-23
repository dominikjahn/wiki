<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Exception\NotAuthorizedToManageUserPermissionsException;
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
			throw new \Exception("The group doesn't exist");
		}
		
		foreach($userIDs as $userID) {
			$user = UserManager::GetInstance()->GetByID($userID);
			
			if(!$user || $user->Status === 0) {
				$data->status = 404;
				throw new \Exception("The user doesn't exist");
			} else 
			
			$success = false;
			
			if($request->Method == "PUT") {
				$success = $user->AddToGroup($group);
			} else if($request->Method == "DELETE") {
				$success = $user->RemoveFromGroup($group);
			}
		}
		
		$data->status = ($success ? 200 : 0);
		$data->message = "The group membership has successfully been changed";
		
	}/* catch(NotAuthorizedToManageUserPermissionsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>