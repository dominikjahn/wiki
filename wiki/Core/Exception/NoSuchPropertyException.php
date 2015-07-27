<?php
	namespace Wiki\Exception;
	
	class NoSuchPropertyException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			$message = "No such field: ".$message;
			
			parent::__construct($message, $code, $previous);
		}
	}
?>