<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$request = Request::GetInstance();
	
	$groupID = (int) $request->Body["groupID"];
	
	$data = (object) ["status" => 500, "message" => "An unknown error occured"];
	
	try {
		$group = GroupManager::GetInstance()->GetByID($groupID);
		
		if(!$group || $group->Status === 0) {
			$this->status = 404;
			throw new \Exception("The group doesn't exist");
		}
		
		$success = $group->Delete();
		
		if(!$success) {
			throw new \Exception("Deleting the group failed");
		}
		
		$data->status = 200;
		$data->message = "The group was deleted successfully";
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>