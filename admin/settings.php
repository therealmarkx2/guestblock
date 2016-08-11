<?php
include_once('common.php');
$gb_smarty->assign('domain','settings');
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/settings.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>