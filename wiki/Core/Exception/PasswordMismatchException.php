<?php
	namespace Wiki\Exception;
	
	class PasswordMismatchException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The password is wrong.";
			}
			
			$code = 401;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>