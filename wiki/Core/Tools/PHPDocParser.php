<?php
	namespace Wiki\Tools;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class PHPDocParser
	{
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		private function __construct() {
		
		}
		
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function AnalyzeAttribute($class, $attribute) {
			try {
				$attribute = new \ReflectionProperty($class, $attribute);
				
				return $this->ParseComment($attribute->getDocComment());
			} catch(\Exception $e) {
				$parentClass = get_parent_class($class);
				
				if(!$parentClass) {
					throw $e;
				}
				
				return $this->AnalyzeAttribute($parentClass, $attribute);
			}
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		private function ParseComment($comment) {
			$comment = trim($comment);
			$comment = trim(substr($comment,3,-2));
			$lines = explode(\PHP_EOL, $comment);
			
			$docComment = new PHPDocComment();
			
			$description = null;
			
			foreach($lines as $line) {
				// Remove space (_): _* line -> * line
				$line = trim($line);
				// Remove *: * line -> line
				$line = trim(substr($line,1));
				
				if(substr($line,0,1) != "@") {
					$description .= $line.PHP_EOL;
				} else {
					$line = explode(" ",$line,2);
					
					$field = substr($line[0],1);
					$value = trim($line[1]);
					
					switch($field) {
						case "field": $docComment->Field = $value; break;
					}
				}
			}
			
			$description = trim($description);
			
			$docComment->Description = $description;
			
			return $docComment;
		}
		
		  //
		 // FUNCTIONS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public static function GetInstance() {
			if(!self::$instance) {
				self::$instance = new PHPDocParser();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		private static $instance;
	}
?>