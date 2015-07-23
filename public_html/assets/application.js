var online = true;
var pageID = 1;
var page = 'Homepage';
var view = 'DisplayPage';
var mode = '';
var userID = 1;
var loginname = '';
var password = '';
var isSignedIn = false;
var isFirstSignIn = true;
var cookies = false;
var statusbar = false;
var cache = {};
var NewPageEditor = null;
var EditPageEditor = null;

var wiki;

var DoSomething = function() {
	//wiki.GetPageByName('Homepage', false, function(response) { alert(response.page.title); });
}

$(function()
{
	var fromCache = localStorage.getItem("wiki_cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
	var loginname = GetCookie('wiki_loginname');
	var password = GetCookie('wiki_password');
	
	wiki = new Wiki();
	wiki.SignIn(loginname, password , DoSomething);
	
	
	
	$('[data-toggle="tooltip"]').tooltip();
	
	NewPageEditor = ace.edit("NewPage-InputContent-Editor");
	NewPageEditor.getSession().setMode("ace/mode/html");
	NewPageEditor.getSession().setMode("ace/mode/javascript");
	NewPageEditor.getSession().setMode("ace/mode/css");
	NewPageEditor.getSession().setMode("ace/mode/php");
	//NewPageEditor.getSession().setMode("ace/mode/markdown");
	NewPageEditor.setOptions({ maxLines: Infinity });
	
	EditPageEditor = ace.edit("EditPage-InputContent-Editor");
	EditPageEditor.getSession().setMode("ace/mode/html");
	EditPageEditor.getSession().setMode("ace/mode/javascript");
	EditPageEditor.getSession().setMode("ace/mode/css");
	EditPageEditor.getSession().setMode("ace/mode/php");
	//EditPageEditor.getSession().setMode("ace/mode/markdown");
	EditPageEditor.setOptions({ maxLines: Infinity });
	
	var startWith = window.location.hash.substring(1);
	
	switch(startWith) {
		case "Edit": view = "EditPage"; break;
		case "Versions": view = "GetVersions"; break;
		case "NewPage": view = "NewPage"; break;
	}
	
	ExtractPageName();
	
	
	
	$('#SignInForm').submit(SignIn);
	$('#SignUpForm').submit(SignUp);
	$('#NewUserForm').submit(CreateNewUser);
	$('#NewGroupForm').submit(CreateNewGroup);
	$('#ChangePasswordForm').submit(ChangePassword);
	$('#NavEditPage').click(EditPage);
	
	$('#NavSaveChanges').click(SaveChanges);
	$('#NavPreviewChanges').click(PreviewChanges);
	$('#NavDropChanges').click(DropChanges);
	$('#NavBackToComposer').click(BackToComposer);
	$('#NavGetVersions').click(GetVersions);
	$('#NavNewPage').click(NewPage);
	
	$('#EditPage-DeletePage').click(DeletePage);
	$('#EditPermissions-NewPermissionSet').click(CreateAndGrantPermission);
	$('#Users-NewUser').click(DisplayNewUserForm);
	$('#Users-NewGroup').click(DisplayNewGroupForm);
	
	$("#GroupUsers-Remove").click(RemoveUsersFromGroup);
	$("#GroupUsers-Add").click(AddUsersToGroup);
	
	//CheckLoginCredentials();
});

var ExtractPageName = function() {
	var currentUrl = window.location.href;
	var pageName = currentUrl.split("").reverse().join("").split('/',1);
	pageName = pageName[0].split("").reverse().join("").split(".",1);
	pageName = pageName[0];
	
	if(pageName == "" || pageName == "index") {
		//document.location = "Homepage.html";
		pageName = "Homepage";
	}
	
	page = pageName;
	
	if(page == "Users") {
		view = "UserManagement";
	}
}

/*
 * Sign in
 */
var CheckLoginCredentials = function() {
	var url = 'request.php?command=CheckLoginCredentials&loginname='+loginname+'&password='+password;
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			if(response.status == 0) {
				if(!isFirstSignIn) {
					alert(response.message);
				}
				
				//DisplaySignInForm();
				
				isSignedIn = false;
				
				var loginLink = $('<a href="#SignIn">Sign in</a>');
				loginLink.click(DisplaySignInForm);
				
				var sep = $('<span> &bull; </span>');
				
				var signupLink = $('<a href="#SignUp">Sign up</a>');
				signupLink.click(DisplaySignUpForm);
				
				$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
			} else {
				
				HideSignInForm();
				isSignedIn = true;
				
				userID = response.user.user_id;
				
				var preText = $('<span>Signed in as </span>');
				var changePasswordLink = $('<a href="#ChangePassword"><strong>'+loginname+'</strong></a>');
				var postText = $('<span> &bull; </span>');
				var logoutLink = $('<a href="#SignOut">Sign out</a>');
				
				changePasswordLink.click(DisplayChangePasswordForm);
				logoutLink.click(SignOut);
				
				$('#SignInText').empty().append(preText).append(changePasswordLink).append(postText).append(logoutLink);
	
				HasPermission("MANAGE_USERS", function() { $("#NavUsers").css("display","block"); }, function() { $("#NavUsers").css("display","none"); });
			}
			
			isFirstSignIn = false;
			DisplayAction();
		},
		error: function(xhr, err1, err2) {
			if(!online) {
				DisplayAction();
			} else {
				alert(err1+"\nLogin credential check error\n"+err2);
			}
		}
		/*beforeSend: function()
		{
			AddRequest();
		},
		complete: function()
		{
			RemoveRequest();
		}*/});
}

