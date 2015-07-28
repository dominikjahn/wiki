<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetGroup extends Response
	{
		public function Run() {
			$name = (isset($_GET["name"]) ? $_GET["name"] : null);
			$groupID = (isset($_GET["groupID"]) ? (int) $_GET["groupID"] : null);
			
			$group = null;
			$groupManager = GroupManager::GetInstance();
			
			if($name) {
				$group = $groupManager->GetByName($name);
			} else {
				$group = $groupManager->GetByID($groupID);
			}
			
			if(!$group || $group->status === 0) {
				throw new GroupNotFoundException();
			}
			
			$this->Status = 200;
			$this->Message = "Group found";
			$this->Data = ["group" => $group];
		}
	}

?>