<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');
if($_POST['edit'] == TRUE){
	if($_POST['display']!='TRUE'){
		$_POST['display'] = 'FALSE';
	}
	if($_POST['code']!='TRUE'){
		$_POST['code'] = 'FALSE';
	}
	if($_POST['active']!='TRUE'){
		$_POST['active'] = 'FALSE';
	}
	$query="UPDATE ".$tablePrefix."types SET name='".addslashes($_POST['name'])."', bucket='".addslashes($_POST['bucket'])."', description='".addslashes($_POST['description'])."', display='".addslashes($_POST['display'])."', code='".addslashes($_POST['code'])."', active='".addslashes($_POST['active'])."' WHERE type='".addslashes($_POST['typeEdit'])."'";
	$dbObject->query($query);
	header('location: typesBrowser.php#'.$_POST['typeEdit']);
	exit;
}

if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$_REQUEST['type'].'/code/block.'.$_REQUEST['type'].'.class.php') == TRUE){
	$gb_smarty->assign('codeFound',TRUE);	
} else {
	$gb_smarty->assign('codeFound',FALSE);
}

$gb_smarty->assign('bucketData',$statsObject->getBucket());
$blockData = $statsObject->getBlockType($_REQUEST['type']);

$gb_smarty->assign('blockData',$blockData);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/typesEdit.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>