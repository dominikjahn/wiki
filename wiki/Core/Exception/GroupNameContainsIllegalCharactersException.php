<?php
	namespace Wiki\Exception;
	
	class GroupNameContainsIllegalCharactersException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The name contains characters which are not allowed for a group name. Three to twenty characters, only lower-cased letters and numbers.";
			}
			
			$code = 403;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>