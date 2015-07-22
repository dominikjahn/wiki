<?php
	namespace Wiki\Exception;
	
	class NotAuthorizedToCreateOrEditGroupsException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You are not authorized to create or edit groups";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>