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
	$photos = $album->getPhotos();

	$html = "";
	if (empty($photos)) {
		$html = "<i><b>This album is empty.</b></i>";	
	}
	else {
		$html = getPhotosHTML($photos);
	}
	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
