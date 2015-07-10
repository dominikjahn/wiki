<?php
	class PageManager extends DomainManager
	{
		  //
		 // METHODS
		//
		
		public function GetByID($id) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT page_id, status, name, title, content, owner_id, visibility FROM page WHERE status = 100 AND page_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$stmObject) {
				return null;
			}
			
			$objectFactory = PageFactory::GetInstance();
			
			$object = new Page();
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		public function GetByName($name) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT page_id, status, name, title, content, owner_id, visibility FROM page WHERE status = 100 AND name = :name";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["name" => $name]);
			
			if(!$stmObject) {
				return null;
			}
			
			$objectFactory = PageFactory::GetInstance();
			
			$object = new Page();
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		  //
		 // FUNCTIONS
		//
		
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