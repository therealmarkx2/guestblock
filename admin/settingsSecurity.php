<?php
include_once('common.php');
$gb_smarty->assign('domain','settings');

if($_POST['update'] == TRUE){
	if($_POST['password'] != $_POST['retype']){
		$update = array('status'=>'FAIL','reason'=>'retype');
	}
	if((strlen(trim($_POST['username'])) < 6) & (strlen(trim($_POST['password'])) < 6)){
		$update = array('status'=>'FAIL','reason'=>'length');
	}
	if($update['status'] != 'FAIL'){
		$dbObject->query("UPDATE ".$tablePrefix."settings SET value='".md5(addslashes($_POST['password']))."' WHERE setting='password'");
		$dbObject->query("UPDATE ".$tablePrefix."settings SET value='".md5(addslashes($_POST['username']))."' WHERE setting='username'");
		$update = array('status'=>'PASS');
	}
	$gb_smarty->assign('update',$update);
}

if($_POST['adminEmail'] == TRUE){
	$dbObject->query("UPDATE ".$tablePrefix."settings SET value='".addslashes($_POST['email'])."' WHERE setting='adminEmail'");
}

$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'settings');
while($result->fetchInto($row)){
	$setting[$row['setting']] = $row['value'];
}

$gb_smarty->assign('adminEmail',$setting['adminEmail']);
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/settingsSecurity.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>