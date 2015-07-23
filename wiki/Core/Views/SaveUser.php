<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\NotAuthorizedToCreateNewUsersException;
	use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	use Wiki\Exception\CurrentPasswordDoesNotMatchException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$username = (isset($_GET["user"]) ? (int) $_GET["user"] : null);
	
	$loginname = (isset($_POST["loginname"]) ? $_POST["loginname"] : null);
	$password = (isset($_POST["password"]) ? $_POST["password"] : null);
	$currentpassword = (isset($_POST["currentpassword"]) ? $_POST["currentpassword"] : null);
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$db = DatabaseConnection::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		$isNewUser = true;
		
		$db->BeginTransaction();
		
		$user = null;
		
		if(!is_null($username)) {
			$user = UserManager::GetInstance()->GetByLoginname($username);
			
			if(!$user || $user->Status === 0) {
				$data->status = 404;
				throw new \Exception("The user doesn't exist");
			}
			
			$isNewUser = false;
			
			if(!is_null($password)) {
				// First we need to check if $currentpassword matches the password
				if(!$user->MatchPassword($currentpassword)) {
					throw new CurrentPasswordDoesNotMatchException();
				}
				
				$user->Password = $password;
			}
		} else {
			$user = new User();
			
			$user->Status = 100;
			$user->Loginname = $loginname;
			$user->Password = $password;
		}
		
		$success = $user->Save();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the user failed");
		}
		
		$db->Commit();
		
		$data->status = 200;
		$data->message = "The user was saved successfully";
		
	} catch(NotAuthorizedToCreateNewUsersException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	} catch(NotAuthorizedToEditOtherUsersException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>