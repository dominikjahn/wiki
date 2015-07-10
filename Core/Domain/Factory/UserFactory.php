<?php
	class UserFactory extends DomainFactory
	{
		  //
		 // METHODS
		//
		
		public function FromDataRow(Domain $user, DatabaseRow $row) {
			$user->ID = $row->user_id->Integer;
			$user->Status = $row->status->Integer;
			$user->Loginname = $row->loginname->String;
			$user->Password = $row->password->String;
		}
		
		  //
		 // FUNCTIONS
		//
		
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new UserFactory();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>