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
<li><img src="../images/admin/current.gif" alt="image"/>Introduction</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Smarty</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Permissions</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Database</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Settings</li>
<li><img src="../images/admin/pending.gif" alt="image"/>Finished</li>
</ul>
</div>
</div></div>
<div id="main"><div id="main-content">
<h2>welcome to guestblock</h2>
<p>Thank you for downloading guestblock. This installation is designed to help you install the guestblock package in a few simple steps.</p>
<h2>about</h2>
<p>Guestblock has been created by Matthew Hadley from <a title="visit differentSky" href="http://www.differentsky.com/">differentsky.com</a>.</p>
<p>Guestblock is a kind of guest<i>book</i> (hence the name) but it's more a kind of collaberative input experiment. It was was inspired by <a title="visit the inspiration for guestblock" href="http://www.nec.co.jp/eco/en/ecotonoha/index.html">ecotonoha</a>. 
The aim was to achieve something similar, yet different. It was a project to make use of xhtml, css, javascript (originally, but now no longer used), mysql and some php code rather than flash. The ideas behind guestblock will evolve and change as people contribute toward it.</p>
<h2>licence agreement</h2>
<p>Guestblock is distributed under the GNU licence agreement. You may <a title="view licence agreement" href="../gpl.txt">view the agreement here</a>.</p>
<p>By installing and using guestblock you are agreeing to the stated terms and conditions. Additionally you must display somewhere the linking information to credit the original author:</p>
<div id="creditHolder">
<div id="credit"><a title="visit guestblock.com" href="http://www.guestblock.com"><img title="visit guestblock.com" alt="guestblock blueprint" src="../images/admin/bluePrint.gif" />built by guestblock.com</a></div>
</div>
<p>Guestblock was made possible by:</p>
<ul>
<li>Matthew from <img title="visit differentSky.com" alt="differentSky" src="../images/admin/differentSky.gif" /> <a title="visit differentsky.com" href="http://www.differentsky.com">differentSky.com</a>, the original developer.</li>
<li><a title="visit flumpcakes.co.uk" href="http://flumpcakes.co.uk">Flump</a> for css, security checking and suggesting new features.</li>
<li><a title="visit Jokai Fan's website" href="http://blogs.njimko.de/">Jokai Fan</a> for graphic optimisations and code suggestions.</li>
<li><a title="visit webjones.net" href="http://www.webjones.net">Webjones</a> for coming up wth new features, including the notion of new blocks.</li>
<li>All the beta testers (<a href="http://nitevilla.net/">Scotbuff</a>, <a href="http://thebombsite.com/">Stuart</a>, <a href="http://www.tamba2.org.uk/T2/">Podz</a>, <a href="http://www.onesmartwoman.com/">One Smart Woman</a>)</li>
<li>Everyone who contributed at the <a title="visit differentsky forums" href="http://www.differentsky.com/forums/">differentsky guestblock forums</a>.</li>
</ul>
<h2>Requirements</h2>
<p>Guestblock requires the following to function.</p>
<ul>
<li>PHP 4.0.6 or later</li>
<li>MySQL</li>
<li>SMARTY template engine</li>
<li>PEAR DB Package</li>
<li>Support for PHP sessions</li>
<li>Cookies should be enabled</li>
</ul>
<h2>Before you start</h2>
<p>Your webhost (or yourself if you run your own webserver) should be providing you with PHP and MySQL support. You should have a MySQL database for Guestblock to use (either an existing database or create a new one), with a username and password for MySQL connections.</p>
<p>Guestblock uses some open source packages which you will need to obtain a copy of.</p>
<p><img alt="Smarty" title="Smarty" src="../images/admin/smarty.gif"/> <img alt="Pear" title="Pear" src="../images/admin/pear.gif"/></p>
<p>The <a title="visit SMART website" href="http://smarty.php.net/" >SMARTY Template Engine</a> is availabe for <a title="download Smarty" href="http://smarty.php.net/download.php">download from their site</a>. Installation should be a simple case of unzipping and uploading the contents to a directory on your webserver. For security reasons you should put Smarty in a directory outside of your public webspace.</p>
<p><a title="visit PEAR website" href="http://pear.php.net/">PEAR</a> is a structured library of open-sourced code for PHP users. The database package is used by Guestblock to communicate with MySQL. PHP installations often come with a bundled PEAR distribution that you can instruct Guestblock to use. The simplest installation option is to try and use this distribution and you will not need to download anything from the PEAR website.</p>
<p>If this is not available to you or you want to get hold of the latest PEAR distribution then you should <a title="download" href="http://pear.php.net/package/DB">download this package</a> and also the main <a title="download" href="http://pear.php.net/package/PEAR">PEAR package</a>. The main PEAR package should be unzipped and the DB package unzipped and copied to your webserver. For security reasons you should put Smarty in a directory outside of your public webspace. Confused? Here's a few <a title="explanation" href="pearDetail.php">screenshots as explanation</a>. Remember these steps are only necessary if you are not using the PHP bundled PEAR distribution.</p>
<h2>start installation</h2>
<p>Once your MySQL database is in place and you have uploaded the open source packages you need then you can begin the install. Providing the path to your installation of the SMARTY package is the <a href="smarty.php">start of the installation</a>.</p>
</div>
<hr class="cleaner" />
</div>
</body>
</html>