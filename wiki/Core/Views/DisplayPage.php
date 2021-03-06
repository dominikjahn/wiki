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
	class DisplayPage extends Response
	{
		public function Run() {
	
			$pagename = (isset($_GET["page"]) ? $_GET["page"] : null);
			$pageID = (isset($_GET["pageID"]) ? (int) $_GET["pageID"] : null);
			
			$pageID = $title = $content = $owner_id = $visibility = $lastedit = null;
			
			$noHeadline = $noNavbar = $noFooterbar = $customOutput = false;
		
			$page = null;
			$pageManager = PageManager::GetInstance();
			
			if($pagename) {
				$page = $pageManager->GetByName($pagename);
			} else {
				$page = $pageManager->GetByID($pageID);
			}
			
			$currentUser = User::GetCurrentUser();
			
			if(!$page || $page->status === 0) {
				$this->Status = 404;
				$this->Message = "The page was not found";
				
				// This is for "Create this page" button, so we only need this if the page name was used (for the title)
				if($pagename) {
					$this->Data = ["page" => $pagename];
				}
			} else if(!$page->IsVisible) {
				$this->Status = 401;
				$this->Message = "You are not authorized to see the content on this page";
			} else {
				Page::SetCurrentPage($page);
				
				$content = $page->Content;
				$content = $page->Render($noHeadline, $noNavbar, $noFooterbar, $customOutput);
				
				$page->Content = $content;
				
				if(!$customOutput) {
					$this->Status = 200;
					$this->Message = "Page found";
					$this->Data = ["page" => $page, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar];
	
					//(object) ["pageID" => $page->ID, "name" => $page->Name, "title" => $page->Title, "content" => $content, "visibility" => $page->Visibility, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar, "lastedit" => $lastedit];
				} else {
					$this->Data = $page->Content;
				}
			}
		}
	}
?>