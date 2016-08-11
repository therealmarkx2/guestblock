<?php
include_once('common.php');
$gb_smarty->assign('domain','settings');
$spamCount = $dbObject->getOne('SELECT COUNT(blockid) FROM '.$tablePrefix.'blocks WHERE approved="FALSE"');

if($_POST['update'] == TRUE){	
	$bad = array();
	$badWords = explode(chr(10),$_POST['bad']);
	foreach($badWords as $word){
		$word = trim($word);
		$word = strtolower($word);
		if($word != '' & (in_array($word, $bad) == FALSE)){
			$bad[] = $word;
		}
	}	
	$bad = implode(chr(10),$bad);
	$spam = array();
	$spamWords = explode(chr(10),$_POST['spam']);
	foreach($spamWords as $word){
		$word = trim($word);
		$word = strtolower($word);
		if($word != '' & (in_array($word, $spam) == FALSE)){
			$spam[] = $word;
		}
	}	
	$spam = implode(chr(10),$spam);
	
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($spam).'" WHERE setting="spamWords"');
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($bad).'" WHERE setting="badWords"');
	$dbObject->query('UPDATE '.$tablePrefix.'settings set value="'.addslashes($_POST['spamNotification']).'" WHERE setting="spamNotification"');
	if($_POST['approveAll'] == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="TRUE" WHERE setting="approveAll"');	
	} else {
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="FALSE" WHERE setting="approveAll"');		
	}
	if($_POST['url'] == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="TRUE" WHERE setting="allowUrl"');	
	} else {
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="FALSE" WHERE setting="allowUrl"');		
	}
	if($_POST['email'] == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="TRUE" WHERE setting="allowEmail"');	
	} else {
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="FALSE" WHERE setting="allowEmail"');		
	}
	if($_POST['spamIpCapture'] == TRUE){
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="TRUE" WHERE setting="spamIpCapture"');	
	} else {
		$dbObject->query('UPDATE '.$tablePrefix.'settings set value="FALSE" WHERE setting="spamIpCapture"');		
	}
	header('location: settingsSpam.php');
	exit;
}
if($_POST['retro'] == TRUE){	
	// apply retrospective filtering
	$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'blocks');
	while($result->fetchInto($row)){
		$uncleanBlocks[$row['blockid']] = array(
			'blockid' => $row['blockid'],
			'name' => $row['name'],
			'email' => $row['email'],
			'url' => $row['url'],
			'message' => $row['message'],
			'ip' => $row['ip'],
			'approved' => $row['approved']);
	}
	$cleanBlocks = $uncleanBlocks;
	foreach($uncleanBlocks as $block){
		$cleanBlocks[$block['blockid']]['name'] = $guestblockObject->replaceBadWords($block['name']);	
		$cleanBlocks[$block['blockid']]['message'] = $guestblockObject->replaceBadWords($block['message']);	
	}
	foreach($uncleanBlocks as $block){
		$updateName = NULL;
		$updateMessage = NULL;
		if($block['name'] != $cleanBlocks[$block['blockid']]['name']){
			// update name	
			$updateName = ' name = "'.addslashes($cleanBlocks[$block['blockid']]['name']).'"';
		}
		if($block['message'] != $cleanBlocks[$block['blockid']]['message']){
			// update message
			if($updateName != NULL){
				$updateMessage = ',';
			}
			$updateMessage .= ' message = "'.addslashes($cleanBlocks[$block['blockid']]['message']).'"';
		}
		if($updateName != NULL | $updateMessage != NULL){
			$filterBad[] = 'UPDATE '.$tablePrefix.'blocks SET '.$updateName.$updateMessage.' WHERE blockid='.$block['blockid'];
		}
	}
	$_SESSION['gb_retroBad'] = sizeof($filterBad);
	foreach($cleanBlocks as $block){
		if($block['approved'] == 'TRUE' && $guestblockObject->checkSpamClear($block['name'], $block['email'], $block['url'], $block['message'], $block['ip']) == FALSE)
		{
			$filterSpam[] = 'UPDATE '.$tablePrefix.'blocks SET approved="FALSE" WHERE blockid='.$block['blockid'];
		}	
	}
	$_SESSION['gb_retroSpam'] = sizeof($filterSpam);
	foreach($filterBad as $sql){
		$dbObject->query($sql);	
	}
	foreach($filterSpam as $sql){
		$dbObject->query($sql);	
	}
	$_SESSION['gb_retroAttempt'] = TRUE;
	header('location: settingsSpam.php#filter');
	exit;
}

$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'settings');
while($result->fetchInto($row)){
	$setting[$row['setting']] = $row['value'];
}

if($_SESSION['gb_retroAttempt'] == TRUE){
	$gb_smarty->assign('retroBad', $_SESSION['gb_retroBad']);	
	$gb_smarty->assign('retroSpam', $_SESSION['gb_retroSpam']);
	$gb_smarty->assign('retroAttempt', 'TRUE');	
	$_SESSION['gb_retroBad'] = NULL;
	$_SESSION['gb_retroSpam'] = NULL;
	$_SESSION['gb_retroAttempt'] = NULL;
}

$gb_smarty->assign('spamWords', $setting['spamWords']);
$gb_smarty->assign('badWords', $setting['badWords']);
$gb_smarty->assign('spamIpCapture', $setting['spamIpCapture']);
$gb_smarty->assign('allowUrl', $setting['allowUrl']);
$gb_smarty->assign('allowEmail', $setting['allowEmail']);
$gb_smarty->assign('spamNotification', $setting['spamNotification']);
$gb_smarty->assign('approveAll', $setting['approveAll']);
$gb_smarty->assign('spamCount', $spamCount);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/settingsSpam.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>