<?php
	namespace Wiki\Exception;
	
	class PageNotVisibleToCurrentUserException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You are not allowed to see the contents of this page.";
			}
			
			$code = 500;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>