<?php
	namespace Wiki\Domain;
	
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Exception\NotAuthorizedToCreateOrEditPagesWithScriptsException;
	
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
			
			return parent::Save();
		}
		
		public function Delete() {
			if($this->id === 1) {
				throw new \Exception("You cannot delete the 'Homepage' page");
			}
			
			return parent::Delete();
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
		 * @field visibility
		 */
		protected $visiblity;
		
		/**
		 * @field manipulation
		 */
		protected $manipulation;
		
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
			$name = str_replace([" ","\t"]," ",$title);
			$name = str_replace(["Ä", "ä", "Ö", "ö", "Ü", "ü", "ß", "Á","á","À","à","Ã","ã","É","é","È","è","Ó","ó","Ò","ò","Õ","õ","Í","í","Ì","ì","Ú","ú","Ù","ù","Ñ","ñ"],
								["Ae","ae","Oe","oe","Ue","ue","ss","A","a","A","a","A","a","E","e","E","e","O","o","O","o","O","o","I","i","I","i","U","u","U","u","N","n"],
								$name);
								
			$name = trim($name);
			
			$name = str_replace(" ","_",$name);
			
			// Remove all non-allowed characters
			$nameClean = null;
			
			for($p = 0; $p < strlen($name); $p++) {
				$char = ord(substr($name,$p,1));
				
				if(($char >= 48 && $char <=57) || ($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122) || $char == 95) {
					$nameClean .= substr($name,$p,1);
				}
			}
			
			while(strpos($name,"__") !== false) {
				$name = str_replace("__","_",$name);
			}
			
			return $nameClean;
		}
		
		public static function CheckForDuplicatePageName($name) {
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
		}
		
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