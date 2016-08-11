<?php
include_once('../../config.php');
if($installDate == NULL){
	header('location: install/');
	exit;
}
include_once('../../classes/guestblock.class.php');
$guestblockObject = new guestblock($dbObject, $tablePrefix);
include_once('../../classes/stats.class.php');
$statsObject = new stats($dbObject, $guestblockObject);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head  profile="http://gmpg.org/xfn/1">
<title>Guestblock</title>
<link rel="Shortcut Icon" href="/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
	@import url('../../css/default.css');
	@import url('../../css/guestblock.css');
</style>
</head>
<body>
<div id="header">
<h1>Guestblock</h1>
</div>
<div id="content">
<h1>Meet The Blocks</h1>
<p><small><a title="go back" href="javascript:history.back()">Back</a></small></p>
<div id="meetTheBlocks">
<?php
echo $statsObject->getAboutBlockTypes();
?>
<hr class="cleaner" />
</div>
</div>
</body>
</html>