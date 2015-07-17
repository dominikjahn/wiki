<?php
	namespace Wiki\Tools;
	
	class StringTools
	{
		public static function UpperCamelCaseToLowerUnderscore($text) {
			$result = strtolower(substr($text,0,1));
			
			for($pos = 1; $pos < strlen($text); $pos++) {
				$char = substr($text,$pos,1);
				
				if(ord($char) >= 65 && ord($char) <= 90) {
					$result .= $char."_";
				} else {
					$result .= $char;
				}
			}
			
			return $result;
		}
	}
?>