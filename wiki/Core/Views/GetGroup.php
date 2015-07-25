<?php
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$name = (isset($_GET["name"]) ? $_GET["name"] : null);
	$groupID = (isset($_GET["groupID"]) ? (int) $_GET["groupID"] : null);
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$group = null;
		$groupManager = GroupManager::GetInstance();
		
		if($name) {
			$group = $groupManager->GetByName($name);
		} else {
			$group = $groupManager->GetByID($groupID);
		}
		
		if(!$group || $group->status === 0) {
			$data->status = 404;
			$data->message = "The group was not found";
		} else {
			$data->status = 200;
			$data->message = "Group found";
			$data->group = $group;
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>