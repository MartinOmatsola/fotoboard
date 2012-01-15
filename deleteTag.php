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

	$photo = Photo::createFromXML($_GET['id']);

	//handle comma seperated case
	$tag = htmlspecialchars(stripslashes(trim($_GET['tag'])), ENT_QUOTES);
	$photo->deleteTag($tag);
	
	print getManageTagsHTML($photo->getTags(), $photo->getId());
} catch (Exception $e) {
	print $e->getMessage();
}

?>
