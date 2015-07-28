<?php
	namespace Wiki\Exception;
	
	class PageNotFoundException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The page does not exist anymore";
			}
			
			$code = 404;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>