var SignIn = function() {
	var $this = $(this);
	
	loginname = $('#InputLoginName').val();
	password = $('#InputPassword').val();
	
	password = md5(password);
	
	SetCookie("loginname", loginname);
	SetCookie("password", password);
	
	CheckLoginCredentials();
	
	return false;
}

var SignOut = function() {
	SetCookie("loginname", "");
	SetCookie("password", "");
	
	loginname = "";
	password = "";
	
	isSignedIn = false;
				
	var loginLink = $('<a href="#SignIn">Sign in</a>');
	loginLink.click(DisplaySignInForm);
	
	var sep = $('<span> &bull; </span>');
	
	var signupLink = $('<a href="#SignUp">Sign up</a>');
	signupLink.click(DisplaySignUpForm);
	
	$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
	$('#NavUsers').css("display","none");
	
	// Back to the previous action
	DisplayAction();
}

var SignUp = function() {
	var $this = $(this);
	
	loginname = $('#SignUpForm-InputLoginName').val();
	password = $('#SignUpForm-InputPassword').val();
	passwordconfirmation = $('#SignUpForm-InputConfirmPassword').val();
	
	if(password != passwordconfirmation) {
		alert("The passwords aren't alike");
		return;
	}
	
	password = md5(password);
	
	var data = {"loginname":loginname, "password":password};
	
	$.ajax({
		'type': 'POST',
		'url': 'request.php?command=SaveUser',
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			alert(response.message);
			
			if(response.status == 200) {
				$("#SignUpForm").css("display","none");
				
				DisplaySignInForm();
			}
		}
	});
	
	return false;
}

