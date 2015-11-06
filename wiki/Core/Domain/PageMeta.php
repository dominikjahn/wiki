<?php
namespace Wiki\Domain;

use Wiki\Domain\Manager\PageManager;
use Wiki\Domain\Manager\CategoryManager;
use Wiki\Domain\Manager\CategoryPageManager;
use Wiki\Domain\Manager\PageLinkManager;
use Wiki\Domain\Manager\PageMetaManager;
use Wiki\Exception\AuthorizationMissingException;
use Wiki\Exception\PagenameAlreadyTakenException;
use Wiki\Exception\CannotDeleteHomepageException;
use Wiki\Tools\StringTools;

/**
 * @table pagemeta
 * @author Dominik Jahn <dominik1991jahn@gmail.com>
 * @version 0.1
 * @since 0.1
 */
class PageMeta extends Domain
{
	//
	// CONSTRUCTOR
	//

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	public function __construct() {
			
	}

	//
	// METHODS
	//

	public function jsonSerialize() {
			
		return [
			"data" => $this->data
		];
	}

	public function Save() {

		$this->data = json_encode($this->data);
		$success = parent::Save();
		$this->data = json_decode($this->data);

		return $success;
	}

	public function Delete() {
		return parent::Delete();
	}

	protected function CalculateChecksum() {
		return md5($this->Status.$this->page->ID.$this->user->ID.$this->data);
	}

	public function __tostring() {
		return (string) $this->data;
	}

	//
	// ATTRIBUTES
	//

	/**
	 * @field page_id
	 */
	protected $page;

	/**
	 * @field user_id
	 */
	protected $user;

	/**
	 * @field data
	 */
	protected $data;

	  //
	 // GETTERS / SETTERS
	//

	# Page

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetPage() {
		return $this->page;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetPage(Page $value) {
		$this->page = $value;
	}

	# User

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetUser() {
		return $this->user;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetUser(User $value) {
		$this->user = $value;
	}

	# Data

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	* @version 0.1
	* @since 0.1
	*/
	protected function GetData() {
		return $this->data;
	}

	/**
	 * @author Dominik Jahn <dominik1991jahn@gmail.com>
	 * @version 0.1
	 * @since 0.1
	 */
	protected function SetData($value) {
		$this->data = $value;
	}

	  //
	 // FUNCTIONS
	//

	public static function LoadMetaData(Page $page, User $user)
	{
		$metaManager = PageMetaManager::GetInstance();

		if(!self::$currentGlobalPageMeta) {
			self::$currentGlobalPageMeta = $metaManager->GetGlobalByPage($page);
		}

		if(!self::$currentUserPageMeta) {
			self::$currentUserPageMeta = $metaManager->GetByPageAndUser($page, $user);
		}

		$globalMeta = self::$currentGlobalPageMeta;
		$userMeta = self::$currentUserPageMeta;

		if($globalMeta) {
			$globalMeta = $globalMeta->Data;
		} else {
			$globalMeta = [];
		}

		if($userMeta)
		{
			$userMeta = $userMeta->Data;
		}
		else
		{
			$userMeta = [];
		}

		$meta = (object)array_merge_recursive((array)$globalMeta, (array)$userMeta);

		return $meta;
	}

	public static function AddMetaData($key, $value, Page $page, User $user = null)
	{
		$metaManager = PageMetaManager::GetInstance();

		if(!self::$currentGlobalPageMeta) {
			self::$currentGlobalPageMeta = $metaManager->GetGlobalByPage($page);
		}

		if(!self::$currentUserPageMeta) {
			self::$currentUserPageMeta = $metaManager->GetByPageAndUser($page, $user);
		}

		$meta = ($user ? self::$currentUserPageMeta : self::$currentGlobalPageMeta);

		$meta->Data->$key = $value;

		$meta->Save();

		return $meta->Data;
	}

	  //
	 // VARIABLES
	//

	private static $currentGlobalPageMeta;
	private static $currentUserPageMeta;

	  //
	 // CONSTANTS
	//

	const DB_TABLE = "pagemeta";

}
?>