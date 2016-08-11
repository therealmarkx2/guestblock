<?php
include_once('../config.php');
if($installDate == NULL){
	header('location: ../install/');
	exit;
}
include('secure.php');

// include guestblock class file
include('../classes/guestblock.class.php');
$guestblockObject = new guestblock($dbObject, $tablePrefix);
// include stats class file
include('../classes/stats.class.php');
$statsObject = new stats($dbObject, $guestblockObject);

$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'settings');
while($result->fetchInto($row)){
	$setting[$row['setting']] = $row['value'];
}
$gb_smarty->assign('version', $setting['version']);

$css[] = '../css/admin.css';
$gb_smarty->assign('css',$css);
$gb_smarty->assign('title','Guestblock Admin');
?>