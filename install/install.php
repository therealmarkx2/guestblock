<?php
ini_set('error_reporting',2039);
ini_set('display_errors', 1);

// create session
session_start();

include('../config.php');
if($installDate != NULL){
	header('location: reinstall.php');
	exit;
}

// create instance of smarty
require_once($_SERVER['DOCUMENT_ROOT'].$_SESSION['gb_smartyPath']."Smarty.class.php");

$gb_smarty = new smarty();
$gb_smarty->template_dir = '../templates/source/';
$gb_smarty->compile_dir = '../templates/compiled/';

$action = $_REQUEST['action'];

$menu = array(
'smarty' => 'completed',
'files' => 'pending',
'database' => 'pending',
'settings' => 'pending',
'finish' => 'pending');

$gb_smarty->assign('title','guestblock installation');

switch($action){
	case 'files':{
		$menu['files'] = 'current';

		if(is_writable('../config.php')){
			$settingsWritable = 'TRUE'	;
		} else {
			$settingsWritable = 'FALSE'	;
		}
		$template = 'files';
		$gb_smarty->assign('settingsWritable',$settingsWritable);
		break;
	}
	case 'database':{
		if(!is_writable('../config.php')){
			header('location: install.php?action=files');
			exit;
		}
		$menu['files'] = 'completed';
		$menu['database'] = 'current';
		$template = 'database';
		break;
	}
	case 'databaseCheck':{
		if(!is_writable('../config.php')){
			header('location: install.php?action=files');
			exit;
		}
		$menu['files'] = 'completed';
		$menu['database'] = 'current';
		$template = 'databaseCheck';

		$pear = $_POST['pear'];

		$pearDebug = $pear;
		$gb_smarty->assign('pearDebug',$pearDebug);
		
		if($pear != NULL){
			ini_set("include_path", $_SERVER['DOCUMENT_ROOT'].$pear.";".ini_get("include_path"));
			if(!@include_once('DB.php')){
				restore_include_path();
				ini_set("include_path", $_SERVER['DOCUMENT_ROOT'].$pear.":".ini_get("include_path"));
				$sep = ':';
			} else {
				$sep = ';';
			}
		}

		// check for PEAR existence
		if(!@include_once('DB.php')){
			$gb_smarty->assign('pear','FAIL');
		} else {
			
			$dataUser = $_POST['username'];
			$dataPass = $_POST['password'];
			$dataHost = $_POST['host'];
			$database = $_POST['database'];
			$prefix = $_POST['prefix'];

			$_SESSION['gb_dataUser'] = $dataUser;
			$_SESSION['gb_dataPass'] = $dataPass;
			$_SESSION['gb_dataHost'] = $dataHost;
			$_SESSION['gb_database'] = $database;
			$_SESSION['gb_prefix'] = $prefix;
			$_SESSION['gb_sep'] = $sep;
			$_SESSION['gb_pearPath'] = $pear;
			
			$gb_smarty->assign('prefix',$prefix);

			// Data Source Name: This is the universal connection string
			$dsn = "mysql://$dataUser:$dataPass@$dataHost/$database";

			// DB::connect will return a PEAR DB object on success
			// or an PEAR DB Error object on error
			$dbObject = DB::connect($dsn);

			// With DB::isError you can differentiate between an error or
			// a valid connection.
			if (DB::isError($dbObject)) {
				$gb_smarty->assign('databaseConnect','FALSE');
				$gb_smarty->assign('databaseDebug',$dbObject->getDebugInfo());
			} else {
				// check database exists
				$result = $dbObject->query("SHOW TABLES FROM `$database`");
				if (DB::isError($result)) {
					$gb_smarty->assign('databaseConnect','FALSE');
					$gb_smarty->assign('databaseDebug',$result->getDebugInfo());

				} else {
					$gb_smarty->assign('databaseConnect','TRUE');
					// attempt to create tables
					$continue = TRUE;

					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."blocks;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."blocks (
					  blockid mediumint(10) unsigned NOT NULL auto_increment,
					  type varchar(50) NOT NULL default '',
					  stackid smallint(10) unsigned NOT NULL default '0',
					  name varchar(32) default NULL,
					  message varchar(100) default NULL,
					  time time NOT NULL default '00:00:00',
					  email varchar(255) default NULL,
					  url varchar(255) default NULL,
					  ip varchar(15) NOT NULL default '',
					  horizontalpos tinyint(1) unsigned default NULL,
					  verticalpos tinyint(1) unsigned default NULL,
					  approved enum('TRUE','FALSE') NOT NULL default 'TRUE',
					  selected enum('SELECTED','RANDOM','OVERRIDE') NOT NULL default 'SELECTED',
					  PRIMARY KEY  (blockid),
					  KEY type (type),
					  KEY stack (stackid),
					  KEY name (name(10))
					) ENGINE=MyISAM;");
					
					if (DB::isError($result)) {
						$tableBlocks['create'] = 'FAIL';
						$tableBlocks['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableBlocks['create'] = 'PASS';
					}

					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."blocks_data;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."blocks_data (
					  blockid mediumint(7) unsigned NOT NULL,
					  setting varchar(50) NOT NULL,
					  value text,
					  PRIMARY KEY  (blockid,setting),
					  KEY blockid (blockid)
					) ENGINE=MyISAM;");
					
					if (DB::isError($result)) {
						$tableBlocksData['create'] = 'FAIL';
						$tableBlocksData['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableBlocksData['create'] = 'PASS';
					}
					
					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."buckets;");
					$dbObject->query("CREATE TABLE ".$prefix."buckets (
					  bucket varchar(50) NOT NULL,
					  name varchar(100) NOT NULL,
					  description varchar(255) default NULL,
					  PRIMARY KEY  (bucket)
					) ENGINE=MyISAM;");

					if (DB::isError($result)) {
						$tableBuckets['create'] = 'FAIL';
						$tableBuckets['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableBuckets['create'] = 'PASS';
					}
					
					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."flood;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."flood (
					  floodid smallint(10) NOT NULL auto_increment,
					  ip varchar(15) NOT NULL default '',
					  timestamp datetime default NULL,
					  PRIMARY KEY  (floodid)
					) ENGINE=MyISAM;");

					if (DB::isError($result)) {
						$tableFlood['create'] = 'FAIL';
						$tableFlood['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableFlood['create'] = 'PASS';
					}

					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."settings;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."settings (
					  setting varchar(50) NOT NULL default '0',
					  value text,
					  PRIMARY KEY  (setting)
					) ENGINE=MyISAM;");

					if (DB::isError($result)) {
						$tableSettings['create'] = 'FAIL';
						$tableSettings['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableSettings['create'] = 'PASS';
					}
					
					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."stacks;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."stacks (
					  stackid smallint(10) unsigned NOT NULL auto_increment,
					  date date NOT NULL default '0000-00-00',
					  PRIMARY KEY  (stackid)
					) ENGINE=MyISAM;");

					if (DB::isError($result)) {
						$tableStacks['create'] = 'FAIL';
						$tableStacks['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableStacks['create'] = 'PASS';
					}

					$dbObject->query("DROP TABLE IF EXISTS ".$prefix."types;");
					$result = $dbObject->query("CREATE TABLE ".$prefix."types (
					  type varchar(50) NOT NULL default '',
					  bucket varchar(50),
					  name varchar(100) default NULL,
					  description varchar(255) default NULL,
					  code enum('TRUE','FALSE') NOT NULL default 'FALSE',
					  display enum('TRUE','FALSE') NOT NULL default 'TRUE',
					  active enum('TRUE','FALSE') NOT NULL default 'TRUE',
					  PRIMARY KEY  (type),
					  KEY bucket (bucket)
					) ENGINE=MyISAM;");

					if (DB::isError($result)) {
						$tableTypes['create'] = 'FAIL';
						$tableTypes['debug'] = $result->getDebugInfo();
						$continue = FALSE;
					} else {
						$tableTypes['create'] = 'PASS';
					}

					$gb_smarty->assign('tableBlocks',$tableBlocks);
					$gb_smarty->assign('tableBlocksData',$tableBlocksData);
					$gb_smarty->assign('tableFlood',$tableFlood);
					$gb_smarty->assign('tableBuckets',$tableBuckets);
					$gb_smarty->assign('tableSettings',$tableSettings);
					$gb_smarty->assign('tableStacks',$tableStacks);
					$gb_smarty->assign('tableTypes',$tableTypes);

					$gb_smarty->assign('continue',$continue);
				}
			}
		}
		break;
	}
	case 'settings':{
		if(!is_writable('../config.php')){
			header('location: install.php?action=files');
			exit;
		}
		$menu['files'] = 'completed';
		$menu['database'] = 'completed';
		$menu['settings'] = 'current';
		$template = 'settings';
		break;
	}
	case 'settingsSave':{
		$menu['files'] = 'completed';
		$menu['database'] = 'completed';
		$menu['settings'] = 'completed';
		$menu['finish'] = 'current';
		$template = 'settingsSave';

		$path = $_POST['path'];
		$root = '$_SERVER["DOCUMENT_ROOT"]';
		$settingsfile = 
"<?php
// for xhtml compliance remove PEPSESSID from links
ini_set('session.use_trans_sid', 0);
ini_set('url_rewriter.tags', 0);

// for xhtml compliance amend arg_separator
ini_set('arg_separator.output', '&amp;');

// remove magic quotes
if(get_magic_quotes_gpc()||get_magic_quotes_runtime())
{
    foreach(\$_GET as \$k=>\$v) \$_GET[\"\$k\"]=stripslashes(\$v);
	foreach(\$_POST as \$k=>\$v) \$_POST[\"\$k\"]=stripslashes(\$v);
	foreach(\$_COOKIE as \$k=>\$v) \$_COOKIE[\"\$k\"]=stripslashes(\$v);
}

// create session
session_start();

// PEAR setup
";
		if($_SESSION['gb_pearPath'] == NULL){
			$settingsfile .= '// using PHP PEAR distribution';
"";
		} else {
			$settingsfile .= 
"ini_set('include_path',".$root.".'".$_SESSION['gb_pearPath'].$_SESSION['gb_sep']."'.ini_get('include_path'));";
		}
		$settingsfile .= 
"

// create instance of smarty
require_once(".$root.".'".$_SESSION['gb_smartyPath']."Smarty.class.php');
\$gb_smarty = new smarty();
\$gb_smarty->template_dir = ".$root.".'".$path."templates/source/';
\$gb_smarty->compile_dir = ".$root.".'".$path."templates/compiled/';

// MySQL setup
\$username = '".$_SESSION['gb_dataUser']."';
\$password = '".$_SESSION['gb_dataPass']."';
\$host = '".$_SESSION['gb_dataHost']."';
\$database = '".$_SESSION['gb_database']."';

require_once('DB.php');

// Data Source Name: This is the universal connection string
\$dsn = \"mysql://\$username:\$password@\$host/\$database\";

// DB::connect will return a PEAR DB object on success
// or an PEAR DB Error object on error
\$dbObject = DB::connect(\$dsn);

if (DB::isError(\$dbObject)) {
	echo \$dbObject->getMessage();
	exit;
}
\$dbObject->setFetchMode(DB_FETCHMODE_ASSOC);

// table prefix
\$tablePrefix = '".$_SESSION['gb_prefix']."';

// generate the random seed
srand((double)microtime()*1000000);

// installation tag
\$installDate = '".date('Y-m-d H:i')."'
?>";
		
		$fp = @fopen('../config.php', 'w');
		if($fp == FALSE){
			$gb_smarty->assign('settingsSave','FAIL');
		} else {
			fwrite($fp, $settingsfile);
			fclose($fp);
			$gb_smarty->assign('settingsSave','PASS');
		}
		
		// generate username, password and cookie hash
		$uniqueString = md5(rand(0,9999999));
		$username = substr($uniqueString,0,10);
		$uniqueString = md5(rand(0,9999999));
		$password = substr($uniqueString,0,10);
		$uniqueString = md5(rand(0,9999999));
		$cookie = substr($uniqueString,0,10);
		
		if($_SESSION['gb_pearPath'] != NULL){
			ini_set("include_path",$_SERVER['DOCUMENT_ROOT'].$_SESSION['gb_pearPath'].$_SESSION['gb_sep'].ini_get("include_path"));
		} else {
			// attempting to use default PEAR distribution
		}

		require_once('DB.php');
		
		$dataUser = $_SESSION['gb_dataUser'];
		$dataPass = $_SESSION['gb_dataPass'];
		$dataHost = $_SESSION['gb_dataHost'];
		$database = $_SESSION['gb_database'];
		$prefix = $_SESSION['gb_prefix'];
					
		// Data Source Name: This is the universal connection string
		$dsn = "mysql://$dataUser:$dataPass@$dataHost/$database";

		// DB::connect will return a PEAR DB object on success
		// or an PEAR DB Error object on error
		$dbObject = DB::connect($dsn);
		
		$gb_smarty->assign('username',$username);
		$gb_smarty->assign('password',$password);
		$username = md5($username);
		$password = md5($password);
		
		$dbObject->query('TRUNCATE '.$prefix.'settings');
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('version', '0.6.1')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('username', '$username')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('password', '$password')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('cookie', '$cookie')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('floodControl', '60')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('emailNotification', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('spamNotification', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('spamWords', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('badWords', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('span', '3')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('allowEmail', 'TRUE')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('allowUrl', 'TRUE')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('installPath', '".$path."')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('approveAll', 'FALSE')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('timeZone', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('hidePopup', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('disableUrl', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('overrideUrl', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('resetString', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('resetTime', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('spamIpCapture', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('blockSelectTable', NULL)");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('spanLimiter', '20')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('drawEmptyStacks', 'TRUE')");
		$dbObject->query("INSERT into ".$prefix."settings (setting,value) VALUES ('adminEmail', '".addslashes($_POST['adminEmail'])."')");
		break;
	}
}

$gb_smarty->assign('menu',$menu);

// the css to use
$gb_smarty->assign('css','../css/admin.css');

$gb_smarty->display('layout/headerInstall.tpl');
$gb_smarty->display('install/'.$template.'.tpl');
$gb_smarty->display('layout/footerInstall.tpl');
?>