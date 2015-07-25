var Wiki = function() {
	return {
		'currentUserID': null,
		'currentUserLoginname': null,
		'currentUserPassword': null,
		'defaultPositiveHandler': function(response) { alert(response.message); },
		'defaultNegativeHandler': function(response) { alert(response.message); },
		'defaultErrorHandler': function(xhr, type, message) {
				alert("! Error !\n\n"+type+": "+message+"\n\nSigned in as "+this.currentUserLoginname); 
			},
		
		'Request': function(type, url, data, positive_callback, negative_callback, error_callback) {
			var wiki = this;
			
			var data = data || null;
			var type = type || "GET";
			
			var positive_callback = positive_callback || this.defaultPositiveHandler;
			var negative_callback = negative_callback || this.defaultNegativeHandler;
			var error_callback = error_callback       || this.defaultErrorHandler;
			
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
		 * Render a page
		 * @returns {status, message, page: {page_id, name, content, visibility, modification, last_edit: {user_id, loginname, timestamp}}}
		 */
		'DisplayPage': function(name, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=DisplayPage&page="+name, null, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Retrieves a page by its name
		 * @returns {status, message, page: {page_id, name, content, visibility, modification, last_edit: {user_id, loginname, timestamp}}}
		 */
		'GetPageByName': function(name, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetPage&page="+name, null, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Retrieves a page by its ID
		 */
		'GetPageByID': function(pageID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetPage&pageID="+pageID, null, positive_callback, negative_callback, error_callback);
		},
		
		'CreateOrSavePage': function(pagedata, positive_callback, negative_callback, error_callback) {
			this.Request("POST", "request.php?command=SavePage", pagedata, positive_callback, negative_callback, error_callback);
		},
		
		'DeletePage': function(pageID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=DeletePage", {'pageID':pageID}, positive_callback, negative_callback, error_callback);
		},
		
		'GetVersionsByPage': function(pageID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetVersions&pageID="+pageID, null, positive_callback, negative_callback, error_callback);
		},
		
		'GetVersionByID': function(versionID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetVersion&versionID="+versionID, null, positive_callback, negative_callback, error_callback);
		},
		
		'GetUsers': function(positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetUsers", null, positive_callback, negative_callback, error_callback);
		},
		
		'GetUserByLoginname': function(loginname, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetUser&loginname="+loginname, null, positive_callback, negative_callback, error_callback);
		},
		
		'GetUserByID': function(userID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetUser&userID="+userID, null, positive_callback, negative_callback, error_callback);
		},
		
		'CreateOrSaveUser': function(userdata, positive_callback, negative_callback, error_callback) {
			this.Request("POST", "request.php?command=SaveUser", userdata, positive_callback, negative_callback, error_callback);
		},
		
		'DeleteUser': function(userID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=DeleteUser", {'userID':userID}, positive_callback, negative_callback, error_callback);
		},
		
		'GetGroups': function(positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetGroups", null, positive_callback, negative_callback, error_callback);
		},
		
		'GetGroupByName': function(name, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetGroup&name="+name, null, positive_callback, negative_callback, error_callback);
		},
		
		'GetGroupByID': function(groupID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetGroup&groupID="+groupID, null, positive_callback, negative_callback, error_callback);
		},
		
		'CreateOrSaveGroup': function(groupdata, positive_callback, negative_callback, error_callback) {
			this.Request("POST", "request.php?command=SaveGroup", groupdata, positive_callback, negative_callback, error_callback);
		},
		
		'DeleteGroup': function(groupID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=DeleteGroup", {'groupID':groupID}, positive_callback, negative_callback, error_callback);
		},
		
		'GetUsersInGroup': function(groupID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=GetUsers&groupID="+groupID+"&mode=INCLUDE", null, positive_callback, negative_callback, error_callback);
		},
		
		'GetUsersNotInGroup': function(groupID, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=GetUsers&groupID="+groupID+"&mode=EXCLUDE", null, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Adds multiple users to a group
		 * @parameter groupID integer
		 * @parameter users array with userID's (e.g. [1,2,3])
		 */
		'AddUsersToGroup': function(groupID, users, positive_callback, negative_callback, error_callback) {
			this.Request("PUT", "request.php?command=SaveGroupMember", {'groupID': groupID, 'userIDs': users}, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Removes multiple users from a group
		 * @parameter groupID integer
		 * @parameter users array with userID's (e.g. [1,2,3])
		 */
		'RemoveUsersFromGroup': function(groupID, users, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=SaveGroupMember", {'groupID': groupID, 'userIDs': users}, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Grants multiple permissions to a user
		 * @parameter userID integer
		 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
		 */
		'GrantPermissions': function(userID, permissions, positive_callback, negative_callback, error_callback) {
			this.Request("PUT", "request.php?command=SaveUserPermission", {'userID': userID, 'permissions': permissions}, positive_callback, negative_callback, error_callback);
		},
		
		/**
		 * Revokes multiple permissions of a user
		 * @parameter userID integer
		 * @parameter permissions array (e.g. [CREATE_PAGES, CREATE_USERS])
		 */
		'RevokePermissions': function(userID, permissions, positive_callback, negative_callback, error_callback) {
			this.Request("DELETE", "request.php?command=SaveUserPermission", {'userID': userID, 'permissions': permissions}, positive_callback, negative_callback, error_callback);
		},
		
		'GetPermissionsForUser': function(userID, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=GetUserPermissions&userID="+userID, null, positive_callback, negative_callback, error_callback);
			
		},
		
		'UserHasPermission': function(permission, positive_callback, negative_callback, error_callback) {
			this.Request("GET", "request.php?command=UserHasPermission&userID="+this.currentUserID+"&permission="+permission, null, positive_callback, negative_callback, error_callback);
		}
	}
};