var ChangePassword = function() {
	var $this = $(this);

	currentpassword = $('#ChangePasswordForm-CurrentPassword').val();
	newpassword = $('#ChangePasswordForm-InputNewPassword').val();
	newpasswordconfirmation = $('#ChangePasswordForm-InputConfirmNewPassword').val();
	
	if(newpassword != newpasswordconfirmation) {
		alert("The passwords aren't alike");
		return;
	}

	currentpassword = md5(currentpassword);
	newpassword = md5(newpassword);
	
	var data = {"currentpassword":currentpassword, "password":newpassword};
	
	$.ajax({
		'type': 'POST',
		'url': 'request.php?command=SaveUser&user='+loginname,
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			alert(response.message);
			
			SignOut();
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
	
	return false;
}

var DisplaySignInForm = function() {
	HideAllActions();
	HideLoading();
	$("#SignInForm").css("display","block");
	document.title = "Sign in";
}

var HideSignInForm = function() {
	$("#SignInForm").css("display","none");
	DisplayLoading();
}

var DisplaySignUpForm = function() {
	HideAllActions();
	HideLoading();
	$("#SignUpForm").css("display","block");
	document.title = "Sign up";
}

var DisplayChangePasswordForm = function() {
	HideAllActions();
	HideLoading();
	$("#ChangePasswordForm").css("display","block");
	document.title = "Change your password";
}

var GoToPage = function(pagename) {
	page = pagename;
	
	DisplayPage();
}

var HideStatusBar = function() {
	$("#statusbar").css("display","none");
	statusbar = false;
}

var ShowStatusBar = function(message, color) {
	$("#statusbar").css("display","block").html("<p>"+message+"</p>").css("background-color","#"+color);
	statusbar = true;
}

/*
 * Actions
 */
var DisplayAction = function() {
	if(view == "" || view == "undefined") {
		view = "DisplayPage";
		page = "Homepage";
	}
	
	switch(view) {
		case "DisplayPage":
			DisplayPage();
			break;
			
		case "EditPage":
			EditPage();
			break;
			
		case "NewPage":
			NewPage();
			break;
			
		case "GetVersions":
			GetVersions();
			break;
			
		case "UserManagement":
			DisplayUserManagement();
			break;
	}
}

var HideAllActions = function() {
	view = "";
	document.title = "Wiki";
	
	UnbindKeys();
	
	HideSignInForm();
	HideOfflineMessage();
	
	$('#Navbar').css("display","block");
	$('#content').css("margin-top","60px");
	$('#FooterBar').css("display","block");
	
	// Sign up form
	$("#SignUpForm").css("display","none");
	
	// Change password form
	$("#ChangePasswordForm").css("display","none");
	
	// Display page
	$("#DisplayPage").css("display","none");
		$("#NavEditPage").css("display","none");
		$("#NavGetVersions").css("display","none");
		
		$("#NavPublicPage").css("display","none");
		$("#NavProtectedPage").css("display","none");
		$("#NavPrivatePage").css("display","none");
	
	// Edit page & New Page
	$("#EditPage").css("display","none");
	$("#NewPage").css("display","none");
		$("#NavDropChanges").css("display","none");
		$("#NavPreviewChanges").css("display","none");
		$("#NavSaveChanges").css("display","none");
	
	// Preview
	$('#PreviewPage').css("display","none");
		$('#NavBackToComposer').css("display","none");
	
	// Versions
	$('#Versions').css("display","none");
	
	// Users
	$('#UserManagement').css("display","none");
	$('#EditPermissions').css("display","none");
	$('#NewUserForm').css("display","none");
	$('#NewGroupForm').css("display","none");
	$('#GroupUsers').css("display","none");
	
	// Error pages
	$("#PageNotFound").css("display","none");
	$("#NotAuthorized").css("display","none");
	
	DisplayLoading();
}

/*
 * Display page
 */
var DisplayPage = function() {
	HideAllActions();
	
	view = "DisplayPage";
	
	var url = 'request.php?command=DisplayPage&page=' + page;
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			
			if(response.status == 0) {
				if(page in cache) {
					HideLoading();
					
					var pagedata = cache[page];
					
					DisplayPageContent(pagedata, true);
				} else {
					alert(response.message);
				}
				
				return;
			} else if(response.status == 404) {
				DisplayPageNotFound();
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			HideLoading();
			
			cache[page] = response;
			localStorage.setItem("wiki_cache", JSON.stringify(cache));
			
			if(response.page.manipulation == "EVERYONE" || (response.page.manipulation == "REGISTERED" && isSignedIn) || (response.page.manipulation == "OWNER" && response.page.owner.loginname == loginname)) {
				$("#NavEditPage").css("display","block");
				$("#NavGetVersions").css("display","block");
			}
			
			DisplayPageContent(response);
			/*
			if(response.page.hasTableOfContents) {
				$("#NavTableOfContents").css("display","block");
			}
			*/
			
			BindKey(69,EditPage,true); // Ctrl+E
			//BindKey(86,GetVersions,true); // Ctrl+V
		},
		error: function(xhr, err1, err2) {
			alert("Display page\n"+err1+": "+err2);
			if(page in cache) {
				HideLoading();
				
				var pagedata = cache[page];
				
				DisplayPageContent(pagedata, true);
			} else {
				DisplayOfflineMessage();
			}
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}/*,
		complete: function()
		{
			RemoveRequest();
		}*/
	});
}

var DisplayPageContent = function(pagedata, fromCache) {
	var fromCache = fromCache || false;
	
	window.history.replaceState({}, pagedata.page.title, page+".html");
				
	pageID = pagedata.page.page_id;
	
	$('#DisplayPage-Title').css("display", "block");
	$('#DisplayPage-TitleSeparator').css("display", "block");
	
	$("#DisplayPage").css("display","block");
	$("#DisplayPage-Title").html(pagedata.page.title);
	$("#DisplayPage-Content").html(pagedata.page.content);
	
	document.title = pagedata.page.title + (fromCache ? " (From cache)" : "");
	
	switch(pagedata.page.visibility) {
		case "PUBLIC": $('#NavPublicPage').css("display","block"); break;
		case "PROTECTED": $('#NavProtectedPage').css("display","block"); break;
		case "PRIVATE": $('#NavPrivatePage').css("display","block"); break;
		case "GROUPPRIVATE": $('#NavGroupPrivatePage').css("display","block"); break;
	}
	
	if(pagedata.no_headline) {
		$('#DisplayPage-Title').css("display", "none");
		$('#DisplayPage-TitleSeparator').css("display", "none");
	}
	
	if(pagedata.no_navbar) {
		$('#Navbar').css("display","none");
		$('#content').css("margin-top","0px");
	}
	
	if(pagedata.no_footerbar) {
		$('#FooterBar').css("display","none");
	}
	
	$('#DisplayPage-LastEdit-Timestamp').html(pagedata.page.last_edit.timestamp);
	$('#DisplayPage-LastEdit-User').html(pagedata.page.last_edit.user);
	
}

