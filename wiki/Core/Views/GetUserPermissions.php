<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	 
	$user = (int) $_GET["userID"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$userManager = UserManager::GetInstance();
		$currentUser = User::GetCurrentUser();
		$user = $userManager->GetByID($user);
		
		if(!$user || $user->Status === 0) {
			$data->status = 404;
			throw new \Exception("The user doesn't exist");
		}
		
		if($user->ID != $currentUser->ID && !$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
			$data->status = 401;
			throw new \Exception("You are not allowed to manage user permissions");
		}
		
		// This isn't good, but gotta do it for now
		$sqlPermissions = "	SELECT DISTINCT(permission.permission), CASE WHEN userperm.userpermission_id IS NULL THEN '0' ELSE '100' END AS status
							FROM userpermission AS permission
							LEFT JOIN userpermission AS userperm
								ON userperm.permission = permission.permission AND userperm.user_id = :user";
		$rsPermissions = DatabaseConnection::GetInstance()->PrepareAndRead($sqlPermissions, ["user" => $user]);
		
		$permissions = [];
		
		while($dsPermission = $rsPermissions->NextRow()) {
			$permission = $dsPermission->permission->String;
			$status = $dsPermission->status->Integer;
			
			$permissions[] = (object) ["permission" => $permission, "status" => $status];
		}
		
		$data->status = 1;
		$data->message = count($permissions)." permissions found";
		$data->permissions = $permissions;
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>