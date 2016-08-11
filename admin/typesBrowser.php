<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');
switch($_REQUEST['action']){
	case 'delete':{
		// delete requested block
		// delete any blocks laid of that type
		$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE type="'.addslashes($_REQUEST['type']).'"');
		// delete the type
		$dbObject->query('DELETE FROM '.$tablePrefix.'types WHERE type="'.addslashes($_REQUEST['type']).'"');
		// delete any stacks that now have no blocks in them
		$result = $dbObject->query('SELECT '.$tablePrefix.'stacks.stackid, COUNT('.$tablePrefix.'blocks.stackid) as count FROM '.$tablePrefix.'stacks LEFT JOIN '.$tablePrefix.'blocks ON '.$tablePrefix.'stacks.stackid = '.$tablePrefix.'blocks.stackid GROUP BY '.$tablePrefix.'stacks.stackid ORDER BY count');
		while($result->fetchInto($row)){
			if($row['count'] > 0){
				break;
			} else {
				// delete the stack
				$dbObject->query('DELETE FROM '.$tablePrefix.'stacks WHERE stackid='.$row['stackid']);
			}
		}
		header('location: typesBrowser.php');
		exit;
		break;
	}
	case 'activate':{
		// delete requested block
		$dbObject->query('UPDATE '.$tablePrefix.'types SET active="TRUE" WHERE type="'.addslashes($_REQUEST['type']).'"');
		header('location: typesBrowser.php');
		exit;
		break;
	}
	case 'deactivate':{
		// delete requested block
		$dbObject->query('UPDATE '.$tablePrefix.'types SET active="FALSE" WHERE type="'.addslashes($_REQUEST['type']).'"');
		header('location: typesBrowser.php');
		exit;
		break;
	}
}

if($_POST['moderate'] == TRUE){
	foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,7) == 'delete_'){
			// delete any blocks laid of that type
			$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE type="'.addslashes($postValue).'"');
			// delete the type
			$dbObject->query('DELETE FROM '.$tablePrefix.'types WHERE type="'.addslashes($postValue).'"');
			$delete = TRUE;
		}
		if(substr($postKey,0,7) == 'active_'){
			$activeTypes[] = addslashes($postValue);
		}
	}
	$result=$dbObject->query('SELECT * FROM '.$tablePrefix.'types');
	while($result->fetchInto($row)){
		if(in_array($row['type'],$activeTypes) == TRUE){
			$dbObject->query('UPDATE '.$tablePrefix.'types SET active="TRUE" WHERE type="'.$row['type'].'"');			
		} else {
			$dbObject->query('UPDATE '.$tablePrefix.'types SET active="FALSE" WHERE type="'.$row['type'].'"');			
		}
	}
	if($delete == TRUE){
		// delete any stacks that now have no blocks in them
		$result = $dbObject->query('SELECT '.$tablePrefix.'stacks.stackid, COUNT('.$tablePrefix.'blocks.stackid) as count FROM '.$tablePrefix.'stacks LEFT JOIN '.$tablePrefix.'blocks ON '.$tablePrefix.'stacks.stackid = '.$tablePrefix.'blocks.stackid GROUP BY '.$tablePrefix.'stacks.stackid ORDER BY count');
		while($result->fetchInto($row)){
			if($row['count'] > 0){
				break;
			} else {
				// delete the stack
				$dbObject->query('DELETE FROM '.$tablePrefix.'stacks WHERE stackid='.$row['stackid']);
			}
		}
	}
	header('location: typesBrowser.php');
	exit;
}

$buckets = $statsObject->getBucket();
$gb_smarty->assign('bucketData',$buckets);

if($_POST['bucketView'] == 'gb_bucketless'){
	$viewBucketless = TRUE;	
	$gb_smarty->assign('bucketView','Blocks without a ');
} else {
	$gb_smarty->assign('bucketView',addslashes($_POST['bucketView']));
	$gb_smarty->assign('bucketName',$buckets[addslashes($_POST['bucketView'])]['name']);
}
$blockData = $statsObject->getBlockType(NULL,addslashes($_POST['bucketView']),$viewBucketless);
$gb_smarty->assign('blockData',$blockData);
	
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/typesBrowser.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>