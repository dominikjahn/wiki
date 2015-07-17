<?php
	date_default_timezone_set("Europe/Berlin");
	
	header("Content-Type: text/cache-manifest");
	echo "CACHE MANIFEST\nNETWORK:\n*\n#".date("Y-m-d H:i");
	die();
	
	$files = array(
		"index.html",
		"assets/application.css",
		"assets/application.js",
		"assets/css/bootstrap.min.css",
		"assets/js/jquery-1.11.2.min.js",
		"assets/js/bootstrap.min.js");
	$files = array_merge(
					$files,
					#glob("assets/css/*.*"),
					glob("../assets/fonts/*.*")
					#,glob("assets/js/*.*")
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