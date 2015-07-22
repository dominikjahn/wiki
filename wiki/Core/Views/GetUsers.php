<?php

	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Manager\GroupMemberManager;
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	$groupID = (isset($_GET["groupID"]) ? (int) $_GET["groupID"] : null);
	$mode = (isset($_GET["mode"]) ? strtoupper($_GET["mode"]) : "INCLUDE");
	
	try {
		$userManager = UserManager::GetInstance();
		
		$users = $userManager->GetAll();
		
		if($groupID) {
			$groupManager = GroupManager::GetInstance();
			$groupMemManager = GroupMemberManager::GetInstance();
			
			$group = $groupManager->GetByID($groupID);
			$members = $groupMemManager->GetByGroup($group);
			
			$filtered = [];
			
			if($mode == "EXCLUDE") {
				$filtered = $users;
			}
			
			foreach($members as $member) {
				foreach($users as $u => $user) {
					if($user->ID === $member->ID) {
						if($mode == "INCLUDE") {
							$filtered[] = $user;
						} else if($mode == "EXCLUDE") {
							unset($filtered[$u]);
						}
					}
				}
			}
			
			$users = $filtered;
		}
		
		$data->status = 1;
		$data->message = count($users)." users found";
		$data->users = $users;
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>