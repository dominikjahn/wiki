<?php
	namespace Wiki\Exception;
	
	class CannotDeletePublicGroupException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot delete the 'public' group.";
			}
			
			$code = 403;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>