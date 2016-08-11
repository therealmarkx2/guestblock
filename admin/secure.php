<?php
// get the source
$path=explode('/',$_SERVER['REQUEST_URI']);
$source=$path[(sizeof($path)-1)];

switch($source){
	case 'stacksModeration.php':{
		$action = '?action=moderation';	
	}	
}

if($_SESSION['gb_admin'] == NULL){
	header('location: logon/'.$action);	
	exit;
}
?>