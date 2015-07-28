<!DOCTYPE html>
<?php
	require_once "../Configuration.php";
	
	if($_SERVER["HTTP_HOST"] == "localhost" || $_SERVER["HTTP_HOST"] == "127.0.0.1" || $_SERVER["HTTP_HOST"] == "::1") {
		echo '<html lang="en">';
	} else {
		echo '<html lang="en" manifest="wiki.appcache">';
	}
?>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		
		<title>Wiki</title>
		
		<base href="<?php echo Wiki\Configuration::WWW_ROOT; ?>" />

		<link href="assets/css/bootstrap.min.css" rel="stylesheet" />
		<link href="assets/application.css" rel="stylesheet" />
		<link href="favicon.ico" type="image/ico" rel="shortcut icon" />
	</head>
	<body>

		<nav class="navbar navbar-inverse navbar-fixed-top" id="Navbar">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="./Homepage.html" onclick="return GoToPage('Homepage');"><i class="glyphicon glyphicon-home navbar-logo" aria-hidden="true" id="logo"></i></a>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav navbar-left">
						<li><a href="./Search.html" onclick="return DisplaySearchForm();" role="button" aria-expanded="false" id="NavSearch"><i class="glyphicon glyphicon-search"></i> &#160; Search</a></li>
						<li><a href="./NewPage.html" onclick="return DisplayNewPageForm();" role="button" aria-expanded="false" id="NavNewPage"><i class="glyphicon glyphicon-plus"></i> &#160; New page</a></li>
						<li><a href="./Users.html" onclick="return DisplayUserList();" role="button" aria-expanded="false" id="NavUsers"><i class="glyphicon glyphicon-user"></i> &#160; User management</a></li>
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						
						<li data-toggle="tooltip" title="This page is public" data-placement="bottom"><a href="#" role="button" aria-expanded="false" id="NavPublicPage"><i class="glyphicon glyphicon-globe"></i></a></li>
						<li data-toggle="tooltip" title="This page is protected" data-placement="bottom"><a href="#" role="button" aria-expanded="false" id="NavProtectedPage"><i class="glyphicon glyphicon-eye-open"></i></a></li>
						<li data-toggle="tooltip" title="This page is private" data-placement="bottom"><a href="#" role="button" aria-expanded="false" id="NavPrivatePage"><i class="glyphicon glyphicon-user"></i></a></li>
						<li data-toggle="tooltip" title="This page is private to its group" data-placement="bottom"><a href="#" role="button" aria-expanded="false" id="NavGroupPrivatePage"><i class="glyphicon glyphicon-lock"></i></a></li>
						
						<li><a href="#TableOfContents" role="button" aria-expanded="false" id="NavTableOfContents"><i class="glyphicon glyphicon-th-list"></i> &#160; Table of Contents</a></li>
						<li><a href="./EditPage-Homepage.html" onclick="return GoToEditPageForm('Homepage')" role="button" aria-expanded="false" id="NavEditPage"><i class="glyphicon glyphicon-pencil" style="color:#833477"></i> &#160; Edit</a></li>
						<li><a href="./Versions-Homepage.html" role="button" aria-expanded="false" id="NavGetVersions"><i class="glyphicon glyphicon-repeat"></i> &#160; Versions</a></li>
						<li><a href="#" role="button" aria-expanded="false" id="NavDropChanges"><i class="glyphicon glyphicon-remove" style="color:#FF5742"></i> &#160; Drop changes</a></li>
						<li><a href="#" role="button" aria-expanded="false" id="NavPreviewChanges"><i class="glyphicon glyphicon-eye-open" style="color:#FF8800"></i> &#160; Preview changes</a></li>
						<li><a href="#" role="button" aria-expanded="false" id="NavSaveChanges"><i class="glyphicon glyphicon-floppy-disk" style="color:#8FD55A"></i> &#160; Save changes</a></li>
						<li><a href="#" role="button" aria-expanded="false" id="NavBackToComposer"><i class="glyphicon glyphicon-chevron-left" style="color:#8FD55A"></i> &#160; Back to composer</a></li>
					</ul>
				</div>
			</div>
		</nav>
		
		<div class="container-fluid" id="statusbar">
			<p>Statusbar</p>
		</div>

		<div id="content">
			<div id="Loading" class="jumbotron">
				<div class="container">
					<h1><small><i class="glyphicon glyphicon-refresh glyphicon-animate-slow"></i></small> Loading...</h1>
				
					<p>If this takes a little longer you may want to refresh the page.</p>
				</div>
			</div>
			
			<div id="Error" class="jumbotron">
				<div class="container">
					<h1><small><i class="glyphicon glyphicon-flash"></i></small> Error!</h1>
				
					<p>Something bad has happened.</p>
				</div>
			</div>
			
			<div id="PageNotFound" class="jumbotron">
				<div class="container">
					<h1>Page not found <small>404</small><h1>
					
					<p>The page you are looking for does not exist (anymore).</p>
					
					<p><a href="Homepage.html" class="btn btn-primary btn-lg">Go back to the homepage</a></p>
				</div>
			</div>
			
			<div id="NotAuthorized" class="jumbotron">
				<div class="container">
					<h1>Not authorized <small>401</small><h1>
					
					<p>Sorry, but you are not allowed to see the contents of this page.</p>
					
					<p><a href="Homepage.html" class="btn btn-primary btn-lg">Go back to the homepage</a></p>
				</div>
			</div>
			
			<div id="Offline" class="jumbotron">
				<div class="container">
					<h1>Offline <small><i class="glyphicon glyphicon-flash"></i></small><h1>
					
					<p>Either you or our server is offline, and we can't serve you this page from the offline cache.</p>
				</div>
			</div>
			
			<!--form id="SignInForm" accept-charset="UTF-8">
				<h1>Sign in</h1>
				
				<div class="form-group">
					<label for="SignInForm-InputLoginName">Login name</label>
					<input type="text" class="form-control" id="SignInForm-InputLoginName" />
				</div>
				<div class="form-group">
					<label for="SignInForm-InputPassword">Password</label>
					<input type="password" class="form-control" id="SignInForm-InputPassword" />
				</div>
				
				<button type="submit" class="btn btn-lg btn-success">Sign in</button>
			</form-->
			
			<form id="SignInForm" accept-charset="UTF-8" class="modal fade" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Sign in</h4>
						</div>
						<div class="modal-body">
							
							<div class="form-group">
								<label for="SignInForm-InputLoginName">Login name</label>
								<input type="text" class="form-control" id="SignInForm-InputLoginName" />
							</div>
							<div class="form-group">
								<label for="SignInForm-InputPassword">Password</label>
								<input type="password" class="form-control" id="SignInForm-InputPassword" />
							</div>
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" type="submit">Sign in</button>
						</div>
					</div>
				</div>
			</form>
			
			<form id="SignUpForm" accept-charset="UTF-8" class="modal fade" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Sign up</h4>
						</div>
						<div class="modal-body">
							
							<div class="form-group">
								<label for="SignUpForm-InputLoginName">Login name</label>
								<input type="text" class="form-control" id="SignUpForm-InputLoginName" />
							</div>
							<div class="form-group">
								<label for="SignUpForm-InputPassword">Password</label>
								<input type="password" class="form-control" id="SignUpForm-InputPassword" />
							</div>
							<div class="form-group">
								<label for="SignUpForm-InputConfirmPassword">Confirm password</label>
								<input type="password" class="form-control" id="SignUpForm-InputConfirmPassword" />
							</div>
							
						</div>
						<div class="modal-footer">
							<button class="btn btn-primary" type="submit">Sign up</button>
						</div>
					</div>
				</div>
			</form>
			
			<form id="ChangePasswordForm" accept-charset="UTF-8">
				<h1>Change password</h1>
				
				<div class="form-group">
					<label for="ChangePasswordForm-InputCurrentPassword">Current password</label>
					<input type="password" class="form-control" id="ChangePasswordForm-CurrentPassword" />
				</div>
				<div class="form-group">
					<label for="ChangePasswordForm-InputNewPassword">New password</label>
					<input type="password" class="form-control" id="ChangePasswordForm-InputNewPassword" />
				</div>
				<div class="form-group">
					<label for="ChangePasswordForm-InputConfirmNewPassword">Confirm new password</label>
					<input type="password" class="form-control" id="ChangePasswordForm-InputConfirmNewPassword" />
				</div>
				
				<button type="submit" class="btn btn-lg btn-success">Change password</button>
			</form>
			
			<div id="DisplayPage">
				<h1 id="DisplayPage-Title"></h1>
				<hr id="DisplayPage-TitleSeparator" />
				<div id="DisplayPage-Content"></div>
			</div>
			
			<!-- form id="NewPage" accept-charset="UTF-8">
				<h1>Create a new page</h1>
				
				<div class="panel panel-default">
					<div class="panel-body">
						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active"><a href="#NewPage-ContentTab" role="tab" data-toggle="tab">Content</a></li>
							<li role="presentation"><a href="#NewPage-AccessTab" role="tab" data-toggle="tab">Access</a></li>
						</ul>
							
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="NewPage-ContentTab">
								<div class="form-group">
									<label for="NewPage-InputTitle">Title</label>
									<input type="text" class="form-control" id="NewPage-InputTitle" placeholder="The title of the page" />
								</div>
								<div class="form-group">
									<label for="NewPage-InputContent">Content <button type="button" id="NewPage-ShowEditingHelp" class="btn btn-xs btn-primary">Show editing help (in a new window)</button></label>
									<textarea class="form-control tab" id="NewPage-InputContent" style="min-height:400px" placeholder="The content of the page"></textarea>
									<div id="NewPage-InputContent-Editor"></div>
								</div>
								<div class="form-group">
									<label for="NewPage-InputSummary">Summary of changes</label>
									<textarea class="form-control" id="NewPage-InputSummary" placeholder="Leave a note explaining what you have changed on this page">Created page</textarea>
								</div>
							</div>
  								
							<div role="tabpanel" class="tab-pane" id="NewPage-AccessTab">
								<h4>Visibility</h4>
								
								<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is visible to everyone who knows the URL to it"><input type="radio" name="NewPage-Visibility" id="NewPage-PublicPage" value="PUBLIC" checked="checked" /> Public page</label></div>
		        				<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is visible to everyone is signed in on this wiki"><input type="radio" name="NewPage-Visibility" id="NewPage-ProtectedPage" value="PROTECTED" /> Protected page</label></div>
		        				<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is only visible to its owner"><input type="radio" name="NewPage-Visibility" id="NewPage-PrivatePage" value="PRIVATE" /> Private page</label></div>
		        				<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is only visible to its owning group and the owner (even if he is not in the group)"><input type="radio" name="NewPage-Visibility" id="NewPage-GroupPrivatePage" value="GROUPPRIVATE" /> Group private page</label></div>
							  
						  		<hr/>
						  		
						  		<h4>Manipulation</h4>
						  		
								<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by everyone. If the page is at least protected or only visible to the owner or group this setting will be overwritten."><input type="radio" name="NewPage-Manipulation" id="NewPage-Manipulation-Everyone" value="EVERYONE" /> Everyone</label></div>
								<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by every registered user. If the page is only visible by the owner or group this setting will be overwritten."><input type="radio" name="NewPage-Manipulation" id="NewPage-Manipulation-Registered" value="REGISTERED" checked="checked" /> Registered users only</label></div>
								<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can only be edited by the owner itself."><input type="radio" name="NewPage-Manipulation" id="NewPage-Manipulation-Owner" value="OWNER" /> The owner only</label></div>
								<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by everyone in the group and the owner (even if he is not in the group)"><input type="radio" name="NewPage-Manipulation" id="NewPage-Manipulation-Group" value="OWNER" /> Users in the group only</label></div>
							  
						  		<hr/>
						  		
						  		<h4>Ownership</h4>
								
								<div class="form-group">
									<label for="NewPage-Owner">Owner</label>
									<select id="NewPage-Owner" class="form-control">
										
									</select>
								</div>
								
								<div class="form-group">
									<label for="NewPage-Group">Group</label>
									<select id="NewPage-Group" class="form-control">
										
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form-->
			
			<form id="EditPageForm" accept-charset="UTF-8" data-pageid="">
				
				<h1 id="EditPageForm-Title">New page</h1>
				<hr/>
				
				<div>
					<div class="panel panel-default">
  						<div class="panel-body">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#EditPageForm-ContentTab" role="tab" data-toggle="tab">Content</a></li>
								<li role="presentation"><a href="#EditPageForm-AccessTab" role="tab" data-toggle="tab">Access</a></li>
							</ul>
							
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="EditPageForm-ContentTab">
									<div class="form-group">
										<label for="EditPageForm-InputTitle">Title</label>
										<input type="text" class="form-control" id="EditPageForm-InputTitle" placeholder="The title of the page" />
									</div>
									<div class="form-group">
										<label for="EditPageForm-InputContent">Content <!-- button type="button" id="EditPageForm-ShowEditingHelp" class="btn btn-xs btn-primary">Show editing help (in a new window)</button --></label>
										<textarea class="form-control tab" id="EditPageForm-InputContent" style="min-height:400px" placeholder="The content of the page"></textarea>
										<div class="panel panel-default">
  											<div class="panel-body">
  												<div id="EditPageForm-InputContent-Editor"></div>
  											</div>
  										</div>
									</div>
									
									<hr/>
									
									<div class="form-group">
										<label for="EditPageForm-InputSummary" data-toggle="tooltip" data-placement="right" title="This information is helpful when browsing the revision history of this page">Summary of changes</label>
										<textarea class="form-control" id="EditPageForm-InputSummary" placeholder="Leave a note explaining what you have changed on this page"></textarea>
									</div>
									
									<div class="checkbox" id="EditPageForm-MinorChangeWrapper"><label data-toggle="tooltip" data-placement="right" title="Check this box if the changes you performed are only minor (e.g. corrected a spelling mistake)"><input type="checkbox" id="EditPageForm-MinorChange" /> Minor changes only</label></div>
								</div>
								
								<div role="tabpanel" class="tab-pane" id="EditPageForm-AccessTab">
									<h4>Visibility</h4>
									
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is visible to everyone who knows the URL to it">  <input type="radio" name="EditPageForm-Visibility" id="EditPageForm-Visibility-PUBLIC" value="PUBLIC"/> Public page</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is visible to everyone is signed in on this wiki"><input type="radio" name="EditPageForm-Visibility" id="EditPageForm-Visibility-PROTECTED" value="PROTECTED" /> Protected page</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is only visible to its owner">                    <input type="radio" name="EditPageForm-Visibility" id="EditPageForm-Visibility-PRIVATE" value="PRIVATE" /> Private page</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page is only visible to its owning group">             <input type="radio" name="EditPageForm-Visibility" id="EditPageForm-Visibility-GROUPPRIVATE" value="GROUPPRIVATE" /> Group private page</label></div>
									
									<hr/>
						  		
							  		<h4>Manipulation</h4>
							  		
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by everyone. If the page is at least protected or only visible to the owner or group this setting will be overwritten."><input type="radio" name="EditPageForm-Manipulation" id="EditPageForm-Manipulation-EVERYONE" value="EVERYONE" /> Everyone</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by every registered user. If the page is only visible by the owner or group this setting will be overwritten.">         <input type="radio" name="EditPageForm-Manipulation" id="EditPageForm-Manipulation-REGISTERED" value="REGISTERED" /> Registered users only</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can only be edited by the owner itself.">                                                                                             <input type="radio" name="EditPageForm-Manipulation" id="EditPageForm-Manipulation-OWNER" value="OWNER" /> The owner only</label></div>
									<div class="radio"><label data-toggle="tooltip" data-placement="right" title="This page can be edited by everyone in the group and the owner (even if he is not in the group)">                                               <input type="radio" name="EditPageForm-Manipulation" id="EditPageForm-Manipulation-GROUP" value="GROUP" /> Users in the group only</label></div>
								  
							  		<hr/>
									
									<h4>Ownership</h4>
									
									<div class="form-group">
										<label for="EditPageForm-Owner">Owner</label>
										<select id="EditPageForm-Owner" class="form-control">
											
										</select>
									</div>
									
									<div class="form-group">
										<label for="EditPageForm-Group">Group</label>
										<select id="EditPageForm-Group" class="form-control">
											
										</select>
									</div>
									
									<hr/>
									
									<button type="button" id="EditPageForm-DeletePage" class="btn btn-danger">Delete this page</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			
			
			<div class="modal fade" id="DeletePageDialog" role="dialog" data-pageid="">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Delete page</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to delete this page?</p>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal">No</button>
							<button class="btn btn-danger" id="DeletePageDialog-Confirm">Yes, delete this page</button>
						</div>
					</div>
				</div>
			</div>
			
			<!-- div id="PreviewPage">
				<h1 id="PreviewPage-Title"></h1>
				<hr id="PreviewPage-TitleSeparator" />
				<div id="PreviewPage-Content"></div>
			</div-->
		
			<div id="Versions">
				<h1>Revisions for &quot;<span id="Versions-PageTitle">...</span>&quot;</h1>
				
				<table class="table table-striped table-hover">
					<thead>
						<th colspan="2"> </th>
						<th>Timestamp</th>
						<th>User</th>
						<th>Summary</th>
						<th>Minor edit</th>
					</thead>
					<tbody id="Versions-List">
					</tbody>
				</table>
			</div>
			
			<form id="SearchForm">
				<h1>Search</h1>
				
				<div class="form-group">
					<label for="SearchForm-Keywords">Keywords</label>
					<textarea class="form-control" id="SearchForm-Keywords" placeholder="Keywords"></textarea>
				</div>
				
				<hr/>
				
				<p>
					Keywords are separated by white spaces. The title and content of a page will be evaluated.<br/>
					Use quotation marks (&quot;) to group keywords.<br/>
					Use <code>title:keywords</code> to limit keywords to the title only or <code>-title:keywords</code> for pages where the title doesn't contain the keyword.<br/>
					Use <code>content:keywords</code> to limit keywords to the content only or <code>-content:keywords</code> for pages where the content doesn't contain the keyword.<br/>
					Use <code>%words</code>, <code>key%</code> or <code>ke%rds</code> as wildcards in combination with <code>title:</code> and <code>content:</code><br/>
					Use <code>category:name</code> to look for pages inside a category, or <code>-category:name</code> to avoid pages in that category.
				</p>
				
				<hr/>
				
				<p><button class="btn btn-primary" type="submit">Search</button></p>
			</form>
			
			<div id="SearchResults">
				<h1>Results</h1>
				
				<p>We found <span id="SearchResults-NumberOfResults">...</span> pages matching your criteria:</p>
				
				<div id="SearchResults-List">
				
				</div>
			</div>
			
			<div id="UserManagement">
				<h1>User &amp; Group management</h1>
				
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#Users-UserTab" role="tab" data-toggle="tab" id="UserManagement-UserTab">Users</a></li>
					<li role="presentation"><a href="#Users-GroupTab" role="tab" data-toggle="tab" id="UserManagement-GroupTab">Groups</a></li>
				</ul>
					
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="Users-UserTab">
						<p><button class="btn btn-primary" id="Users-NewUser"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create a new user</button></p>
						
						<table class="table table-striped table-hover">
							<thead>
								<th>User</th>
								<th colspan="3">Action</th>
							</thead>
							<tbody id="User-List">
							</tbody>
						</table>
					</div>
					
					<div role="tabpanel" class="tab-pane" id="Users-GroupTab">
						<p><button class="btn btn-primary" id="Users-NewGroup"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create a new group</button></p>
						
						<table class="table table-striped table-hover">
							<thead>
								<th>Group</th>
								<th colspan="3">Action</th>
							</thead>
							<tbody id="Group-List">
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
			<form id="EditPermissions" accept-charset="UTF-8" data-userid="">
				<h1>Edit permissions for user '<span id="EditPermissions-Loginname">...</span>'</h1>
				
				<table class="table table-striped table-hover">
					<thead>
						<th>Permission</th>
						<th>Granted</th>
					</thead>
					<tbody id="UserPermissions-List">
					</tbody>
					<tfoot>
						<tr>
							<td><input type="text" id="EditPermissions-NewPermission" class="form-control" /></td>
							<td><button type="button" id="EditPermissions-NewPermissionSet" class="btn btn-primary"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Set</button></td>
						</tr>
					</tfoot>
				</table>
			</form>
			
			<form id="EditUserForm" accept-charset="UTF-8">
				<h1 id="EditUserForm-Title">New user</h1>
				
				<div class="form-group">
					<label for="EditUserForm-InputLoginName">Login name</label>
					<input type="text" class="form-control" id="EditUserForm-InputLoginname" />
				</div>
				<div class="form-group">
					<label for="EditUserForm-InputPassword">Password</label>
					<input type="password" class="form-control" id="EditUserForm-InputPassword" />
				</div>
				<div class="form-group">
					<label for="EditUserForm-InputConfirmPassword">Confirm password</label>
					<input type="password" class="form-control" id="EditUserForm-InputConfirmPassword" />
				</div>
				
				<button type="submit" class="btn btn-success" id="EditUserForm-Button"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create user</button>
			</form>
			
			<form id="EditGroupForm" accept-charset="UTF-8">
				<h1 id="EditGroupForm-Title">New group</h1>
				
				<div class="form-group">
					<label for="EditGroupForm-InputName">Name</label>
					<input type="text" class="form-control" id="EditGroupForm-InputName" />
				</div>
				
				<button type="submit" class="btn btn-success" id="EditGroupForm-Button"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i> Create group</button>
			</form>
			
			<div id="GroupUsers" data-groupid="">
				<h1>Users in group '<span id="GroupUsers-Groupname">...</span>'</h1>
				
				<div class="row">
					<div class="col-md-6">
						<button class="btn btn-danger btn-block" id="GroupUsers-Remove">Remove selected users from group <i class="glyphicon glyphicon-chevron-right" aria-hidden="true"></i></button>
						<br/>
						<select class="form-control" multiple="multiple" size="15" id="GroupUsers-InGroup">
							
						</select>
					</div>
					
					<div class="col-md-6">
						<button class="btn btn-success btn-block" id="GroupUsers-Add"><i class="glyphicon glyphicon-chevron-left" aria-hidden="true"></i> Add selected users to group</button>
						<br/>
						<select class="form-control" multiple="multiple" size="15" id="GroupUsers-NotInGroup">
							
						</select>
					</div>
				</div>
			</div>
			
			
			<div class="modal fade" id="DeleteUserDialog" role="dialog" data-userid="">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Delete user</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to delete this user?</p>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal">No</button>
							<button class="btn btn-danger" id="DeleteUserDialog-Confirm">Yes, delete this user</button>
						</div>
					</div>
				</div>
			</div>
			
			
			<div class="modal fade" id="DeleteGroupDialog" role="dialog" data-groupid="">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Delete group</h4>
						</div>
						<div class="modal-body">
							<p>Are you sure you want to delete this group?</p>
						</div>
						<div class="modal-footer">
							<button class="btn btn-default" data-dismiss="modal">No</button>
							<button class="btn btn-danger" id="DeleteGroupDialog-Confirm">Yes, delete this group</button>
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal fade" id="AboutWiki" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">About Wiki</h4>
						</div>
						<div class="modal-body">
							<p>This website is based on <a href="https://github.com/dominikjahn/wiki" target="_blank">Wiki.</a> Click the link to help improving it.</p>
							<hr/>
							<p>This website uses <a href="http://glyphicons.com/" target="_blank">Glyphicons</a> as provided by the Twitter Bootstrap Framework.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<footer id="FooterBar">
			<div class="navbar navbar-inverse navbar-fixed-bottom">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-footer">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="navbar-collapse collapse" id="navbar-footer">
						
						<p class="navbar-text navbar-left" id="SignInText">Not signed in</p>
						
						<ul class="nav navbar-nav navbar-right">
							<li><a href="#" data-toggle="modal" data-target="#AboutWiki"><i class="glyphicon glyphicon-info-sign"></i></a>
						</ul>
						
						<p class="navbar-text navbar-right">This page was last edited <strong id="DisplayPage-LastEdit-Timestamp">...</strong> by <span id="DisplayPage-LastEdit-User">...</span>. &#160; </p>
						
						
					</div>
				</div>
			</div>
		</footer>

		<script src="assets/js/jquery-1.11.2.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.0/ace.js"></script>
		<script src="assets/js/md5.min.js"></script>
		<script src="assets/data.js"></script>
		<script src="assets/view.js"></script>
	</body>
</html>
