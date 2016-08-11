<?php
include_once('common.php');
$gb_smarty->assign('domain','info');

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/info.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>