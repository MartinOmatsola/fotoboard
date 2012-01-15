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

	$album = Album::createFromXML($_GET['aid']);
	$album->makeShared();
	
	$contrib = User::createFromXML($_GET['email']);
	
	if ($contrib->getEmail() == $user->getEmail()) {
		print "<i><b>You cannot share an album with yourself!</b></i>";
	}
	elseif (!in_array($album, $contrib->getSharedAlbums())) {
	
		//add this album to user's shared albums
		$user->addSharedAlbum($album);
	
		//add member to album
		$album->addMember($_GET['email']);
	
		//add this album to contributor's shared albums
		$contrib->addSharedAlbum($album);
		
		print "<i>the album <b>{$album->getName()}</b> is now shared with <b>{$contrib->getFname()} {$contrib->getLname()}</b></i>";
	}
	else {
		print "<i>the album <b>{$album->getName()}</b> is already shared with <b>{$contrib->getFname()} {$contrib->getLname()}</b></i>";
	}
} catch (Exception $e) {
	print $e->getMessage();
}

?>
