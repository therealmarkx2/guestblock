<?php
// create session
session_start();

include('../config.php');
if($installDate != NULL){
	header('location: reinstall.php');
	exit;
}

if($_POST['install'] == 'start'){
	if($_POST['path'].'Smarty.class.php')	{
		// smarty installtion found - proceed
		$_SESSION['gb_smartyPath'] = $_POST['path'];
		$_SESSION['gb_compilePath'] = $_POST['compilePath'];
		header ('location: smartySuccess.php');
		exit;
	} else {
		$smartyError = TRUE;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head  profile="http://gmpg.org/xfn/1">
<title>guestblock installation</title>
<link rel="Shortcut Icon" href="../favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
		@import url('../css/admin.css');	
</style>
</head>
<body class="install">
<div id="header">
<h1>guestblock installation</h1>
</div>
<div id="content">
<div id="sidebar"><div id="sidebar-content">
<div id="menu">
<ul id="nav">
<li><img src="../images/admin/completed.gif" alt="image"/>Introduction</li>
<li><img src="../images/admin/current.gif" alt="image"/>Smarty</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Permissions</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Database</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Settings</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Finished</li>
</ul>
</div>
</div></div>
<div id="main"><div id="main-content">
<?php
if($smartyError == TRUE){
?>
<h2>SMARTY package</h2>
<p>Searching for SMARTY installation... <span class="red">FAIL</span></p>
<p class="red">Warning! SMARTY package could not be found. Please go back and amend the SMARTY path setting. The path searched was:</p>
<blockquote><p><?php echo $_POST['path']; ?></p></blockquote>
<p><a title="go back" href="javascript:history.back()">Go back to correct Smarty path</a>.</p>
<?php
} else {
?>
<h2>SMARTY package</h2>
<p>To begin the installtion you must first provide the path to the SMARTY package. For <strong>security reasons</strong> (to do with smarty) it is advisable that your smarty files are outside of your public web space.</p>
<form class="install" action="" method="post">
<fieldset>
<legend>smarty details</legend>
<div class="row">
<label for="path">Smarty Path</label>
<input name="path" id="path" type="text" />
<input name="install" id="install" type="hidden" value="start"/>
</div>
<p>This should be the path to the SMARTY package on your webserver. You should enter the path to the directory that contains this file (amongst others):</p>
<blockquote><p>Smarty.class.php</p></blockquote>
<p>If, for example, the package is located at:</p>
<blockquote><p>/home/username/packages/smarty/libs/</p></blockquote>
<p>Then you would enter (<span class="red">note the slashes at the start and end</span>):</p>
<blockquote><p>/home/username/packages/smarty/libs/</p></blockquote>
<div class="row">
<p>As part of the working of Smarty it creates what it calls <em>compiled templates</em>. Please enter the path to where these should be stored. As with the main Smarty files it is advisable for <strong>security reasons</strong> that this is outside of your public websapce. You should enter the path in the same format as above.</p>
<label for="compilePath">Compiled Path</label>
<input name="compilePath" id="compilePath" type="text" />
<p>You should create this directory now if it does not yet exist and ensure that the compiled templates directory is writable. To do this you must CHMOD the directory to 755.</p>
</div>
<div class="row">
<input id="submit" class="submit" type="submit" value="Check for SMARTY" />
</div>
</fieldset>
</form>
</div>
<hr class="cleaner" />
</div>
<?php
}
?>
</body>
</html>