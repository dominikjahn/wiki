<?php
	namespace Wiki\Exception;
	
	class CannotDeleteGuestOrAdminUserException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot delete the 'guest' or 'admin' users";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>