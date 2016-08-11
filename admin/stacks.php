<?php
include_once('common.php');
$gb_smarty->assign('domain','stacks');
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/stacks.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>