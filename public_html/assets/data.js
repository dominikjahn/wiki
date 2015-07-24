var Wiki = function() {
	return {
		'currentUserID': null,
		'currentUserLoginname': null,
		'currentUserPassword': null,
		
		'Request': function(type, url, data, positive_callback, negative_callback, error_callback) {
			var wiki = this;
			
			var data = data || null;
			var type = type || "GET";
			
			var positive_callback = positive_callback || function() {};
			var negative_callback = negative_callback || function() {};
			var error_callback = error_callback || /*function() {}; */	function(xhr, type, message) {
																			alert("! Error !\n\n"+type+": "+message+"\n\nSigned in as "+this.currentUserLoginname); 
																		};
			
			$.ajax({
				'type': type,
				'url': url,
				'data': data,
				'dataType': 'json',
				'success': function(response) {
					if(response.status == 200) {
						positive_callback(response);
					} else {
						negative_callback(response);
					}
				},
				'error': function(xhr, type, message) {
					error_callback(xhr, type, message);
				},
				beforeSend: function(xhr)
				{
					if(wiki.currentUserID) {
						xhr.setRequestHeader("Authorization", "Basic " + window.btoa(wiki.currentUserLoginname+":"+wiki.currentUserPassword));
					}
				}
			});
		},
		
		/**
		 * Validates login credentials with server and sets currentUser information for this instance
		 * @returns {status, message, user: {user_id, loginname}}
		 */
		'SignIn': function(loginname, password, positive_callback, negative_callback, error_callback) {
			var wiki = this;
			
			this.Request(	"GET", "request.php?command=CheckLoginCredentials&loginname="+loginname+"&password="+password, null,
			
							// positive_callback
							function(response) {
								wiki.currentUserID = response.user.user_id;
								wiki.currentUserLoginname = loginname;
								wiki.currentUserPassword = password;
								
								positive_callback(response);
							},
							// negative_callback
							function(response) {
								wiki.currentUserID = null;
								wiki.currentUserLoginname = null;
								wiki.currentUserPassword = null;
								
								negative_callback(response);
							},
							
							// error_callback
							error_callback
			);
		},
		
		/**
		 * Removes currentUser information from this instance
		 */
		'SignOut': function(callback) {
			this.currentUserID = null;
			this.currentUserLoginname = null;
			this.currentUserPassword = null;
			
			callback();
		},
		
		/**
		 * Retrieves a page by its name
		 * @returns {status, message, page: {page_id, name, content, visibility, modification, last_edit: {user_id, loginname, timestamp}}}
		 */
		'GetPageByName': function(name, raw, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=DisplayPage&page="+name+(raw ? "&raw" : ""), null, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Retrieves a page by its ID
		 */
		'GetPageByID': function(pageID, raw, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=DisplayPage&pageID="+pageID+(raw ? "&raw" : ""), null, positive_callback, negative_callback, error_callback);
		},
		
		'CreateOrSavePage': function(pagedata, positive_callback, negative_callback, error_callback) {
			this.Request("POST", "request.php?command=SavePage", pagedata, positive_callback, negative_callback, error_callback);
		},
		
		'DeletePage': function(pageID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=DeletePage", {'pageID':pageID}, positive_callback, negative_callback, error_callback);
		},
		
		'GetVersionsByPage': function(pageID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetVersionByID': function(versionID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetUsers': function(positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetUsers", null, positive_callback, negative_callback, error_callback);
		},
		
		'GetUserByLoginname': function(loginname, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetUserByID': function(userID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'CreateOrSaveUser': function(userdata, positive_callback, negative_callback, error_callback) {
		
		},
		
		'DeleteUser': function(userID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetGroups': function(positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetGroups", null, positive_callback, negative_callback, error_callback);
		},
		
		'GetGroupByName': function(name, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetGroupByID': function(groupID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'CreateOrSaveGroup': function(groupdata, positive_callback, negative_callback, error_callback) {
		
		},
		
		'DeleteGroup': function(groupID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetUsersInGroup': function(groupID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetUsersNotInGroup': function(groupID, positive_callback, negative_callback, error_callback) {
		
		},
		
		/**
		 * Adds multiple users to a group
		 * @parameter groupID integer
		 * @parameter users array with userID's (e.g. [1,2,3])
		 */
		'AddUsersToGroup': function(groupID, users, positive_callback, negative_callback, error_callback) {
		
		},
		
		/**
		 * Removes multiple users from a group
		 * @parameter groupID integer
		 * @parameter users array with userID's (e.g. [1,2,3])
		 */
		'RemoveUsersFromGroup': function(groupID, users, positive_callback, negative_callback, error_callback) {
		
		},
		
		/**
		 * Grants multiple permissions to a user
		 * @parameter userID integer
		 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
		 */
		'GrantPermissions': function(userID, permissions, positive_callback, negative_callback, error_callback) {
		
		},
		
		/**
		 * Revokes multiple permissions of a user
		 * @parameter userID integer
		 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
		 */
		'RevokePermissions': function(userID, permissions, positive_callback, negative_callback, error_callback) {
		
		},
		
		'GetPermissionsForUser': function(userID, positive_callback, negative_callback, error_callback) {
		
		},
		
		'UserHasPermission': function(userID, permission, positive_callback, negative_callback, error_callback) {
			
		}
	}
};