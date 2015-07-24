<?php
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Page;
	use Wiki\Domain\Version;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Domain\Manager\GroupManager;
	use Wiki\Exception\NotAuthorizedToCreateOrEditPagesWithScriptsException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$pageID = (isset($_POST["pageID"]) ? (int) $_POST["pageID"] : null);
	$title = $_POST["title"];
	$content = $_POST["content"];
	$summary = (isset($_POST["summary"]) ? $_POST["summary"] : null);
	$minor_edit = ((isset($_POST["minor_edit"]) && $_POST["minor_edit"] == "true") ? true : false);
	$visibility = (isset($_POST["visibility"]) ? $_POST["visibility"] : Page::VIS_PROTECTED);
	$manipulation = (isset($_POST["manipulation"]) ? $_POST["manipulation"] : Page::MAN_REGISTERED);
	$ownerID = (isset($_POST["owner"]) ? (int) $_POST["owner"] : null);
	$groupID = (isset($_POST["group"]) ? (int) $_POST["group"] : null);
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$db = DatabaseConnection::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		$isNewPage = true;
		$timestamp =  date("Y-m-d H:i:s");
		
		$db->BeginTransaction();
		
		$page = null;
		$name = null;
		
		if($pageID) {
			$page = PageManager::GetInstance()->GetByID($pageID);
			
			if(!$page || $page->Status === 0) {
				$data->status = 0;
				throw new \Exception("The page doesn't exist");
			}
			
			$isNewPage = false;
		} else {
			$page = new Page();
			
			$name = Page::NormalizeTitle($title);
			$name = Page::CheckForDuplicatePageName($name);
			
			$page->Status = 100;
			$page->Name = $name;
		}
		
		// Check for manipulation mode
		if(!$isNewPage) {
			if(!$page->CanEdit) {
				$db->Rollback();
				throw new \Exception("You are not authorized to edit this page");
			}
		} else if(!$currentUser->HasPermission("CREATE_PAGES")) {
			$db->Rollback();
			throw new \Exception("You are not authorized to create new pages");
		}
		
		$owner = User::GetCurrentUser();
		if($ownerID) {
			$userManager = UserManager::GetInstance();
			
			$owner = $userManager->GetByID($ownerID);
			
			if(!$owner || $owner->Status === 0) {
				throw new \Exception("User not found");
			}
		}
		
		$groupManager = GroupManager::GetInstance();
		$group = $groupManager->GetByID($groupID);
		
		if(!$group || $group->Status === 0) {
			throw new \Exception("Group not found");
		}
		
		$page->Title = $title;
		$page->Content = $content;
		$page->Visibility = $visibility;
		$page->Manipulation = $manipulation;
		$page->Owner = $owner;
		$page->Group = $group;
		
		$success = $page->Save();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the page failed");
		}
		
		$version = new Version();
		$version->Status = 100;
		$version->Page = $page;
		$version->Title = $title;
		$version->Content = $content;
		$version->Summary = $summary;
		$version->MinorEdit = $minor_edit;
		
		$success = $version->Save();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the revision failed");
		}
		
		$db->Commit();
		
		$data->status = 200;
		$data->message = "The page was saved successfully";
		
		$data->page = PageManager::GetInstance()->GetByID($page->ID);
		
	} catch(NotAuthorizedToCreateOrEditPagesWithScriptsException $e) {
		$data->status = 401;
		$data->message = $e->getMessage();
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>