var DisplayPageNotFound = function() {
	HideAllActions();
	HideLoading();
	
	$("#PageNotFound").css("display","block");
	document.title = "Page not found";
}

var DisplayNotAuthorized = function() {
	HideAllActions();
	HideLoading();
	
	$("#NotAuthorized").css("display","block");
	document.title = "Not authorized";
}

/*
 * New page
 */
var NewPage = function() {
	HideAllActions();
	
	view = "newpage";
	
	HideLoading();
	pageID = 0;
	mode = 'NewPage';
	
	document.title = "New page";
	$("#NewPage").css("display","block");
	$("#NewPage-InputTitle").val("");
	$("#NewPage-InputContent").val("").hide();
			
		$("#NavDropChanges").css("display","block");
		$("#NavPreviewChanges").css("display","block");
		$("#NavSaveChanges").css("display","block");
			
	//$("textarea.tab").keydown(indent);
	//$('textarea.tab').keypress(autoindent);
	
	$("#NewPage-Owner").empty();
	GetUserList(function(response) {
		for(var u = 0; u < response.users.length; u++) {
			var user = response.users[u];
			
			$("#NewPage-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == userID ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
		}
	});
	
	$("#NewPage-Group").empty();
	GetGroupList(function(response) {
		for(var g = 0; g < response.groups.length; g++) {
			var group = response.groups[g];
			
			$("#NewPage-Group").append('<option value="'+group.group_id+'">'+group.name+'</option>');
		}
	});
	
	BindKey(83,SaveChanges,true); // Ctrl+S
	BindKey(27,DropChanges,false); // ESC
}

/*
 * Edit page
 */
var EditPage = function() {
	HideAllActions();
	
	view = "editpage";
	
	mode = 'EditPage';
	
	var url = 'request.php?command=DisplayPage&page=' + page + "&raw";
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 404) {
				DisplayPageNotFound();
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			HideLoading();
			
			pageID = response.page.page_id;
			document.title = "Edit page '"+response.page.title+"'";
			
			$("#EditPage").css("display","block");
			$("#EditPage-Title").html(response.page.title);
			$("#EditPage-InputTitle").val(response.page.title);
			$("#EditPage-InputContent").val(response.page.content).hide();
			$("#EditPage-InputPageID").val(response.page.pageID);
			$("#EditPage-Visiblity-" + response.page.visibility).attr("checked",true);
			$("#EditPage-Manipulation-" + response.page.manipulation).attr("checked",true);
			
			$("#NavDropChanges").css("display","block");
			$("#NavPreviewChanges").css("display","block");
			$("#NavSaveChanges").css("display","block");
			
			$("#EditPage-MinorChange").attr("checked",false);
			
			var owner_user = response.page.owner;
			var owner_group = response.page.group;
			
			$("#EditPage-Owner").empty();
			GetUserList(function(response) {
				for(var u = 0; u < response.users.length; u++) {
					var user = response.users[u];
					
					$("#EditPage-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == owner_user.user_id ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
				}
			});
			
			$("#EditPage-Group").empty();
			GetGroupList(function(response) {
				for(var g = 0; g < response.groups.length; g++) {
					var group = response.groups[g];
					
					$("#EditPage-Group").append('<option value="'+group.group_id+'"'+(group.group_id == owner_group.group_id ? ' selected="selected"' : '')+'>'+group.name+'</option>');
				}
			});
				
			if(response.page.page_id == 1) {
				$("#EditPage-DeletePage").attr("disabled",true);
			}
			
			//$("textarea.tab").keydown(indent);
			//$('textarea.tab').keypress(autoindent);
			
			EditPageEditor.getSession().setValue($("#EditPage-InputContent").val());
			
			BindKey(83,SaveChanges,true); // Ctrl+S
			BindKey(27,DropChanges,false); // ESC
			
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
		/*,complete: function()
		{
			RemoveRequest();
		}*/
	});
}

