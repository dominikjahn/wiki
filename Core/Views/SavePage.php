<?php
	$pageID = (isset($_GET["pageID"]) ? (int) $_GET["pageID"] : null);
	
	$title = $_POST["title"];
	$content = $_POST["content"];
	$summary = $_POST["summary"];
	$minor_edit = (isset($_POST["minor_edit"]) ? (bool) $_POST["minor_edit"] : false);
	$visibility = $_POST["visibility"];
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		if(!SIGNED_IN) {
			throw new \Exception("You are not authorized to perform this action");
		}
		
		$userID = SIGNED_IN;
		$isNewPage = true;
		$timestamp =  date("Y-m-d H:i:s");
		
		$db->BeginTransaction();
		
		if(is_null($pageID)) {
			$name = NormalizeTitle($title);
			$name = CheckForDuplicatePageName($name);
			
			$sqlCreatePage = "INSERT INTO page (`status`, `name`, `title`, `content`, `owner_id`, `visibility`) VALUES (100, :name, :title, :content, :owner_id, :visibility);";
			$stmCreatePage = $db->Prepare($sqlCreatePage);
			$success = $stmCreatePage->Execute(["name" => $name, "title" => $title, "content" => $content, "user_id" => $userID, "visibility" => $visibility]);
			$stmCreatePage->Close();
			
			if(!$success) {
				$db->Rollback();
				throw new \Exception("Storing the page failed");
			}
			
			$pageID = $db->insert_id;
		} else {
			$isNewPage = false;
			
			$sqlUpdatePage = "UPDATE page SET `title` = :title, `content` = :content, `visibility` = :visibility WHERE `page_id` = :page_id;";
			$stmUpdatePage = $db->Prepare($sqlUpdatePage);
			$success = $stmUpdatePage->Execute(["title" => $title, "content" => $content, "visibility" => $visibility, "page_id" => $pageID]);
			$stmUpdatePage->Close();
			
			if(!$success) {
				$db->Rollback();
				throw new \Exception("Storing the page failed");
			}
		}
			
		$sqlCreateVersion = "INSERT INTO version (`status`, `page_id`, `title`, `content`, `summary`, `minor_edit`) VALUES (100, :page_id, :title, :content, :summary, :minor_edit);";
		$stmCreateVersion = $db->Prepare($sqlCreateVersion);
		$success = $stmCreateVersion->Execute(["page_id" => $pageID, "title" => $title, "content" => $content, "summary" => $summary]);
		$stmCreateVersion->Close();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the revision failed");
		}
		
		$versionID = $db->insert_id;
		
		$sqlCreateLogForPage = "INSERT INTO log (`object_table`, `object_id`, `user_id`, `type`, `timestamp`) VALUES ('page', :object_id, :user_id, '".($isNewPage ? "CREATE" : "MODIFY")."', :timestamp);";
		$stmCreateLogForPage = $db->Prepare($sqlCreateLogForPage);
		$success = $stmCreateLogForPage->Execute(["object_id" => $pageID, "user_id" => $userID, "timestamp" => $timestamp]);
		$stmCreateLogForPage->Close();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the log for the page failed");
		}
		
		$sqlCreateLogForVersion = "INSERT INTO log (`object_table`, `object_id`, `user_id`, `type`, `timestamp`) VALUES ('version', :object_id, :user_id, 'CREATE', :timestamp);";
		$stmCreateLogForVersion = $db->Prepare($sqlCreateLogForVersion);
		$success = $stmCreateLogForVersion->execute(["object_id" => $versionID, "user_id" => $userID, "timestamp" => $timestamp]);
		$stmCreateLogForVersion->Close();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the log for the version failed");
		}
		
		$db->Commit();
		
		$data->status = 1;
		$data->message = "The page was saved successfully";
		
		if($isNewPage) {
			$data->page = $name;
		}
		
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
	
	
	function NormalizeTitle($title) {
		$name = str_replace([" ","\t"]," ",$title);
		$name = str_replace(["Ä", "ä", "Ö", "ö", "Ü", "ü", "ß", "Á","á","À","à","Ã","ã","É","é","È","è","Ó","ó","Ò","ò","Õ","õ","Í","í","Ì","ì","Ú","ú","Ù","ù","Ñ","ñ"],
							["Ae","ae","Oe","oe","Ue","ue","ss","A","a","A","a","A","a","E","e","E","e","O","o","O","o","O","o","I","i","I","i","U","u","U","u","N","n"],
							$name);
							
		$name = trim($name);
		
		$name = str_replace(" ","_",$name);
		
		// Remove all non-allowed characters
		$nameClean = null;
		
		for($p = 0; $p < strlen($name); $p++) {
			$char = ord(substr($name,$p,1));
			
			if(($char >= 48 && $char <=57) || ($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122) || $char == 95) {
				$nameClean .= substr($name,$p,1);
			}
		}
		
		while(strpos($name,"__") !== false) {
			$name = str_replace("__","_",$name);
		}
		
		return $nameClean;
	}
	
	function CheckForDuplicatePageName($name) {
		global $db;
		
		$origName = $name;
		
		$attempt = 0;
		
		while(true) {
			$sqlPage = "SELECT page_id FROM page WHERE status = 100 AND name = :name";
			$stmPage = $db->Prepare($sqlPage);
			$rowPage = $stmPage->ReadSingle(["name" => $name]);
			$stmPage->Close();
			
			$pageID = $rowPage->page_id->IntegerOrNull;
			
			if(!$pageID) {
				break;
			} else {
				$attempt++;
				
				$name = $origName."-".$attempt;
			}
		}
		
		return $name;
	}
?>