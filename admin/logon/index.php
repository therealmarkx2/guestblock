<?php
include_once('../../config.php');

$css[] = '../../css/admin.css';

if($_POST['logonAttempt'] == TRUE){
	// process logon attempt
	$usernameAttempt = md5(addslashes($_POST['username']));
	$passwordAttempt = md5(addslashes($_POST['password']));
	
	$username = $dbObject->getOne("SELECT value FROM ".$tablePrefix."settings WHERE setting='username'");
	$password = $dbObject->getOne("SELECT value FROM ".$tablePrefix."settings WHERE setting='password'");
	
	if(($username == $usernameAttempt) & ($password == $passwordAttempt)){
		// logon success
		$_SESSION['gb_admin'] = TRUE;
		switch($_REQUEST['action']){
			case NULL:{
				header('location: ../');
				exit;
				break;	
			}
			case 'moderation':{
				header('location: ../stacksModeration.php');
				exit;
				break;	
			}
		}
	} else {
		// logon failure
		$gb_smarty->assign('logon','FAIL');
	}
}

$adminEmail = $dbObject->getOne('SELECT value FROM '.$tablePrefix.'settings WHERE setting="adminEmail"');
if($adminEmail != NULL){
	if($_REQUEST['reset'] == 'TRUE'){
		$installPath = $dbObject->getOne('SELECT value FROM '.$tablePrefix.'settings WHERE setting="installPath"');

		srand((double)microtime()*1000000);
		$uniqueString = md5(rand(0,9999999));
		
		$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="'.md5($uniqueString).'" WHERE setting="resetString"');
		$dbObject->query('UPDATE '.$tablePrefix.'settings SET value="'.date('Y-m-d H:i').'" WHERE setting="resetTime"');
		
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$headers .= "From: \"Guestblock\" <Guestblock@".$_SERVER['SERVER_NAME'].">\r\n";
	
		$content = "A password reset request has been initiated from the following IP address: \r\n";
		$content .= "Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=".$_SERVER['REMOTE_ADDR']." \r\n\r\n";
		$content .= "To reset the username and password please visit this link: \r\n";
		$content .= "http://".$_SERVER['HTTP_HOST'].$installPath."admin/logon/reset.php?reset=".$uniqueString." \r\n\r\n";
		$content .= "You have 10 minutes before this request expires.";
		
		mail($adminEmail,'admin username/password reset',$content,$headers);
		
		$gb_smarty->assign('reset','TRUE');
	}
	$gb_smarty->assign('resetOn','TRUE');
}


$gb_smarty->assign('css',$css);
$gb_smarty->assign('title','Guestblock Logon');
$gb_smarty->display('layout/headerLogon.tpl');
$gb_smarty->display('admin/logon.tpl');
$gb_smarty->display('layout/footerLogon.tpl');
?>