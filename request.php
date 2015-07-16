<?php
	/**
	 * @version 0.1
	 * @since 0.1
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 */
	
	ini_set("display_errors",true);
	error_reporting(E_ALL);
	
	header("Content-Type: application/json; charset=UTF-8");
	http_response_code(200);
	
	require_once "Core/Exception/BaseException.php";
	require_once "Core/Exception/ClassNotFoundException.php";
	require_once "Core/ClassLoader.php";
	
	use Wiki\Domain\Manager\UserManager;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\User;
	
	$command = (isset($_GET["command"]) ? $_GET["command"] : null);
	
	if(!$command)
	{
		echo "Invalid command";
		die();
	}
	
	/*
	 * Check login information (currently using HTTP Basic authentification, which is not secure at all, but for now that's good enough)
	 */
	
	$loginname = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
	$password = (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null);
	
	if($loginname && $password) {
	
		$userManager = UserManager::GetInstance();
		
		$user = $userManager->GetByLoginname($loginname);
		
		if($user->MatchPassword($password)) {
			User::SetCurrentUser($user);
		}
	}
	
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
		 * Get a list of all users
		 */
		case "GetUsers": require_once "Core/Views/GetUsers.php"; break;
		
		/*
		 * Do an online check
		 */
		case "ConnectivityCheck":
			print json_encode(["status" => 0, "message" => "You are still connected", "timestamp" => date("Y-m-d H:i:s")]);
			break;
		
		default:
			print json_encode(["status" => 0, "message" => "This command is not supported"]);
	}
	
	$db = DatabaseConnection::GetInstance();
	$db->Close();
?>
