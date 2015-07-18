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
var NewPageEditor = null;
var EditPageEditor = null;

$(function()
{
	var fromCache = localStorage.getItem("cache");
	
	if(fromCache != 'undefined' && fromCache != null)
	{
		cache = $.parseJSON(fromCache);
	}
	
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
	
	loginname = GetCookie('wiki_loginname');
	password = GetCookie('wiki_password');
	
	$('#SignInForm').submit(SignIn);
	$('#SignUpForm').submit(SignUp);
	$('#NavEditPage').click(EditPage);
	
	$('#NavSaveChanges').click(SaveChanges);
	$('#NavPreviewChanges').click(PreviewChanges);
	$('#NavDropChanges').click(DropChanges);
	$('#NavBackToComposer').click(BackToComposer);
	$('#NavGetVersions').click(GetVersions);
	$('#NavNewPage').click(NewPage);
	
	$('#EditPage-DeletePage').click(DeletePage);
	
	CheckLoginCredentials();
});

var ExtractPageName = function() {
	var currentUrl = window.location.href;
	var pageName = currentUrl.split("").reverse().join("").split('/',1);
	pageName = pageName[0].split("").reverse().join("").split(".",1);
	pageName = pageName[0];
	
	if(pageName == "" || pageName == "index") {
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
				
				var sep = $('<span> &bull; </span>');
				
				var signupLink = $('<a href="#SignUp">Sign up</a>');
				signupLink.click(DisplaySignUpForm);
				
				$('#SignInText').empty().append(loginLink).append(sep).append(signupLink);
			} else {
				HideSignInForm();
				isSignedIn = true;
				
				$('#SignInText').html("Signed in as <strong>"+loginname+"</strong> &bull; ");
				
				var logoutLink = $('<a href="#SignOut">Sign out</a>');
				logoutLink.click(SignOut);
				
				$('#SignInText').append(logoutLink);
			}
			
			isFirstSignIn = false;
			DisplayAction();
		},
		error: function(xhr, err1, err2) {
			if(!online) {
				DisplayAction();
			} else {
				alert(err1+"\n\n"+err2);
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
	
	// Back to the previous action
	DisplayAction();
}

var SignUp = function() {
	var $this = $(this);
	
	loginname = $('#SignUpForm-InputLoginName').val();
	password = $('#SignUpForm-InputPassword').val();
	passwordconfirmation = $('#SignUpForm-InputPassword').val();
	
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
			
			if(response.status == 1) {
				$("#SignUpForm").css("display","none");
				
				DisplaySignInForm();
			}
		}
	});
	
	return false;
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

var DisplaySignUpForm = function() {
	HideAllActions();
	HideLoading();
	$("#SignUpForm").css("display","block");
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
	}
}

var HideAllActions = function() {
	view = "";
	
	UnbindKeys();
	
	HideSignInForm();
	HideOfflineMessage();
	
	$('#Navbar').css("display","block");
	$('#content').css("margin-top","60px");
	$('#FooterBar').css("display","block");
	
	// Sign up form
	$("#SignUpForm").css("display","none");
	
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
			
			cache[page] = response;
			localStorage.setItem("cache", JSON.stringify(cache));
			
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
	
	switch(pagedata.visibility) {
		case "PUBLIC": $('#NavPublicPage').css("display","block"); break;
		case "PROTECTED": $('#NavProtectedPage').css("display","block"); break;
		case "PRIVATE": $('#NavPrivatePage').css("display","block"); break;
		case "GROUPPRIVATE": $('#NavPrivatePage').css("display","block"); break;
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
}

var DisplayNotAuthorized = function() {
	HideAllActions();
	HideLoading();
	
	$("#NotAuthorized").css("display","block");
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
	$("#NewPage-InputContent").val("").hide();
			
		$("#NavDropChanges").css("display","block");
		$("#NavPreviewChanges").css("display","block");
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
			} else if(response.status == 404) {
				DisplayPageNotFound();
				return;
			} else if(response.status == 401) {
				DisplayNotAuthorized();
				return;
			}
			
			HideLoading();
			
			pageID = response.page.page_id;
			
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
		'manipulation': $('input[name='+mode+'-Manipulation]:checked').val()
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
}

/*
 * Display page
 */
var GetVersions = function() {
	HideAllActions();
	
	view = "DisplayPage";
	
	var url = 'request.php?command=GetVersions&page=' + page;
	
	if(!online)
	{
		alert("You are not online.");
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
					'		<td>' + timestamp.toLocaleDateString() + '</td>' +
					'		<td>' + timestamp.toLocaleTimeString() + '</td>' +
					'		<td>' + version.user + '</td>' +
					'		<td>' + version.summary + '</td>' +
					'		<td>' + minor_edit + '</td>' +
					'	</tr>');
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
}

var DisplayLoading = function() { $("#Loading").css("display","block"); }
var HideLoading = function() { $("#Loading").css("display","none"); }

var DisplayOfflineMessage = function() { $("#Offline").css("display","block"); }
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


