<?php
	namespace Wiki\Exception;
	
	class NotAuthorizedToCreateOrEditPagesWithScriptsException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You are not authorized to create or edit pages with scripts";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>