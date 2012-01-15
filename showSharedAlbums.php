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

	$albums = $user->getSharedAlbums();
	print getAlbumsHTML($albums);
} catch (Exception $e) {
	print $e->getMessage();
}

?>
