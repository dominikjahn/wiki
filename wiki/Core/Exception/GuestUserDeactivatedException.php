<?php
	namespace Wiki\Exception;
	
	class GuestUserDeactivatedException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The guest user cannot be deleted. Please reactivate the user.";
			}
			
			$code = 500;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>