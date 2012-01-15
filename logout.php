<?php
include_once 'display.php';

try {
	$uid = $_COOKIE['uid'];
	$session_key = $_COOKIE['session_key'];
	$user = User::createFromXML($uid);
	$user->setSessionKey("");
	header("Location: .");
} catch (Exception $e) {
	header("Location: .");
}
?>
