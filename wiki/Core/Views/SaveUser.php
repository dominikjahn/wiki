<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\NotAuthorizedToCreateNewUsersException;
	use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	use Wiki\Exception\CurrentPasswordDoesNotMatchException;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Exception\CannotCreateUserWithoutPasswordException;
	use Wiki\Configuration;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class SaveUser extends Response
	{
		public function Run() {
			$userID = (isset($_POST["userID"]) ? (int) $_POST["userID"] : null);
			
			$loginname = (isset($_POST["loginname"]) ? $_POST["loginname"] : null);
			$password = (isset($_POST["password"]) ? $_POST["password"] : null);
			$currentpassword = (isset($_POST["currentpassword"]) ? $_POST["currentpassword"] : null);
		
			$db = DatabaseConnection::GetInstance();
			
			$currentUser = User::GetCurrentUser();
			
			$isNewUser = true;
			
			$db->BeginTransaction();
			
			$user = null;
			
			if($userID) {
				$user = UserManager::GetInstance()->GetByID($userID);
				
				if(!$user || $user->Status === 0) {
					$this->Status = 404;
					throw new UserNotFoundException();
				}
				
				$isNewUser = false;
				
				if(!is_null($password)) {
					// First we need to check if $currentpassword matches the password
					if(!$user->MatchPassword($currentpassword) && $user->ID == $currentUser->ID) {
						throw new CurrentPasswordDoesNotMatchException();
					}
					
					$user->Password = $password;
				}
			} else {
				$user = new User();
				
				$user->Status = 100;
			}
			
			$user->Loginname = $loginname;
			
			if(!is_null($password)) {
				if(is_null($userID)) {
					throw new CannotCreateUserWithoutPasswordException();
				}
				
				$user->Password = password_hash($password, \PASSWORD_DEFAULT);
			}
			
			$success = $user->Save();
			
			if(!$success) {
				$db->Rollback();
				throw new \Exception("Storing the user failed");
			}
			
			$db->Commit();
			
			$this->Status = 200;
			$this->Message = "The user was saved successfully";
		}
	}
?>