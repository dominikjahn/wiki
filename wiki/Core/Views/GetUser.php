<?php
	namespace Wiki\Views;
	
	use Wiki\Response;	
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	class GetUser extends Response
	{
		public function Run() {
			$loginname = (isset($_GET["loginname"]) ? $_GET["loginname"] : null);
			$userID = (isset($_GET["userID"]) ? (int) $_GET["userID"] : null);
		
			$user = null;
			$userManager = UserManager::GetInstance();
			
			if($loginname) {
				$user = $userManager->GetByLoginname($loginname);
			} else {
				$user = $userManager->GetByID($userID);
			}
			
			if(!$user || $user->status === 0) {
				throw new UserNotFoundException();
			}
			
			$this->Status = 200;
			$this->Message = "User found";
			$this->Data = ["user" => $user];
		}
	}
?>