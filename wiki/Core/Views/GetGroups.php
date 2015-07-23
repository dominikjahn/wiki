<?php

	use Wiki\Domain\Manager\GroupManager;
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$groupManager = GroupManager::GetInstance();
		
		$groups = $groupManager->GetAll();
		
		$data->status = 200;
		$data->message = count($groups)." groups found";
		$data->groups = $groups;
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>