<?php
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	abstract class DomainManager
	{
		  //
		 // ATTRIBUTES
		//
		
		private $cache = [];
		
		  //
		 // CONSTRUCTOR
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function __construct() {
		
		}
		
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public abstract function GetByID($id);
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function AddToCache(Domain $object, $key = null, $type = "id") {
			if(!array_key_exists($type, $this->cache)) {
				$this->cache[$type] = [];
			}
			
			if(!$key) {
				$key = $object->ID;
			}
			
			$this->cache[$type][$key] = $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetFromCache($key, $type = "id") {
			if(!array_key_exists($type, $this->cache)) {
				return null;
			}
			
			if(!array_key_exists($key, $this->cache[$type])) {
				return null;
			}
			
			return $this->cache[$type][$key];
		}
		
		  //
		 // FUNCTIONS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		//public static abstract function GetInstance();
	}
?>