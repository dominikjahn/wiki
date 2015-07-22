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
					throw new \Exception("The guest user cannot be deleted. Please reactivate the user.");
				}
			}
			
			User::SetCurrentUser($user);
			
			/*
			 * Redirect to command handler
			 */
			
			switch($command)
			{
				/*
				 * Display the content of a specific page
				 */
				case "DisplayPage": require_once "Core/Views/DisplayPage.php"; break;
				
				/*
				 * Save changes to a page
				 */
				case "SavePage": require_once "Core/Views/SavePage.php"; break;
				
				/*
				 * Preview changes to a page
				 */
				case "PreviewPage": require_once "Core/Views/PreviewPage.php"; break;
				
				/*
				 * Get a list of revisions of a specific page
				 */
				case "GetVersions": require_once "Core/Views/GetVersions.php"; break;
				
				/*
				 * Get a specific version
				 */
				//case "DisplayVersion": require_once "Core/Views/DisplayVersion.php"; break;
					
				/*
				 * Delete a page
				 */
				case "DeletePage": require_once "Core/Views/DeletePage.php"; break;
					
				/*
				 * Check login credentials
				 */
				case "CheckLoginCredentials": require_once "Core/Views/CheckLoginCredentials.php"; break;
					
				/*
				 * Create/edit a user account
				 */
				case "SaveUser": require_once "Core/Views/SaveUser.php"; break;
					
				/*
				 * Create/edit a user account
				 */
				case "UserHasPermission": require_once "Core/Views/UserHasPermission.php"; break;
					
				/*
				 * Create/edit a user account
				 */
				case "GetUserPermissions": require_once "Core/Views/GetUserPermissions.php"; break;
					
				/*
				 * Create/edit a user account
				 */
				case "SaveUserPermission": require_once "Core/Views/SaveUserPermission.php"; break;
					
				/*
				 * Get a list of all users
				 */
				case "GetUsers": require_once "Core/Views/GetUsers.php"; break;
				
				/*
				 * Create/edit a group
				 */
				case "SaveGroup": require_once "Core/Views/SaveGroup.php"; break;
				
				/*
				 * Get a list of all groups
				 */
				case "GetGroups": require_once "Core/Views/GetGroups.php"; break;
				
				/*
				 * Add/remove user to/from group
				 */
				case "SaveGroupMember": require_once "Core/Views/SaveGroupMember.php"; break;
				
				/*
				 * Do an online check
				 */
				//case "ConnectivityCheck":
				//	print json_encode(["status" => 0, "message" => "You are still connected", "timestamp" => date("Y-m-d H:i:s")]);
				//	break;
				
				default:
					print json_encode(["status" => 0, "message" => "This command is not supported"]);
			}
			
			// Close the database connection
			DatabaseConnection::GetInstance()->Close();
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