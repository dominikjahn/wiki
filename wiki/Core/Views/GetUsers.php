<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Manager\GroupMemberManager;
	use Wiki\Domain\User;
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetUsers extends Response
	{
		public function Run() {
			$groupID = (isset($_GET["groupID"]) ? (int) $_GET["groupID"] : null);
			$mode = (isset($_GET["mode"]) ? strtoupper($_GET["mode"]) : "INCLUDE");
		
			$users = [];
			$group = null;
			
			if(!User::GetCurrentUser()->HasPermission("MANAGE_USERS") && !User::GetCurrentUser()->HasPermission("MANAGE_GROUPS")) {
				$users[] = User::GetCurrentUser();
			} else {
				$userManager = UserManager::GetInstance();
				
				$users = [];
				
				if(is_null($groupID)) {
					$users = $userManager->GetAll();
				} else {
					$groupManager = GroupManager::GetInstance();
					$groupMemManager = GroupMemberManager::GetInstance();
					
					$group = $groupManager->GetByID($groupID);
					
					if(!$group || $group->Status === 0) {
						$this->Status = 404;
						throw new GroupNotFoundException();
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
			}
			
			$users = array_values($users);
			
			$this->Status = 200;
			$this->Message = count($users)." users found";
			$data = ["users" => $users];
			if($group) {
				$data["group"] = $group;
			}
			$this->Data = $data;
		}
	}
?>