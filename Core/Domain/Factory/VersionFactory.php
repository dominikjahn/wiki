<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class VersionFactory extends DomainFactory
	{
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function FromDataRow(Domain $object, DatabaseRow $row) {
			$pageManager = PageManager::GetInstance();
			
			$object->ID = $row->version_id->Integer;
			$object->Status = $row->status->Integer;
			$object->Page = $pageManager->GetByID($row->page_id->Integer);
			$object->Title = $row->title->String;
			$object->Content = $row->content->String;
			$object->Summary = $row->summary->String;
			$object->MinorEdit = $row->minor_edit->Boolean;
			
			$object->IsLoadedFromDatabase = true;
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
				self::$instance = new VersionFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>