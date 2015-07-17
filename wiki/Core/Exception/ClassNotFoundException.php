<?php
	namespace Wiki\Exception;
	
	class ClassNotFoundException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			
			parent::__construct($message, $code, $previous);
		}
	}
?>