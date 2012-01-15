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
	
	if ($_GET['admin']) {
		echo 'New password: <input type="password" id="passwd" class="updates" />
						<input type="button" class="options" value="update" onclick="javascript:adminSetPassword()" />';
	} else {
		print getUserDetailsHTML(User::createFromXML($_GET["uid"]));
	}
} catch (Exception $e) {
	print $e->getMessage();
}
?>