var SaveChanges = function() {
	var url = 'request.php?command=SavePage';
	
	if(pageID) {
		url = url + '&pageID=' + pageID;
	}
	
	var minor_edit = 0;
	
	if(mode == "EditPage") {
		minor_edit = ($('#EditPage-MinorChange:checked').length > 0);
	}
	
	var editor = NewPageEditor;
	
	if(mode == "EditPage") {
		editor = EditPageEditor;
	}
	
	var data = {
		'title': $("#"+mode+"-InputTitle").val(),
		'content': editor.getSession().getValue(), //$("#"+mode+"-InputContent").val(),
		'summary': $("#"+mode+"-InputSummary").val(),
		'minor_edit': minor_edit,
		'visibility': $('input[name='+mode+'-Visiblity]:checked').val(),
		'manipulation': $('input[name='+mode+'-Manipulation]:checked').val(),
		'owner': $('#'+mode+'-Owner').val(),
		'group': $('#'+mode+'-Group').val()
	};
	
	$.ajax({
		'type': 'POST',
		'url': url,
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 401) {
				alert(response.message);
				return;
			}
			
			alert(response.message);
			
			if(mode == "NewPage") {
				page = response.page;
			}
			
			DisplayPage();
			
		},
		beforeSend: function(xhr)
		{
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
			//AddRequest();
		}
		/*,complete: function()
		{
			RemoveRequest();
		}*/
	});
};

var DeletePage = function() {
	var url = 'request.php?command=DeletePage&pageID=' + pageID;
	
	$.ajax({
		'type': 'DELETE',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			alert(response.message);
			
			view = "DisplayPage";
			page = "Homepage";
			
			DisplayAction();
			
		},
		beforeSend: function(xhr)
		{
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
			//AddRequest();
		}
		/*,complete: function()
		{
			RemoveRequest();
		}*/
	});
};

var DropChanges = function() {
	DisplayPage();
};

var PreviewChanges = function() {
	HideAllActions();
	
	var url = 'request.php?command=PreviewPage';
	
	var editor = NewPageEditor;
	
	if(mode == "EditPage") {
		editor = EditPageEditor;
	}
	
	var data = {
		'title': $("#"+mode+"-InputTitle").val(),
		'content': editor.getSession().getValue(), //$("#"+mode+"-InputContent").val()
	};
	
	//var previewWindow = window.open("", "_blank");
	//previewWindow.blur();
	
	$.ajax({
		'type': 'POST',
		'url': url,
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			if(response.status == 401) {
				alert(response.message);
				return;
			}
			
			HideLoading();
			
			//previewWindow.location.href = 'http://www.google.com';
			//previewWindow.focus();
			
			$('#PreviewPage-Title').css("display", "block");
			$('#PreviewPage-TitleSeparator').css("display", "block");
			
			$("#PreviewPage").css("display","block");
			$("#PreviewPage-Title").html(response.page.title);
			$("#PreviewPage-Content").html(response.page.content);
			
			document.title = "Preview for '" + response.page.title + "'";
			
			if(response.no_headline) {
				$('#PreviewPage-Title').css("display", "none");
				$('#PreviewPage-TitleSeparator').css("display", "none");
			}
			
			if(response.no_navbar) {
				$('#Navbar').css("display","none");
				$('#content').css("margin-top","0px");
			}
			
			if(response.no_footerbar) {
				$('#FooterBar').css("display","none");
			}
			
			$('#NavBackToComposer').css("display","block");
		},
		beforeSend: function(xhr)
		{
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
			//AddRequest();
		}
		/*,complete: function()
		{
			RemoveRequest();
		}*/
	});
};

var BackToComposer = function() {
	HideAllActions();
	HideLoading();
	
	$("#"+mode).css("display","block");
	$("#NavDropChanges").css("display","block");
	$("#NavPreviewChanges").css("display","block");
	$("#NavSaveChanges").css("display","block");
	
	// Maybe a distinction between "New page" / "Edit page 'mmmm'" would be good here
	document.title = "Edit page";
}

/*
 * Get a list of revisions
 */
