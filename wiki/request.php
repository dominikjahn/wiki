<?php
	/**
	 * @version 0.1
	 * @since 0.1
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 */
	
	ini_set("display_errors",true);
	ini_set("default_charset", "UTF-8");
	error_reporting(E_ALL);
	
	chdir(__DIR__);
	
	require_once "../Configuration.php";
	require_once "Core/Exception/BaseException.php";
	require_once "Core/Exception/ClassNotFoundException.php";
	require_once "Core/ClassLoader.php";
	
	try {
		Wiki\Application::Main();
	} catch(\Exception $e) {
		if($e->getCode() == 0) {
			$status = 500;
		} else {
			$status = $e->getCode();
		}
		
		http_response_code(200);
		
		print json_encode(["status" => $status, "message" => $e->getMessage()]);//, "exception" => get_class($e), "details" => $e->getTraceAsString()]);
	}
?>
