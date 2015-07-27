<?php
	namespace Wiki\Exception;
	
	class LoginnameContainsIllegalCharactersException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The name contains characters which are not allowed for a login name. Three to twenty characters, only lower-cased letters and numbers.";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>