<?php
	namespace Wiki\Domain;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Tools\PHPDocParser;
	use Wiki\Exception\ChecksumMismatchException;
	use Wiki\Exception\NoSuchPropertyException;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	abstract class Domain implements \JsonSerializable
	{
		  //
		 // CONSTRUCTOR
		//
		
		public function __construct() {
			$this->status = 0;
		}
		
		  //
		 // METHODS
		//
		
		public function Save() {
			
			$phpDocParser = PHPDocParser::GetInstance();
			
			$isNew = ($this->id ? false : true);
			
			$table = static::DB_TABLE;
			
			$sqlSave = null;
			$parameters = [];
			
			$insertFields = [];
			$insertPlaceholders = [];
			$updateFields = [];
			
			foreach($this as $field => $value) {
				
				$docComment = $phpDocParser->AnalyzeAttribute(get_class($this), $field);
				
				$dbField = $docComment->Field;
				
				if(!$dbField) { continue; } // Fields without @field are not persistent!
				if($dbField == "_id") { $dbField = $table."_id"; }
				
				if($field != "id") {
					$insertFields[] = "`".$dbField."`";
					$insertPlaceholders[] = ":".$dbField;
					$updateFields[] = "`".$dbField."` = :".$dbField;
				}
				
				if($field != "id" || !$isNew) {
					$parameters[$dbField] = $this->$field;
				}
			}
			
			$parameters["checksum"] = $this->Checksum;
				
			if($isNew) {
				$sqlSave = "INSERT INTO `%PREFIX%".$table."` (".join(", ",$insertFields).", `checksum`) VALUES (".join(", ", $insertPlaceholders).", :checksum);";
			} else {
				$sqlSave = "UPDATE `%PREFIX%".$table."` SET ".join(", ",$updateFields).", `checksum` = :checksum WHERE `".$table."_id` = :".$table."_id;";
			}
			
			$db = DatabaseConnection::GetInstance();
			
			$success = $db->PrepareAndExecute($sqlSave, $parameters);
			
			if($isNew) {
				$this->ID = $db->LastInsertedID;
			}
			
			if($table != "log") {
				$log = new Log();
				
				$log->Status = 100;
				$log->ObjectTable = $table;
				$log->{"Object"} = $this->ID;
				$log->User = User::GetCurrentUser();
				$log->Type = ($isNew ? Log::TYPE_CREATE : Log::TYPE_MODIFY);
				$log->Timestamp = new \DateTime();
				
				$log->Save();
			}
			
			return $success;
		}
		
		public function Delete() {
			
			$table = static::DB_TABLE;
			
			$this->status = 0;
			$checksum = $this->Checksum;
			
			$sqlDelete = "UPDATE `%PREFIX%".$table."` SET `status` = 0, `checksum` = :checksum WHERE `".$table."_id` = :id;";
			$parameters = ["checksum" => $checksum, "id" => $this->id];
			
			$db = DatabaseConnection::GetInstance();
			
			$success = $db->PrepareAndExecute($sqlDelete, $parameters);
			
			if($table != "log") {
				$log = new Log();
				
				$log->Status = 100;
				$log->ObjectTable = $table;
				$log->{"Object"} = $this;
				$log->User = User::GetCurrentUser();
				$log->Type = Log::TYPE_DELETE;
				$log->Timestamp = new \DateTime();
				
				$log->Save();
			}
			
			return $success;
		}
		
		public function __toString() {
			return "<".get_class($this)."> ".($this->id ? "#".$this->id : ":memory");
		}
		
		protected abstract function CalculateChecksum();
		
		public function ValidateChecksum($checksum) {
			return true;
			
			if($checksum != $this->CalculateChecksum()) {
				echo "Expected: ".$this->CalculateChecksum()."\nGiven: ".$checksum."\n\n";
				throw new ChecksumMismatchException();
			}
		}
		
		  //
		 // PROPERTIES
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __get($field) {
			$getter = "Get".$field;
			
			if(!method_exists($this, $getter)) {
				throw new NoSuchPropertyException(get_class($this)."->".$field);
			}
			
			return static::$getter();
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function __set($field, $value) {
			$setter = "Set".$field;
			
			if(!method_exists($this, $setter)) {
				throw new NoSuchPropertyException(get_class($this)."->".$field);
			}
			
			$oldValue = $this->__get($field);
			
			if($field != "IsLoadedFromDatabase" && $this->loadedFromDatabase && $oldValue !== $value) {
				$this->changedFields[] = $field;
			}
			
			static::$setter($value);
		}
		
		  //
		 // ATTRIBUTES
		//
		
		/**
		 * The id of the object
		 * @field _id
		 */
		protected $id;
		
		/**
		 * The status of the object (0 - 100)
		 * @field status
		 */
		protected $status;
		
		/**
		 * The log item when the object was created
		 */
		protected $logCreated;
		
		/**
		 * The log item when the object was modified
		 */
		protected $logModified;
		
		/**
		 * The log item when the object was deleted
		 */
		protected $logDeleted;
		
		/**
		 * A flag that the object has been loaded from the database
		 */
		private $loadedFromDatabase = false;
		
		/**
		 * A list of fields which have changed
		 */
		private $changedFields = [];
		
		  //
		 // GETTERS/SETTERS
		//
		
		# ID
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetID() {
			return $this->id;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetID($value) {
			$this->id = $value;
		}
		
		# Status
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetStatus() {
			return $this->status;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetStatus($value) {
			$this->status = $value;
		}
		
		# IsLoadedFromDatabase
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetIsLoadedFromDatabase() {
			return $this->loadedFromDatabase;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetIsLoadedFromDatabase($value) {
			$this->loadedFromDatabase = $value;
		}
		
		# LogCreated
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetLogCreated() {
			return $this->logCreated;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetLogCreated(Log $value) {
			$this->logCreated = $value;
		}
		
		# LogModified
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetLogModified() {
			return $this->logModified;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetLogModified(Log $value = null) {
			$this->logModified = $value;
		}
		
		# LogDeleted
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function GetLogDeleted() {
			return $this->logDeleted;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		protected function SetLogDeleted(Log $value = null) {
			$this->logDeleted = $value;
		}
		
		# Checksum
		
		protected function GetChecksum() {
			$checksum = null;
			$phpDocParser = PHPDocParser::GetInstance();
			
			foreach($this as $field => $value) {
			
				if($field == "id") {
					continue;
				}
				
				$docComment = $phpDocParser->AnalyzeAttribute(get_class($this), $field);
			
				$dbField = $docComment->Field;
			
				if(!$dbField) {
					continue;
				}
				
				if($value instanceof Domain) {
					$value = $value->ID;
				} else if($value instanceof \DateTime) {
					$value = $value->format("Y-m-d H:i:s");
				}
				
				$checksum .= $value;
			}
			
			return md5($checksum);
		}
	}
?>