<?php
	namespace Wiki\Exception;
	
	class CannotDeleteHomepageException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot delete the homepage";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>