<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	 
	$pagename = (int) $_GET["page"];
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$pageManager = PageManager::GetInstance();
		$versionManager = VersionManager::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		$page = $pageManager->GetByName($pagename);
		
		if(!$page) {
			$data->status = 404;
			$data->message = "The page you are trying to get versions from doesn't exist";
		} else if(
			($page->Visibility == Page::VIS_PROTECTED && !$currentUser) ||
			($page->Visibility == Page::VIS_PRIVATE && (!$currentUser || $page->Owner->ID != $currentUser->ID))
		) {
			$data->status = 401;
			$data->message = "You are not authorized to see the versions of this page";
		} else {
			$versions = $versionManager->GetByPage($page);
			
			$data->status = 1;
			$data->message = count($versions)." versions found";
			$data->page = $page;
			$data->versions = $versions;
		}
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>