<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Manager\GroupMemberManager;
	use Wiki\Domain\User;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetGroups extends Response
	{
		public function Run() {
			$groups = [];
			if(!User::GetCurrentUser()->HasPermission("MANAGE_GROUPS")) {
				$groupMemberManager = GroupMemberManager::GetInstance();
				// Get list of memberships
				$memberships = $groupMemberManager->GetByUser(User::GetCurrentUser());
				
				foreach($memberships as $membership) {
					$groups[] = $membership->Group;
				}
			} else {
				$groupManager = GroupManager::GetInstance();
				$groups = $groupManager->GetAll();
			}
			
			$this->Status = 200;
			$this->Message = count($groups)." groups found";
			$this->Data = ["groups" => $groups];
		}
	}
?>