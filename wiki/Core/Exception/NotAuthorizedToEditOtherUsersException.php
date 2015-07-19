<?php
	namespace Wiki\Exception;
	
	class NotAuthorizedToEditOtherUsersException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You are not authorized to edit other users";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>