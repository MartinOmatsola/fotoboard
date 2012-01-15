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
	$user->deleteAlbumById($album->getId());
	print getManageAlbumsHTML($user->getAlbums());
} catch (Exception $e) {
	print $e->getMessage();
}
?>
