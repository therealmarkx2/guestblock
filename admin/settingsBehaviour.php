<?php
include_once('common.php');
$gb_smarty->assign('domain','settings');
if($_POST['update'] == TRUE){
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['email']).'" WHERE setting="emailNotification"');
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['hidePopup']).'" WHERE setting="hidePopup"');
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['disableUrl']).'" WHERE setting="disableUrl"');
	$url = addslashes($_POST['overrideUrl']);
	if (strpos($url, 'http://') === FALSE & $url!=NULL)
	{ // note: three equal signs
		$url='http://'.$url;
	}
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.$url.'" WHERE setting="overrideUrl"');
	$flood = $_POST['flood'];
	if(is_numeric($flood) == FALSE){
		$flood = NULL;
	}
	if($flood != $setting['floodControl']){
		$dbObject->query('TRUNCATE '.$tablePrefix.'flood');
	}
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($flood).'" WHERE setting="floodControl"');
	if($_POST['timeZone'] != $setting['timeZone']){
		// timeZone change, clear flood control
		$dbObject->query('TRUNCATE '.$tablePrefix.'flood');
	}
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['timeZone']).'" WHERE setting="timeZone"');
}

$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'settings');
while($result->fetchInto($row)){
	$setting[$row['setting']] = $row['value'];
}

$gb_smarty->assign('email', htmlspecialchars($setting['emailNotification']));
$gb_smarty->assign('flood', $setting['floodControl']);
$gb_smarty->assign('timeZone', $setting['timeZone']);
$gb_smarty->assign('overrideUrl', htmlspecialchars($setting['overrideUrl']));
$gb_smarty->assign('hidePopup', $setting['hidePopup']);
$gb_smarty->assign('disableUrl', $setting['disableUrl']);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/settingsBehaviour.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>