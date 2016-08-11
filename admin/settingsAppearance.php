<?php
include_once('common.php');
$gb_smarty->assign('domain','settings');
if($_POST['update'] == TRUE){
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['blockSelectTable']).'" WHERE setting="blockSelectTable"');
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['drawEmptyStacks']).'" WHERE setting="drawEmptyStacks"');
}

$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'settings');
while($result->fetchInto($row)){
	$setting[$row['setting']] = $row['value'];
}


$gb_smarty->assign('drawEmptyStacks', $setting['drawEmptyStacks']);
$gb_smarty->assign('blockSelectTable', $setting['blockSelectTable']);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/settingsAppearance.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>