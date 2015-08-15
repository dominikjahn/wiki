<?php
namespace Wiki\Domain;

use Wiki\Domain\Manager\PageManager;
use Wiki\Domain\Manager\CategoryManager;
use Wiki\Domain\Manager\CategoryPageManager;
use Wiki\Domain\Manager\PageLinkManager;
use Wiki\Exception\AuthorizationMissingException;
use Wiki\Exception\PagenameAlreadyTakenException;
use Wiki\Exception\CannotDeleteHomepageException;
use Wiki\Tools\StringTools;

/**
 * @table page
 * @author Dominik Jahn <dominik1991jahn@gmail.com>
 * @version 0.1
 * @since 0.1
 */
class Page extends Domain
{
	//
	// CONSTRUCTOR
	//

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	public function __construct() {
			
	}

	//
	// METHODS
	//

	public function jsonSerialize() {
			
		return [
		"page_id" => $this->id,
		"name" => $this->name,
		"title" => $this->title,
		"content" => $this->content,
		"visibility" => $this->visibility,
		"manipulation" => $this->manipulation,
		"owner" => $this->owner,
		"group" => $this->group,
		"categories" => $this->Categories,
		"can_edit" => $this->CanEdit,
		"last_edit" => [
		"timestamp" => $this->logModified->Timestamp->format("Y-m-d H:i:s"),
		"user" => $this->logModified->User->Loginname
		],
		"created" => [
		"timestamp" => $this->logCreated->Timestamp->format("Y-m-d H:i:s"),
		"user" => $this->logCreated->User->Loginname
		]
		];
	}

