<?php
	namespace Wiki\Exception;
	
	class CannotRemoveUserFromPublicGroupException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot remove a user from the 'public' group.";
			}
			
			$code = 403;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>