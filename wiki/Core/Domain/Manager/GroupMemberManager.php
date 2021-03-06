<?php
	namespace Wiki\Domain\Manager;
	
	use Wiki\Database\DatabaseConnection;
	use Wiki\Domain\Factory\GroupMemberFactory;
	use Wiki\Domain\Group;
	use Wiki\Domain\User;
	use Wiki\Domain\GroupMember;
	
	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	class GroupMemberManager extends DomainManager
	{
		  //
		 // METHODS
		//
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByID($id) {
			$fromCache = $this->GetFromCache($id);
		  
		    if($fromCache) return $fromCache;
		  
			$db = DatabaseConnection::GetInstance();
			
			$sqlObject = "SELECT groupmember_id, status, checksum, group_id, user_id FROM %PREFIX%groupmember WHERE groupmember_id = :id";
			$stmObject = $db->Prepare($sqlObject);
			$rowObject = $stmObject->ReadSingle(["id" => $id]);
			
			if(!$rowObject) {
				return null;
			}
			
			$objectFactory = GroupMemberFactory::GetInstance();
			
			$object = new GroupMember();
			
			$this->AddToCache($object, $id);
			
			$objectFactory->FromDataRow($object, $rowObject);
		
			$stmObject->Close();
			
			return $object;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByGroup(Group $group) {
			
			$objects = [];
			
			if($group->ID !== 1) {
				$db = DatabaseConnection::GetInstance();
				
				$sqlObjects = "SELECT groupmember_id, status, checksum, group_id, user_id FROM %PREFIX%groupmember WHERE status = 100 AND group_id = :group";
				$stmObjects = $db->Prepare($sqlObjects);
				
				$resObjects = $stmObjects->Read(["group" => $group]);
				
				if(!$resObjects) {
					return null;
				}
				
				$objectFactory = GroupMemberFactory::GetInstance();
				
				while(($rowObject = $resObjects->NextRow()) != null) {
					$object = new GroupMember();
					$this->AddToCache($object);
					$objectFactory->FromDataRow($object, $rowObject);
					
					$objects[] = $object;
				}
			
				$stmObjects->Close();
			} else {
				$users = UserManager::GetInstance()->GetAll();
				
				foreach($users as $user) {
					$member = new GroupMember();
					$member->Status = 100;
					$member->Group = $group;
					$member->User = $user;
					
					$objects[] = $member;
				}
			}
			
			return $objects;
		}
		
		/**
		 * @author Dominik Jahn <dominik1991jahn@gmail.com>
		 * @version 0.1
		 * @since 0.1
		 */
		public function GetByUser(User $user) {
			$db = DatabaseConnection::GetInstance();
			
			$sqlObjects = "SELECT groupmember_id, status, checksum, group_id, user_id FROM %PREFIX%groupmember WHERE status = 100 AND user_id = :user";
			$stmObjects = $db->Prepare($sqlObjects);
			$resObjects = $stmObjects->Read(["user" => $user]);
			
			if(!$resObjects) {
				return null;
			}
			
			$objects = [];
			$objectFactory = GroupMemberFactory::GetInstance();
			
			while(($rowObject = $resObjects->NextRow()) != null) {
				$object = new GroupMember();
				$this->AddToCache($object);
				$objectFactory->FromDataRow($object, $rowObject);
				
				$objects[] = $object;
			}
		
			$stmObjects->Close();
			
			// Check if 'public' group is included
			$public_found = false;
			
			foreach($objects as $object) {
				if($object->Group->ID === 1) {
					$public_found = true;
					break;
				}
			}
			
			if(!$public_found) {
				$member = new GroupMember();
				$member->Status = 100;
				$member->Group = GroupManager::GetInstance()->GetByID(1);
				$member->User = $user;
				
				$objects[] = $member;
			}
			
			return $objects;
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
				self::$instance = new GroupMemberManager();
			}
			
			return self::$instance;
		}
		
		  //
		 // VARIABLES
		//
		
		private static $instance;
	}
?>