<?php
	$loginname = $_GET["loginname"];
	$password = $_GET["password"]; // This should already be md5'ed
	$found = false;
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$sqlUserFound = "SELECT COUNT(user_id) AS found FROM user WHERE status = 100 AND loginname = :loginname AND password = :password";
		$stmUserFound = $db->Prepare($sqlUserFound);
		$rowUserFound = $stmUserFound->ReadSingle(["loginname" => $loginname, "password" => $password]);
		
		$found = $rowUserFound->found->Integer;
		if($found) {
			$data->status = 1;
			$data->message = "The login credentials are correct";
		} else {
			$data->message = "The login credentials are incorrect";
		}
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>