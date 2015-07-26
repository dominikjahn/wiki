<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\CategoryManager;
	use Wiki\Domain\Manager\CategoryPageManager;
	use Wiki\Exception\NotAuthorizedToCreateOrEditPagesWithScriptsException;
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
				throw new NotAuthorizedToCreateOrEditPagesWithScriptsException();
			}
			
			$duplicateName = self::NameTaken($this->Name);
				
			if($duplicateName && $duplicateName->ID != $this->ID) {
				throw new \Exception("The name is already taken");
			}
			
			$success = parent::Save();
			
			if(!$success) {
				return false;
			}
			
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
			
			
			return true;
		}
		
		public function Delete() {
			if($this->id === 1) {
				throw new \Exception("You cannot delete the 'Homepage' page");
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
			return StringTools::NormailzeString($title);
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