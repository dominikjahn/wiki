<?php
	require_once "ThirdParty/ParseDown.php";
	
	$pagename = $_GET["page"];
	$raw = (isset($_GET["raw"]) ? true : false);
	
	$pageID = $title = $content = $owner_id = $visibility = $lastedit = null;
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$sqlPage = "	SELECT `page_id`, `title`, `content`, `owner_id`, `visibility`, `log`.`timestamp` AS `lastedit`
						FROM page
						INNER JOIN log ON object_table = 'page' AND object_id = page_id AND type = 'MODIFY'
						WHERE status = 100 AND name = :name";
		$stmPage = $db->Prepare($sqlPage);
		$rowPage = $stmPage->ReadSingle(["name" => $pagename]);
		
		$pageID = $rowPage->page_id->Integer;
		$title = $rowPage->title->String;
		$content = $rowPage->content->String;
		$owner_id = $rowPage->owner_id->Integer;
		$visibility = $rowPage->visibility->String;
		$lastedit = $rowPage->lastedit->String;
		
		if(!$pageID) {
			$data->status = 404;
			$data->message = "The page was not found";
		} else {
			if(($visibility == "PROTECTED" && !SIGNED_IN) || ($visibility == "PRIVATE" && $owner_id <> SIGNED_IN)) {
				throw new \Exception("You are not authorized to see the content on this page");
			}
			
			$noHeadline = $noNavbar = $noFooterbar = false;
			
			if(!$raw) {
				$content = ParseWiki($content, $noHeadline, $noNavbar, $noFooterbar);
				
				$parseDown = new ParseDown;
				$content = $parseDown->text($content);
			}
			
			$data->status = 1;
			$data->message = "Page found";
			$data->page = (object) ["pageID" => $pageID, "name" => $pagename, "title" => $title, "content" => $content, "visibility" => $visibility, "no_headline" => $noHeadline, "no_navbar" => $noNavbar, "no_footerbar" => $noFooterbar, "lastedit" => $lastedit];
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
	
	
	
	function ParseWiki($text, &$noHeadline, &$noNavbar, &$noFooterbar) {
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
		 
		# Basic link <Wiki:Link page="Name_of_page"/>
		$links = [];
		preg_match_all("/<Wiki:Link page=['\"](?<page>([a-zA-Z0-9_ ]+))['\"]\/>/muis",$parsed,$links, PREG_SET_ORDER);
			
		foreach($links as $link)
		{
			$wrapper = $link[0];
			$page = str_replace(" ","_",$link["page"]);
			
			$parsed = str_replace($wrapper, '<a href="'.$page.'.html" onclick="return GoToPage(\''.$page.'\');">'.$page.'</a>', $parsed);
		}
		 
		# Link with alternative text <Wiki:Link page="Name_of_page">Text</Wiki:Link>
		$links = array();
		preg_match_all("/<Wiki:Link page=['\"](?<page>([a-zA-Z0-9_]+))['\"]>(?<text>.+?)<\/Wiki:Link>/muis",$parsed,$links, PREG_SET_ORDER);
			
		foreach($links as $link)
		{
			$wrapper = $link[0];
			$page = $link["page"];
			$text = $link["text"];
			
			$parsed = str_replace($wrapper, '<a href="'.$page.'.html" onclick="return GoToPage(\''.$page.'\');">'.$text.'</a>', $parsed);
		}
		
		/*
		 * Icons
		 */
		 
		# <Wiki:Icon name="Icon"/>
		$icons = array();
		preg_match_all("/<Wiki:Icon name=['\"](?<name>[a-zA-Z0-9\-]+)['\"]\/>/muis",$parsed,$icons, PREG_SET_ORDER);
			
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
		preg_match_all("/<Wiki:Panel>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
		foreach($panels as $panel)
		{
			$wrapper = $panel[0];
			$content = $panel["content"];
			
			$panel = '<div class="panel panel-default"><div class="panel-body">'.$content.'</div></div>';
			
			$parsed = str_replace($wrapper, $panel, $parsed);
		}
		 
		# Basic panel with title: <Wiki:Panel title="Title">Content</Wiki:Panel>
		$panels = array();
		preg_match_all("/<Wiki:Panel title=['\"](?<title>.+?)['\"]>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
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
		preg_match_all("/<Wiki:Panel theme=['\"](?<theme>([a-zA-Z]+))['\"]>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
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
		preg_match_all("/<Wiki:Panel theme=['\"](?<theme>([a-zA-Z]+))['\"] title=['\"](?<title>.+?)['\"]>(?<content>.+?)<\/Wiki:Panel>/muis",$parsed,$panels, PREG_SET_ORDER);
			
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
		preg_match_all("/<Wiki:Alert theme=['\"](?<theme>([a-zA-Z]+))['\"]>(?<content>.+?)<\/Wiki:Alert>/muis",$parsed,$alerts, PREG_SET_ORDER);
			
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
		preg_match_all("/<Wiki:Label theme=['\"](?<theme>([a-zA-Z]+))['\"]>(?<content>.+?)<\/Wiki:Label>/muis",$parsed,$labels, PREG_SET_ORDER);
		
		foreach($labels as $label)
		{
			$wrapper = $label[0];
			$content = $label["content"];
			$theme = $label["theme"];
			
			$label = '<div class="label label-'.$theme.'">'.$content.'</div>';
			
			$parsed = str_replace($wrapper, $label, $parsed);
		}
		
		/*
		 * Don't show a headline
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoHeadline\/>/msu",$parsed, $match)) {
			$noHeadline = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Don't show a navbar
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoNavbar\/>/msu",$parsed, $match)) {
			$noNavbar = true;
			$parsed = str_replace($match[0],null,$parsed);
		}
		
		/*
		 * Don't show a footerbar
		 */
		
		$match = [];
		if(preg_match("/<Wiki:NoFooterbar\/>/msu",$parsed, $match)) {
			$noFooterbar = true;
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