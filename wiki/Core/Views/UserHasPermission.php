<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
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
	class UserHasPermission extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
			
			$userID = (int) $_GET["userID"];
			$permissionName = $_GET["permission"];
		
			$user = UserManager::GetInstance()->GetByID($userID);
			$currentUser = User::GetCurrentUser();
			
			if(!$user || $user->Status === 0) {
				$this->Status = 404;
				throw new UserNotFoundException();
			}
			
			if($user->ID != $currentUser->ID && !$currentUser->HasPermission("ALTER_USERPERMISSIONS")) {
				$this->Status = 401;
				throw new AuthorizationMissingException("You are not authorized to check the permissions of other users");
			}
			
			if(!$user->HasPermission($permissionName)) {
				$this->Status = 0;
				$this->Message = "The user does not have this permission";
			} else {
				$this->Status = 200;
				$this->Message = "The user has the permission";
			}
		}
	}
?>