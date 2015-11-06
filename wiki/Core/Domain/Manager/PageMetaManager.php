<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\PageMetaFactory;
	use Wiki\Domain\Page;
	use Wiki\Domain\PageMeta;
	use Wiki\Domain\User;

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PageMetaManager extends DomainManager
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
			
			$sqlObject = "SELECT pagemeta_id, status, checksum, page_id, user_id, `data` FROM %PREFIX%pagemeta WHERE pagemeta_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = PageMetaFactory::GetInstance();
			
			$object = new PageMeta();
			
			$this->AddToCache($object, $id);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();

			if($object->User != null)
			{
				$this->AddToCache($object, $object->Page->ID.":".$object->User->ID, "pageuser");
			}
			else
			{
				$this->AddToCache($object, $object->Page->ID, "page");
			}
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByPageAndUser(Page $page, User $user) {
			$fromCache = $this->GetFromCache($page->ID.":".$user->ID, "pageuser");
		  
		  if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT pagemeta_id, status, checksum, page_id, user_id, `data` FROM %PREFIX%pagemeta WHERE status = 100 AND page_id = :page AND user_id = :user";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["page" => $page, "user" => $user]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = PageMetaFactory::GetInstance();
			
			$object = new PageMeta();
			
			$objectFactory->FromDataRow($object, $rowObject);

			$this->AddToCache($object, $page->ID.":".$user->ID, "pageuser");
			$this->AddToCache($object, $rowObject->pagemeta_id->Integer);
		
			$stmObject->Close();
			
			return $object;
		}

		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetGlobalByPage(Page $page) {
			$fromCache = $this->GetFromCache($page->ID, "page");

			if($fromCache) return $fromCache;

			$db = DatabaseConnection::GetInstance();

			$sqlObject = "SELECT pagemeta_id, status, checksum, page_id, user_id, `data` FROM %PREFIX%pagemeta WHERE status = 100 AND page_id = :page AND user_id IS NULL";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["page" => $page]);

			if(!$rowObject) {
				return null;
			}

			$objectFactory = PageMetaFactory::GetInstance();

			$object = new PageMeta();

			$this->AddToCache($object, $rowObject->pagemeta_id->Integer);

			$objectFactory->FromDataRow($object, $rowObject);
			$this->AddToCache($object, $object->ID, "page");

			$stmObject->Close();

			return $object;
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
				self::$instance = new PageMetaManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>