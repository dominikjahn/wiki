<?php
	namespace Wiki\Exception;
	
	class GroupNotFoundException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The group was not found";
			}
			
			$code = 404;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>