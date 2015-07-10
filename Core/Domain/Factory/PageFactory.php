<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PageFactory extends DomainFactory
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
			$userManager = UserManager::GetInstance();
			
			$object->ID = $row->page_id->Integer;
			$object->Status = $row->status->Integer;
			$object->Name = $row->name->String;
			$object->Title = $row->title->String;
			$object->Content = $row->content->String;
			$object->Visibility = $row->visibility->String;
			$object->Owner = $userManager->GetByID($row->owner_id->Integer);
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
				self::$instance = new PageFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>