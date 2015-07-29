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
			$permissions = $_GET["permissions"];
			$mode = (isset($_GET["mode"]) ? strtoupper($_GET["mode"]) : "ALL");
		
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
			
			$granted = 0;
			
			foreach($permissions as $permission) {
				
				if($user->HasPermission($permission)) {
					$granted++;
				}
			}
			
			$success = false;
			
			if(($mode == "ALL" && $granted == count($permissions)) || ($mode == "ANY" && $granted > 0) || ($mode == "NONE" && $granted === 0)) {
				$success = true;
			}
			
			$this->Status = ($success ? 200 : 401);
			switch($mode) {
				case "ALL":
					$this->Message = "The user ".($success?"has":"doesn't have")." the permissions.";
					break;
					
				case "ANY":
					$this->Message = "The user ".($success?"has some or all":"doesn't have any")." of the permissions.";
					break;
					
				case "NONE":
					$this->Message = "The user ".($success?"doesn't have":"has")." the permissions.";
					break;
			}
			
		}
	}
?>