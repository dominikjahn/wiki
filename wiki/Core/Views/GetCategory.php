<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Domain\User;
	use Wiki\Domain\Manager\CategoryManager;
	use Wiki\Domain\Page;
	use Wiki\Exception\CategoryNotFoundException();
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GetCategory extends Response
	{
		public function Run() {
			$name = (isset($_GET["name"]) ? $_GET["name"] : null);
			$categoryID = (isset($_GET["categoryID"]) ? (int) $_GET["categoryID"] : null);
	
			$category = null;
			$categoryManager = CategoryManager::GetInstance();
			
			if($name) {
				$category = $categoryManager->GetByName($name);
			} else {
				$category = $categoryManager->GetByID($groupID);
			}
			
			if(!$category || $category->status === 0) {
				throw new CategoryNotFoundException();
			}
			
			$this->Status = 200;
			$this->Message = "Category found";
			$this->Data = ["category" => $category];
		}
	}
?>