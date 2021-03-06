<?php
	namespace Wiki\Exception;
	
	class LoginnameAlreadyTakenException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The loginname is already taken";
			}
			
			$code = 409;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>