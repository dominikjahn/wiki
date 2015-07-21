<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\CategoryPageFactory;
	use Wiki\Domain\Category;
	use Wiki\Domain\Page;
	use Wiki\Domain\CategoryPage;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class CategoryPageManager extends DomainManager
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
			
			$sqlObject = "SELECT categorypage_id, status, checksum, category_id, page_id, alias FROM %PREFIX%categorypage WHERE category_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = CategoryPageFactory::GetInstance();
			
			$object = new CategoryPage();
			
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
		public function GetByPage(Page $page) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT categorypage_id, status, checksum, category_id, page_id, alias FROM %PREFIX%categorypage WHERE status = 100 AND page_id = :page";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read(["page" => $page]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = CategoryPageFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new CategoryPage();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				$objects[] = $object;
			}
		
			$stmObjects->Close();
			
			return $objects;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByCategory(Category $category) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT categorypage_id, status, checksum, category_id, page_id, alias FROM %PREFIX%categorypage WHERE status = 100 AND category_id = :category";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read(["category" => $category]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = CategoryPageFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new CategoryPage();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				$objects[] = $object;
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
				self::$instance = new CategoryPageManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>