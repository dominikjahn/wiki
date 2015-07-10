<?php
	abstract class DomainManager
	{
		  //
		 // ATTRIBUTES
		//
		
		private $cache = [];
		
		  //
		 // CONSTRUCTOR
		//
		
		protected function __construct() {
		
		}
		
		  //
		 // METHODS
		//
		
		public abstract function GetByID($id);
		
		public function AddToCache(Domain $object, $key = null, $type = "id") {
			if(!array_key_exists($type, $this->cache)) {
				$this->cache[$type] = [];
			}
			
			if(!$key) {
				$key = $object->ID;
			}
			
			$this->cache[$type][$key] = $object;
		}
		
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
		
		public static abstract function GetInstance();
	}
?>