var GetVersions = function() {
	HideAllActions();
	
	view = "DisplayPage";
	
	var url = 'request.php?command=GetVersions&page=' + page;
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 404) {
				DisplayPageNotFound();
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			HideLoading();
			
			pageID = response.page.page_id;
			
			$('#Versions').css("display", "block");
			
			$('#Versions-PageTitle').html(response.page.title);
			
			document.title = "Revisions for '" + response.page.title + "'";
			
			$("#Versions-List").empty();
			
			for(var v = 0; v < response.versions.length; v++) {
				var version = response.versions[v];
				
				var timestamp = new Date(version.timestamp);
				
				var minor_edit = "";
				
				if(version.minor_edit) {
					minor_edit = '<span class="label label-warning"><i class="glyphicon glyphicon-ok-circle" aria-hidden="true"></i> Yes';
				}
				
				$("#Versions-List").append('' +
				'	<tr>' +
				'		<td><input type="radio" name="left" /></td>' +
				'		<td><input type="radio" name="right" /></td>' +
				'		<td><a href="#" data-version="'+version.version_id+'" class="DisplayVersion">' + timestamp.toLocaleDateString() + ' @ ' + timestamp.toLocaleTimeString() + '</a></td>' +
				'		<td>' + version.user + '</td>' +
				'		<td>' + version.summary + '</td>' +
				'		<td>' + minor_edit + '</td>' +
				'	</tr>');
			}
			
			$(".DisplayVersion").click(DisplayVersion);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}/*,
		complete: function()
		{
			RemoveRequest();
		}*/
	});
}

var DisplayVersion = function() {
	var $this = $(this);
	var versionID = $this.data("version");
	
	alert("Display revision #"+versionID);

	return false;
}

var DisplayUserManagement = function() {
	HideAllActions();
	GetUserList(function (response) {
		$("#User-List").empty();
			
		for(var u = 0; u < response.users.length; u++) {
			var user = response.users[u];
			
			$("#User-List").append('' +
			'	<tr>' +
			'		<td>' + user.loginname + '</td>' +
			'		<td><button type="button" class="btn btn-xs btn-warning EditPermissions" data-user="'+user.user_id+'" data-loginname="'+user.loginname+'" '+(user.user_id == 2 ? 'disabled="disabled"' : '')+'><i class="glyphicon glyphicon-cog" aria-hidden="true"></i> Permissions</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-danger DeleteUser" data-user="'+user.user_id+'" '+((user.user_id == 1 || user.user_id == 2) ? 'disabled="disabled"' : '')+'><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Delete</button></td>' +
			'	</tr>');
		}
		
		$(".EditPermissions").click(EditPermissions);
		$(".DeleteUser").click(DeleteUser);
	});
	
	GetGroupList(function(response) {
		$("#Group-List").empty();
			
		for(var g = 0; g < response.groups.length; g++) {
			var group = response.groups[g];
			
			$("#Group-List").append('' +
			'	<tr>' +
			'		<td>' + group.name + '</td>' +
			'		<td><button type="button" class="btn btn-xs btn-primary GetGroupUsers" data-group="'+group.group_id+'"><i class="glyphicon glyphicon-user" aria-hidden="true"></i> Users</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-danger DeleteGroup" data-group="'+group.group_id+'"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Delete</button></td>' +
			'	</tr>');
		}
		
		$(".GetGroupUsers").click(DisplayGroupUsers);
		$(".DeleteGroup").click(DeleteGroup);
	});
	
	HideLoading();
	
	$("#UserManagement").css("display","block");
	document.title = 'User & Group management';
	
	return false;
}

var GetUserList = function(positive_callback) {
	$.ajax({
		'type': 'GET',
		'url': 'request.php?command=GetUsers',
		'dataType': 'json',
		'success': function(response) {
			
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			positive_callback(response);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}/*,
		complete: function()
		{
			RemoveRequest();
		}*/
	});
	
	return false;
}

var GetGroupList = function(positive_callback) {
	$.ajax({
		'type': 'GET',
		'url': 'request.php?command=GetGroups',
		'dataType': 'json',
		'success': function(response) {
			
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			positive_callback(response);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}/*,
		complete: function()
		{
			RemoveRequest();
		}*/
	});
	
	return false;
}

var DisplayNewUserForm = function() {
	HideAllActions();
	HideLoading();
	$('#NewUserForm').css("display","block");
	document.title = "Create a new user";
}

var DisplayNewGroupForm = function() {
	HideAllActions();
	HideLoading();
	$('#NewGroupForm').css("display","block");
	document.title = "Create a new group";
}

