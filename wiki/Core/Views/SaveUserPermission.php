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
	$permissions = $request->Body["permissions"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = UserManager::GetInstance()->GetByID($userID);
		
		if(!$user || $user->Status === 0) {
			$data->status = 404;
			throw new \Exception("The user doesn't exist");
		}
		
		$total_success = false;
		
		foreach($permissions as $permissionName) {
			$total_success = true;
			
		
			if($request->Method == "PUT") {
				$success = $user->GrantPermission($permissionName);
			} else if($request->Method == "DELETE") {
				$success = $user->RevokePermission($permissionName);
			}
			
			if(!$success) {
				$total_success = false;
				// rollback
				throw new \Exception("Something failed");
				break;
			}
		}
		
		$data->status = ($total_success ? 200 : 0);
		$data->message = "The permission has successfully been ".($request->Method == "PUT" ? "granted" : "revoked");
		
	}/* catch(NotAuthorizedToManageUserPermissionsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>