<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\PageFactory;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PageManager extends DomainManager
	{
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByID($id) {
			$fromCache = $this->GetFromCache($id);
		  
		  if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT page_id, status, checksum, name, title, content, user_owner_id, group_owner_id, visibility, manipulation FROM %PREFIX%page WHERE page_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = PageFactory::GetInstance();
			
			$object = new Page();
			
			$this->AddToCache($object, $id);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			$this->AddToCache($object, $object->Name, "name");
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByName($name) {
			$fromCache = $this->GetFromCache($name, "name");
		  
		  if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT page_id, status, checksum, name, title, content, user_owner_id, group_owner_id, visibility, manipulation FROM %PREFIX%page WHERE status = 100 AND name = :name";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["name" => $name]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = PageFactory::GetInstance();
			
			$object = new Page();
			
			$this->AddToCache($object, $name, "name");
			$this->AddToCache($object, $rowObject->page_id->Integer);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		public function SearchByFilters($inTitleOrContent, $inTitle, $inContent, $inCategory, $notInTitleOrContent, $notInTitle, $notInContent, $notInCategory) {
			$db = DatabaseConnection::GetInstance();
				
			$sqlObjects = "SELECT page_id, status, checksum, name, title, content, user_owner_id, group_owner_id, visibility, manipulation FROM %PREFIX%page WHERE status = 100 ";
			$params = [];
			
			/*
			 * In title or content
			*/
				
			if(count($inTitleOrContent)) {
				$sqlObjects .= " AND (";
			
				foreach($inTitleOrContent as $k => $keyword) {
					$param = "param".(count($params)+1);
						
					$sqlObjects .= "(title LIKE :".$param." OR content LIKE :".$param.")";
						
					$params[$param] = $keyword;
						
					if($k+1 < count($inTitleOrContent)) $sqlObjects .= " AND ";
				}
			
				$sqlObjects .= ") ";
			}
			
			/*
			 * In title
			 */
			
			if(count($inTitle)) {
				$sqlObjects .= " AND (";
				
				foreach($inTitle as $k => $keyword) {
					$param = "param".(count($params)+1);
					
					$sqlObjects .= "title LIKE :".$param;
					
					$params[$param] = $keyword;
					
					if($k+1 < count($inTitle)) $sqlObjects .= " AND ";
				}
				
				$sqlObjects .= ") ";
			}
			
			/*
			 * Not in title or content
			*/
				
			if(count($notInTitleOrContent)) {
				$sqlObjects .= " AND (";
			
				foreach($notInTitleOrContent as $k => $keyword) {
					$param = "param".(count($params)+1);
						
					$sqlObjects .= "(title NOT LIKE :".$param." AND content NOT LIKE :".$param.")";
						
					$params[$param] = $keyword;
						
					if($k+1 < count($notInTitleOrContent)) $sqlObjects .= " AND ";
				}
			
				$sqlObjects .= ") ";
			}
			
			/*
			 * Not in title
			*/
				
			if(count($notInTitle)) {
				$sqlObjects .= " AND (";
			
				foreach($notInTitle as $k => $keyword) {
					$param = "param".(count($params)+1);
						
					$sqlObjects .= "title NOT LIKE :".$param;
						
					$params[$param] = $keyword;
						
					if($k+1 < count($notInTitle)) $sqlObjects .= " AND ";
				}
			
				$sqlObjects .= ") ";
			}
			
			/*
			 * In content
			 */
			
			if(count($inContent)) {
				$sqlObjects .= " AND (";
				
				foreach($inContent as $k => $keyword) {
					$param = "param".(count($params)+1);
					
					$sqlObjects .= "content LIKE :".$param;
					
					$params[$param] = $keyword;
					
					if($k+1 < count($inContent)) $sqlObjects .= " AND ";
				}
				
				$sqlObjects .= ") ";
			}
			
			/*
			 * Not in content
			 */
			
			if(count($notInContent)) {
				$sqlObjects .= " AND (";
				
				foreach($notInContent as $k => $keyword) {
					$param = "param".(count($params)+1);
					
					$sqlObjects .= "content LIKE :".$param;
					
					$params[$param] = $keyword;
					
					if($k+1 < count($notInContent)) $sqlObjects .= " AND ";
				}
				
				$sqlObjects .= ") ";
			}
			
			/*
			 * In category
			 */
			
			if(count($inCategory)) {
				$sqlObjects .= " AND page_id IN (SELECT page_id FROM categorypage WHERE category_id IN (".join(",",$inCategory)."))";
			}
			
			/*
			 * Not in category
			 */
			
			if(count($notInCategory)) {
				$sqlObjects .= " AND page_id NOT IN (SELECT page_id FROM categorypage WHERE category_id IN (".join(",",$notInCategory)."))";
			}
			//var_dump($sqlObjects, $params);
			
			$stmObjects = $db->Prepare($sqlObjects);
				
			$resObjects = $stmObjects->Read($params);
				
			if(!$resObjects) {
				return null;
			}
				
			$objects = [];
			$objectFactory = PageFactory::GetInstance();
				
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new Page();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				if($object->IsVisible) {
					$objects[] = $object;
				}
			}
			
			$stmObjects->Close();
				
			return $objects;
		}
		
		  //
		 // FUNCTIONS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new PageManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>