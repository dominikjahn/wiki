<?php
	namespace Wiki\Exception;
	
	class AuthorizationMissingException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You are not authorized to perform this action.";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>