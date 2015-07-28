<?php
	namespace Wiki\Views;
	
	use Wiki\Response;
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Manager\PageManager;
	use Wiki\Tools\Request;
	use Wiki\Exception\PageNotFoundException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	
	class DeletePage extends Response
	{
		public function Run() {
			$request = Request::GetInstance();
			
			$pageID = (int) $request->Body["pageID"];
			
			$page = PageManager::GetInstance()->GetByID($pageID);
			
			if(!$page || $page->Status === 0) {
				$this->Status = 404;
				throw new PageNotFoundException();
			}
			
			$success = $page->Delete();
			
			if(!$success) {
				$this->Status = 500;
				throw new \Exception("Deleting the page failed");
			}
			
			$this->Status = 200;
			$this->Message = "The page was deleted successfully";
		}
	}
?>