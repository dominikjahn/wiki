var Wiki = {
	var currentUserID;
	var currentUserLoginname;
	var currentUserPassword;
	
	/**
	 * Validates login credentials with server and sets currentUser information for this instance
	 * @returns {status, message, user: {user_id, loginname}}
	 */
	var SignIn = function(loginname, password, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Removes currentUser information from this instance
	 */
	var SignOut = function(callback) {
	
	}
	
	/**
	 * Retrieves a page by its name
	 * @returns {status, message, page: {page_id, name, content, visibility, modification, last_edit: {user_id, loginname, timestamp}}}
	 */
	var GetPageByName = function(name, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Retrieves a page by its ID
	 */
	var GetPageByID = function(name, raw, positive_callback, negative_callback, error_callback) {
	
	}
	
	var CreateOrSavePage = function(pagedata, positive_callback, negative_callback, error_callback) {
	
	}
	
	var DeletePage = function(pageID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetVersionsByPage = function(pageID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetVersionByID = function(versionID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetUsers = function(positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetUserByLoginname = function(loginname, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetUserByID = function(userID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var CreateOrSaveUser = function(userdata, positive_callback, negative_callback, error_callback) {
	
	}
	
	var DeleteUser = function(userID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetGroups = function(positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetGroupByName = function(name, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetGroupByID = function(groupID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var CreateOrSaveGroup = function(groupdata, positive_callback, negative_callback, error_callback) {
	
	}
	
	var DeleteGroup = function(groupID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetUsersInGroup = function(groupID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetUsersNotInGroup = function(groupID, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Adds multiple users to a group
	 * @parameter groupID integer
	 * @parameter users array with userID's (e.g. [1,2,3])
	 */
	var AddUsersToGroup = function(groupID, users, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Removes multiple users from a group
	 * @parameter groupID integer
	 * @parameter users array with userID's (e.g. [1,2,3])
	 */
	var RemoveUsersFromGroup = function(groupID, users, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Grants multiple permissions to a user
	 * @parameter userID integer
	 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
	 */
	var GrantPermissions = function(userID, permissions, positive_callback, negative_callback, error_callback) {
	
	}
	
	/**
	 * Revokes multiple permissions of a user
	 * @parameter userID integer
	 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
	 */
	var RevokePermissions = function(userID, permissions, positive_callback, negative_callback, error_callback) {
	
	}
	
	var GetPermissionsForUser = function(userID, positive_callback, negative_callback, error_callback) {
	
	}
	
	var UserHasPermission = function(userID, permission, positive_callback, negative_callback, error_callback) {
		
	}
};