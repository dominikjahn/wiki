var wiki;
var cache = [];

$(function() {
	Reset();
	
	var loginname = GetCookie('wiki_loginname');
	var password = GetCookie('wiki_password');
	var view = ExtractPageName();
	
	wiki = new Wiki();
	wiki.defaultNegativeHandler = HandleErrorCodes;
	wiki.defaultErrorHandler = DisplayError;
	
	wiki.SignIn(loginname, password, function(response) { SetCookie('loginname', loginname); SetCookie('password', password); ShowUserInfo(response); GoToView(view); }, function(response) { ShowSignInUpLinks(response); GoToView(view); } );
	
	var fromCache = localStorage.getItem("wiki_cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
	$(window).on('popstate', GoToState);

	$("#NavSearchForm-Uncollapsed").unbind("submit").submit(Search);
	$("#NavSearchForm-Collapsed").unbind("submit").submit(Search);
});

var CloseNavbars = function() {
	if($('.navbar[aria-expanded=true]').length>0) {
		$(".navbar-collapse").collapse('hide');
	}
}

var GoToView = function(view) {
	
	//if(view.substring(0,1) == "#") {
	//	view = view.substring(1);
	//}
	
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
			
		//case "Versions":
		//	DisplayVersions(view[1]);
		//	break;
			
		//case "Version":
		//	DisplayVersion(view[1]);
		//	break;
			
		//case "User":
		//	DisplayUser(view[1]);
		//	break;
			
		case "NewUser":
			DisplayNewUserForm();
			break;
			
		case "EditUser":
			GoToEditUserFormByLoginname(view[1]);
			break;
			
		case "EditUserPermissions":
			GoToEditPermissionsFormByLoginname(view[1]);
			break;
			
		//case "Group":
		//	DisplayGroup(view[1]);
		//	break;
			
		case "NewGroup":
			DisplayNewGroupForm();
			break;
			
		case "EditGroup":
			GoToEditGroupFormByName(view[1]);
			break;
			
		case "GroupMembers":
			GoToGroupMembersByName(view[1]);
			break;
			
		//case "Category":
		//	DisplayCategory(viewName[1]);
		//	break;
			
		case "Search":
			DisplaySearchForm();
			break;
			
		default:
			GoToPage(view[0]);
	}
}

var Reset = function() {
	CloseNavbars();
	
	$('#Navbar').show();
	$('#content').css("margin-top","60px");
	$('#FooterBar').show();
	
	// Views
	$("#Loading").show();
	$("#PageNotFound").hide();
	$("#NotAuthorized").hide();
	$("#Offline").hide();
	$("#Error").hide();
	//$("#SignInForm").hide();
	//$("#SignUpForm").hide();
	$("#ChangePasswordForm").hide();
	$("#DisplayPage").hide();
	//$("#NewPage").hide();
	$("#EditPageForm").hide();
	$("#Versions").hide();
	$("#SearchForm").hide();
	$("#SearchResults").hide();
	$("#UserManagement").hide();
	$("#EditPermissions").hide();
	$("#EditUserForm").hide();
	$("#EditGroupForm").hide();
	$("#GroupUsers").hide();
	
	// Navbar
	//$("#NavNewPage").hide();
	$("#NavEditPage").hide();
	$("#NavGetVersions").hide();
	$("#NavDropChanges").hide();
	$("#NavPreviewChanges").hide();
	$("#NavSaveChanges").hide();
	$("#NavBackToComposer").hide();
	$("#NavPublicPage").hide();
	$("#NavProtectedPage").hide();
	$("#NavPrivatePage").hide();
	$("#NavGroupPrivatePage").hide();
	
	// Display loading screen
	$("#Loading").show();
	
	// Unbind all hotkeys
	$(document).unbind('keydown');
}

var HideLoading = function() {
	$("#Loading").hide();
}

