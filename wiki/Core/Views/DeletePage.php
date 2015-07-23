<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Page;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\PageManager;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$pageID = $_GET["pageID"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$db = DatabaseConnection::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		$db->BeginTransaction();
		
		$page = PageManager::GetInstance()->GetByID($pageID);
		
		if(!$page || $page->Status === 0) {
			$this->status = 404;
			throw new \Exception("The page doesn't exist");
		}
		
		if(
			// User needs to be registered
			($page->Manipulation != Page::MAN_EVERYONE && $currentUser->ID === 1) ||
			// User needs to be the owner
			($page->Manipulation == Page::MAN_OWNER && $currentUser->ID != $page->Owner->ID)
		) {
			$db->Rollback();
			throw new \Exception("You are not authorized to delete this page");
		}
		
		$success = $page->Delete();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Deleting the page failed");
		}
		
		$db->Commit();
		
		$data->status = 200;
		$data->message = "The page was deleted successfully";
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>