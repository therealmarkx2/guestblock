<?php
include_once('common.php');
$gb_smarty->assign('domain','stacks');
switch($_REQUEST['origin']){
	case 'moderation':{
		$gb_smarty->assign('location','moderation');
		break;
	}
	case 'browse':{
		$gb_smarty->assign('location','browse');
		break;
	}		
}

if($_POST['edit'] == TRUE){
	// update block details
	// check valid url
	if (strpos($_POST['url'], 'http://') === FALSE & $_POST['url']!=NULL)
	{ // note: three equal signs
	$_POST['url']='http://'.$_POST['url'];
	}
	// update the block in the database
	$query="UPDATE ".$tablePrefix."blocks SET type='".addslashes($_POST['type'])."', name='".addslashes($_POST['name'])."', message='".addslashes($_POST['message'])."', email='".addslashes($_POST['email'])."', url='".addslashes($_POST['url'])."' WHERE blockid=".$_POST['blockid'];
	$dbObject->query($query);
	
	switch($_POST['location']){
		case 'moderation':{
			header('location: stacksModeration.php#id'.$_POST['blockid']);
			exit;
		}
		case 'browse':{
			header('location: stacksDate.php#id'.$_POST['blockid']);
			exit;
		}		
	}
}

$blockData = $statsObject->getBlock($_REQUEST['blockid']);
$gb_smarty->assign('blockData',$blockData);

$blockTypes = $statsObject->getBlockType();
$gb_smarty->assign('blockTypes',$blockTypes);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/stacksEdit.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>