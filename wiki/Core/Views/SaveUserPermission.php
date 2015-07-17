<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\UserPermissionManager;
	use Wiki\Exception\NotAuthorizedToManageUserPermissionsException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$var = [];
	parse_str(file_get_contents("php://input"),$var);
	
	$userID = (int) $var["userID"];
	$permissionName = $var["permission"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$currentUser = User::GetCurrentUser();
		
		if(!$currentUser->HasPermissions("ALTER_USERPERMISSIONS")) {
			//throw new NotAuthorizedToManageUserPermissionsException();
		}
		
		$user = UserManager::GetInstance()->GetByID($userID);
		
		$success = false;
		
		if($_SERVER["REQUEST_METHOD"] == "PUT") {
			$permission = new UserPermission();
			$permission->User = $user;
			$permission->Permission = $permissionName;
			
			$success = $permission->Save();
		} else if($_SERVER["REQUEST_METHOD"] == "DELETE") {
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