var CreateNewUser = function() {
	var $this = $(this);
	
	loginname = $('#NewUserForm-InputLoginName').val();
	password = $('#NewUserForm-InputPassword').val();
	passwordconfirmation = $('#NewUserForm-InputConfirmPassword').val();
	
	if(password != passwordconfirmation) {
		alert("The passwords aren't alike");
		return;
	}
	
	password = md5(password);
	
	var data = {"loginname":loginname, "password":password};
	
	HideAllActions();
	
	$.ajax({
		'type': 'POST',
		'url': 'request.php?command=SaveUser',
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			HideLoading();
			
			alert(response.message);
			
			$('#NewUserForm-InputLoginName').val('');
			$('#NewUserForm-InputPassword').val('');
			$('#NewUserForm-InputConfirmPassword').val('');
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
	
	return false;
}

var CreateNewGroup = function() {
	var $this = $(this);
	
	name = $('#NewGroupForm-InputName').val();
	
	var data = {"name":name};
	
	HideAllActions();
	
	$.ajax({
		'type': 'POST',
		'url': 'request.php?command=SaveGroup',
		'dataType': 'json',
		'data': data,
		'success': function(response) {
			HideLoading();
			
			alert(response.message);
			
			$('#NewGroupForm-InputName').val('');
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
	
	return false;
}

var EditPermissions = function() {
	var $this = $(this);
	
	var userUserID = $this.data("user");
	var userLoginname = $this.data("loginname");
	
	var url = "request.php?command=GetUserPermissions&userID="+userUserID;
	
	HideAllActions();
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			
			HideLoading();
			
			if(response.status == 0) {
				alert(response.message);
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			$('#EditPermissions').css("display", "block");
			$('#EditPermissions-Loginname').html(userLoginname);
			
			document.title = "Edit permissions for '"+userLoginname+"'";
			
			$("#UserPermissions-List").empty();
			
			for(var p = 0; p < response.permissions.length; p++) {
				var permission = response.permissions[p];
				
				$("#UserPermissions-List").append('' +
				'	<tr>' +
				'		<td>' + permission.permission + '</td>' +
				'		<td><input type="checkbox" '+(permission.status == 20000 ? 'checked="checked"' : '')+' class="EditPermissions-Checkbox" data-user="'+userUserID+'" data-permission="'+permission.permission+'" /></td>' +
				'	</tr>');
			}
			
			$(".EditPermissions-Checkbox").change(GrantOrRevokePermission);
			$('#EditPermissions-NewPermission').data("user", userUserID);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}/*,
		complete: function()
		{
			RemoveRequest();
		}*/
	});
}

var GrantOrRevokePermission = function() {
	var $this = $(this);
	
	var userUserID = $this.data("user");
	var permission = $this.data("permission");
	var type = "PUT";
	
	if(!this.checked) {
		type = "DELETE";
	}
	
	var url = "request.php?command=SaveUserPermission";
	var data = {"userID": userUserID, "permission": permission};
	
	$.ajax({
		'type': type,
		'url': url,
		'data': data,
		'dataType': 'json',
		'success': function(response) {
			alert(response.message);
		},
		'error': function(xhr, type, message) {
			alert("GrantOrRevokePermission\n"+type + ": "+message);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var CreateAndGrantPermission = function() {
	var userUserID = $('#EditPermissions-NewPermission').data("user");
	var permission = $('#EditPermissions-NewPermission').val();
	
	var url = "request.php?command=SaveUserPermission";
	var data = {"userID": userUserID, "permission": permission};
	
	$.ajax({
		'type': 'PUT',
		'url': url,
		'data': data,
		'dataType': 'json',
		'success': function(response) {
			alert(response.message);
		},
		'error': function(xhr, type, message) {
			alert("CreateAndGrantPermission\n"+type + ": "+message);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var DisplayGroupUsers = function() {
	var $this = $(this);
	
	var groupID = $this.data("group");
	
	GetGroupUsers(groupID);
}

var GetGroupUsers = function(groupID) {
	HideAllActions();
	HideLoading();
	$("#GroupUsers").css("display","block").data("group",groupID);
	
	GetUsersInGroup(groupID, function(response) {
		$("#GroupUsers-InGroup").empty();
		
		for(var u = 0; u < response.users.length; u++) {
			var user = response.users[u];
			
			$("#GroupUsers-InGroup").append('<option value="'+user.user_id+'">'+user.loginname+'</option>');
		}
	});
	
	GetUsersNotInGroup(groupID, function(response) {
		$("#GroupUsers-NotInGroup").empty();
		
		for(var u = 0; u < response.users.length; u++) {
			var user = response.users[u];
			
			$("#GroupUsers-NotInGroup").append('<option value="'+user.user_id+'">'+user.loginname+'</option>');
		}
	});
}

var GetUsersInGroup = function(groupID, positive_callback) {
	var url = "request.php?command=GetUsers&groupID="+groupID+"&mode=INCLUDE";
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			if(response.status == 404 || response.status == 401) {
				alert(response.message);
			} else if(response.status == 200) {
				positive_callback(response);
			}
		},
		'error': function(xhr, type, message) {
			alert("GetUsersInGroup\n"+type + ": "+message);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var GetUsersNotInGroup = function(groupID, positive_callback) {
	var url = "request.php?command=GetUsers&groupID="+groupID+"&mode=EXCLUDE";
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			if(response.status == 404 || response.status == 401) {
				alert(response.message);
			} else if(response.status == 200) {
				positive_callback(response);
			}
		},
		'error': function(xhr, type, message) {
			alert("GetUsersInGroup\n"+type + ": "+message);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var RemoveUsersFromGroup = function() {
	
	var selected = $("#GroupUsers-InGroup").val();
	var groupID = $("#GroupUsers").data("group");
	
	var url = "request.php?command=SaveGroupMember";
	var data = {"userIDs": selected, "groupID": groupID};
	
	$.ajax({
		'type': "DELETE",
		'url': url,
		'data': data,
		'dataType': 'json',
		'success': function(response) {
			alert(response.message);
			GetGroupUsers(groupID);
		},
		'error': function(xhr, type, message) {
			alert("RemoveUsersFromGroup\n"+type + ": "+message);
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var AddUsersToGroup = function() {
	
	var selected = $("#GroupUsers-NotInGroup").val();
	var groupID = $("#GroupUsers").data("group");
	
	var url = "request.php?command=SaveGroupMember";
	var data = {"userIDs": selected, "groupID": groupID};
	
	$.ajax({
		'type': "PUT",
		'url': url,
		'data': data,
		'dataType': 'json',
		'success': function(response) {
			alert(response.message);
			GetGroupUsers(groupID);
		},
		'error': function(xhr, type, message) {
			alert("AddUsersToGroup\n"+type + ": "+message);
			
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var DeleteUser = function() {
	var $this = $(this);
	
	var userUserID = $this.data("user");
	
	alert(userUserID);
}

var DeleteGroup = function() {
	var $this = $(this);
	
	var groupID = $this.data("group");
	
	alert(groupID);
}

var HasPermission = function(permission, positive_callback, negative_callback) {
	var url = "request.php?command=UserHasPermission&userID="+userID+"&permission="+permission;
	
	$.ajax({
		'type': 'GET',
		'url': url,
		'dataType': 'json',
		'success': function(response) {
			if(response.status == 404 || response.status == 401) {
				alert(response.message);
			} else if(response.status == 200) {
				positive_callback();
			} else {
				negative_callback();
			}
		},
		'error': function(xhr, type, message) {
			alert("HasPermission\n"+type + ": "+message);
			negative_callback();
		},
		beforeSend: function(xhr)
		{
			//AddRequest();
			xhr.setRequestHeader("Authorization", "Basic " + window.btoa(loginname+":"+password));
		}
	});
}

var DisplayLoading = function() { $("#Loading").css("display","block"); }
var HideLoading = function() { $("#Loading").css("display","none"); }

var DisplayOfflineMessage = function() { HideLoading(); $("#Offline").css("display","block"); }
var HideOfflineMessage = function() { $("#Offline").css("display","none"); }

/*
 * Cookies
 */
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
 * Indent on tab
 */

/*
var indent = function(e) {

	if(e.keyCode === 9) { // tab was pressed
		// get caret position/selection
		var start = this.selectionStart;
		var end = this.selectionEnd;

		var $this = $(this);
		var value = $this.val();

		// set textarea value to: text before caret + tab + text after caret
		$this.val(value.substring(0, start)
					+ "\t"
					+ value.substring(end));

		// put caret at right position again (add one for the tab)
		this.selectionStart = this.selectionEnd = start + 1;

		// prevent the focus lose
		e.preventDefault();
	}
};
//*/

/*
 * Auto indent
 */
/*
function autoindent(e) {
  var k = e.keyCode || e.charCode;
  if (k != 13) return true;
  e.preventDefault();
  var range = $(this).getSelection();
  var pos = range.start;
  var ws = $(this).val().substr(0,pos);
  ws = ws.match(/(^|\n)([ \t]*)[^\n]*$/);
  ws = ws[2];
  ws = "\n" + ws;
  $(this).replaceSelection(ws);
  $(this).setSelection({pos: pos + ws.length});
}
//*/
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

function UnbindKeys() {
	$(document).unbind('keydown');
}