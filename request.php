<?php
	header("Content-Type: application/json; charset=UTF-8");
	http_response_code(200);
	
	require_once "Core/Configuration.php";
	require_once "Core/Database/DatabaseConnection.php";
	require_once "Core/Database/DatabaseStatement.php";
	require_once "Core/Database/DatabaseResultset.php";
	require_once "Core/Database/DatabaseRow.php";
	require_once "Core/Database/DatabaseColumn.php";
	
	require_once "Core/Domain/Manager/DomainManager.php";
	require_once "Core/Domain/Factory/DomainFactory.php";
	require_once "Core/Domain/Domain.php";
	
	require_once "Core/Domain/Manager/UserManager.php";
	require_once "Core/Domain/Factory/UserFactory.php";
	require_once "Core/Domain/User.php";
	
	require_once "Core/Domain/Manager/PageManager.php";
	require_once "Core/Domain/Factory/PageFactory.php";
	require_once "Core/Domain/Page.php";
	
	$userManager = UserManager::GetInstance();
	
	$command = (isset($_GET["command"]) ? $_GET["command"] : null);
	
	if(!$command)
	{
		echo "Invalid command";
		die();
	}
	
	$db = DatabaseConnection::GetInstance();
	
	$loginname = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
	$password = (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null);
	
	if($loginname && $password) {
		$user = $userManager->GetByLoginname($loginname);
		
		if($user->Password == $password) {
			User::SetCurrentUser($user);
		}
	}
	
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
		 * Get a list of revisions of a specific page
		 */
		//case "GetVersions": require_once "Core/Views/GetVersions.php"; break;
		
		/*
		 * Get a specific version
		 */
		//case "DisplayVersion": require_once "Core/Views/DisplayVersion.php"; break;
			
		/*
		 * Delete a page
		 */
		//case "DeletePage": require_once "Core/Views/DeletePage.php"; break;
			
		/*
		 * Check login credentials
		 */
		case "CheckLoginCredentials": require_once "Core/Views/CheckLoginCredentials.php"; break;
		
		/*
		 * Do an online check
		 */
		case "ConnectivityCheck": print 1; break;
	}
	
	$db->Close();
?>
