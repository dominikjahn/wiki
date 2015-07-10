<?php
	header("Content-Type: application/json; charset=UTF-8");
	http_response_code(200);
	
	require_once "Core/Configuration.php";
	require_once "Core/Database/DatabaseConnection.php";
	require_once "Core/Database/DatabaseStatement.php";
	require_once "Core/Database/DatabaseResultset.php";
	require_once "Core/Database/DatabaseRow.php";
	require_once "Core/Database/DatabaseColumn.php";
	
	$command = (isset($_GET["command"]) ? $_GET["command"] : null);
	
	if(!$command)
	{
		echo "Invalid command";
		die();
	}
	
	$db = DatabaseConnection::GetInstance();
	//$db = new mysqli("localhost","root","","wiki");
	//$db->autocommit(false);
	
	$loginname = (isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
	$password = (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null);
	
	$signedIn = null;
	
	if($loginname && $password) {
		$userID = 0;
		
		$sqlUserFound = "SELECT user_id FROM user WHERE status = 100 AND loginname = :loginname AND password = :password";
		$stmUserFound = $db->Prepare($sqlUserFound);
		$rowUser = $stmUserFound->ReadSingle(["loginname" => $loginname, "password" => $password]);
		
		$signedIn = $rowUser->user_id->Integer;
		
		$stmUserFound->close();
	}
	
	define("SIGNED_IN", $signedIn);
	
	switch($command)
	{
		/*
		 * Display the content of a specific page
		 */
		case "DisplayPage": require_once "Views/DisplayPage.php"; break;
		
		/*
		 * Save changes to a page
		 */
		case "SavePage": require_once "Views/SavePage.php"; break;
		
		/*
		 * Get a list of revisions of a specific page
		 */
		//case "GetVersions": require_once "Views/GetVersions.php"; break;
		
		/*
		 * Get a specific version
		 */
		//case "DisplayVersion": require_once "Views/DisplayVersion.php"; break;
			
		/*
		 * Delete a page
		 */
		//case "DeletePage": require_once "Views/DeletePage.php"; break;
			
		/*
		 * Check login credentials
		 */
		case "CheckLoginCredentials": require_once "Views/CheckLoginCredentials.php"; break;
		
		/*
		 * Do an online check
		 */
		case "ConnectivityCheck": print 1; break;
	}
	
	$db->Close();
?>
