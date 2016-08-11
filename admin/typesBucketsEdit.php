<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');

if($_POST['edit'] == TRUE){
	// update the bucket
	$query="UPDATE ".$tablePrefix."buckets SET name='".addslashes($_POST['name'])."', bucket='".addslashes($_POST['bucket'])."', description='".addslashes($_POST['description'])."' WHERE bucket='".addslashes($_GET['bucket'])."'";
	$dbObject->query($query);
	// update blocks in the bucket
	$query="UPDATE ".$tablePrefix."types SET bucket='".addslashes($_POST['bucket'])."' WHERE bucket='".addslashes($_GET['bucket'])."'";
	$dbObject->query($query);
}
if($_POST['manage'] == TRUE){	
	foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,7) == 'bucket_'){
			$len = strlen($postKey);
			$type = substr($postKey,7,($len-7));
			$newBucket[$type] = addslashes($postValue);
		}
	}
	foreach($newBucket as $block => $bucket){
		$dbObject->query("UPDATE ".$tablePrefix."types SET bucket='".$bucket."' WHERE type='".$block."'");
	}
}

if($_POST['bucketless'] == TRUE){
foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,11) == 'bucketless_'){
			$len = strlen($postKey);
			$type = substr($postKey,11,($len-11));
			$newBucket[$type] = addslashes($postValue);
		}
	}
	foreach($newBucket as $block => $bucket){
		$dbObject->query("UPDATE ".$tablePrefix."types SET bucket='".$bucket."' WHERE type='".$block."'");
	}
	header('location: typesBuckets.php#'.$_REQUEST['bucket']);
	exit;
}

$gb_smarty->assign('bucketData',$statsObject->getBucket(addslashes($_REQUEST['bucket'])));
$gb_smarty->assign('bucketDataAll',$statsObject->getBucket());
$gb_smarty->assign('bucketDataNull',$statsObject->getBlockType(NULL,NULL,TRUE));

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/typesBucketsEdit.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>