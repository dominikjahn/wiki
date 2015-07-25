var wiki;
var cache = [];

$(function() {
	Reset();
	
	var loginname = GetCookie('wiki_loginname');
	var password = GetCookie('wiki_password');
	
	var view = ExtractPageName();
	
	wiki = new Wiki();
	wiki.SignIn(loginname, password, function(response) { ShowUserInfo(response); GoToView(view); }, function(response) { ShowSignInUpLinks(response); GoToView(view); } );
	
	var fromCache = localStorage.getItem("wiki_cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
});

var GoToView = function(view) {
	
	var view = view.split('-');
	
	switch(view[0]) {
		case "NewPage":
			DisplayNewPageForm();
			break;
			
		case "Search":
			DisplaySearchForm();
			break;
		
		case "SignUp":
			DisplaySignUpForm();
			break;
			
		case "SignIn":
			DisplaySignInForm();
			break;
			
		case "SignOut":
			SignOut();
			break;
			
		case "ChangePassword":
			DisplayChangePasswordForm();
			break;
			
		case "Users":
			GoToUserList();
			break;
			
		case "Groups":
			GoToGroupList();
			break;
		
		case "EditPage":
			GoToEditPageForm(view[1]);
			break;
			
		case "Versions":
			DisplayVersions(view[1]);
			break;
			
		case "Version":
			DisplayVersion(view[1]);
			break;
			
		case "User":
			DisplayUser(view[1]);
			break;
			
		case "EditUser":
			DisplayEditUserForm(view[1]);
			break;
			
		case "Group":
			DisplayGroup(view[1]);
			break;
			
		case "EditGroup":
			DisplayEditGroupForm(view[1]);
			
		case "Category":
			DisplayCategory(viewName[1]);
			break;
			
		default:
			GoToPage(view[0]);
	}
}

var Reset = function() {
	// Views
	$("#Loading").css("display","block");
	$("#PageNotFound").css("display","none");
	$("#NotAuthorized").css("display","none");
	$("#Offline").css("display","none");
	$("#Error").css("display","none");
	$("#SignInForm").css("display","none");
	$("#SignUpForm").css("display","none");
	$("#ChangePasswordForm").css("display","none");
	$("#DisplayPage").css("display","none");
	$("#NewPage").css("display","none");
	$("#EditPage").css("display","none");
	$("#Versions").css("display","none");
	$("#UserManagement").css("display","none");
	$("#EditPermissions").css("display","none");
	$("#NewUserForm").css("display","none");
	$("#NewGroupForm").css("display","none");
	$("#GroupUsers").css("display","none");
	
	// Navbar
	$("#NavEditPage").css("display","none");
	$("#NavGetVersions").css("display","none");
	$("#NavDropChanges").css("display","none");
	$("#NavPreviewChanges").css("display","none");
	$("#NavSaveChanges").css("display","none");
	$("#NavBackToComposer").css("display","none");
	$("#NavPublicPage").css("display","none");
	$("#NavProtectedPage").css("display","none");
	$("#NavPrivatePage").css("display","none");
	$("#NavGroupPrivatePage").css("display","none");
	
	// Display loading screen
	$("#Loading").css("display","block");
	
	// Unbind all hotkeys
	$(document).unbind('keydown');
}

var HideLoading = function() {
	$("#Loading").css("display","none");
}

var ExtractPageName = function() {
	var currentUrl = window.location.href;
	var pageName = currentUrl.split("").reverse().join("").split('/',1);
	pageName = pageName[0].split("").reverse().join("").split(".",1);
	pageName = pageName[0];
	
	if(pageName == "" || pageName == "index") {
		pageName = "Homepage";
	}
	
	return pageName;
}

var UpdateWindow = function(title, url) {
	document.title = title;
	
	if(url) {
		window.history.replaceState({}, title, './'+url);
	}
}

var HandleErrorCodes = function(response) {
	switch(response.status) {
		case 401:
			DisplayNotAuthorizedError();
			break;
			
		case 404:
			DisplayNotFoundError();
			break;
	}
}

/*
 * Footer bar
 */

var ShowUserInfo = function(response) {
	var preText = $('<span>Signed in as </span>');
	var changePasswordLink = $('<a href="ChangePassword.html"><strong>'+response.user.loginname+'</strong></a>');
	var postText = $('<span> &bull; </span>');
	var logoutLink = $('<a href="SignOut.html">Sign out</a>');
	
	changePasswordLink.click(DisplayChangePasswordForm);
	logoutLink.click(SignOut);
	
	$('#SignInText').empty().append(preText).append(changePasswordLink).append(postText).append(logoutLink);
	
	wiki.UserHasPermission("MANAGE_USERS",
							function() {
								$("#NavUsers").css("display","block").unbind("click").click(GoToUserList); },
							function() {
								$("#NavUsers").css("display","none").unbind("click"); });
}

var ShowSignInUpLinks = function() {
	var loginLink = $('<a href="SignIn.html">Sign in</a>');
	loginLink.click(DisplaySignInForm);
	
	var sep = $('<span> &bull; </span>');
	
	var signupLink = $('<a href="SignUp.html">Sign up</a>');
	signupLink.click(DisplaySignUpForm);
	
	$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
}

/*
 * Sign in / sign up / Change password
 */

var DisplaySignInForm = function(e) {
	if(e != 'undefined') e.preventDefault();
	
	Reset();
	HideLoading();
	
	UpdateWindow("Sign in", "SignIn.html");
	$("#SignInForm").css("display","block").unbind("submit").submit(SignIn);
	
	return false;
}

var DisplaySignUpForm = function(e) {
	if(e != 'undefined') e.preventDefault();
	
	Reset();
	HideLoading();
	
	UpdateWindow("Sign up", "SignUp.html");
	$("#SignUpForm").css("display","block").unbind("submit").submit(SignUp);
	
	return false;
}

var DisplayChangePasswordForm = function(e) {
	if(e) { e.preventDefault() };
	
	Reset();
	HideLoading();
	
	UpdateWindow("Change password", "ChangePassword.html");
	$("#ChangePasswordForm").css("display","block").unbind("submit").submit(ChangePassword);
	
	return false;
}

var SignIn = function() {
	Reset();
	
	loginname = $('#SignInForm-InputLoginName').val();
	password = $('#SignInForm-InputPassword').val();
	
	password = md5(password);
	
	SetCookie("loginname", loginname);
	SetCookie("password", password);
	
	wiki.SignIn(loginname, password, function(response) { ShowUserInfo(response); GoToPage('Homepage'); }, function(response) { DisplaySignInForm(); alert(response.message); });
	
	return true;
}

var SignOut = function() {
	SetCookie("loginname", "");
	SetCookie("password", "");
	
	wiki.SignOut(function() { ShowSignInUpLinks(); GoToPage('Homepage'); });
	
	return false;
}

var SignUp = function() {
	var loginname = $('#SignUpForm-InputLoginName').val();
	var password = $('#SignUpForm-InputPassword').val();
	var passwordconfirmation = $('#SignUpForm-InputConfirmPassword').val();
	
	if(password != passwordconfirmation) {
		alert("The passwords aren't alike");
		return;
	}
	
	password = md5(password);
	
	userdata = {'userID': 0, 'loginname': loginname, 'password': password};
	
	wiki.CreateOrSaveUser(userdata,
		function(response) {
			alert(response.message);
			DisplaySignInForm();
		},
		HandleErrorCodes,
		DisplayError);
	
	return false;
}

var ChangePassword = function() {
	
}

/*
 * Page
 */

var GoToPage = function(pagename) {
	wiki.GetPageByName(pagename, false,
									function(response) {
											cache[pagename] = response;
											localStorage.setItem("wiki_cache", JSON.stringify(cache));
											DisplayPage(response);
											
											if(response.page.can_edit) {
												$("#NavEditPage").css("display","block").unbind("click").click(function() { GoToEditPageForm(pagename); }).attr("href","./EditPage-"+pagename+".html");
												$("#NavGetVersions").css("display","block").unbind("click").click(function() { GoToVersions(pagename); }).attr("href","./Versions-"+pagename+".html");
											}
											
											UpdateWindow(response.page.title, response.page.name+".html");
									},
									HandleErrorCodes,
									//function(response) { GetPageFromCache(pagename, false, response); },
									
									function(xhr, type, message) { GetPageFromCache(pagename, true, xhr, type, message); }
	);
	
	return false;
}

var GetPageFromCache = function(pagename, error, response_or_xhr) {
	if(pagename in cache) {
		DisplayPage(cache[pagename]);
		UpdateWindow(cache[pagename].page.title + ' (from cache)', 'Cache:'+pagename+'.html');
	} else if(!error) {
		HandleErrorCodes(response_or_xhr);
	} else {
		DisplayError(response_or_xhr, type, message);
	}
}

var DisplayPage = function(response, titlewrap) {
	Reset();
	
	$("#DisplayPage").css("display","block");
	$("#DisplayPage-Title").html(response.page.title);
	$("#DisplayPage-Content").html(response.page.content);
	
	switch(response.page.visibility) {
		case "PUBLIC": $('#NavPublicPage').css("display","block"); break;
		case "PROTECTED": $('#NavProtectedPage').css("display","block"); break;
		case "PRIVATE": $('#NavPrivatePage').css("display","block"); break;
		case "GROUPPRIVATE": $('#NavGroupPrivatePage').css("display","block"); break;
	}

	if(response.no_headline) {
		$('#DisplayPage-Title').css("display", "none");
		$('#DisplayPage-TitleSeparator').css("display", "none");
	}

	if(response.no_navbar) {
		$('#Navbar').css("display","none");
		$('#content').css("margin-top","0px");
	}
	
	if(response.no_footerbar) {
		$('#FooterBar').css("display","none");
	}
	
	$('#DisplayPage-LastEdit-Timestamp').html(response.page.last_edit.timestamp);
	$('#DisplayPage-LastEdit-User').html(response.page.last_edit.user);
	
	HideLoading();
}

var GoToEditPageForm = function(pagename) {
	Reset();
	
	wiki.GetPageByName(pagename, true, DisplayEditPageForm);
	
	return false;
}

var DisplayEditPageForm = function(response) {
	UpdateWindow('Editing page \''+response.page.title+'\'','EditPage-'+response.page.name+'.html');
	
	$("#EditPage-Title").html(response.page.title);
	$("#EditPage-InputTitle").val(response.page.title);
	$("#EditPage-InputContent").val(response.page.content).hide();
	$("#EditPage-InputSummary").val("");
	$("#EditPage-Visibility-"+response.page.visibility).attr("checked",true);
	$("#EditPage-Manipulation-"+response.page.manipulation).attr("checked",true);
	$("#EditPage-MinorChange").attr("checked",false);
	$("#EditPage-Owner").empty();
	$("#EditPage-Group").empty();
	
	var currentOwner = response.page.owner;
	var currentGroup = response.page.group;
	
	wiki.GetUsers(
		function(response) {
			for(var u = 0; u < response.users.length; u++) {
				var user = response.users[u];
					
				$("#EditPage-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == currentOwner.user_id ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
			}
		}
	);
	
	wiki.GetGroups(
		function(response) {
			for(var g = 0; g < response.groups.length; g++) {
				var group = response.groups[g];
					
				$("#EditPage-Group").append('<option value="'+group.group_id+'"'+(group.group_id == currentGroup.group_id ? ' selected="selected"' : '')+'>'+group.name+'</option>');
			}
		}
	);
	
	if(response.page.page_id === 1) {
		$("#EditPage-DeletePage").attr("disabled", true);
	} else {
		$("#EditPage-DeletePage").attr("disabled", false).unbind("click").click(GoToDeletePageDialog);
	}
	
	$("#NavDropChanges").css("display","block");
	$("#NavPreviewChanges").css("display","block").unbind("click").click(PreviewExistingPage);
	$("#NavSaveChanges").css("display","block").unbind("click").click(SaveExistingPage);
	
	BindKey(83,SaveExistingPage,true); // Ctrl+S
	BindKey(27,DropChanges,false); // ESC
	
	EditPageEditor = ace.edit("EditPage-InputContent-Editor");
	EditPageEditor.getSession().setMode("ace/mode/html");
	EditPageEditor.getSession().setMode("ace/mode/javascript");
	EditPageEditor.getSession().setMode("ace/mode/css");
	EditPageEditor.getSession().setMode("ace/mode/php");
	//EditPageEditor.getSession().setMode("ace/mode/markdown");
	EditPageEditor.setOptions({ maxLines: Infinity });
	EditPageEditor.getSession().setValue($("#EditPage-InputContent").val());
	
	HideLoading();
	$("#EditPage").css("display","block").data("pageID",response.page.page_id);
}

var PreviewExistingPage = function() {

}

var SaveExistingPage = function() {
	var pagedata = {
		'pageID': $("#EditPage").data("pageID"),
		'title': $("#EditPage-InputTitle").val(),
		'content': $("#EditPage-InputContent").val(),
		'summary': $("#EditPage-InputSummary").val(),
		'minor_edit': ($('#EditPage-MinorChange:checked').length > 0),
		'visibility': $('input[name=EditPage-Visiblity]:checked').val(),
		'manipulation': $('input[name=EditPage-Manipulation]:checked').val(),
		'owner': $("#EditPage-Owner").val(),
		'group': $("#EditPage-Group").val()
	};
	
	wiki.CreateOrSavePage(pagedata,
		function(response) {
			alert("The page was saved successfully");
			
			GoToPage(response.page.name);
		},
		
		HandleErrorCodes,
		DisplayError
	);
}

var DisplayNewPageForm = function() {
	Reset();
	
	$("#NewPage-InputTitle").val("");
	$("#NewPage-InputContent").val("").hide();
	$("#NewPage-InputSummary").val("Initialized page");
	$("#NewPage-Visibility-Public").attr("checked",true);
	$("#NewPage-Manipulation-Everyone").attr("checked",true);
	$("#NewPage-Owner").empty();
	$("#NewPage-Group").empty();
	
	wiki.GetUsers(
		function(response) {
			for(var u = 0; u < response.users.length; u++) {
				var user = response.users[u];
					
				$("#NewPage-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == wiki.currentUserID ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
			}
		}
	);
	
	wiki.GetGroups(
		function(response) {
			for(var g = 0; g < response.groups.length; g++) {
				var group = response.groups[g];
					
				$("#NewPage-Group").append('<option value="'+group.group_id+'">'+group.name+'</option>');
			}
		}
	);
	
	$("#NavDropChanges").css("display","block");
	$("#NavPreviewChanges").css("display","block").unbind("click").click(PreviewNewPage);
	$("#NavSaveChanges").css("display","block").unbind("click").click(SaveNewPage);
	
	BindKey(83,SaveNewPage,true); // Ctrl+S
	BindKey(27,DropChanges,false); // ESC
	
	NewPageEditor = ace.edit("NewPage-InputContent-Editor");
	NewPageEditor.getSession().setMode("ace/mode/html");
	NewPageEditor.getSession().setMode("ace/mode/javascript");
	NewPageEditor.getSession().setMode("ace/mode/css");
	NewPageEditor.getSession().setMode("ace/mode/php");
	//NewPageEditor.getSession().setMode("ace/mode/markdown");
	NewPageEditor.setOptions({ maxLines: Infinity });
	NewPageEditor.getSession().setValue("");
	
	HideLoading();
	$("#NewPage").css("display","block");
	
	return false;
}

var PreviewNewPage = function() {

}

var SaveNewPage = function() {
	var pagedata = {
		'pageID': null,
		'title': $("#NewPage-InputTitle").val(),
		'content': $("#NewPage-InputContent").val(),
		'summary': $("#NewPage-InputSummary").val(),
		'minor_edit': false,
		'visibility': $('input[name=NewPage-Visiblity]:checked').val(),
		'manipulation': $('input[name=NewPage-Manipulation]:checked').val(),
		'owner': $("#NewPage-Owner").val(),
		'group': $("#NewPage-Group").val()
	};
	
	wiki.CreateOrSavePage(pagedata,
		
		function(response) {
			alert("The page was created successfully");
			
			GoToPage(response.page.name);
		},
		
		HandleErrorCodes,
		DisplayError);
}

var DropChanges = function() {
	
}

var GoToDeletePageDialog = function(e) {
	var pageID = $("#EditPage").data("pageid");

	$("#DeletePageDialog-Confirm").unbind("click").click(DeletePage);
	$("#DeletePageDialog").data("pageid",pageID).modal();
}

var DeletePage = function() {
	var pageID = $("#DeletePageDialog").data("pageid");
	
	wiki.DeletePage(pageID,
					function(response) {
						alert(response.message);
						$("#DeletePageDialog").modal("hide");
						GoToPage('Homepage');
					}, HandleErrorCodes, DisplayError);
}

/*
 * Users & Groups
 */

var userListPopulated;
var groupListPopulated;
var GoToUserList = function(e) {
	if(e) { e.preventDefault(); }
	
	Reset();
	
	userListPopulated = false;
	groupListPopulated = false;
	
	wiki.GetUsers(function(response) { PopulateUserList(response, DisplayUserList); });
	wiki.GetGroups(function(response) { PopulateGroupList(response, DisplayUserList); });
	
	return false;
}

var GoToGroupList = function(e) {
	if(e) { e.preventDefault(); }
	
	Reset();
	
	userListPopulated = false;
	groupListPopulated = false;
	
	wiki.GetUsers(function(response) { PopulateUserList(response, DisplayGroupList); });
	wiki.GetGroups(function(response) { PopulateGroupList(response, DisplayGroupList); });
	
	return false;
}

var PopulateUserList = function(response, callback) {
	
	$("#User-List").empty();
	
	for(var u = 0; u < response.users.length; u++) {
		var user = response.users[u];
		
		$("#User-List").append('' +
			'	<tr>' +
			'		<td>' + user.loginname + '</td>' +
			'		<td><button type="button" class="btn btn-xs btn-warning EditPermissions" data-userid="'+user.user_id+'" data-loginname="'+user.loginname+'" '+(user.user_id == 2 ? 'disabled="disabled"' : '')+'><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> Permissions</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-danger DeleteUser" data-userid="'+user.user_id+'" '+((user.user_id == 1 || user.user_id == 2) ? 'disabled="disabled"' : '')+'><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Delete</button></td>' +
			'	</tr>');
	}
	
	userListPopulated = true;
	
	if(groupListPopulated == true) {
		callback();
	}
	
}

var PopulateGroupList = function(response, callback) {
	$("#Group-List").empty();
	
	for(var g = 0; g < response.groups.length; g++) {
		var group = response.groups[g];
		
		$("#Group-List").append('' +
			'	<tr>' +
			'		<td>' + group.name + '</td>' +
			'		<td><button type="button" class="btn btn-xs btn-primary GetGroupUsers" data-groupid="'+group.group_id+'"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> Users</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-danger DeleteGroup" data-groupid="'+group.group_id+'"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Delete</button></td>' +
			'	</tr>');
	}
	
	groupListPopulated = true;
	
	if(userListPopulated == true) {
		callback();
	}
}

var DisplayUserList = function() {

	HideLoading();
	
	UpdateWindow("User management", "Users.html");
	
	$("#UserManagement").css("display","block");
	$("#UserManagement-UserTab").tab("show");
	
	FinalizeUserManagement();
}

var DisplayGroupList = function() {
	
	HideLoading();
	
	UpdateWindow("Group management", "Groups.html");
	$("#UserManagement").css("display","block");
	$("#UserManagement-GroupTab").tab("show");
	
	FinalizeUserManagement();
}

var FinalizeUserManagement = function() {
	$("#Users-NewUser").unbind("click").click(DisplayNewUserForm);
	$("#Users-NewGroup").unbind("click").click(DisplayNewGroupForm);
	
	$(".EditPermissions").unbind("click").click(GoToEditPermissionsForm);
	$(".DeleteUser").unbind("click").click(GoToDeleteUserDialog);
	
	$(".GetGroupUsers").unbind("click").click(GoToGroupMembers);
	$(".DeleteGroup").unbind("click").click(GoToDeleteGroupDialog);
	
	$("#UserManagement-UserTab").on("shown.bs.tab", function() { UpdateWindow("User management", "Users.html"); } );
	$("#UserManagement-GroupTab").on("shown.bs.tab", function() { UpdateWindow("Group management", "Groups.html"); } );
}

var DisplayNewUserForm = function(e) {
	Reset();
	UpdateWindow("Create a new user", "NewUser.html");
	HideLoading();
	$("#NewUserForm").show().submit(SaveNewUser);
}

var SaveNewUser = function(e) {
	if(e) { e.preventDefault(); }
	
	var loginname = $("#NewUserForm-InputLoginname").val();
	var password = $("#NewUserForm-InputPassword").val();
	var confirmPassword = $("#NewUserForm-InputConfirmPassword").val();
	
	if(password != confirmPassword) {
		alert("The passwords don't match");
		return false;
	}
	
	password = md5(password);
	
	var userdata = {
		'userID': null,
		'loginname': loginname,
		'password': password
	};
	
	wiki.CreateOrSaveUser(userdata,
							function(response) {
								alert(response.message);
								GoToUserList();
							},
							HandleErrorCodes,
							DisplayError);
}

var DisplayNewGroupForm = function(e) {
	Reset();
	UpdateWindow("Create a new group", "NewGroup.html");
	HideLoading();
	
	$("#NewGroupForm").show().submit(SaveNewGroup);
}

var SaveNewGroup = function(e) {
	if(e) { e.preventDefault(); }
	
	var name = $("#NewGroupForm-InputName").val();
	
	var groupdata = {
		'groupID': null,
		'name': name
	};
	
	wiki.CreateOrSaveGroup(groupdata,
							function(response) {
								alert(response.message);
								GoToGroupList();
							},
							HandleErrorCodes,
							DisplayError);
}

var GoToEditPermissionsForm = function(e) {
	if(e) { e.preventDefault(); }
	
	Reset();
	
	$this = $(this);
	
	var userID = $this.data("userid");
	
	$("#EditPermissions").data("userid", userID);
	RefreshEditPermissionsForm();
}

var RefreshEditPermissionsForm = function() {
	var userID = $("#EditPermissions").data("userid");
	
	wiki.GetPermissionsForUser(userID,
			DisplayEditPermissionsForm,
			HandleErrorCodes,
			DisplayError);
}

var DisplayEditPermissionsForm = function(response) {
	
	var user = response.user;
	
	UpdateWindow("Edit user permissions", "EditUserPermissions-"+user.loginname+".html");
	
	$('#EditPermissions-Loginname').html(user.loginname);
	$('#UserPermissions-List').empty();
	
	for(var p = 0; p < response.permissions.length; p++) {
		var permission = response.permissions[p];
		
		$("#UserPermissions-List").append('' +
			'	<tr>' +
			'		<td>' + permission.permission + '</td>' +
			'		<td><input type="checkbox" '+(permission.status == 100 ? 'checked="checked"' : '')+' class="EditPermissions-Checkbox" data-userid="'+user.user_id+'" data-permission="'+permission.permission+'" /></td>' +
			'	</tr>');
	}
	
	$(".EditPermissions-Checkbox").change(GrantOrRevokePermission);
	$('#EditPermissions-NewPermission').data("userid", user.user_id)
	$('#EditPermissions-NewPermissionSet').unbind("click").click(CreateAndGrantPermission);
	
	HideLoading();
	
	$("#EditPermissions").show();
}

var GrantOrRevokePermission = function() {
	var $this = $(this);
	
	var userID = $this.data("userid");
	var permission = $this.data("permission");
	var type = $(this).is(":checked");
	
	switch(type) {
		case true:
			wiki.GrantPermissions(userID, [permission],
					function(response) { alert(response.message); },
					HandleErrorCodes,
					DisplayError);
			break;
			
		case false:
			wiki.RevokePermissions(userID, [permission],
					function(response) { alert(response.message); },
					HandleErrorCodes,
					DisplayError);
			break;
	}
}

var CreateAndGrantPermission = function() {
	
	var userID = $('#EditPermissions-NewPermission').data("userid");
	var permission = $('#EditPermissions-NewPermission').val();
	
	wiki.GrantPermissions(userID, [permission],
				function(response) {
					alert(response.message);
					RefreshEditPermissionsForm();
				},
				HandleErrorCodes,
				DisplayError);
}

var GoToDeleteUserDialog = function(e) {
	$this = $(this);
	
	var userID = $this.data("userid");

	$("#DeleteUserDialog-Confirm").unbind("click").click(DeleteUser);
	$("#DeleteUserDialog").data("userid",userID).modal();
}

var DeleteUser = function() {
	var userID = $("#DeleteUserDialog").data("userid");
	
	wiki.DeleteUser(userID,
					function(response) {
						alert(response.message);
						$("#DeleteUserDialog").modal("hide");
						GoToUserList();
					}, HandleErrorCode, DisplayError);
}

var memberListPopulated = false;
var nonMemberListPopulated = false;

var GoToGroupMembers = function(e) {
	Reset();
	
	$this = $(this);
	
	var groupID = $this.data("groupid");
	$("#GroupUsers").data("groupid", groupID);
	
	RefreshGroupMembers();
}

var RefreshGroupMembers = function() {
	var groupID = $("#GroupUsers").data("groupid");
	
	memberListPopulated = false;
	nonMemberListPopulated = false;
	
	wiki.GetUsersInGroup(groupID,
			function(response) { PopulateMemberList(response, DisplayGroupMembers); },
			HandleErrorCodes,
			DisplayError);
	
	wiki.GetUsersNotInGroup(groupID,
			function(response) { PopulateNonMemberList(response, DisplayGroupMembers); },
			HandleErrorCodes,
			DisplayError);
}

var PopulateMemberList = function(response, callback) {
	
	$('#GroupUsers-InGroup').empty();
	
	for(var u = 0; u < response.users.length; u++) {
		var user = response.users[u];
		
		$("#GroupUsers-InGroup").append('<option value="'+user.user_id+'">'+user.loginname+'</option>');
	}
	
	memberListPopulated = true;
	
	if(nonMemberListPopulated == true) {
		callback(response);
	}
}

var PopulateNonMemberList = function(response, callback) {
	
	$('#GroupUsers-NotInGroup').empty();
	
	for(var u = 0; u < response.users.length; u++) {
		var user = response.users[u];
		
		$("#GroupUsers-NotInGroup").append('<option value="'+user.user_id+'">'+user.loginname+'</option>');
	}
	
	nonMemberListPopulated = true;
	
	if(memberListPopulated == true) {
		callback(response);
	}
}

var DisplayGroupMembers = function(response) {
	var group = response.group;
	
	$('#GroupUsers-Groupname').html(group.name);
	
	UpdateWindow("Assign group members in '"+group.name+"'", "GroupMembers-"+group.name+".html");
	
	$("#GroupUsers-Remove").unbind("click").click(RemoveUsersFromGroup);
	$("#GroupUsers-Add").unbind("click").click(AddUsersToGroup);
	
	HideLoading();
	
	$("#GroupUsers").show();
}

var RemoveUsersFromGroup = function() {
	var groupID = $("#GroupUsers").data("groupid");
	var selected = $("#GroupUsers-InGroup").val();
	
	wiki.RemoveUsersFromGroup(groupID, selected,
			function(response) { alert(response.message); RefreshGroupMembers(); },
			HandleErrorCodes,
			DisplayError);
}

var AddUsersToGroup = function() {
	var groupID = $("#GroupUsers").data("groupid");
	var selected = $("#GroupUsers-NotInGroup").val();
	
	wiki.AddUsersToGroup(groupID, selected,
			function(response) { alert(response.message); RefreshGroupMembers(); },
			HandleErrorCodes,
			DisplayError);
}

var DeleteGroup = function() {
	var groupID = $("#DeleteGroupDialog").data("groupid");
	
	wiki.DeleteGroup(groupID,
					function(response) {
						alert(response.message);
						$("#DeleteGroupDialog").modal("hide");
						GoToGroupList();
					}, HandleErrorCodes, DisplayError);
}

var GoToDeleteGroupDialog = function(e) {
$this = $(this);
	
	var groupID = $this.data("groupid");
	
	$("#DeleteGroupDialog-Confirm").unbind("click").click(DeleteGroup);
	$("#DeleteGroupDialog").data("groupid",groupID).modal();
}

var DeleteGroup = function() {
	var groupID = $("#DeleteGroupDialog").data("groupid");
	
	wiki.DeleteGroup(groupID,
					function(response) {
						alert(response.message);
						$("#DeleteGroupDialog").modal("hide");
						GoToGroupList();
					}, HandleErrorCodes, DisplayError);
}

/*
 * Error messages
 */

var DisplayNotAuthorizedError = function() {
	Reset();
	HideLoading();
	$("#NotAuthorized").css("display","block");
}

var DisplayNotFoundError = function() {
	Reset();
	HideLoading();
	$("#PageNotFound").css("display","block");
}

var DisplayError = function(xhr, type, message) {
	Reset();
	HideLoading();
	$("#Error").css("display","block");
}

/*
 * Cookies
 */
var cookies;

var GetCookie = function(name) {
	if(cookies) {
		for(var c = 0; c < cookies.length; c++) {
			var cookie = cookies[c];
			
			if(cookie.name == name) {
				return cookie.value;
			}
		}
		
		return "";
	} else {
		cookies = [];
		
		var docCookies = document.cookie.split(';');
		
		for(var c = 0; c < docCookies.length; c++) {
			var docCookie = docCookies[c].trim().split('=');
			
			cookies.push({"name": docCookie[0], "value": docCookie[1]});
		}
		
		return GetCookie(name);
	}
}

var SetCookie = function(name, value) {
	document.cookie = "wiki_"+name+"="+value;
}

/*
 * Bind hotkeys
 */

 function BindKey(key, functionName, ctrl) {
	var ctrl = ctrl || false;
	
	$(document).bind('keydown', function (e) {
		if ((!ctrl || e.ctrlKey) && (e.which == key)) {
			e.preventDefault();
			functionName();
			return false;
		}
	});
}