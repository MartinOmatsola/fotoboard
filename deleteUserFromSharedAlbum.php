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
	$album = Album::createFromXML($_GET["aid"]);
	
	//only the owner can delete peeps from album
	if ($album->getOwnerId() == $uid) {
		$user->removeSharedAlbumById($_GET["aid"]);
	}
	include  "manageAlbums.php";
	
} catch (Exception $e) {
	print $e->getMessage();
}
?>