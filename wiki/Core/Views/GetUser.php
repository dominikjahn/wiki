<?php
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$loginname = (isset($_GET["loginname"]) ? $_GET["loginname"] : null);
	$userID = (isset($_GET["userID"]) ? (int) $_GET["userID"] : null);
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = null;
		$userManager = UserManager::GetInstance();
		
		if($loginname) {
			$user = $userManager->GetByLoginname($loginname);
		} else {
			$user = $userManager->GetByID($userID);
		}
		
		if(!$user || $user->status === 0) {
			$data->status = 404;
			$data->message = "The user was not found";
		} else {
			$data->status = 200;
			$data->message = "User found";
			$data->user = $user;
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>