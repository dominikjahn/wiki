<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Group;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Exception\GroupNotFoundException;
	//use Wiki\Exception\NotAuthorizedToEditOtherUsersException;
	//use Wiki\Exception\CurrentPasswordDoesNotMatchException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class SaveGroup extends Response
	{
		public function Run() {
			$groupID = (isset($_POST["groupID"]) ? (int) $_POST["groupID"] : null);
		
			$name = $_POST["name"];
		
			$isNewGroup = true;
			
			$group = null;
			
			if($groupID) {
				$group = GroupManager::GetInstance()->GetByID($groupID);
				
				if(!$group || $group->Status === 0) {
					throw new GroupNotFoundException();
				}
				
				$isNewGroup = false;
			} else {
				$group = new Group();
				
				$group->Status = 100;
			}
			
			$group->Name = $name;
			
			$success = $group->Save();
			
			if(!$success) {
				throw new \Exception("Storing the group failed");
			}
			
			$this->Status = 200;
			$this->Message = "The group was saved successfully";
		}
	}
?>