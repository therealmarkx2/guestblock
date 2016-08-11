<?php
$result = $dbObject->query('SELECT * FROM '.$tablePrefix.'types ORDER BY type');
while($result->fetchInto($row)){		
	if($row['type'] == NULL){
		$nullErrors++;
		$errorFound++;	
	}
	
	// check for missing folders
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$row['type'].'/') == FALSE){
		$folderErrors[] = $row['type'];
		$errorFound++;
	}
	
	// check for missing default images
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$row['type'].'/images/block.gif') == FALSE){
		$imageErrors[] = $row['type'];
		$errorFound++;
	}
	
	// check for code class existence	
	if($row['code'] == 'TRUE'){
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$row['type'].'/code/block.'.$row['type'].'.class.php') == FALSE){
			$codeErrors[] = $row['type'];
			$errorFound++;
		}
	}
}

// serach type for entries in blocks table that do not have corresponding match in types table
$result = $dbObject->query('SELECT '.$tablePrefix.'blocks.blockid, '.$tablePrefix.'blocks.type AS missingType, '.$tablePrefix.'types.type FROM '.$tablePrefix.'blocks LEFT JOIN '.$tablePrefix.'types ON '.$tablePrefix.'blocks.type='.$tablePrefix.'types.type WHERE '.$tablePrefix.'types.type IS NULL');
while($result->fetchInto($row)){
	$errorFound++;
	$blocksType[] = array(
	'blockid' => $row['blockid'],
	'type' => $row['missingType']);
}

// search for NULL entries
$result = $dbObject->query('SELECT blockid FROM '.$tablePrefix.'blocks WHERE type IS NULL OR type = "" ORDER BY blockid DESC');
while($result->fetchInto($row)){
	$errorFound++;
	$blocksNull[] = array(
	'blockid' => $row['blockid']);
}

if($errorFound != NULL){
	$types = array(
		'nulls' => $nullErrors++,
		'folders' => $folderErrors,
		'images' => $imageErrors,
		'codes' => $codeErrors
	);
	
	$blocks = array(
		'nulls' => $blocksNull,
		'types' => $blocksType,
	);
	
	
	$errorReport = array(
		'count' => $errorFound,
		'types' => $types,
		'blocks' => $blocks
	);
}
?>