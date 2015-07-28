<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\UserNotFoundException;
	use Wiki\Exception\PasswordMismatchException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class CheckLoginCredentials extends Response
	{
		public function Run() {
			$loginname = $_GET["loginname"];
			$password = $_GET["password"]; // This should already be md5'ed
			$found = false;
			
			$userManager = UserManager::GetInstance();
			
			$user = $userManager->GetByLoginname($loginname);
		
			if(!$user || !$user->Status === 0) {
				$this->Status = 401;
				throw new UserNotFoundException("The login credentials are incorrect.");
			} else if(!$user->MatchPassword($password)) {
				$this->Status = 401;
				throw new PasswordMismatchException("The login credentials are incorrect.");
			}
			
			$this->Status = 200;
			$this->Message = "The login credentials are correct.";
			$this->Data = ["user" => $user];
		}
	}
?>