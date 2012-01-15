<?php
include_once('display.php');

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
	$owner = User::createFromXML($album->getOwnerId());
	$ownerName = ($uid == $owner->getEmail()) ? "You" : $owner->getFname() . " " . $owner->getLname();

	$html = '<b>Name:</b> <i>' . $album->getName() . '</i>  &nbsp;<b>Created:</b> <i>' . $album->getDate() . 
			'</i>  &nbsp;<b>Images:</b> <i>' . $album->getSize() . '</i>  &nbsp;<b>Created by:</b> <i>' . /*$album->getMdate()*/$ownerName . '</i>'; 

	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
