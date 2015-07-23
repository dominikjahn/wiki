var wiki;
var cache = [];

$(function() {
	var loginname = GetCookie('wiki_loginname');
	var password = GetCookie('wiki_password');
	
	wiki = new Wiki();
	wiki.SignIn(loginname, password, ShowUserInfo, ShowSignInUpLinks);
	
	var fromCache = localStorage.getItem("wiki_cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
	var pageName = ExtractPageName();
	
	GoToView(pageName);
});

var SignOut = function() {
	SetCookie("loginname", "");
	SetCookie("password", "");
	
	loginname = "";
	password = "";
	
	wiki.SignOut(ShowSignInUpLinks);
}

var GoToView = function(view) {
	
	var view = view.split(':');
	
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
			DisplayUserList();
			break;
			
		case "Groups":
			DisplayGroupList();
			break;
		
		case "EditPage":
			DisplayEditPageForm(view[1]);
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
			DisplayPage(view[0]);
	}
}

var Reset = function() {
	$("#Loading").css("display","block");
	$("#PageNotFound").css("display","none");
	$("#NotAuthorized").css("display","none");
	$("#Offline").css("display","none");
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

var ShowUserInfo = function(response) {
	var preText = $('<span>Signed in as </span>');
	var changePasswordLink = $('<a href="ChangePassword.html"><strong>'+response.user.loginname+'</strong></a>');
	var postText = $('<span> &bull; </span>');
	var logoutLink = $('<a href="SignOut.html">Sign out</a>');
	
	changePasswordLink.click(DisplayChangePasswordForm);
	logoutLink.click(SignOut);
	
	$('#SignInText').empty().append(preText).append(changePasswordLink).append(postText).append(logoutLink);
}

var ShowSignInUpLinks = function() {
	var loginLink = $('<a href="SignIn.html">Sign in</a>');
	loginLink.click(DisplaySignInForm);
	
	var sep = $('<span> &bull; </span>');
	
	var signupLink = $('<a href="SignUp.html">Sign up</a>');
	signupLink.click(DisplaySignUpForm);
	
	$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
}


var DisplaySignInForm = function() {
	
}

var DisplaySignUpForm = function() {
	
}

var DisplayChangePasswordForm = function() {
	
}

var DisplayPage = function(pagename) {
	wiki.GetPageByName(pagename, false,
									function(response) {
											cache[pagename] = response;
											localStorage.setItem("wiki_cache", JSON.stringify(cache));
											DisplayPageContent(response);
									},
									function(response) { GetPageFromCache(pagename, false, reponse); },
									function(xhr, type, message) { GetPageFromCache(pagename, true, xhr, type, message); }
	);
}

var GetPageFromCache = function(pagename, error, response_or_xhr) {
	if(pagename in cache) {
		DisplayPageContent(cache[pagename]);
	} else if(!error) {
		HandleErrorCodes(response_or_xhr);
	} else {
		DisplayError(response_or_xhr, type, message);
	}
}

var DisplayPageContent = function(response) {
	HideLoading();
	
	$("#DisplayPage").css("display","block");
	$("#DisplayPage-Title").html(response.page.title);
	$("#DisplayPage-Content").html(response.page.content);
}

/*
 * Error messages
 */

var DisplayNotAuthorizedError = function() {
	HideLoading();
	alert("NOT AUTHORIZED");
}

var DisplayNotFoundError = function() {
	HideLoading();
	alert("NOT FOUND");
}

var DisplayError = function(xhr, type, message) {
	HideLoading();
	alert("ERROR");
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