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

	
	
	if ($uid == 'admin' && !$_GET['uid']) {
		$user = User::createFromXML($uid);
		$user->setPassword($_GET["password"]);	
		echo '<i><b>Admin password reset successfully</b></i>';
	} else {
		$user = User::createFromXML($_GET["uid"]);
		$user->setPassword($_GET["password"]);
		if (!$_GET['user']) {
			print getUserDetailsHTML($user);
		}
	}
	
} catch (Exception $e) {
	print $e->getMessage();
}
?>
