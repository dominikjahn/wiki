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
	protected function SetUser(User $value = null) {
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

	private static function PrepareMetaData(Page $page, User $user = null)
	{
		$metaManager = PageMetaManager::GetInstance();

		if(!self::$currentGlobalPageMeta)
		{
			self::$currentGlobalPageMeta = $metaManager->GetGlobalByPage($page);
		}

		// If it's still null, create it
		if(!self::$currentGlobalPageMeta)
		{
			self::$currentGlobalPageMeta = new PageMeta();
			self::$currentGlobalPageMeta->Status = 100;
			self::$currentGlobalPageMeta->Page = $page;
		}

		if(!self::$currentUserPageMeta && $user)
		{
			self::$currentUserPageMeta = $metaManager->GetByPageAndUser($page, $user);
		}

		// If it's still null, create it
		if(!self::$currentUserPageMeta && $user)
		{
			self::$currentUserPageMeta = new PageMeta();
			self::$currentUserPageMeta->Status = 100;
			self::$currentUserPageMeta->Page = $page;
			self::$currentUserPageMeta->User = $user;
		}
	}

	public static function LoadMetaData(Page $page, User $user = null)
	{
		self::PrepareMetaData($page,$user);

		$globalMeta = self::$currentGlobalPageMeta;
		$userMeta = self::$currentUserPageMeta;

		if($globalMeta) {
			$globalMeta = $globalMeta->Data;
		} else {
			$globalMeta = [];
		}

		if($userMeta && $user)
		{
			$userMeta = $userMeta->Data;
		}
		else
		{
			$userMeta = [];
		}

		$meta = (object)array_merge((array)$globalMeta, (array)$userMeta);

		return $meta;
	}

	public static function AddGlobalMetaData($key, $value, Page $page)
	{
		self::PrepareMetaData($page);

		$meta = self::$currentGlobalPageMeta;

		$data = $meta->Data ?: new \stdClass();

		$data->$key = $value;
		$meta->Data = $data;

		$meta->Save();

		return $meta->Data;
	}

	public static function AddUserMetaData($key, $value, Page $page, User $user)
	{
		self::PrepareMetaData($page,$user);

		$meta = self::$currentUserPageMeta;

		$data = $meta->Data;

		$data = $meta->Data ?: new \stdClass();

		$data->$key = $value;
		$meta->Data = $data;

		$meta->Save();

		return $meta->Data;
	}

	public static function RemoveGlobalMetaData($key, Page $page)
	{
		self::PrepareMetaData($page);

		$meta = self::$currentGlobalPageMeta;

		$data = (array) $meta->Data;

		unset($data[$key]);
		$meta->Data = (object) $data;

		$meta->Save();

		return $meta->Data;
	}

	public static function RemoveUserMetaData($key, Page $page, User $user)
	{
		self::PrepareMetaData($page,$user);

		$meta = self::$currentUserPageMeta;

		$data = (array) $meta->Data;

		unset($data[$key]);
		$meta->Data = (object) $data;

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