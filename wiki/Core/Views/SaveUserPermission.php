<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\UserPermission;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\UserPermissionManager;
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
		$currentUser = User::GetCurrentUser();
		
		if(!$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
			//throw new NotAuthorizedToManageUserPermissionsException();
		}
		
		$user = UserManager::GetInstance()->GetByID($userID);
		
		$success = false;
		
		if($request->Method == "PUT") {
			$permission = new UserPermission();
			$permission->Status = 100;
			$permission->User = $user;
			$permission->Permission = $permissionName;
			
			$success = $permission->Save();
		} else if($request->Method == "DELETE") {
			$permissionManager = UserPermissionManager::GetInstance();
			
			$permission = $permissionManager->GetByUserAndName($user, $permissionName);
			
			if(!$permission) {
				throw new \Exception("The user has no permission as '".$permissionName."'");
			}
			
			$success = $permission->Delete();
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