<?php
include_once('common.php');
$gb_smarty->assign('domain','stacks');
switch($_REQUEST['action']){
	case 'delete':{
		// delete requested block
		$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE blockid='.$_REQUEST['blockid']);
		header('location: stacksModeration.php');
		break;
	}
	case 'approve':{
		// check for and remove IP address from spam filter
		$ip = $dbObject->getOne('SELECT ip FROM '.$tablePrefix.'blocks WHERE blockid='.addslashes($_REQUEST['blockid']));
		if (strpos($setting['spamWords'], $ip) !== FALSE)
		{ // note: three equal signs
		$setting['spamWords'] = str_replace(chr(10).$ip, '', $setting['spamWords']);
		// check if ip was at top of list, so without line break
		$setting['spamWords'] = str_replace($ip, '', $setting['spamWords']);
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.$setting['spamWords'].'" WHERE setting="spamWords"');
		}
		// approve requested block
		$dbObject->query('UPDATE '.$tablePrefix.'blocks SET approved="TRUE" WHERE blockid='.$_REQUEST['blockid']);
		header('location: stacksModeration.php');
		break;
	}
}

if($_POST['moderate'] == TRUE){
	foreach($_POST as $postKey => $postValue){
		if(substr($postKey,0,7) == 'delete_'){
			// delete requested block
			$dbObject->query('DELETE FROM '.$tablePrefix.'blocks WHERE blockid='.$postValue);
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
		}
	}
	if($updateSpam == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.$setting['spamWords'].'" WHERE setting="spamWords"');
	}
	header('location: stacksModeration.php');
}

// get block details for a particular stack
$result = $dbObject->query('
SELECT date, '.$tablePrefix.'blocks.*, '.$tablePrefix.'types.type, '.$tablePrefix.'types.name AS blockName
FROM '.$tablePrefix.'blocks 
LEFT JOIN '.$tablePrefix.'types ON '.$tablePrefix.'blocks.type = '.$tablePrefix.'types.type
LEFT JOIN '.$tablePrefix.'stacks ON '.$tablePrefix.'blocks.stackid = '.$tablePrefix.'stacks.stackid
WHERE approved="FALSE" ORDER BY time DESC
');
while($result->fetchInto($row)){
	$year = substr($row['date'],0,4);
	$month = substr($row['date'],5,2);
	$day = substr($row['date'],8,2);

	$blockData[] = array(
	'date' => $row['date'],
	'year' => $year,
	'month' => $month,
	'day' => $day,
	'blockid' => $row['blockid'],
	'type' => $row['type'],
	'block' => $row['blockName'],
	'time' => $row['time'],
	'name' => htmlspecialchars($row['name']),
	'url' => htmlspecialchars($row['url']),
	'email' => htmlspecialchars($row['email']),
	'message' => htmlspecialchars($row['message']),
	'ip' => $row['ip']);

	// build possible new spam words
	$possibleSpam[] = htmlspecialchars($row['name']);
	$possibleSpam[] = htmlspecialchars($row['url']);
	$possibleSpam[] = htmlspecialchars($row['email']);
	$possibleSpam[] = htmlspecialchars($row['message']);
	$possibleSpam[] = htmlspecialchars($row['ip']);
}

// clean up possibleSpam
if($possibleSpam != NULL){
	$spam = array();
	$spamWords = explode(chr(10),$setting['spamWords']);
	foreach($possibleSpam as $word){
		$word = trim($word);
		$word = strtolower($word);
		if($word != '' & (in_array($word, $spam) == FALSE) & (in_array($word, $spamWords) == FALSE)){
			$spam[] = $word;
		}
	}
}


$gb_smarty->assign('blockData',$blockData);
$gb_smarty->assign('possibleSpam',$spam);
$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/stacksModeration.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>