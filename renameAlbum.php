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
	$album = Album::createFromXML($_GET['id']);
	$name = htmlspecialchars(stripslashes(trim($_GET['name'])), ENT_QUOTES);
	$album->setName($name);

	include "manageAlbums.php";
	
} catch (Exception $e) {
	print $e->getMessage();
}
?>
