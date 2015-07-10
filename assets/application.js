var online = true;
var pageID = 1;
var page = 'Homepage';
var view = 'DisplayPage';
var mode = '';
var loginname = '';
var password = '';
var isSignedIn = false;
var isFirstSignIn = true;
var cookies = false;
var statusbar = false;
var cache = {};

$(function()
{
	var fromCache = localStorage.getItem("cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
	CheckConnectivity();
	setInterval(CheckConnectivity, 5000);
	
	$('[data-toggle="tooltip"]').tooltip();
	
	var startWith = window.location.hash.substring(1);
	
	switch(startWith) {
		case "Edit": view = "EditPage"; break;
	}
	
	ExtractPageName();
	
	loginname = GetCookie('wiki_loginname');
	password = GetCookie('wiki_password');
	
	$('#SignInForm').submit(SignIn);
	$('#NavEditPage').click(EditPage);
	
	$('#NavSaveChanges').click(SaveChanges);
	$('#NavDropChanges').click(DropChanges);
	$('#NavNewPage').click(NewPage);
	
	CheckLoginCredentials();
});

var ExtractPageName = function() {
	var currentUrl = window.location.href;
	var pageName = currentUrl.split("").reverse().join("").split('/',1);
	pageName = pageName[0].split("").reverse().join("").split(".",1);
	pageName = pageName[0];
	
	if(pageName == "index") {
		document.location = "Homepage.html";
	}
	
	page = pageName;
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
				
				$('#SignInText').empty().append(loginLink);
			} else {
				HideSignInForm();
				isSignedIn = true;
				
				$('#SignInText').html("Signed in as <strong>"+loginname+"</strong> ");
				
				var logoutLink = $('<a href="#SignOut">Sign out</a>');
				logoutLink.click(SignOut);
				
				$('#SignInText').append(logoutLink);
			}
			
			isFirstSignIn = false;
			DisplayAction();
		},
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
				
				$('#SignInText').empty().append(loginLink);
	
	// Back to the previous action
	DisplayAction();
}

var DisplaySignInForm = function() {
	HideAllActions();
	HideLoading();
	$("#SignInForm").css("display","block");
}

var HideSignInForm = function() {
	$("#SignInForm").css("display","none");
	DisplayLoading();
}

var GoToPage = function(pagename) {
	page = pagename;
	DisplayPage();
}

/*
 * Online check
 */
var CheckConnectivity = function() {
	if(!navigator.onLine) {
		online = false;
		ShowStatusBar("You are not online.");
		return;
	}
	
	$.ajax({
		'type': 'GET',
		'url': 'request.php?command=ConnectivityCheck',
		'dataType': 'json',
		'success': function() {
			HideStatusBar();
			online = true;
		},
		'error': function() {
			ShowStatusBar("You are not online.");
			online = false;
		}
	});
}

var HideStatusBar = function() {
	$("#statusbar").css("display","none");
	statusbar = false;
}

var ShowStatusBar = function() {
	$("#statusbar").css("display","block");
	statusbar = true;
}

/*
 * Actions
 */
var DisplayAction = function() {
	switch(view) {
		case "DisplayPage":
			DisplayPage();
			break;
			
		case "EditPage":
			EditPage();
			break;
	}
}

var HideAllActions = function() {
	view = "";
	
	UnbindKeys();
	
	HideSignInForm();
	
	$('#Navbar').css("display","block");
	$('#content').css("margin-top","60px");
	$('#FooterBar').css("display","block");
	
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
		$("#NavSaveChanges").css("display","none");
	
	$("#PageNotFound").css("display","none");
	
	DisplayLoading();
}

/*
 * Display page
 */
var DisplayPage = function() {
	HideAllActions();
	
	view = "DisplayPage";
	
	var url = 'request.php?command=DisplayPage&page=' + page;
	
	if(!online)
	{
		if(page in cache) {
			alert("Page in cache");
		} else {
			alert("Page not in cache");
		}
	} else {
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
				}
				
				HideLoading();
				
				cache[page] = response.page;
				localStorage.setItem("cache", JSON.stringify(cache));
				
				// Change URL in address bar to this page
				window.history.replaceState({}, response.page.title, page+".html");
				
				pageID = response.page.pageID;
				
				$('#DisplayPage-Title').css("display", "block");
				$('#DisplayPage-TitleSeparator').css("display", "block");
				
				$("#DisplayPage").css("display","block");
				$("#DisplayPage-Title").html(response.page.title);
				$("#DisplayPage-Content").html(response.page.content);
				
				$("#NavEditPage").css("display","block");
				$("#NavGetVersions").css("display","block");
				
				document.title = response.page.title;
				
				switch(response.page.visibility) {
					case "PUBLIC": $('#NavPublicPage').css("display","block"); break;
					case "PROTECTED": $('#NavProtectedPage').css("display","block"); break;
					case "PRIVATE": $('#NavPrivatePage').css("display","block"); break;
					case "GROUPPRIVATE": $('#NavPrivatePage').css("display","block"); break;
				}
				
				if(response.page.no_headline) {
					$('#DisplayPage-Title').css("display", "none");
					$('#DisplayPage-TitleSeparator').css("display", "none");
				}
				
				if(response.page.no_navbar) {
					$('#Navbar').css("display","none");
					$('#content').css("margin-top","0px");
				}
				
				if(response.page.no_footerbar) {
					$('#FooterBar').css("display","none");
				}
				
				$('#DisplayPage-LastEdit').html(response.page.lastedit);
				
				/*
				if(response.page.hasTableOfContents) {
					$("#NavTableOfContents").css("display","block");
				}
				*/
				
				BindKey(69,EditPage,true); // Ctrl+E
				//BindKey(86,GetVersions,true); // Ctrl+V
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
}

var DisplayPageNotFound = function() {
	HideAllActions();
	
	$("#PageNotFound").css("display","block");
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
	
	$("#NewPage").css("display","block");
	$("#NewPage-InputTitle").val("");
	$("#NewPage-InputContent").val("");
			
		$("#NavDropChanges").css("display","block");
		$("#NavSaveChanges").css("display","block");
			
	$("textarea.tab").keydown(indent);
	$('textarea.tab').keypress(autoindent);
	
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
			}
			
			HideLoading();
			
			pageID = response.page.pageID;
			
			$("#EditPage").css("display","block");
			$("#EditPage-Title").html(response.page.title);
			$("#EditPage-InputTitle").val(response.page.title);
			$("#EditPage-InputContent").val(response.page.content);
			$("#EditPage-InputPageID").val(response.page.pageID);
			$("#EditPage-Visiblity-" + response.page.visibility).attr("checked",true);
			
			$("#NavDropChanges").css("display","block");
			$("#NavSaveChanges").css("display","block");
			
			$("textarea.tab").keydown(indent);
			$('textarea.tab').keypress(autoindent);
			
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
	
	var data = {
		'title': $("#"+mode+"-InputTitle").val(),
		'content': $("#"+mode+"-InputContent").val(),
		'summary': $("#"+mode+"-InputSummary").val(),
		'minor_edit': minor_edit,
		'visibility': $('input[name='+mode+'-Visiblity]:checked').val()
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

var DropChanges = function() {
	DisplayPage();
};

var DisplayLoading = function() { $("#Loading").css("display","block"); }
var HideLoading = function() { $("#Loading").css("display","none"); }

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

/*
 * Auto indent
 */

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


