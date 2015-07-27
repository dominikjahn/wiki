<?php
	namespace Wiki\Exception;
	
	class UserNotFoundException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The user was not found";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>