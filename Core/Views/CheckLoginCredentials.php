<?php
	use Wiki\Domain\Manager\UserManager;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$loginname = $_GET["loginname"];
	$password = $_GET["password"]; // This should already be md5'ed
	$found = false;
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$userManager = UserManager::GetInstance();
		
		$user = $userManager->GetByLoginname($loginname);
		
		if(!$user || $user->Password != $password) {
			$data->status = 0;
			$data->message = "The login credentials are incorrect";
		} else {
			$data->status = 1;
			$data->message = "The login credentials are correct";
		}
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>