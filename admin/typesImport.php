<?php
include_once('common.php');
$gb_smarty->assign('domain','blocks');
include_once('errorCheck.php');
if($errorReport['types']['nulls'] != NULL | $errorReport['types']['folders'] != NULL | $errorReport['types']['images'] != NULL | $errorReport['types']['codes'] != NULL){
	header('location: infoCheck.php');
	exit;	
}

if($_SESSION['gb_installSuccess'] == TRUE){
	$_SESSION['gb_installSuccess'] = NULL;
	$installSuccess = TRUE;
}
	
if($_POST['import'] == TRUE){
	foreach($_POST as $postKey => $postValue){
		
		if(substr($postKey,0,14) == 'importInstall_'){
			$importInstall[] = $postValue;
		}
		if(substr($postKey,0,16) == 'activateInstall_'){
			$activateInstall[$postValue] = 'TRUE';
		}
		
		if(substr($postKey,0,16) == 'importNoInstall_'){
			$importNoInstall[] = addslashes($postValue);
		}
		if(substr($postKey,0,18) == 'activateNoInstall_'){
			$activateNoInstall[$postValue] = 'TRUE';
		}
		if(substr($postKey,0,5) == 'name_'){
			$len = strlen($postKey);
			$type = substr($postKey,5,($len-5));
			$nameNoInstall[$type] = addslashes($postValue);
		}
		if(substr($postKey,0,7) == 'bucket_'){
			$len = strlen($postKey);
			$type = substr($postKey,7,($len-7));
			$bucketNoInstall[$type] = addslashes($postValue);
		}
		if(substr($postKey,0,12) == 'description_'){
			$len = strlen($postKey);
			$type = substr($postKey,12,($len-12));
			$descriptionNoInstall[$type] = addslashes($postValue);
		}
		if(substr($postKey,0,5) == 'code_'){
			$len = strlen($postKey);
			$type = substr($postKey,5,($len-5));
			$codeNoInstall[$type] = addslashes($postValue);
		}
		if(substr($postKey,0,8) == 'display_'){
			$len = strlen($postKey);
			$type = substr($postKey,8,($len-8));
			$displayNoInstall[$type] = addslashes($postValue);
		}
	}

	if($importInstall != NULL){
		foreach($importInstall as $installType){
			if($activateInstall[$installType] == NULL){
				$activateInstall[$installType] = 'FALSE';
			}
			include($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$installType.'/code/install.php');
			$query = "INSERT INTO ".$tablePrefix."types VALUES ('$installType','".addslashes($_POST['installBucket_'.$installType])."','".addslashes($name)."','".addslashes($description)."','".addslashes($code)."','".addslashes($display)."','".$activateInstall[$installType]."')";
			$dbObject->query($query);
			$install = TRUE;
		}
	}

	if($importNoInstall != NULL){
		foreach($importNoInstall as $installType){
			if($codeNoInstall[$installType] == NULL){
				$codeNoInstall[$installType] = 'FALSE';
			}
			if($displayNoInstall[$installType] == NULL){
				$displayNoInstall[$installType] = 'FALSE';
			}
			if($activateNoInstall[$installType] == NULL){
				$activateNoInstall[$installType] = 'FALSE';
			}
			if($nameNoInstall[$installType] == NULL){
				$nameNoInstall[$installType] = 	$installType;
			}
			if($bucketNoInstall[$installType] == NULL){
				$bucket = 'NULL';
			} else {
				$bucket = "'".$bucketNoInstall[$installType]."'";
			}
			$query = "INSERT INTO ".$tablePrefix."types VALUES ('$installType',$bucket,'".$nameNoInstall[$installType]."','$descriptionNoInstall[$installType]','".$codeNoInstall[$installType]."','".$displayNoInstall[$installType]."','".$activateNoInstall[$installType]."')";	
			$dbObject->query($query);
			$install = TRUE;
		}
	}
}

if($install == TRUE){
	$_SESSION['gb_installSuccess'] = TRUE;
	header('location: typesImport.php');
	exit;	
}

$dir = $_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'; 

// Open a known directory, and proceed to read its contents 
if (is_dir($dir)) { 
   if ($dh = opendir($dir)) { 
       while (($file = readdir($dh)) !== false) { 
         if(filetype($dir . $file) == 'dir' & $file != '.' & $file != '..'){
         		// no whitespace in names
         		if(strstr($file," ") == FALSE){
         			// check for default image
         			if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$file.'/images/block.gif') == TRUE){
         				$blockFolders[] = $file;
         			}
         		}
         }
       } 
       closedir($dh); 
   } 
} 

$result=$dbObject->query('SELECT * FROM '.$tablePrefix.'types');
while($result->fetchInto($row)){
	$blockTypes[]=$row['type'];
}

foreach($blockFolders as $folder){
	if(($blockTypes == NULL) || in_array($folder,$blockTypes) == FALSE){
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$folder.'/code/install.php') == TRUE){
			$blockDataInstall[]=$folder;
		} else {
			$blockDataNoInstall[]=$folder;
			// check for code
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$setting['installPath'].'blocks/'.$folder.'/code/block.'.$folder.'.class.php') == TRUE){
				$blockDataNoInstallCode[$folder]=TRUE;
			}
		}
		$importsFound = TRUE;
	}
}

if($importsFound == TRUE){
	$gb_smarty->assign('bucketData',$statsObject->getBucket());
	$gb_smarty->assign('importsFound','PASS');
} else {
	$gb_smarty->assign('importsFound','FAIL');
}

$blockPath = 'http://'.$_SERVER['HTTP_HOST'].$setting['installPath'].'blocks/';
$gb_smarty->assign('blockPath',$blockPath);
$gb_smarty->assign('install',$installSuccess);
$gb_smarty->assign('blockDataInstall',$blockDataInstall);
$gb_smarty->assign('blockDataNoInstall',$blockDataNoInstall);
$gb_smarty->assign('blockDataNoInstallCode',$blockDataNoInstallCode);

$gb_smarty->display('layout/headerAdmin.tpl');
$gb_smarty->display("admin/typesImport.tpl");
$gb_smarty->display('layout/footerAdmin.tpl');
?>