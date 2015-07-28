<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\Page;
	use Wiki\Domain\Manager\VersionManager;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\User;
	use Wiki\Exception\PageNotFoundException;
	use Wiki\Exception\PageNotVisibleToCurrentUserException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	class DisplayVersion extends Response
	{
		public function Run() {
			require_once "Core/ThirdParty/ParseDown.php";
			require_once "Core/ThirdParty/ParsedownExtra.php";
			
			$versionID = (int) $_GET["versionID"];
			
			$noHeadline = $noNavbar = $noFooterbar = $customOutput = false;
		
			$version = VersionManager::GetInstance()->GetByID($versionID);
			
			if(!$version || $version->status === 0) {
				throw new PageNotFoundException();
			} else if(!$version->Page->IsVisible) {
				throw new PageNotVisibleToCurrentUserException();
			} else {
				Page::SetCurrentPage($version->Page);
				
				//$content = $version->Content;
				//$content = ParseWiki($content, $noHeadline, $noNavbar, $noFooterbar, $customOutput);
				$parseDown = new \ParsedownExtra;
				$content = $parseDown->text($content);
				$content = str_replace("<table>","<table class='table table-bordered'>",$content);
				
				$version->Content = $content;
				
				if(!$customOutput) {
					$this->Status = 200;
					$this->Message = "Page found";
					$this->Data = ["version" => $version, "no_headline" => $noHeadline, "no_navbar" = $noNavbar, "no_footerbar" => $noFooterbar];
		
					//(object) ["pageID" => $page->ID, "name" => $page->Name, "title" => $page->Title, "content" => $content, "visibility" => $page->Visibility, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar, "lastedit" => $lastedit];
				} else {
					$this->Data = $version->Content;
				}
			}
		}
	}
?>