<?php
	class DatabaseColumn
	{
		  //
		 // ATTRIBUTES
		//
		
		private $name;
		private $value;
		
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct($name, $value) {
			$this->name = $name;
			$this->value = $value;
		}
		
		  //
		 // METHODS
		//
		
		protected function GetIsNull() {
			return is_null($this->value);
		}
		
		protected function GetString($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			return (string) $this->value;
		}
		
		protected function GetInteger($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			return (integer) $this->value;
		}
		
		protected function GetFloat($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			return (float) $this->value;
		}
		
		protected function GetBoolean($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			$value = strtolower((string) $this->value);
			
			$negative = ["0","","no","false"];
			
			if(in_array($value, $negative)) {
				return false;
			}
			
			return true;
		}
		
		protected function GetUnixTimestamp($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			return strtotime((string) $this->value);
		}
		
		protected function GetDateTime($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			return new \DateTime((string) $this->value);
		}
		
		protected function GetObject($canbenull = false) {
			if($canbenull && $this->GetIsNull()) {
				return false;
			}
			
			$type = explode("_",$this->name);
			$type = $type[0];
			
			switch($type) {
				case "user":
					return UserManager::GetInstance()->GetByID($this->value);
					break;
					
				case "page":
					return PageManager::GetInstance()->GetByID($this->value);
					break;
				
				default:
					throw new \Exception("Unknown object type '".$type."'");
			}
		}
		
		  //
		 // PROPERTIES
		//
		
		public function __get($field) {
			switch($field) {
				case "IsNull": return $this->GetIsNull(); break;
				
				case "String": return $this->GetString(); break;
				case "StringOrNull": return $this->GetString(true); break;
				
				case "Integer": return $this->GetInteger(); break;
				case "IntegerOrNull": return $this->GetInteger(true); break;
				
				case "Float": return $this->GetFloat(); break;
				case "FloatOrNull": return $this->GetFloat(true); break;
				
				case "Boolean": return $this->GetBoolean(); break;
				case "BooleanOrNull": return $this->GetBoolean(true); break;
				
				case "UnixTimestamp": return $this->GetUnixTimestamp(); break;
				case "UnixTimestampOrNull": return $this->GetUnixTimestamp(true); break;
				
				case "DateTime": return $this->GetDateTime(); break;
				case "DateTimeOrNull": return $this->GetDateTime(true); break;
				
				case "Object": return $this->GetObject(); break;
				case "ObjectOrNull": return $this->GetObject(true); break;
			}
		}
	}
?>