<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Domain\Manager\CategoryManager;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetPages extends Response
	{
		public function Run() {
			$keywords = $_GET["keywords"];
		
			$pages = [];
			$pageManager = PageManager::GetInstance();
			$catManager = CategoryManager::GetInstance();
			
			$filter = [];
			
			$instring = false;
			$backslashes = 0;
			
			$keyword = null;
			
			for($p = 0; $p < strlen($keywords); $p++) {
				$char = $keywords[$p];
				
				switch($char) {
					case "\"":
						
						if($instring == "\"" && $backslashes%2===0) {
							$instring = null;
							$keyword .= "\"";
						} else if(!$instring) {
							$instring = "\"";
							$keyword .= "\"";
						} else {
							$keyword .= "\"";
						}
						
						$backslashes = 0;
						
						break;
						
					case "'":
						
						if($instring == "'" && $backslashes%2===0) {
							$instring = null;
							$keyword .= "\"";
						} else if(!$instring) {
							$instring = "'";
							$keyword .= "\"";
						} else {
							$keyword .= "'";
						}
						
						$backslashes = 0;
						
						break;
						
					case "`":
						
						if($instring == "`" && $backslashes%2===0) {
							$instring = null;
							$keyword .= "\"";
						} else if(!$instring) {
							$instring = "`";
							$keyword .= "\"";
						} else {
							$keyword .= "`";
						}
						
						$backslashes = 0;
						
						break;
						
					case "\\":
						
						$backslashes++;
						
						break;
						
					case " ":
					case "\t":
					case "\r":
					case "\n":
						
						if($instring) {
							$keyword .= $char;
						} else {
							$filters[] = $keyword;
							$keyword = null;
						}
						
						$backslashes = 0;
						
						break;
						
					default:
						
						$keyword .= $char;
						$backslashes = 0;
						
						break;
				}
			}
			
			$filters[] = $keyword;
			
			$inTitleOrContent = [];
			$notInTitleOrContent = [];
			
			$inTitle = [];
			$notInTitle = [];
			
			$inContent = [];
			$notInContent = [];
			
			$inCategories = [];
			$notInCategories = [];
			
			foreach($filters as $filter) {
				if(strpos($filter,":")) {
					$filter = explode(":",$filter,2);
				} else {
					$mode = "any";
					
					if(substr($filter,0,1) == "-") {
						$mode = "-any";
						$filter = substr($filter,1);
						
					}
					$filter = [$mode,$filter];
				}
				
				// Remove " surrounding grouped keywords
				if(substr($filter[1],0,1) == '"' && substr($filter[1],-1,1) == '"') {
					$filter[1] = substr($filter[1],1,-1);
				}
				
				// Wrap with % if keyword doesn't contain it itself
				if(strpos($filter[1],"%") === false) {
					$filter[1] = "%".$filter[1]."%";
				}
				
				// Lower-case keywords
				//$filter[1] = strtolower($filter[1]);
				
				if($filter[0] == "any") {
					$inTitleOrContent[] = $filter[1];
				}
				
				if($filter[0] == "-any" || $filter[0] == "-title") {
					$notInTitleOrContent[] = $filter[1];
				}
				
				if($filter[0] == "title") {
					$inTitle[] = $filter[1];
				}
				
				if($filter[0] == "-title") {
					$notInTitle[] = $filter[1];
				}
				
				if($filter[0] == "content") {
					$inContent[] = $filter[1];
				}
				
				if($filter[0] == "-content") {
					$notInContent[] = $filter[1];
				}
				
				if($filter[0] == "category") {
					$categories = $catManager->SearchByTitle($filter[1]);
					
					if($categories) {
						foreach($categories as $category) {
							$inCategories[] = $category->ID;
						}
					}
				} else if($filter[0] == "-category") {
					$categories = $catManager->SearchByTitle($filter[1]);
					
					if($categories) {
						foreach($categories as $category) {
							$notInCategories[] = $category->ID;
						}
					}
				}
			}
			
			$pages = $pageManager->SearchByFilters($inTitleOrContent, $inTitle, $inContent, $inCategories, $notInTitleOrContent, $notInTitle, $notInContent, $notInCategories);
			
			$this->Status = 200;
			$this->Message = count($pages)." pages found";
			$this->Data = ["pages" => $pages];
		}
	}
?>