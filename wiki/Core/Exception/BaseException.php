<?php
	namespace Wiki\Exception;
	
	abstract class BaseException extends \Exception
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "An unknown error has occured";
			}
			
			if(!$code) {
				$code = 500;
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>