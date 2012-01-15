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

	Manager::createUser($_GET["fname"], $_GET["lname"], $_GET["uid"], $_GET["password"]);
	
} catch (Exception $e) {
	print $e->getMessage();
}
?>