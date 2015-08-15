<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\User;
	use Wiki\Domain\Page;
	use Wiki\Domain\Log;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\CategoryManager;
	
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PreviewPage extends Response
	{
		public function Run() {
			require_once "Core/ThirdParty/ParseDown.php";
			require_once "Core/ThirdParty/ParsedownExtra.php";
			
			$title = $_POST["title"];
			$content = $_POST["content"];
			
			
			$noHeadline = $noNavbar = $noFooterbar = $customOutput = false;
			
			
			$page = new Page();
			
			$page->Title = $title;
			$page->Content = $content;
			
			$content = $page->Content;
			
			/*
			 * First check if the page contains any scripts
			 */
			$scripts = preg_match("/<?php(.+?)?>/msu",$content);
			if($scripts > 0 && (!User::GetCurrentUser() || !User::GetCurrentUser()->HasPermission("SCRIPTING"))) {
				throw new AuthorizationMissingException("You are not permitted to preview pages with scripts");
			}
				
			$content = $page->Render($noHeadline, $noNavbar, $noFooterbar, $customOutput);
			
			if(!$customOutput) {
				$parseDown = new \ParsedownExtra;
				$content = $parseDown->text($content);
				$content = str_replace("<table>","<table class='table table-bordered'>",$content);
			}
			
			$page->Content = $content;
			$page->LogModified = new Log();
			$page->LogModified->User = User::GetCurrentUser();
			$page->LogModified->Timestamp = new \DateTime();
			$page->LogCreated = $page->LogModified;
			
			if(!$customOutput) {
				$this->Status = 200;
				$this->Message = "Preview created";
				$this->Data = ["page" => $page, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar];
			} else {
				$this->Data = $page->Content;
			}
		}
	}
?>