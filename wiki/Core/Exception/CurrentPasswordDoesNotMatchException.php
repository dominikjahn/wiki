<?php
	namespace Wiki\Exception;
	
	class CurrentPasswordDoesNotMatchException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The current password does not match with your input";
			}
			
			$code = 401;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>