<?php
	$loginname = $_GET["loginname"];
	$password = $_GET["password"]; // This should already be md5'ed
	$found = false;
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$user = $userManager->GetByLoginname($loginname);
		
		if(!$user || $user->Password != $password) {
			$data->status = 0;
			$data->message = "The login credentials are incorrect";
		} else {
			$data->status = 1;
			$data->message = "The login credentials are correct";
		}
	} catch(\Exception $e) {
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>