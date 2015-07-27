<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\User;
	use Wiki\Exception\AuthorizationMissingException;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$request = Request::GetInstance();
	
	$userID = (int) $_GET["userID"];
	$permissionName = $_GET["permission"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = UserManager::GetInstance()->GetByID($userID);
		$currentUser = User::GetCurrentUser();
		
		if(!$user || $user->Status === 0) {
			$data->status = 404;
			throw new UserNotFoundException();
		}
		
		if($user->ID != $currentUser->ID && !$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
			$data->status = 401;
			throw new AuthorizationMissingException("You are not authorized to check the permissions of other users");
		}
		
		if(!$user->HasPermission($permissionName)) {
			$data->status = 0;
			$data->message = "The user does not have this permission";
		} else {
			$data->status = 200;
			$data->message = "The user has the permission";
		}
		
	}/* catch(NotAuthorizedToManageUserPermissionsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>