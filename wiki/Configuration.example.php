<?php
	namespace Wiki;
	use Wiki\Database\DatabaseConnection;
	
	class Configuration
	{
		const DATABASE_DRIVER = DatabaseConnection::DRIVER_MYSQL;
		const DATABASE_HOST = "localhost";
		const DATABASE_USER = "root";
		const DATABASE_PASSWORD = "";
		const DATABASE_NAME = "wiki";
		const DATABASE_CHARSET = "utf8mb4";
		const DATABASE_PREFIX = "";
		const WWW_ROOT = "/";
	}
?>