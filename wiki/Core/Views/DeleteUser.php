<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DeleteUser extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
			
			$userID = (int) $request->Body["userID"];
			
			$user = UserManager::GetInstance()->GetByID($userID);
				
			if(!$user || $user->Status === 0) {
				$this->Status = 404;
				throw new UserNotFoundException();
			}
			
			$success = $user->Delete();
			
			if(!$success) {
				$this->Status = 500;
				throw new \Exception("Deleting the user failed");
			}
			
			$this->Status = 200;
			$this->Message = "The user was deleted successfully";
		}
	}
?>