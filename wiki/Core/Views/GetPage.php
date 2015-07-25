<?php
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$pagename = (isset($_GET["name"]) ? $_GET["name"] : null);
	$pageID = (isset($_GET["pageID"]) ? (int) $_GET["pageID"] : null);
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$page = null;
		$pageManager = PageManager::GetInstance();
		
		if($pagename) {
			$page = $pageManager->GetByName($pagename);
		} else {
			$page = $pageManager->GetByID($pageID);
		}
		
		if(!$page || $page->status === 0) {
			$data->status = 404;
			$data->message = "The page was not found";
		} else if(!$page->IsVisible) {
			$data->status = 401;
			$data->message = "You are not authorized to see the content on this page";
			//throw new \Exception("You are not authorized to see the content on this page");
		} else {
			Page::SetCurrentPage($page);
			$data->status = 200;
			$data->message = "Page found";
			$data->page = $page;
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>