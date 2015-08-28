<?php
	date_default_timezone_set("Europe/Berlin");
	
	//header("Content-Type: text/cache-manifest");
	
	if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "127.0.0.1" || $_SERVER["HTTP_HOST"] == "::1") {
		file_put_contents("wiki.appcache", "CACHE MANIFEST\n\nNETWORK:\n*\n\n# ".date("Y-m-d H:i")."\n# This is a local environment, ApplicationCache is deactivated");
		die();
	}
	
	$files = array(
		"index.html","favicon.ico");
	$files = array_merge(
					$files,
					glob("assets/*.*"),
					glob("assets/css/*.*"),
					glob("assets/fonts/*.*"),
					glob("assets/js/*.*")
					//glob("assets/js/ace/*.*")
					// Since editing is not possible in offline mode we don't need to cache Ace Editor
				);
	
	$lastchange = 0;
	$filesize = 0;
	
	foreach($files as $file)
	{
		$modified = filemtime($file);
		$filesize += filesize($file);
		
		if($lastchange < $modified)
		{
			$lastchange = $modified;
		}
	}
	
	$filesize /= 1024;
	$filesize = (int) $filesize;
	#$lastchange = time();
	
	$out = "CACHE MANIFEST".PHP_EOL.PHP_EOL."CACHE:".PHP_EOL;

	foreach($files as $file)
	{
		$out .= $file." # ".md5($file).PHP_EOL;
	}

	$out .= PHP_EOL."NETWORK:".PHP_EOL."*".PHP_EOL;

	//if anything has changed, add this comment:
	//echo "#".$dateOfLastChange;
	$out .= PHP_EOL."# Last modification date: ".date("Y-m-d H:i:s"/*,$lastchange*/).PHP_EOL."# Total file size: ".$filesize." kByte";
	
	if(file_exists("recache"))
	{
		$out .= PHP_EOL."# Cache flush enforced!";
	}
	
	file_put_contents("wiki.appcache",$out);
	echo '<p><strong>File created:</strong></p><p><pre>'.$out.'</pre></p><p><a href="wiki.appcache">Go to appcache file</a></p>';
?>