var ExtractPageName = function(url) {
	var currentUrl = url || window.location.href;
	var pageName = currentUrl.split("").reverse().join("").split('/',1);
	pageName = pageName[0].split("").reverse().join("").split(".",1);
	pageName = pageName[0];
	
	if(pageName == "" || pageName == "index") {
		pageName = "Homepage";
	}
	
	return pageName;
}

var wikiHistory = [];

var UpdateWindow = function(title, state, url) {
	document.title = title;
	
	var state = {"title": title, "body": $("body")[0].outerHTML };
	
	if(url) {
		//window.history.replaceState({}, title, './'+url);
		window.history.pushState(state, title, './'+url);
		
		//wikiHistory.push(url);
	} else {
		window.history.pushState(state, title);
	}
}

var GoToState = function(e) {
	var state = e.originalEvent.state;
	
	if(state) {
		document.title = state.title;
		$("body")[0].outerHTML = state.body;
		
		//alert(state.title + "\n\n" + state.body);
	}
}

/*
var GoBackInHistory = function() {
	wikiHistory.pop();
	var item = wikiHistory[wikiHistory.length-1];
	
	alert("Going back to '"+item+"'");
	
	var view = ExtractPageName(item);
	GoToView(view);
	
}
*/

var HandleErrorCodes = function(response) {
	switch(response.status) {
		case 0:
			alert(response.message);
			break;
		
		case 401:
			DisplayNotAuthorizedError();
			break;
			
		case 404:
			DisplayNotFoundError(response);
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
	
	wiki.UserHasPermission("CREATE_USERS,CREATE_GROUPS,ALTER_USERS,ALTER_GROUPS,MANAGE_PERMISSIONS","any",
			function() {
				$("#NavUsers").css("display","block").unbind("click").click(GoToUserList); },
			function() {
				$("#NavUsers").css("display","hide").unbind("click"); });
	
	wiki.UserHasPermission("CREATE_PAGES","all",
			function() {
				$("#NavNewPage").css("display","block").unbind("click").click(DisplayNewPageForm); },
			function() {
				$("#NavNewPage").css("display","hide").unbind("click"); });
}

var ShowSignInUpLinks = function() {
	var loginLink = $('<a href="SignIn.html">Sign in</a>');
	loginLink.click(DisplaySignInForm);
	
	SetCookie("loginname","");
	SetCookie("password","");
	
	var sep = $('<span> &bull; </span>');
	
	var signupLink = $('<a href="SignUp.html">Sign up</a>');
	signupLink.click(DisplaySignUpForm);
	
	$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
	
	$("#NavUsers").css("display","hide").unbind("click");
	$("#NavNewPage").css("display","hide").unbind("click");
}

/*
 * Sign in / sign up / Change password
 */

var DisplaySignInForm = function(e) {
	
	
	if(e) { e.preventDefault(); }
	
	/*Reset();
	HideLoading();*/
	
	$("#SignInForm").modal("show").unbind("submit").submit(SignIn);
	
	UpdateWindow("Sign in",{method:"DisplaySignInForm"},"SignIn.html");
	
	return false;
}

var DisplaySignUpForm = function(e) {
	if(e) { e.preventDefault(); }
	
	Reset();
	HideLoading();
	
	
	$("#SignUpForm").modal("show").unbind("submit").submit(SignUp);
	UpdateWindow("Sign up",{method:"DisplaySignUpForm"},"SignUp.html");
	
	return false;
}

var DisplayChangePasswordForm = function(e) {
	if(e) { e.preventDefault() };
	
	Reset();
	HideLoading();
	
	$("#ChangePasswordForm").show().unbind("submit").submit(ChangePassword);
	UpdateWindow("Change password", {method:"DisplayChangePasswordForm"}, "ChangePassword.html");
	
	return false;
}

var SignIn = function(e) {
	if(e) { e.preventDefault(); }
	
	$("#SignInForm").modal("hide");
	
	Reset();
	
	loginname = $('#SignInForm-InputLoginName').val();
	password = $('#SignInForm-InputPassword').val();
	
	//password = md5(password);
	
	SetCookie("loginname", loginname);
	SetCookie("password", password);
	
	wiki.SignIn(loginname, password, function(response) { ShowUserInfo(response); GoToPage('Homepage'); }, function(response) { DisplaySignInForm(); alert(response.message); });
	
	return false;
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
	
	//password = md5(password);
	
	userdata = {'userID': 0, 'loginname': loginname, 'password': password};
	
	wiki.CreateOrSaveUser(userdata,
		function(response) {
			alert(response.message);
			$("#SignUpForm").modal("hide");
			DisplaySignInForm();
		},
		HandleErrorCodes,
		DisplayError);
	
	return false;
}

var ChangePassword = function() {
	
	Reset();
	
	currentpassword = $('#ChangePasswordForm-CurrentPassword').val();
	newpassword = $('#ChangePasswordForm-InputNewPassword').val();
	newpasswordconfirmation = $('#ChangePasswordForm-InputConfirmNewPassword').val();
	
	if(newpassword != newpasswordconfirmation) {
		alert("The passwords aren't alike");
		return;
	}

	//currentpassword = md5(currentpassword);
	//newpassword = md5(newpassword);
	
	var userdata = {
			"userID": wiki.currentUserID,
			"currentpassword":encodeURIComponent(currentpassword),
			"password":encodeURIComponent(newpassword)
		};
	
	wiki.CreateOrSaveUser(userdata,
							function(response) { alert(response.message); GoToPage('Homepage'); });
	
	return false;
}

/*
 * Page
 */

var GoToPage = function(pagename) {
	
	wiki.DisplayPage(pagename,
					function(response) {
							cache[pagename] = response;
							localStorage.setItem("wiki_cache", JSON.stringify(cache));
							RenderPage(response,
								function(response) {
									DisplayPage(response, false);

									if (response.page.can_edit) {

										$("#NavEditPage")
											.css("display", "block")
											.unbind("click")
											.click(function (e) {
												e.preventDefault();
												GoToEditPageForm(pagename);
											})
											.attr("href", "./EditPage-" + pagename + ".html");
										$("#NavGetVersions")
											.css("display", "block")
											.unbind("click")
											.click(function (e) {
												e.preventDefault();
												GoToVersions(pagename);
											})
											.attr("href", "./Versions-" + pagename + ".html");

									}
								}
							);
					},
					HandleErrorCodes,
					//function(response) { GetPageFromCache(pagename, false, response); },
					
					function(xhr, type, message) { GetPageFromCache(pagename, null, true, xhr, type, message); }
	);
	
	return false;
}

var GetPageFromCache = function(pagename, data, error, response_or_xhr) {
	if(pagename in cache || data) {
		var data = data || cache[pagename];
		
		DisplayPage(data, true);
	} else if(!error) {
		HandleErrorCodes(response_or_xhr);
	} else {
		DisplayError(response_or_xhr, type, message);
	}
}

var RenderPage = function(data, callback)
{
	$.ajax({
		type: 'POST',
		url: 'render.php',
		data: {data:JSON.stringify(data)},
		dataType: 'json',
		success:
			function(response)
			{
				callback(response);
			}
	});
}

var DisplayPage = function(response, fromCache) {
	Reset();

	$("#DisplayPage").show();
	$("#DisplayPage-Title").html(response.page.title);
	$("#DisplayPage-Content").html(response.page.content);
	
	switch(response.page.visibility) {
		case "PUBLIC": $('#NavPublicPage').css("display","block"); break;
		case "PROTECTED": $('#NavProtectedPage').css("display","block"); break;
		case "PRIVATE": $('#NavPrivatePage').css("display","block"); break;
		case "GROUPPRIVATE": $('#NavGroupPrivatePage').css("display","block"); break;
	}

	if(response.no_headline) {
		$('#DisplayPage-Title').hide();
		$('#DisplayPage-TitleSeparator').hide();
	} else {
		$('#DisplayPage-Title').show();
		$('#DisplayPage-TitleSeparator').show();
	}

	if(response.no_navbar) {
		$('#Navbar').hide();
		$('#content').css("margin-top","0px");
	}
	
	if(response.no_footerbar) {
		$('#FooterBar').hide();
	}
	
	$('#DisplayPage-LastEdit-Timestamp').html(response.page.last_edit.timestamp);
	$('#DisplayPage-LastEdit-User').html(response.page.last_edit.user);
	
	HideLoading();
	
	UpdateWindow(response.page.title, {method:"DisplayPage",args:[response, fromCache]}, response.page.name+".html");
}

var DisplayNewPageForm = function(e, title) {
	var title = title || "";
	Reset();
	
	$("#EditPageForm-Title").html("New page");
	
	$("#EditPageForm-InputTitle").val(title);
	$("#EditPageForm-InputContent").val("");
	$("#EditPageForm-InputSummary").val("Initialized page");
	$("#EditPageForm-MinorChange").prop("checked",true);
	$("#EditPageForm-MinorChangeWrapper").hide();
	$("#EditPageForm-Visibility-PUBLIC").prop("checked",true);
	$("#EditPageForm-Manipulation-EVERYONE").prop("checked",true);
	$("#EditPageForm-DeletePage").hide();
	//InitializeAceEditor("");
	
	wiki.GetUsers(
		function(response) {
			for(var u = 0; u < response.users.length; u++) {
				var user = response.users[u];
					
				$("#EditPageForm-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == wiki.currentUserID ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
			}
		}, function(response) { alert(response.message); }, function() {}
	);
	
	wiki.GetGroups(
		function(response) {
			for(var g = 0; g < response.groups.length; g++) {
				var group = response.groups[g];
					
				$("#EditPageForm-Group").append('<option value="'+group.group_id+'">'+group.name+'</option>');
			}
		}, function(response) { alert(response.message); }, function() {}
	);
	

	//$("#NavDropChanges").css("display","block").unbind("click").click(DropChanges);
	$("#EditPageForm-Drop").unbind("click").click(DropChanges);
	//$("#NavPreviewChanges").css("display","block").unbind("click").click(PreviewPage);
	$("#EditPageForm-Preview").unbind("click").click(PreviewPage);
	//$("#NavSaveChanges").css("display","block").unbind("click").click(SavePage);
	$("#EditPageForm-Save").unbind("click").click(SavePage);
	
	BindKey(83,SavePage,true); // Ctrl+S
	BindKey(27,DropChanges,false); // ESC	
	
	$("#EditPageForm-ShowEditingHelp").unbind("click").click(DisplayEditingHelp);
	
	HideLoading();
	$("#EditPageForm").show().data("pageid","");
	
	$("#EditPageForm-FullscreenToggle").click(FullscreenEditor);

	UpdateWindow("Create a new page", {method:"DisplayNewPageForm", args:[null, title]}, "NewPage.html");
	
	return false;
}

var GoToEditPageForm = function(pagename) {
	Reset();
	
	wiki.GetPageByName(pagename, DisplayEditPageForm);
	
	return false;
}

var DisplayEditPageForm = function(response) {
	
	$("#EditPageForm").data("pageid", response.page.page_id);
	
	$("#EditPageForm-Title").html("Editing page '"+response.page.title+"'");
	$("#EditPageForm-InputTitle").val(response.page.title);
	$("#EditPageForm-InputContent").val(response.page.content);
	$("#EditPageForm-InputSummary").val("");
	$("#EditPageForm-Visibility-"+response.page.visibility).prop("checked",true);
	$("#EditPageForm-Manipulation-"+response.page.manipulation).prop("checked",true);
	$("#EditPageForm-MinorChange").prop("checked",false);
	$("#EditPageForm-MinorChangeWrapper").show();
	$("#EditPageForm-Owner").empty();
	$("#EditPageForm-Group").empty();
	$("#EditPageForm-DeletePage").show();
	
	//InitializeAceEditor(response.page.content);
	
	var currentOwner = response.page.owner;
	var currentGroup = response.page.group;
	
	wiki.GetUsers(
		function(response) {
			for(var u = 0; u < response.users.length; u++) {
				var user = response.users[u];
					
				$("#EditPageForm-Owner").append('<option value="'+user.user_id+'"'+(user.user_id == currentOwner.user_id ? ' selected="selected"' : '')+'>'+user.loginname+'</option>');
			}
		}, function(response) { alert(response.message); }, function() {}
	);
	
	wiki.GetGroups(
		function(response) {
			for(var g = 0; g < response.groups.length; g++) {
				var group = response.groups[g];
					
				$("#EditPageForm-Group").append('<option value="'+group.group_id+'"'+(group.group_id == currentGroup.group_id ? ' selected="selected"' : '')+'>'+group.name+'</option>');
			}
		}, function(response) { alert(response.message); }, function() {}
	);
	
	if(response.page.page_id === 1) {
		$("#EditPageForm-DeletePage").attr("disabled", true);
	} else {
		$("#EditPageForm-DeletePage").attr("disabled", false).unbind("click").click(GoToDeletePageDialog);
	}
	
	//$("#NavDropChanges").css("display","block").unbind("click").click(DropChanges);
	$("#EditPageForm-Drop").unbind("click").click(DropChanges);
	//$("#NavPreviewChanges").css("display","block").unbind("click").click(PreviewPage);
	$("#EditPageForm-Preview").unbind("click").click(PreviewPage);
	//$("#NavSaveChanges").css("display","block").unbind("click").click(SavePage);
	$("#EditPageForm-Save").unbind("click").click(SavePage);
	
	BindKey(83,SavePage,true); // Ctrl+S
	BindKey(27,DropChanges,false); // ESC
	
	$("#EditPageForm-ShowEditingHelp").unbind("click").click(DisplayEditingHelp);
	
	HideLoading();
	 
	$("#EditPageForm").show();
	
	$("#EditPageForm-FullscreenToggle").click(FullscreenEditor);

	UpdateWindow('Editing page \''+response.page.title+'\'', {method:"DisplayEditPageForm", args:[response]}, 'EditPage-'+response.page.name+'.html');
}

var editorPrevHeight, editorPanePrevHeight;

var FullscreenEditor = function(e) {
	e.preventDefault();
	
	if (document.webkitFullscreenEnabled) {
		document.getElementById("EditPageForm-EditorPanel").webkitRequestFullscreen();
		
		editorPanePrevHeight = $("#EditPageForm-EditorPanel").height();
		editorPrevHeight = $("#EditPageForm-InputContent").height();
		
		$("#EditPageForm-FullscreenToggle").text("Close fullscreen").unbind("click").click(UnfullscreenEditor);
		$("#EditPageForm-EditorPanel").css("width","100%").css("height","100vh").css("padding","15px");
		$("#EditPageForm-InputContent").css("height","96%");
	}
	
	return false;
}

var UnfullscreenEditor = function(e) {
	e.preventDefault();
	
	if (document.webkitFullscreenEnabled) {
		document.webkitExitFullscreen();
		
		$("#EditPageForm-FullscreenToggle").text("Fullscreen").unbind("click").click(FullscreenEditor);
		$("#EditPageForm-EditorPanel").css("width","100%").css("height",editorPanePrevHeight).css("padding","0");
		$("#EditPageForm-InputContent").css("width","100%").css("height",editorPrevHeight);
	}
	
	return false;
}

var EditPageEditor;

/*var InitializeAceEditor = function(value) {
	EditPageEditor = ace.edit("EditPageForm-InputContent-Editor");
	EditPageEditor.getSession().setMode("ace/mode/html");
	EditPageEditor.getSession().setMode("ace/mode/javascript");
	EditPageEditor.getSession().setMode("ace/mode/css");
	EditPageEditor.getSession().setMode("ace/mode/php");
	//EditPageEditor.getSession().setMode("ace/mode/markdown");
	//EditPageEditor.setOptions({ maxLines: Infinity });
	EditPageEditor.getSession().setValue(value);
}*/

var DisplayEditingHelp = function() {
	var popup = window.open('./Editing_Help.html','_blank');
}

var PreviewPage = function() {
	Reset();
	
	var title = $("#EditPageForm-InputTitle").val();
	
	var pagedata = {
			'title': title,
			'content': $("#EditPageForm-InputContent").val(),
		};
	
	wiki.PreviewPage(pagedata, DisplayPagePreview);
}

var DisplayPagePreview = function(response) {
	DisplayPage(response);
	$("#NavBackToComposer").css("display","block").unbind("click").click(BackToEditPageForm);
	UpdateWindow(title + ' (Preview)', {method:"DisplayPagePreview", args:[response]}, "Preview.html");
}

var DropChanges = function() {
	$("#DisplayPage").hide();
	$("#EditPageForm").show();
	$("#NavBackToComposer").hide();
	$("#NavDropChanges").css("display","block");
	$("#NavPreviewChanges").css("display","block");
	$("#NavSaveChanges").css("display","block");
}

var SavePage = function(e) {
	if(e) { e.preventDefault(); }
	
	var pageID = $("#EditPageForm").data("pageid");
	
	var pagedata = {
		'pageID': pageID,
		'title': $("#EditPageForm-InputTitle").val(),
		'content': $("#EditPageForm-InputContent").val(),
		'summary': $("#EditPageForm-InputSummary").val(),
		'minor_edit': ($('#EditPageForm-MinorChange:checked').length > 0),
		'visibility': $('input[name=EditPageForm-Visibility]:checked').val(),
		'manipulation': $('input[name=EditPageForm-Manipulation]:checked').val(),
		'owner': $("#EditPageForm-Owner").val(),
		'group': $("#EditPageForm-Group").val()
	};
	
	wiki.CreateOrSavePage(pagedata,
		function(response) {
			alert("The page was saved successfully");
			
			GoToPage(response.page.name);
		}
	);
	
	return false;
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

var DisplaySearchForm = function(terms) {
	
	var terms = terms || "";
	
	if(terms != "") {
		$("#SearchForm-Keywords").val(terms);
	}
	
	Reset();
	HideLoading();
	$("#SearchForm").show().unbind("submit").submit(Search);

	UpdateWindow("Search", {method:"DisplaySearchForm", args: [terms]}, "Search.html");
	
	return false;
}

var Search = function() {
	Reset();
	var keywords = $(this).find("input[type~=text],input[type~=search],textarea").val();

	if(keywords == "") {
		DisplaySearchForm("");
	} else {
		wiki.GetPagesByKeywords(keywords, DisplaySearchResults);
	}

	return false;
}

var DisplaySearchResults = function(response) {
	
	$("#SearchResults-NumberOfResults").html(response.pages.length);
	$("#SearchResults-List").empty();
	
	for(var p = 0; p < response.pages.length; p++) {
		var page = response.pages[p];
		
		$("#SearchResults-List").append(''+
			'<div>' +
			'	<p><a href="'+page.name+'.html" onclick="return GoToPage(\''+page.name+'\')"><strong>'+page.title+'</strong></a></p>' +
			'	<p>'+page.content.substring(0,800)+'</p>' +
			'</div>' +	
			'<hr/>');
	}
	
	HideLoading();
	$("#SearchResults").show();
	UpdateWindow("Search results", {method:"DisplaySearchResults",args:[response]}, "Search.html?keywords=");
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
			'		<td><button type="button" class="btn btn-xs btn-primary EditUser" data-userid="'+user.user_id+'" data-loginname="'+user.loginname+'"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> Edit</button></td>' +
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
			'		<td><button type="button" class="btn btn-xs btn-primary EditGroup" data-groupid="'+group.group_id+'"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> Edit</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-warning GetGroupUsers" data-groupid="'+group.group_id+'"'+(group.group_id == 1 ? 'disabled="disabled"' : "")+'><i class="glyphicon glyphicon-user" aria-hidden="true"></i> Members</button></td>' +
			'		<td><button type="button" class="btn btn-xs btn-danger DeleteGroup" data-groupid="'+group.group_id+'"'+(group.group_id == 1 ? 'disabled="disabled"' : "")+'><i class="glyphicon glyphicon-trash" aria-hidden="true"></i> Delete</button></td>' +
			'	</tr>');
	}
	
	groupListPopulated = true;
	
	if(userListPopulated == true) {
		callback();
	}
}

var DisplayUserList = function() {

	HideLoading();
	
	$("#UserManagement").show();
	$("#UserManagement-UserTab").tab("show");
	
	FinalizeUserManagement();
	
	UpdateWindow("User management", {method:"DisplayUserList", args: []}, "Users.html");
}

var DisplayGroupList = function() {
	
	HideLoading();
	
	$("#UserManagement").show();
	$("#UserManagement-GroupTab").tab("show");
	
	FinalizeUserManagement();
	UpdateWindow("Group management", {method:"DisplayGroupList", args: []}, "Groups.html");
}

var FinalizeUserManagement = function() {
	$("#Users-NewUser").unbind("click").click(DisplayNewUserForm);
	$("#Users-NewGroup").unbind("click").click(DisplayNewGroupForm);
	
	$(".EditUser").unbind("click").click(GoToEditUserForm);
	$(".EditPermissions").unbind("click").click(GoToEditPermissionsForm);
	$(".DeleteUser").unbind("click").click(GoToDeleteUserDialog);
	
	$(".EditGroup").unbind("click").click(GoToEditGroupForm);
	$(".GetGroupUsers").unbind("click").click(GoToGroupMembers);
	$(".DeleteGroup").unbind("click").click(GoToDeleteGroupDialog);
	
	$("#UserManagement-UserTab").on("shown.bs.tab", function() { UpdateWindow("User management", null, "Users.html"); } );
	$("#UserManagement-GroupTab").on("shown.bs.tab", function() { UpdateWindow("Group management", null, "Groups.html"); } );
}

var DisplayNewUserForm = function(e) {
	Reset();
	$("#EditUserForm-Title").html("New user");
	$("#EditUserForm-Button").html('<i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create user');
	
	$("#EditUserForm-InputLoginname").val("");
	$("#EditUserForm-InputPassword").val("");
	$("#EditUserForm-InputConfirmPassword").val("");
	
	HideLoading();
	$("#EditUserForm").show().unbind("submit").submit(SaveUser).data("userid","");
	
	UpdateWindow("Create a new user", {method:"DisplayNewUserForm", args:[null]}, "NewUser.html");
}

var GoToEditUserForm = function() {
	var $this = $(this);
	
	var userid = $this.data("userid");
	
	wiki.GetUserByID(userid, DisplayEditUserForm);
}

var GoToEditUserFormByLoginname = function(loginname) {
	wiki.GetUserByLoginname(loginname, DisplayEditUserForm);
}

var DisplayEditUserForm = function(response) {
	Reset();
	$("#EditUserForm-Title").html("Edit user '"+response.user.loginname+"'");
	$("#EditUserForm-Button").html('<i class="glyphicon glyphicon-edit" aria-hidden="true"></i> Save user');
	$("#EditUserForm-InputLoginname").val(response.user.loginname);
	$("#EditUserForm-InputPassword").val("");
	$("#EditUserForm-InputConfirmPassword").val("");
	HideLoading();
	$("#EditUserForm").show().unbind("submit").submit(SaveUser).data("userid",response.user.user_id);
	
	UpdateWindow("Edit user '"+response.user.loginname+"'", {method:"DisplayEditUserForm", args: [response]}, "EditUser-"+response.user.loginname+".html");
}

var SaveUser = function(e) {
	if(e) { e.preventDefault(); }
	
	var userID = $("#EditUserForm").data("userid");
	var loginname = $("#EditUserForm-InputLoginname").val();
	var password = $("#EditUserForm-InputPassword").val();
	var confirmPassword = $("#EditUserForm-InputConfirmPassword").val();
	
	if(!userID && !password) {
		alert("You need to provide a password");
		return false;
	}
	
	if(password != confirmPassword) {
		alert("The passwords don't match");
		return false;
	}
	
	//password = md5(password);
	
	var userdata = {
		'userID': userID,
		'loginname': loginname,
		'password': encodeURIComponent(password)
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
	$("#EditGroupForm-Title").html("New group");
	$("#EditGroupForm-Button").html('<i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create group');
	
	$("#EditGroupForm-InputName").val("");
	
	HideLoading();
	$("#EditGroupForm").show().unbind("submit").submit(SaveGroup).data("groupid","");
	UpdateWindow("Create a new group", {method:"DisplayNewGroupForm", args: [null]}, "NewGroup.html");
}

var GoToEditGroupForm = function() {
	var $this = $(this);
	
	var groupid = $this.data("groupid");
	
	wiki.GetGroupByID(groupid, DisplayEditGroupForm);
}

var GoToEditGroupFormByName = function(name) {
	wiki.GetGroupByName(name, DisplayEditGroupForm);
}

var DisplayEditGroupForm = function(response) {
	Reset();
	$("#EditGroupForm-Title").html("Edit group '"+response.group.name+"'");
	$("#EditGroupForm-Button").html('<i class="glyphicon glyphicon-edit" aria-hidden="true"></i> Save group');
	$("#EditGroupForm-InputName").val(response.group.name);
	HideLoading();
	$("#EditGroupForm").show().unbind("submit").submit(SaveGroup).data("groupid",response.group.group_id);
	UpdateWindow("Edit group '"+response.group.name+"'", {method:"DisplayEditGroupForm", args: [response]}, "EditGroup-"+response.group.name+".html");
}

var SaveGroup = function(e) {
	if(e) { e.preventDefault(); }
	
	var groupID = $("#EditGroupForm").data("groupid");
	var name = $("#EditGroupForm-InputName").val();
	
	var groupdata = {
		'groupID': groupID,
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

var GoToEditPermissionsFormByLoginname = function(loginname) {
	wiki.GetUserByLoginname(loginname,
						function(response) {
							var userID = response.user.user_id;
							$("#EditPermissions").data("userid", userID);
							
							RefreshEditPermissionsForm();
						});
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
	
	UpdateWindow("Edit user permissions", {method: "DisplayEditPermissionsForm", args: [response]}, "EditUserPermissions-"+user.loginname+".html");
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
				});
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
					});
}

