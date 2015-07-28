<?php
	use Wiki\Exception\ClassNotFoundException;
	
	function WikiClassLoadable($classname) {
		// Check if it's a Wiki class
		if(substr($classname,0,4) != "Wiki") {
			return false;
		}
		
		$path = "Core/".str_replace("\\","/", substr($classname,5)).".php";
		
		if(!file_exists($path))
			return false;
		return true;
	}
	
	function WikiAutoload($classname)
	{
		// Check if it's a Wiki class
		if(substr($classname,0,4) != "Wiki") {
			return;
		}
		
		$path = "Core/".str_replace("\\","/", substr($classname,5)).".php";
		
		if(!file_exists($path))
			throw new ClassNotFoundException('\''.$classname.'\' not found. Expected path: '.$path);
		
		require_once $path;
	}

	spl_autoload_register('WikiAutoload');
?>