<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');


switch($_REQUEST['action']){
	case 'delete':{
		// reset any block types in the bucket
		$dbObject->query('UPDATE '.$tablePrefix.'types SET bucket = NULL WHERE bucket="'.addslashes($_REQUEST['bucket']).'"');
		// delete the bucket
		$dbObject->query('DELETE FROM '.$tablePrefix.'buckets WHERE bucket="'.addslashes($_REQUEST['bucket']).'"');
		header('location: typesBuckets.php');
		exit;
		break;
	}
}

if($_POST['moderate'] == TRUE){
	foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,7) == 'delete_'){
			// reset any block types in the bucket
			$dbObject->query('UPDATE '.$tablePrefix.'types SET bucket = NULL WHERE bucket="'.addslashes($postValue).'"');
			// delete the bucket
			$dbObject->query('DELETE FROM '.$tablePrefix.'buckets WHERE bucket="'.addslashes($postValue).'"');
		}
	}
	header('location: typesBuckets.php');
	exit;
}

if($_POST['bucketCreate'] == TRUE){
	$dbObject->query('INSERT INTO '.$tablePrefix.'buckets (bucket, name, description) VALUES ("'.addslashes($_POST['bucket']).'","'.addslashes($_POST['name']).'","'.addslashes($_POST['description']).'")');
	header('location: typesBuckets.php');
	exit;
}


$gb_smarty->assign('bucketData',$statsObject->getBucket());

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/typesBuckets.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>