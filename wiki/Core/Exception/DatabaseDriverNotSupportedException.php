<?php
	namespace Wiki\Exception;
	
	class DatabaseDriverNotSupportedException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "Unsupported database driver";
			}
			
			$code = 500;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>