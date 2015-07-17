<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Exception\NotAuthorizedToCreateNewUsersException;
	use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$userID = (isset($_GET["userID"]) ? (int) $_GET["userID"] : null);
	
	$loginname = $_POST["loginname"];
	$password = $_POST["password"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$db = DatabaseConnection::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		if(!$currentUser) {
			throw new \Exception("You are not authorized to perform this action");
		}
		
		$isNewUser = true;
		
		$db->BeginTransaction();
		
		$user = null;
		
		if(!is_null($userID)) {
			$user = UserManager::GetInstance()->GetByID($userID);
			
			$isNewUser = false;
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
		
		$data->status = 1;
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