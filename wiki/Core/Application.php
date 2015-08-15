<?php
	namespace Wiki;
	
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	use Wiki\Tools\Request;
	
	class Application {
		
		  //
		 // CONSTRUCTOR
		//
		
		private function __construct() {
		
		}
		
		  //
		 // METHODS
		//
		
		public function Run() {
			date_default_timezone_set(TIMEZONE);
			
			header("Content-Type: application/json; charset=UTF-8");
			http_response_code(200);
			Request::GetInstance();
			$command = (isset($_GET["command"]) ? $_GET["command"] : null);
			
			if(!$command)
			{
				print json_encode((object) ["status" => 0, "message" => "Invalid command"]);
				return;
			}
			
			/*
			 * Check login information (currently using HTTP Basic authentification, which is not secure at all, but for now that's good enough)
			 */
			
			$loginname = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
			$password = (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null);
			
			$userManager = UserManager::GetInstance();
			$user = null;
			
			if($loginname && $password) {
				$user = $userManager->GetByLoginname($loginname);
				
				if(!$user || $user->Status === 0 || !$user->MatchPassword($password)) {
					$user = null;
				}
			}
			
			if(!$user) {
				$user = $userManager->GetByID(1);
				
				if(!$user || $user->Status === 0) {
					throw new GuestUserDeactivatedException();
				}
			}
			
			User::SetCurrentUser($user);
			
			$commandClass = "Wiki\\Views\\".$command;
			$status = 500;
			
			try {
				if(!WikiClassLoadable($commandClass)) {
					$status = 501;
					
					$response = json_encode(["status" => 501, "message" => "This command is not valid"]);
					
				} else {
					$response = new $commandClass();
					$response->Run();
					$status = $response->Status;
				}
				
				print $response;
			} finally {
				http_response_code(200);
				
				// Close the database connection
				DatabaseConnection::GetInstance()->Close();
			}
			
		}
		
		  //
		 // FUNCTIONS
		//
		
		public static function Main() {
			$app = self::GetInstance();
			$app->Run();
		}
		
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new Application;
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>