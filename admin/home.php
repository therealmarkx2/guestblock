<?php
include_once('common.php');

// upgrade data
switch($setting['version']){
	case $setting['version']=='0.6':{
		$dbObject->query("INSERT into ".$tablePrefix."settings (setting,value) VALUES ('blockSelectTable', NULL)");
		$dbObject->query("INSERT into ".$tablePrefix."settings (setting,value) VALUES ('spanLimiter', '20')");
		$dbObject->query("INSERT into ".$tablePrefix."settings (setting,value) VALUES ('drawEmptyStacks', 'TRUE')");
		$dbObject->query("ALTER table ".$tablePrefix."blocks ADD selected ENUM('SELECTED','RANDOM','OVERRIDE')  DEFAULT \"SELECTED\" NOT NULL");
		$dbObject->query("UPDATE table ".$tablePrefix."blocks SET selected='selected'");		
		$dbObject->query("UPDATE ".$tablePrefix."settings set value='0.6.1' WHERE setting='version'");
		$newVersion = '0.6.1';
	}
}
$gb_smarty->assign('upgrade',$newVersion);

$gb_smarty->assign('domain','home');

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/home.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>