var memberListPopulated = false;
var nonMemberListPopulated = false;

var GoToGroupMembersByName = function(name) {
	wiki.GetGroupByName(name,
						function(response) {
							var groupID = response.group.group_id;
							$("#GroupUsers").data("groupid", groupID);
							
							RefreshGroupMembers();
						});
}

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
	
	$("#GroupUsers-Remove").unbind("click").click(RemoveUsersFromGroup);
	$("#GroupUsers-Add").unbind("click").click(AddUsersToGroup);
	
	HideLoading();
	
	$("#GroupUsers").show();
	
	UpdateWindow("Assign group members in '"+group.name+"'", {method:"DisplayGroupMembers", args: [response]}, "GroupMembers-"+group.name+".html");
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
	$("#NotAuthorized").show();
	
	UpdateWindow("Not authorized");
}

var DisplayNotFoundError = function(response) {
	Reset();
	HideLoading();
	$("#PageNotFound").show();
	
	if(response.page != 'undefined') {
		$("#NavEditPage").css("display","block").unbind("click").click(function(e) { e.preventDefault(); DisplayNewPageForm(e, response.page); }).attr("href","./NewPage.html?"+response.page);
		$("#PageNotFound-NewPage").css("display","inline").unbind("click").click(function(e) { e.preventDefault(); DisplayNewPageForm(e, response.page); });
	} else {
		$("#PageNotFound-NewPage").css("display","none");
	}
	
	UpdateWindow("Page not found");
}

var DisplayError = function(xhr, type, message) {
	Reset();
	HideLoading();
	$("#Error").show();
	
	UpdateWindow("Error");
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