	public function Save() {
			
		$scripts = preg_match("/<?php(.+?)?>/msu",$this->content);
			
		if($scripts > 0 && (!User::GetCurrentUser() || !User::GetCurrentUser()->HasPermission("SCRIPTING"))) {
			throw new AuthorizationMissingException("You are not permitted to create or edit pages with scripts");
		}
			
		$duplicateName = self::NameTaken($this->Name);

		if($duplicateName && $duplicateName->ID != $this->ID) {
			throw new PagenameAlreadyTakenException();
		}
			
		$success = parent::Save();
			
		if(!$success) {
			return false;
		}

		  //
		 // CATEGORIES
		//
		
		// Get a list of all categories
		$categories = [];
		$matches = [];
		preg_match_all("/<Wiki:Category\s*>(?<category>.+?)<\/Wiki:Category>/muis", $this->content, $matches, PREG_SET_ORDER);
			
		foreach($matches as $match) {
			$name = self::NormalizeTitle($match["category"]);
			$title = $match["category"];
			$categories[$name] = (object) ["title" => $title, "alias" => ""];
		}
			
		$matches = [];
		preg_match_all("/<Wiki:Category\s*as=['\"](?<alias>.+?)['\"]\s*>(?<category>.+?)<\/Wiki:Category>/muis", $this->content, $matches, PREG_SET_ORDER);
			
		foreach($matches as $match) {
			$name = self::NormalizeTitle($match["category"]);
			$title = $match["category"];
			$categories[$name] = (object) ["title" => $title, "alias" => $match["alias"]];
		}
			
		$catManager = CategoryManager::GetInstance();
			
		//var_dump($categories);
		// Get a list of all current categories and deactivate
		if($this->Categories) {
			foreach($this->Categories as $catpage) {
				//echo $catpage->Category->Name." is already assigned to page with alias ".$catpage->Alias."\n";
				//var_dump($catpage->Category->Name, $catpage->Alias, $categories);
					
				if(!array_key_exists($catpage->Category->Name, $categories)) {
					//echo "Not assigned anymore. Delete\n";
					$catpage->Delete();
				} else if($catpage->Alias != $categories[$catpage->Category->Name]->alias) {
					//echo "Still assigned, but alias changed. Change\n";
					$catpage->Alias = $categories[$catpage->Category->Name]->alias;
					$catpage->Save();
					unset($categories[$catpage->Category->Name]);
				} else {
					//echo "Unchanged\n";
					unset($categories[$catpage->Category->Name]);
				}
			}
		}
			
		// Add new categories
		foreach($categories as $name => $cat) {
			//echo $name." (".$cat->title.") is not assigned\n";

			$category = $catManager->GetByName($name);

			if(!$category || $category->Status === 0) {
				//echo "Is a new category\n";
				$category = new Category();
				$category->Status = 100;
				$category->Name = $name;
				$category->Title = $cat->title;
				$category->Save();
			}

			$catpage = new CategoryPage();
			$catpage->Status = 100;
			$catpage->Category = $category;
			$catpage->Page = $this;
			$catpage->Alias = $cat->alias;
			$catpage->Save();
			//echo "Storing association\n\n";
		}
			
		  //
		 // PAGE LINKS
		//
		
		// Get a list of all links
		$links = [];
		$matches = [];
		preg_match_all("/<Wiki:Link\s*page=['\"](?<page>(.+?))['\"]\s*\/>/muis", $this->content, $matches, PREG_SET_ORDER);
			
		foreach($matches as $match) {
			$name = self::NormalizeTitle($match["page"]);
			$title = $match["page"];
			$links[$name] = (object) ["title" => $title, "text" => ""];
		}
			
		$matches = [];
		preg_match_all("/<Wiki:Link\s*page=['\"](?<page>(.+?))['\"]\s*>(?<text>.+?)<\/Wiki:Link>/muis", $this->content, $matches, PREG_SET_ORDER);
			
		foreach($matches as $match) {
			$name = self::NormalizeTitle($match["page"]);
			$title = $match["page"];
			$links[$name] = (object) ["title" => $title, "text" => $match["text"]];
		}
			
		$pageManager = PageManager::GetInstance();
			
		//var_dump($categories);
		// Get a list of all links from this page
		if($this->OutgoingLinks) {
			foreach($this->OutgoingLinks as $pagelink) {
				//echo $catpage->Category->Name." is already assigned to page with alias ".$catpage->Alias."\n";
				//var_dump($catpage->Category->Name, $catpage->Alias, $categories);
					
				if(!array_key_exists($pagelink->To->Name, $links)) {
					//echo "Not assigned anymore. Delete\n";
					$pagelink->Delete();
				} else if($pagelink->Text != $links[$pagelink->To->Name]->text) {
					//echo "Still assigned, but alias changed. Change\n";
					$pagelink->Text = $links[$pagelink->To->Name]->text;
					$pagelink->Save();
					unset($links[$pagelink->To->Name]);
				} else {
					//echo "Unchanged\n";
					unset($links[$pagelink->To->Name]);
				}
			}
		}
			
		// Add new links
		foreach($links as $name => $link) {
			//echo $name." (".$cat->title.") is not assigned\n";
		
			$page = $pageManager->GetByName($name);
		
			if(!$page || $page->Status === 0) {
				continue; // Page doesn't exist
			}
		
			$pagelink = new PageLink();
			$pagelink->Status = 100;
			$pagelink->From = $this;
			$pagelink->To = $page;
			$pagelink->Text = $link->text;
			$pagelink->Save();
			//echo "Storing association\n\n";
		}
		
		return true;
	}

	public function Delete() {
		if($this->id === 1) {
			throw new CannotDeleteHomepageException();
		}
			
		return parent::Delete();
	}

	protected function CalculateChecksum() {
		return md5($this->Status.$this->name.$this->title.$this->content.$this->owner->ID.$this->visibility.$this->manipulation);
	}

