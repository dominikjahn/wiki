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
	
	require_once "Core/Exception/BaseException.php";
	require_once "Core/Exception/ClassNotFoundException.php";
	require_once "Core/ClassLoader.php";
	
	try {
		Wiki\Application::Main();
	} catch(\Exception $e) {
		echo "Fatal error: ".$e->getMessage()."\n".str_repeat("=",50)."\n\n".$e->getTraceAsString();
	}
?>
