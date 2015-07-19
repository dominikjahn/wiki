<?php
	namespace Wiki\Exception;
	
	class ChecksumMismatchException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "The checksum of the row does not match the calculated checksum. This is a sign that the database has been manipulated!";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>