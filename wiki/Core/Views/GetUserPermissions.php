<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Exception\AuthorizationMissingException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetUserPermissions extends Response
	{
		public function Run() {
			$user = (int) $_GET["userID"];
		
			$userManager = UserManager::GetInstance();
			$currentUser = User::GetCurrentUser();
			$user = $userManager->GetByID($user);
			
			if(!$user || $user->Status === 0) {
				throw new UserNotFoundException();
			}
			
			if($user->ID != $currentUser->ID && !$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
				throw new AuthorizationMissingException("You are not allowed to manage user permissions");
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
			
			$this->Status = 200;
			$this->Message = count($permissions)." permissions found";
			$this->Data = ["permissions" => $permissions, "user" => $user];
		}
	}
?>