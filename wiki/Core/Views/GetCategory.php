<?php
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\CategoryManager;
	use Wiki\Domain\Page;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	$name = (isset($_GET["name"]) ? $_GET["name"] : null);
	$categoryID = (isset($_GET["categoryID"]) ? (int) $_GET["categoryID"] : null);
	
	$data = (object) ["status" => 0, "message" => "An unknown error occured"];
	
	try {
		$category = null;
		$categoryManager = CategoryManager::GetInstance();
		
		if($name) {
			$category = $categoryManager->GetByName($name);
		} else {
			$category = $categoryManager->GetByID($groupID);
		}
		
		if(!$category || $category->status === 0) {
			$data->status = 404;
			$data->message = "The category was not found";
		} else {
			$data->status = 200;
			$data->message = "Category found";
			$data->category = $category;
		}
	} catch(\Exception $e) {
		$data->status = 0;
		$data->message = $e->getMessage();
	}
	
	print json_encode($data);
?>