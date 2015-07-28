<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Tools\Request;
	use Wiki\Exception\GroupNotFoundException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class DeleteGroup extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
			
			$groupID = (int) $request->Body["groupID"];
			
			$group = GroupManager::GetInstance()->GetByID($groupID);
			
			if(!$group || $group->Status === 0) {
				$this->Status = 404;
				throw new GroupNotFoundException();
			}
			
			$success = $group->Delete();
			
			if(!$success) {
				$this->Status = 500;
				throw new \Exception("Deleting the group failed");
			}
			
			$this->Status = 200;
			$this->Message = "The group was deleted successfully";
		}
	}
?>