<?php
	namespace Wiki;
	use Wiki\Database\DatabaseConnection;
	
	class Configuration
	{
		  //
		 // DATABASE SETTINGS
		//
		
		const DATABASE_DRIVER = DatabaseConnection::DRIVER_MYSQL;
		const DATABASE_HOST = "localhost";
		const DATABASE_USER = "root";
		const DATABASE_PASSWORD = "";
		const DATABASE_NAME = "wiki";
		const DATABASE_CHARSET = "utf8mb4";
		const DATABASE_PREFIX = "";
		
		  //
		 // WEBSERVER SETTINGS
		//
		
		const DOC_ROOT = "/";
		const WWW_ROOT = "http://localhost".self::DOC_ROOT;
		
		  //
		 // SECURITY SETTINGS
		//
		
		const STORAGE_SALT = "7213eb4c649732852bcf8b0cf9d809cc";
		const COOKIE_SALT  = "3703611b1556e0ce7acf0ca17dce161e";
	}
?>