<?php
	namespace Wiki;
	use Wiki\Database\DatabaseConnection;
	
	class Configuration
	{
		const DATABASE_DRIVER = DatabaseConnection::DRIVER_MYSQL;
		const DATABASE_HOST = "localhost";
		const DATABASE_USER = "root";
		const DATABASE_PASSWORD = "d9+Se0F$";
		const DATABASE_NAME = "wiki";
		const DATABASE_CHARSET = "utf8mb4";
		const DATABASE_PREFIX = "";
		const DOC_ROOT = "/cb/wiki/public_html/";
		const WWW_ROOT = "http://localhost".self::DOC_ROOT;
	}
?>