<?php
	namespace Wiki\Exception;
	
	class CategoryNotFoundException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The category was not found";
			}
			
			$code = 404;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>