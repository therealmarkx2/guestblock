<?php
// REQUIRED GUESTBLOCK CODE //
include_once('config.php');
if($installDate == NULL){
	header('location: install/');
	exit;
}
include_once('classes/guestblock.class.php');
$guestblockObject = new guestblock($dbObject, $tablePrefix);
$errorMessage = $guestblockObject->processBlock();
// END REQUIRED GUESTBLOCK CODE //
// STATS CODE //
include_once('classes/stats.class.php');
$statsObject = new stats($dbObject, $guestblockObject);
$errorMessage = $guestblockObject->processBlock();
// END STATS CODE //

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head  profile="http://gmpg.org/xfn/1">
<title>Guestblock</title>
<link rel="Shortcut Icon" href="/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
	@import url('css/default.css');
	@import url('css/guestblock.css');
</style>
</head>
<body>
<div id="header">
<h1>guestblock</h1>
</div>
<div id="content">
<div id="sidebar"><div id="sidebar-content">
<!-- START OF STATS CODE -->
<p>
This is the default Guestblock layout. If you want to keep using it that's great, otherwise you might like to customize this appearance to better suit your website.
</p>
<h2>Latest Blocks</h2>
<?php
echo $statsObject->getLatestBlocks(5);
?>
<h2>Quick Stats</h2>
<ul>
<li>Blocks<ul>
<li><?php echo $statsObject->getBlocksTodayCount(TRUE); ?> <a href="index.php" title="view today's Guestblock">laid today</a></li>
<li><?php echo $statsObject->getBlocksCount(TRUE); ?> laid so far in total</li>
</ul></li>
<li>Stacks<ul>
<li><?php echo $statsObject->getStacksCount(TRUE); ?> stacks have been built</li>
<li>tallest stack built <a title="view the tallest stack" href="<?php $stack = $statsObject->getBestStack(); echo $stack['link']  ?>"><?php echo $stack['date']; ?></a>, with <?php echo $stack['count'];?> blocks</li>
</ul></li></ul>
<!-- END OF STATS CODE -->
</div>
</div>
<div id="main"><div id="main-content">
<h1>The Guestblock</h1>

<!-- START OF GUESTBLOCK CODE -->
<p>The local stack time is <?php echo $guestblockObject->getLocalTime(); ?></p>
<?php
echo $errorMessage;
echo $guestblockObject->buildStack();
?>
<h2>Browse Stacks</h2>
<?php
echo $guestblockObject->buildBrowser();
?>
<h2>Add Your Block</h2>
<?php
echo $guestblockObject->blockForm();
?>
<!-- END OF GUESTBLOCK CODE -->

<!-- CREDIT -->
<div id="credit"><a title="visit guestblock.com" href="http://www.guestblock.com"><img title="visit guestblock.com" alt="guestblock blueprint" src="images/admin/bluePrint.gif" />built by guestblock.com</a></div>
<!-- END CREDIT -->

</div></div>
<hr class="cleaner" />
</div>
</body>
</html>