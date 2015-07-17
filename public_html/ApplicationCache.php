<?php
	date_default_timezone_set("Europe/Berlin");
	
	header("Content-Type: text/cache-manifest");
	
	//if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "127.0.0.1" || $_SERVER["HTTP_HOST"] == "::1") {
	//	echo "CACHE MANIFEST\n\nNETWORK:\n*\n\n# ".date("Y-m-d H:i")."\n# This is a local environment, ApplicationCache is deactivated";
	//	die();
	//}
	
	$files = array(
		"index.html");
	$files = array_merge(
					$files,
					glob("assets/css/*.*"),
					glob("assets/fonts/*.*"),
					glob("assets/js/*.*"),
					glob("assets/js/ace/*.*")
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
?>
CACHE MANIFEST

CACHE:
<?php
foreach($files as $file)
{
	echo $file."\n";
}
?>

NETWORK:
*

<?php
	//if anything has changed, add this comment:
	//echo "#".$dateOfLastChange;
	echo "# Last modification date: ".date("Y-m-d H:i:s",$lastchange)."\n# Total file size: ".$filesize." kByte";
	
	if(file_exists("recache"))
	{
		echo "\n# Cache flush enforced!";
	}
?>