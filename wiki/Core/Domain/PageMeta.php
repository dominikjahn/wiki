<?php
namespace Wiki\Domain;

use Wiki\Domain\Manager\PageManager;
use Wiki\Domain\Manager\CategoryManager;
use Wiki\Domain\Manager\CategoryPageManager;
use Wiki\Domain\Manager\PageLinkManager;
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
			
		return parent::Save();
	}

	public function Delete() {
		return parent::Delete();
	}

	protected function CalculateChecksum() {
		return md5($this->Status.$this->page->ID.$this->user->ID.$this->data);
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
	 // CONSTANTS
	//

	const DB_TABLE = "pagemeta";

}
?>