<?php
include_once 'display.php'; 

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

	if ($_GET['term']) {
		print(getUsersHTML(array(User::createFromXML($_GET['term']))));
	}

} catch (Exception $e) {
	print "User with email <i>{$_GET['term']}</i> not found!";
}
?>
