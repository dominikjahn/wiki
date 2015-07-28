<?php
	namespace Wiki\Exception;
	
	class PagenameAlreadyTakenException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The name is already taken";
			}
			
			$code = 409;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>