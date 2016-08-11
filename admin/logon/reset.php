<?php
include_once('../../config.php');

$resetTime = $dbObject->getOne("SELECT value FROM ".$tablePrefix."settings WHERE setting='resetTime'");
$resetString = $dbObject->getOne("SELECT value FROM ".$tablePrefix."settings WHERE setting='resetString'");

if($resetTime == NULL){
	$resetInTime = TRUE;
}else{
	if((strtotime ($resetTime)) < strtotime('now + 10 minutes')){
		$resetInTime = TRUE;
	}else{
		$resetInTime = FALSE;
	}
}

if($resetString == md5($_REQUEST['reset'])){
	$resetStringMatch = TRUE;
}

if($resetInTime == TRUE AND $resetStringMatch == TRUE){
	$adminEmail = $dbObject->getOne('SELECT value FROM '.$tablePrefix.'settings WHERE setting="adminEmail"');
	$installPath = $dbObject->getOne('SELECT value FROM '.$tablePrefix.'settings WHERE setting="installPath"');
	if($adminEmail != NULL){
		if($resetStringMatch == TRUE){
			srand((double)microtime()*1000000);
			$uniqueString = md5(rand(0,9999999));
			$username = substr($uniqueString,0,10);
			$uniqueString = md5(rand(0,9999999));
			$password = substr($uniqueString,0,10);

			$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="" WHERE setting="resetString"');
			$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="" WHERE setting="resetTime"');
			$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="'.md5($username).'" WHERE setting="username"');
			$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="'.md5($password).'" WHERE setting="password"');
			
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
			$headers .= "From: \"Guestblock\" <Guestblock@".$_SERVER['SERVER_NAME'].">\r\n";
		
			$content .= "Following a successful reset procedure your logon details have been changed to: \r\n";
			$content .= "Username: $username \r\n";
			$content .= "Password: $password \r\n\r\n";
			$content .= "You can now logon to your guestblock using these details \r\n";
			$content .= "http://".$_SERVER['HTTP_HOST'].$installPath."admin/logon/";
			mail($adminEmail,'admin username/password reset',$content,$headers);
		}
	}
	$gb_smarty->assign('reset','TRUE');
}
$css[] = '../../css/admin.css';
$gb_smarty->assign('css',$css);
$gb_smarty->assign('title','Guestblock Reset Logon');
$gb_smarty->display('layout/headerLogon.tpl');
$gb_smarty->display('admin/reset.tpl');
$gb_smarty->display('layout/footerLogon.tpl');