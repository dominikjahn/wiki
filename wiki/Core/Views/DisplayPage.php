<?php
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	require_once "Core/ThirdParty/ParseDown.php";
	require_once "Core/ThirdParty/ParsedownExtra.php";
	
	$pagename = $_GET["page"];
	$raw = (isset($_GET["raw"]) ? true : false);
	
	$pageID = $title = $content = $owner_id = $visibility = $lastedit = null;
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	$noHeadline = $noNavbar = $noFooterbar = $customOutput = false;
	
	try {
		$page = PageManager::GetInstance()->GetByName($pagename);
		$currentUser = User::GetCurrentUser();
		
		if(!$page || $page->status === 0) {
			$data->status = 404;
			$data->message = "The page was not found";
		} else if(!$page->IsVisible) {
			$data->status = 401;
			$data->message = "You are not authorized to see the content on this page";
			//throw new \Exception("You are not authorized to see the content on this page");
		} else {
			Page::SetCurrentPage($page);
			
			$content = $page->Content;
			if(!$raw) {
				$content = ParseWiki($content, $noHeadline, $noNavbar, $noFooterbar, $customOutput);
				
				$parseDown = new \ParsedownExtra;
				$content = $parseDown->text($content);
				$content = str_replace("<table>","<table class='table table-bordered'>",$content);
			}
			
			$page->Content = $content;
			
			if(!$customOutput) {
				$data->status = 200;
				$data->message = "Page found";
				$data->page = $page;
				$data->no_headline = $noHeadline;
				$data->no_navbar = $noNavbar;
				$data->no_footerbar = $noFooterbar;

				//(object) ["pageID" => $page->ID, "name" => $page->Name, "title" => $page->Title, "content" => $content, "visibility" => $page->Visibility, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar, "lastedit" => $lastedit];
			} else {
				$data = $page->Content;
			}
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	if(!$customOutput) {
		print json_encode($data);
	} else {
		print $data;
	}
	
	function ParseWiki($text, &$noHeadline, &$noNavbar, &$noFooterbar, &$customOutput) {
		$parsed = $text;
		
		/*
		 * NoParse
		 */
		 
		# <Wiki:NoParse>Content</Wiki:NoParse>
		$blocks = [];
		$noparse = [];
		preg_match_all("/<Wiki:NoParse>(?<content>.+?)<\/Wiki:NoParse>/muis",$parsed,$blocks, PREG_SET_ORDER);
			
		foreach($blocks as $block)
		{
			$wrapper = $block[0];
			$content = $block["content"];
			
			$blockID = md5($content.microtime(true));
			
			$noparse[$blockID] = $content;
			
			$parsed = str_replace($wrapper, '<!-- NOPARSE:'.$blockID.' -->', $parsed);
		}
		
		/*
		 * Scripts
		 */
		
		$scripts = array();
		preg_match_all("/<?php(.+?)?>/msu",$parsed,$scripts,PREG_SET_ORDER);
		
		if(count($scripts)) {
			$parsed = EvalScripts($parsed, $scripts);
		}
		
		/*
		 * Links
		 */
		 
		$pageManager = PageManager::GetInstance();
		$currentUser = User::GetCurrentUser();
		 
		# Basic link <Wiki:Link page="Name_of_page"/>
		$links = [];
		preg_match_all("/<Wiki:Link\s*page=['\"](?<page>(.+?))['\"]\s*\/>/muis",$parsed,$links, PREG_SET_ORDER);

		foreach($links as $link)
		{
			$wrapper = $link[0];
			$text = $link["page"];
			$name = Page::NormalizeTitle($link["page"]);
			
			$page = $pageManager->GetByName($name);
			
			$link = null;
			$click = null;
			$class = null;
			
			if(!$page) {
				$link = $name.".html#NewPage";
				$click = 'return GoToPage(\''.$name.'\')';
				$class = "link-newpage";
			} else if($page && (($page->Visibility == Page::VIS_PROTECTED && !$currentUser) || ($page->Visibility == Page::VIS_PRIVATE && $page->Owner->ID != $currentUser->ID))) {
				$link = $page->Name.".html";
				$click = 'return GoToPage(\''.$page->Name.'\')';
				$text = $page->Title;
				$class = "link-notauthorized";
			} else {
				$link = $page->Name.".html";
				$click = 'return GoToPage(\''.$page->Name.'\')';
				$text = $page->Title;
				$class = "link-gotopage";
			}
			
			$parsed = str_replace($wrapper, '<a href="'.$link.'" onclick="'.$click.'" class="'.$class.'">'.$text.'</a>', $parsed);
		}
		 
		# Link with alternative text <Wiki:Link page="Name_of_page">Text</Wiki:Link>
		$links = array();
		preg_match_all("/<Wiki:Link\s*page=['\"](?<page>([a-zA-Z0-9_]+))['\"]\s*>(?<text>.+?)<\/Wiki:Link>/muis",$parsed,$links, PREG_SET_ORDER);
			
		foreach($links as $link)
		{
			$wrapper = $link[0];
			$name = $link["page"];
			$text = $link["text"];
			
			$page = $pageManager->GetByName($name);
			
			$link = null;
			$click = null;
			$class = null;
			
			if(!$page) {
				$link = $name."#NewPage";
				$click = 'return GoToPage(\''.$name.'\')';
				$class = "link-newpage";
			} else if($page && (($page->Visibility == Page::VIS_PROTECTED && !$currentUser) || ($page->Visibility == Page::VIS_PRIVATE && $page->Owner->ID != $currentUser->ID))) {
				$link = "#";
				$click = 'return GoToPage(\''.$page->Name.'\')';
				$class = "link-notauthorized";
			} else {
				$link = $page->Name.".html";
				$click = 'return GoToPage(\''.$page->Name.'\')';
				$class = "link-gotopage";
			}
			
			$parsed = str_replace($wrapper, '<a href="'.$link.'" onclick="'.$click.'" class="'.$class.'">'.$text.'</a>', $parsed);
		}
		
		/*
		 * Icons
		 */
		 
		# <Wiki:Icon name="Icon"/>
		$icons = array();
		preg_match_all("/<Wiki:Icon\s*name=['\"](?<name>[a-zA-Z0-9\-]+)['\"]\s*\/>/muis",$parsed,$icons, PREG_SET_ORDER);
			
		foreach($icons as $icon)
		{
			$wrapper = $icon[0];
			$name = $icon["name"];
			
			$parsed = str_replace($wrapper, '<span class="glyphicon glyphicon-'.$name.'" aria-hidden="true"></span>', $parsed);
		}
		
		/*
		 * Panels
		 */
		 
		# Basic panel <Wiki:Panel>Content</Wiki:Panel>
		$panels = array();
		preg_match_all("/<Wiki:Panel\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
		foreach($panels as $panel)
		{
			$wrapper = $panel[0];
			$content = $panel["content"];
			
			$panel = '<div class="panel panel-default"><div class="panel-body">'.$content.'</div></div>';
			
			$parsed = str_replace($wrapper, $panel, $parsed);
		}
		 
		# Basic panel with title: <Wiki:Panel title="Title">Content</Wiki:Panel>
		$panels = array();
		preg_match_all("/<Wiki:Panel\s*title=['\"](?<title>.+?)['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
		foreach($panels as $panel)
		{
			$wrapper = $panel[0];
			$content = $panel["content"];
			$title = $panel["title"];
			
			$panel = '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div><div class="panel-body">'.$content.'</div></div>';
			
			$parsed = str_replace($wrapper, $panel, $parsed);
		}
		
		# Basic panel with theme: <Wiki:Panel theme="theme">Content</Wiki:Panel>
		$panels = array();
		preg_match_all("/<Wiki:Panel\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
		foreach($panels as $panel)
		{
			$wrapper = $panel[0];
			$content = $panel["content"];
			$theme = $panel["theme"];
			
			$panel = '<div class="panel panel-'.$theme.'"><div class="panel-body">'.$content.'</div></div>';
			
			$parsed = str_replace($wrapper, $panel, $parsed);
		}
		 
		# Basic panel with title and theme: <Wiki:Panel theme="theme" title="Title">Content</Wiki:Panel>
		$panels = array();
		preg_match_all("/<Wiki:Panel\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*title=['\"](?<title>.+?)['\"]\s*>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
		foreach($panels as $panel)
		{
			$wrapper = $panel[0];
			$content = $panel["content"];
			$title = $panel["title"];
			$theme = $panel["theme"];
			
			$panel = '<div class="panel panel-'.$theme.'"><div class="panel-heading"><h3 class="panel-title">'.$title.'</h3></div><div class="panel-body">'.$content.'</div></div>';
			
			$parsed = str_replace($wrapper, $panel, $parsed);
		}
		 
		/*
		 * Alerts
		 */
		
		# <Wiki:Alert theme="theme">Content</Wiki:Alert>
		$alerts = array();
		preg_match_all("/<Wiki:Alert\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Alert>/muis",$parsed,$alerts, PREG_SET_ORDER);
			
		foreach($alerts as $alert)
		{
			$wrapper = $alert[0];
			$content = $alert["content"];
			$theme = $alert["theme"];
			
			$alert = '<div class="alert alert-'.$theme.'" role="alert">'.$content.'</div>';
			
			$parsed = str_replace($wrapper, $alert, $parsed);
		}
		 
		/*
		 * Labels
		 */
		
		# <Wiki:Label theme="theme">Content</Wiki:Label>
		$labels = array();
		preg_match_all("/<Wiki:Label\s*theme=['\"](?<theme>([a-zA-Z]+))['\"]\s*>(?<content>.+?)<\/Wiki:Label>/muis",$parsed,$labels, PREG_SET_ORDER);
		
		foreach($labels as $label)
		{
			$wrapper = $label[0];
			$content = $label["content"];
			$theme = $label["theme"];
			
			$label = '<div class="label label-'.$theme.'">'.$content.'</div>';
			
			$parsed = str_replace($wrapper, $label, $parsed);
		}
		
		/*
		 * Remove categories
		 */
		$parsed = preg_replace("/<Wiki:Category\s*>(.+?)<\/Wiki:Category>/muis", null, $parsed);
		$parsed = preg_replace("/<Wiki:Category\s*as=['\"](.+?)['\"]\s*>(.+?)<\/Wiki:Category>/muis", null, $parsed);
		
		/*
		 * Don't show a headline
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoHeadline\s*\/>/msu",$parsed, $match)) {
			$noHeadline = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Don't show a navbar
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoNavbar\s*\/>/msu",$parsed, $match)) {
			$noNavbar = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Don't show a footerbar
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoFooterbar\s*\/>/msu",$parsed, $match)) {
			$noFooterbar = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Deactivate JSON-output of page, instead let script take full control over output
		 */
		
		$match = [];
		if(preg_match("/<Wiki:CustomOutput\s*\/>/msu",$parsed, $match)) {
			$customOutput = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Re-insert <Wiki:NoParse>
		 */
		
		foreach($noparse as $blockID => $content) {
			$parsed = str_replace('<!-- NOPARSE:'.$blockID.' -->', $noparse[$blockID], $parsed);
		}
		
		return $parsed;
	}
	
	function EvalScripts($parsed, $scripts) {
		$pagecode = "ob_start();\t\t/* Page code starts here */\t";
				
		// Search for the first occurence of \<?php
		$start = strpos($parsed,"<?php");
			
		if($start > 0) {
			$cstart = substr($parsed,0,$start);
			$pagecode .= "echo \"".addslashes($cstart)."\";";
		}
		
		$pos = $start + 5;
		$a = 0;
			
		while(true) {
			// Look for the corresponding \?\>
			$end = strpos($parsed, "?>", $pos);

			$code = substr($parsed, $pos, $end - $pos);

			$pagecode .= $code;

			// Next, check if there is more code
			if($end+2 == strlen($parsed)) {
				break;
			}
					  
			// Then, we check if it's only static content
			$start = strpos($parsed,"<?php", $end+2);

			if(!$start) {
				$ccontent = substr($parsed, $end+2);

				$pagecode .= "echo \"".addslashes($ccontent)."\";";
				// The end of the page is reached
				break;
			}

			$pos = strpos($parsed, "<?php", $end + 5) + 5;

			if($pos > $end+2) {
				$ccontent = substr($parsed,$end+2, $pos - $end - 4);
				$pagecode .= "echo \"".addslashes($ccontent)."\";";
			}
		}

		$pagecode .= "\t\t/* Page code ends here */\t\$output = ob_get_clean();\treturn array(\"output\" => \$output, \"result\" => true);";

		$result = eval($pagecode);
			  
		if($result["result"])
		{
			$parsed = $result["output"];
		}
		else
		{
			$lines = explode("\n",$pagecode);
			$codelines = "<?php ";
			
			foreach($lines as $num=>$line)
			{
				$codelines .= "/* ".($num+1)." */ $line\n";
			}
			$codelines .= " ?>";
			
			$parsed = highlight_string($codelines, true);
		}
		
		return $parsed;
	}
?>