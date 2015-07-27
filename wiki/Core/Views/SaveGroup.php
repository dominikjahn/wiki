<?php
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
	
	$groupID = (isset($_POST["groupID"]) ? (int) $_POST["groupID"] : null);
	
	$name = $_POST["name"];
	
	$data = (object) ["status" => 500, "message" => "An unknown error occured"];
	
	try {
		$isNewGroup = true;
		
		$group = null;
		
		if($groupID) {
			$group = GroupManager::GetInstance()->GetByID($groupID);
			
			if(!$group || $group->Status === 0) {
				$data->status = 404;
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
		
		$data->status = 200;
		$data->message = "The group was saved successfully";
		
	}/* catch(NotAuthorizedToCreateNewUsersException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	} catch(NotAuthorizedToEditOtherUsersException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	}*/ catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>