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
		
		public static function NormalizeString($string) {
			$string = str_replace([" ","\t"]," ",$string);
			$string = str_replace(["Ä", "ä", "Ö", "ö", "Ü", "ü", "ß", "Á","á","À","à","Ã","ã","É","é","È","è","Ó","ó","Ò","ò","Õ","õ","Í","í","Ì","ì","Ú","ú","Ù","ù","Ñ","ñ"],
					["Ae","ae","Oe","oe","Ue","ue","ss","A","a","A","a","A","a","E","e","E","e","O","o","O","o","O","o","I","i","I","i","U","u","U","u","N","n"],
					$string);
			
			$string = trim($string);
				
			$string = str_replace(" ","_",$string);
				
			// Remove all non-allowed characters
			$stringClean = null;
				
			for($p = 0; $p < strlen($string); $p++) {
				$char = ord(substr($string,$p,1));
			
				if(($char >= 48 && $char <=57) || ($char >= 65 && $char <= 90) || ($char >= 97 && $char <= 122) || $char == 95) {
					$stringClean .= substr($string,$p,1);
				}
			}
				
			while(strpos($string,"__") !== false) {
				$string = str_replace("__","_",$string);
			}
				
			return $stringClean;
		}
	}
?>