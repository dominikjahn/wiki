<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$request = Request::GetInstance();
	
	$userID = (int) $request->Body["userID"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = UserManager::GetInstance()->GetByID($userID);
		
		if(!$user || $user->Status === 0) {
			$this->status = 404;
			throw new \Exception("The user doesn't exist");
		}
		
		$success = $user->Delete();
		
		if(!$success) {
			throw new \Exception("Deleting the user failed");
		}
		
		$data->status = 200;
		$data->message = "The user was deleted successfully";
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>