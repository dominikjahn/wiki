var wiki;

$(function() {
	var loginname = GetCookie('wiki_loginname');
	var password = GetCookie('wiki_password');
	
	wiki = new Wiki();
	wiki.SignIn(loginname, password);
	
	var fromCache = localStorage.getItem("wiki_cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
	var pageName = ExtractPageName().split(':');
	
	switch(pageName) {
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
			DisplayEditPageForm(pageName[1]);
			break;
			
		case "Versions":
			DisplayVersions(pageName[1]);
			break;
			
		case "Version":
			DisplayVersion(pageName[1]);
			break;
			
		case "User":
			DisplayUser(pageName[1]);
			break;
			
		case "EditUser":
			DisplayEditUserForm(pageName[1]);
			break;
			
		case "Group":
			DisplayGroup(pageName[1]);
			break;
			
		case "EditGroup":
			DisplayEditGroupForm(pageName[1]);
			
		case "Category":
			DisplayCategory(pageName[1]);
			break;
			
		default:
			DisplayPage(pageName[1]);
	}
});

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