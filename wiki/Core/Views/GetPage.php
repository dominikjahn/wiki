<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetPage extends Response
	{
		public function Run() {
			$pagename = (isset($_GET["name"]) ? $_GET["name"] : null);
			$pageID = (isset($_GET["pageID"]) ? (int) $_GET["pageID"] : null);
		
			$page = null;
			$pageManager = PageManager::GetInstance();
			
			if($pagename) {
				$page = $pageManager->GetByName($pagename);
			} else {
				$page = $pageManager->GetByID($pageID);
			}
			
			if(!$page || $page->status === 0) {
				throw new PageNotFoundException();
			} else if(!$page->IsVisible) {
				throw new PageNotVisibleToCurrentUserException();
			}
		
			Page::SetCurrentPage($page);
			$this->Status = 200;
			$this->Message = "Page found";
			$this->Data = ["page" => $page];
		}
	}
?>