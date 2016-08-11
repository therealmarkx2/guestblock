<?php
include_once('common.php');
$gb_smarty->assign('domain','stacks');
switch($_REQUEST['action']){
	case 'delete':{
		// get stack id for block to delete as this may be last block in stack
		$stackId = $dbObject->getOne('SELECT stackid FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($_REQUEST['blockid']));
		$blockCount = $dbObject->getOne('SELECT count(blockid) FROM '.$tablePrefix.'blocks WHERE stackid='.$stackId);
		if($blockCount == 1){
			// delete the stack
			$dbObject->query('DELETE FROM '.$tablePrefix.'stacks WHERE stackid='.$stackId);
		}
		// delete requested block
		$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($_REQUEST['blockid']));
		header('location: stacksDate.php');
		exit;
	}
	case 'approve':{
		// check for and remove IP address from spam filter
		$ip = $dbObject->getOne('SELECT ip FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($_REQUEST['blockid']));
		if (strpos($setting['spamWords'], $ip) !== FALSE)
		{ // note: three equal signs
			$setting['spamWords'] = str_replace(chr(10).$ip,'',$setting['spamWords']);
			// check if ip was at top of list, so without line break
			$setting['spamWords'] = str_replace($ip, '', $setting['spamWords']);
			$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.$setting['spamWords'].'" WHERE setting="spamWords"');
		}
		// approve requested block
		$dbObject->query('UPDATE '.$tablePrefix.'blocks SET approved="TRUE" WHERE blockid='.addslashes($_REQUEST['blockid']));
		header('location: stacksDate.php');
		exit;
	}
}

if($_POST['moderate'] == TRUE){
	foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,7) == 'delete_'){
			if($stackId == NULL){
				$stackId = $dbObject->getOne('SELECT stackid FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($postValue));
			}
			// delete requested block
			$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($postValue));
		}
		if(substr($postKey,0,8) == 'approve_'){
			// approve requested block
			$dbObject->query('UPDATE '.$tablePrefix.'blocks SET approved="TRUE" WHERE blockid='.$postValue);
			$ip = $dbObject->getOne('SELECT ip FROM '.$tablePrefix.'blocks WHERE blockid='.$postValue);
			if (strpos($setting['spamWords'], $ip) !== FALSE)
			{ // note: three equal signs
				$setting['spamWords'] = str_replace(chr(10).$ip, '', $setting['spamWords']);
				// check if ip was at top of list, so without line break
				$setting['spamWords'] = str_replace($ip, '', $setting['spamWords']);
				$updateSpam = TRUE;
			}
			$dbObject->query('UPDATE '.$tablePrefix.'blocks SET approved="TRUE" WHERE blockid='.addslashes($postValue));
		}
	}

	if($stackId != NULL){
		$blockCount = $dbObject->getOne('SELECT count(blockid) FROM '.$tablePrefix.'blocks WHERE stackid='.$stackId);
		if($blockCount == 0){
			// delete the stack
			$dbObject->query('DELETE FROM '.$tablePrefix.'stacks WHERE stackid='.$stackId);
		}
	}
	if($updateSpam == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.$setting['spamWords'].'" WHERE setting="spamWords"');	
	}
	header('location: stacksDate.php');
	exit;
}

if($_REQUEST['date'] != NULL){
	$_SESSION['gb_stackDate'] = $_REQUEST['date'];
	header('location: stacksDate.php');
}
if($_SESSION['gb_stackDate'] == NULL){
	$_SESSION['gb_stackDate']	= date(('Y-m-d'),strtotime("now ".$setting['timeZone']));
}
$date = $_SESSION['gb_stackDate'];
$year = substr($date,0,4);
$month = substr($date,5,2);
$day = substr($date,8,2);
if(is_numeric($year) == FALSE | is_numeric($month) == FALSE | is_numeric($day) == FALSE){
	$_SESSION['gb_stackDate'] = date('Y-m-d');
}
$gb_smarty->assign('date',$_SESSION['gb_stackDate']);
$gb_smarty->assign('dateFormat',date('l jS \o\f F Y',strtotime($_SESSION['gb_stackDate'])));

// get block details for a particular stack
$stackId = $guestblockObject->getStackIdbyDate($date);
if($stackId != NULL){
	$result = $dbObject->query('SELECT '.$tablePrefix.'blocks.*, '.$tablePrefix.'types.type, '.$tablePrefix.'types.name AS blockName FROM '.$tablePrefix.'blocks LEFT JOIN '.$tablePrefix.'types ON '.$tablePrefix.'blocks.type = '.$tablePrefix.'types.type WHERE stackid = '.$stackId.' ORDER BY time DESC');
	while($result->fetchInto($row)){
		$blockData[] = array(
		'blockid' => $row['blockid'],
		'type' => $row['type'],
		'block' => $row['blockName'],
		'time' => $row['time'],
		'name' => htmlspecialchars($row['name']),
		'url' => htmlspecialchars($row['url']),
		'email' => htmlspecialchars($row['email']),
		'message' => htmlspecialchars($row['message']),
		'ip' => $row['ip'],
		'approved' => $row['approved']);
	}
}
$gb_smarty->assign('blockData',$blockData);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display('admin/stacksDate.tpl');
$gb_smarty->display('layout/footerAdmin.tpl');
?>