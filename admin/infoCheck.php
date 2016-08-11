<?php
include_once('common.php');
$gb_smarty->assign('domain','info');
include_once('errorCheck.php');

$gb_smarty->assign('errorReport',$errorReport);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/infoCheck.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>