<?php
include('../config.php');
if($installDate != NULL){
	header('location: reinstall.php');
	exit;
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
<h2>SMARTY package</h2>
<p>Searching for SMARTY installation... <span class="green">OK</span></p>
<h2>continue installation</h2>
<p>The next stage is to check the <a title="continue the installation" href="install.php?action=files">file permissions</a>.</p>
</div>
<hr class="cleaner" />
</div>
</body>
</html>