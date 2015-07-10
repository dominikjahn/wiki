<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$pageID = (isset($_GET["pageID"]) ? (int) $_GET["pageID"] : null);
	
	$title = $_POST["title"];
	$content = $_POST["content"];
	$summary = $_POST["summary"];
	$minor_edit = (isset($_POST["minor_edit"]) ? (bool) $_POST["minor_edit"] : false);
	$visibility = $_POST["visibility"];
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$db = DatabaseConnection::GetInstance();
		
		$currentUser = User::GetCurrentUser();
		
		if(!$currentUser) {
			throw new \Exception("You are not authorized to perform this action");
		}
		
		$isNewPage = true;
		$timestamp =  date("Y-m-d H:i:s");
		
		$db->BeginTransaction();
		
		$page = null;
		$name = null;
		
		if(!is_null($pageID)) {
			$page = PageManager::GetInstance()->GetByID($pageID);
			
			$isNewPage = false;
		} else {
			$page = new Page();
			
			$name = NormalizeTitle($title);
			$name = CheckForDuplicatePageName($name);
			
			$page->Status = 100;
			$page->Name = $name;
			$page->Owner = $currentUser;
		}
		
		$page->Title = $title;
		$page->Content = $content;
		$page->Visibility = $visibility;
		
		$success = $page->Save();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the page failed");
		}
		
		$version = new Version();
		$version->Status = 100;
		$version->Page = $page;
		$version->Title = $title;
		$version->Content = $content;
		$version->Summary = $summary;
		$version->MinorEdit = $minor_edit;
		
		$success = $version->Save();
		
		if(!$success) {
			$db->Rollback();
			throw new \Exception("Storing the revision failed");
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
			
			if(!$rowPage || !$rowPage->page_id->IntegerOrNull) {
				break;
			} else {
				$attempt++;
				
				$name = $origName."-".$attempt;
			}
		}
		
		return $name;
	}
?>