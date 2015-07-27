<?php
	namespace Wiki\Exception;
	
	class CannotRevokeAdminPermissionException extends BaseException
	{
		public function __construct($message = null, $code = 0, Exception $previous = null) {
			if(!$message) {
				$message = "You cannot revoke the implicit permission 'ADMIN'.";
			}
			
			parent::__construct($message, $code, $previous);
		}
	}
?>