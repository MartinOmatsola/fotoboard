<?php
require_once 'display.php'; 

try {
	//authenticate
	if (!$_COOKIE['uid']) {
		print sessionErrorMsg();
		exit;
	}	
	$uid = $_COOKIE['uid'];
	$session_key = $_COOKIE['session_key'];
	$user = User::createFromXML($uid);
	if ($user->getSessionKey() != $session_key) {
		print sessionErrorMsg();
		exit;
	}

	$user = User::createFromXML($_GET["uid"]);
	$user->setEmail($_GET["email"]);
	if (!$_GET['user']) {
		print getUserDetailsHTML($user);
	} else {
		setcookie('uid', $_GET["email"], time()+3600, '/');
	}
} catch (Exception $e) {
	print $e->getMessage();
}
?>
