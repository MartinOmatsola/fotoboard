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
	$photo = Photo::createFromXML($_GET['pid']);
	$img = myImageCreate($_GET['src']);
	myImageSave($img, $photo->getSrc());
	print "<b><i>image saved successfully</i></b>";
} catch (Exception $e) {
	print $e->getMessage();
}
