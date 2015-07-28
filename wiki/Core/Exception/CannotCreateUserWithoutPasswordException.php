<?php
	namespace Wiki\Exception;
	
	class CannotCreateUserWithoutPasswordException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot create a new account without supplying a password.";
			}
			
			$code = 403;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>