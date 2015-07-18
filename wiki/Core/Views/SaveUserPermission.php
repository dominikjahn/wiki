<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\NotAuthorizedToManageUserPermissionsException;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$request = Request::GetInstance();
	
	$userID = (int) $request->Body["userID"];
	$permissionName = $request->Body["permission"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = UserManager::GetInstance()->GetByID($userID);
		
		$success = false;
		
		if($request->Method == "PUT") {
			$success = $user->GrantPermission($permissionName);
		} else if($request->Method == "DELETE") {
			$success = $user->RevokePermission($permissionName);
		}
		
		$data->status = (int) $success;
		$data->message = "The permission has successfully been changed";
		
	}/* catch(NotAuthorizedToManageUserPermissionsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>