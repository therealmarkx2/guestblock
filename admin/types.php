<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/types.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>