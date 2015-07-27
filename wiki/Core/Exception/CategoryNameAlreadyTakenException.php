<?php
	namespace Wiki\Exception;
	
	class CategoryNameAlreadyTakenException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The category name is already taken";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>