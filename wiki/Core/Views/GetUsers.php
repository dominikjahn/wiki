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
		
		$users = [];
		$group = null;
		
		if(is_null($groupID)) {
			$users = $userManager->GetAll();
		} else {
			$groupManager = GroupManager::GetInstance();
			$groupMemManager = GroupMemberManager::GetInstance();
			
			$group = $groupManager->GetByID($groupID);
			
			if(!$group || $group->Status === 0) {
				$data->status = 404;
				throw new \Exception("Group not found");	
			}
			
			$members = $groupMemManager->GetByGroup($group);
			
			if($mode == "INCLUDE") {
				foreach($members as $member) {
					$users[] = $member->User;
				}
			} else {
				
				$allUsers = $userManager->GetAll();
				
				foreach($allUsers as $u => $user) {
					$isMember = false;
					
					foreach($members as $member) {
						if($member->User->ID === $user->ID) {
							$isMember = true;
							break;
						}
					}
					
					if(!$isMember) {
						$users[] = $user;
					}
				}
			}
			
			/*echo "All users:\n";
			foreach($users as $user) {
				echo "  * [".$user->ID."] ".$user->Loginname."\n";
			}
			
			echo "\nMembers:\n";
			foreach($members as $member) {
				echo "  * [".$member->User->ID."] ".$member->User->Loginname."\n";
			}
			
			echo "\n";*/
			
		}
		
		$users = array_values($users);
		
		$data->status = 200;
		$data->message = count($users)." users found";
		$data->users = $users;
		if($group) {
			$data->group = $group;
		}
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>