<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Domain\Page;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\VersionManager;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetVersions extends Response
	{
		public function Run() {
			$pagename = (int) $_GET["page"];
		
			$pageManager = PageManager::GetInstance();
			$versionManager = VersionManager::GetInstance();
			
			$currentUser = User::GetCurrentUser();
			
			$page = $pageManager->GetByName($pagename);
			
			if(!$page) {
				throw new PageNotFoundException("The page you are trying to get versions from doesn't exist");
			} else if(!$page->IsVisible) {
				throw new AuthorizationMissingException("You are not authorized to see the versions of this page");
			}
			
			$versions = $versionManager->GetByPage($page);
			
			$this->Status = 200;
			$this->Message = count($versions)." versions found";
			$this->Data = ["page" => $page, "versions" => $versions];
		}
	}
		
?>