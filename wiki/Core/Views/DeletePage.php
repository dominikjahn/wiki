<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Tools\Request;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$request = Request::GetInstance();
	
	$pageID = (int) $request->Body["pageID"];
	
	$data = (object) ["status" => 500, "message" => "An unknown error occured"];
	
	try {
		$page = PageManager::GetInstance()->GetByID($pageID);
		
		if(!$page || $page->Status === 0) {
			$this->status = 404;
			throw new \Exception("The page doesn't exist");
		}
		
		$success = $page->Delete();
		
		if(!$success) {
			throw new \Exception("Deleting the page failed");
		}
		
		$data->status = 200;
		$data->message = "The page was deleted successfully";
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>