	protected function GetIsVisible() {
		$currentUser = User::GetCurrentUser();
			
		if(
				// Page is protected and current user is 'guest'
				($this->visibility == self::VIS_PROTECTED && $currentUser->ID === 1) ||
				// Page is private and current user is not the owner
				($this->visibility == self::VIS_PRIVATE && $this->Owner->ID <> $currentUser->ID) ||
				// Page is group private and current user is not in the group
				($this->visibility == self::VIS_GROUPPRIVATE && !$currentUser->IsInGroup($this->group))
		) {
			return false;
		}
			
		return true;
	}

	protected function GetCanEdit() {
		$currentUser = User::GetCurrentUser();
			
		if(
				// User needs to be registered and current user is 'guest'
				($this->manipulation != self::MAN_EVERYONE && $currentUser->ID === 1) ||
				// User needs to be the owner
				($this->manipulation == self::MAN_OWNER && $currentUser->ID != $this->Owner->ID) ||
				// User needs to be in the group
				($this->manipulation == self::MAN_GROUP && !$currentUser->IsInGroup($this->group))
		) {
			return false;
		}
			
		return true;
	}
		
	public function Render(&$noHeadline, &$noNavbar, &$noFooterbar, &$customOutput) {
		$parsed = $this->content;

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
			$parsed = $this->EvalScripts($parsed, $scripts);
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
				$link = "NewPage.html?title=".$text;
				$click = 'return DisplayNewPageForm(null,decodeURIComponent(\''.rawurlencode($text).'\'))';
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
		preg_match_all("/<Wiki:Link\s*page=['\"](?<page>(.+?))['\"]\s*>(?<text>.+?)<\/Wiki:Link>/muis",$parsed,$links, PREG_SET_ORDER);
		
		foreach($links as $link)
		{
			$wrapper = $link[0];
			$title = $link["page"];
			$name = self::NormalizeTitle($title);
			$text = $link["text"];
				
			$page = $pageManager->GetByName($name);
				
			$link = null;
			$click = null;
			$class = null;
				
			if(!$page) {
				$link = "NewPage.html?title=".$title;
				$click = 'return DisplayNewPageForm(null,decodeURIComponent(\''.rawurlencode($title).'\'))';
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
		
		/* Exclude <script> and <style> */
		$blocks = [];
		$nomarkdown = [];
		preg_match_all("/<(script|style).*?>.+?<\/\1>/muis",$parsed,$blocks, PREG_SET_ORDER);
		
		foreach($blocks as $block)
		{
			$wrapper = $block[0];
				
			$blockID = md5($wrapper.microtime(true));
				
			$nomarkdown[$blockID] = $wrapper;
				
			$parsed = str_replace($wrapper, '<!-- NOMARKDOWN:'.$blockID.' -->', $parsed);
		}

		/*
		 * Re-insert <Wiki:NoParse>
		*/

		foreach($noparse as $blockID => $content) {
			$parsed = str_replace('<!-- NOPARSE:'.$blockID.' -->', $noparse[$blockID], $parsed);
		}
		
		if(!$customOutput) {
			require_once "Core/ThirdParty/ParseDown.php";
			require_once "Core/ThirdParty/ParsedownExtra.php";
			
			$parseDown = new \ParsedownExtra;
			$parsed = $parseDown->text($parsed);
			$parsed = str_replace("<table>","<table class='table table-bordered'>",$parsed);
		}
		
		/*
		 * Re-insert <script>/<style>
		 */
			
		foreach($nomarkdown as $blockID => $block) {
			$parsed = str_replace('<!-- NOMARKDOWN:'.$blockID.' -->', $block, $parsed);
		}

		return $parsed;
	}
		
	private function EvalScripts($parsed, $scripts) {
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
				$ccontent = substr($parsed,$end+2, $pos - $end - 7);
				$pagecode .= "echo \"".addslashes($ccontent)."\";";
			}
		}

		$pagecode .= "\t\t/* Page code ends here */\t\$output = ob_get_clean();\treturn array(\"output\" => \$output, \"result\" => true);";

		$result = eval($pagecode);
			
		if($result["result"])
		{
			$parsed = stripslashes($result["output"]);
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
	
	//
	// ATTRIBUTES
	//

	/**
	 * @field name
	 */
	protected $name;

	/**
	 * @field title
	 */
	protected $title;

	/**
	 * @field content
	 */
	protected $content;

	/**
	 * @field user_owner_id
	 */
	protected $owner;

	/**
	 * @field group_owner_id
	 */
	protected $group;

	/**
	 * @field visibility
	 */
	protected $visiblity;

	/**
	 * @field manipulation
	 */
	protected $manipulation;

	/**
	 *
	 */
	protected $categories;
	
	/**
	 * 
	 */
	protected $outgoingLinks;
	
	/**
	 * 
	 */
	protected $incomingLinks;

	//
	// GETTERS / SETTERS
	//

	# Name

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetName() {
		return $this->name;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetName($value) {
		$this->name = $value;
	}

	# Title

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetTitle() {
		return $this->title;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetTitle($value) {
		$this->title = $value;
	}

	# Content

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetContent() {
		return $this->content;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetContent($value) {
		$this->content = $value;
	}

	# Owner

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetOwner() {
		return $this->owner;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetOwner(User $value) {
		$this->owner = $value;
	}

	# Group

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetGroup() {
		return $this->group;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetGroup(Group $value) {
		$this->group = $value;
	}

	# Visibility

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetVisibility() {
		return $this->visiblity;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetVisibility($value) {
		$this->visiblity = $value;
	}

	# Manipulation

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetManipulation() {
		return $this->manipulation;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetManipulation($value) {
		$this->manipulation = $value;
	}

	# Categories

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetCategories() {
		if(!$this->categories && $this->ID) {
			$this->categories = CategoryPageManager::GetInstance()->GetByPage($this);
		}
			
		return $this->categories;
	}
	
	# OutgoingLinks
	
	protected function GetOutgoingLinks() {
		if(!$this->outgoingLinks && $this->ID) {
			$this->outgoingLinks = PageLinkManager::GetInstance()->GetByFromPage($this);
		}
		
		return $this->outgoingLinks;
	}
	
	# IncomingLinks
	
	protected function GetIncomingLinks() {
		if(!$this->incomingLinks && $this->ID) {
			$this->incomingLinks = PageLinkManager::GetInstance()->GetByToPage($this);
		}
		
		return $this->incomingLinks;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	//protected function SetCategories($value) {
	//	$this->categories = $value;
	//}

	//
	// FUNCTIONS
	//

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	public static function GetCurrentPage() {
		return self::$currentPage;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	public static function SetCurrentPage(Page $value) {
		self::$currentPage = $value;
	}

	//
	// VARIABLES
	//

	private static $currentPage;

	//
	// FUNCTIONS
	//

	public static function NormalizeTitle($title) {
		return StringTools::NormalizeString($title);
	}

	public static function NameTaken($name) {
		$pageManager = PageManager::GetInstance();
		$page = $pageManager->GetByName($name);

		if(!$page) {
			return false;
		}

		return $page;
	}

	/*public static function CheckForDuplicatePageName($name) {
	 $origName = $name;
		
	$attempt = 0;
		
	$pageManager = PageManager::GetInstance();
		
	while(true) {
	$page = $pageManager->GetByName($name);

	if(!$page) {
	break;
	} else {
	$attempt++;
		
	$name = $origName."-".$attempt;
	}
	}
		
	return $name;
	}*/

	//
	// CONSTANTS
	//

	const DB_TABLE = "page";

	const VIS_PUBLIC = "PUBLIC";
	const VIS_PROTECTED = "PROTECTED";
	const VIS_PRIVATE = "PRIVATE";
	const VIS_GROUPPRIVATE = "GROUPPRIVATE";

	const MAN_EVERYONE = "EVERYONE";
	const MAN_REGISTERED = "REGISTERED";
	const MAN_OWNER = "OWNER";
	const MAN_GROUP = "GROUP";
}
?>