<?php

	use Wiki\Domain\Manager\UserManager;
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$userManager = UserManager::GetInstance();
		
		$users = $userManager->GetAll();
		
		$data->status = 1;
		$data->message = count($users)." users found";
		$data->users = $users;
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>