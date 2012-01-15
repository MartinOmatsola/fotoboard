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

	$photo = Photo::createFromXML($_GET['id']);
	$img = myImageCreate($photo->getSrc());
	$caption = $photo->getCaption();
	if ($caption == '') {
		$caption = "none";
	}
	
	$parentAlbum = Album::createFromXML($photo->getAlbumId());
	
	$owner = User::createFromXML($parentAlbum->getOwnerId());
	$name = ($uid == $owner->getEmail()) ? "You" : $owner->getFname() . ' ' . $owner->getLname(); 

	$html = '<b>Caption:</b> <i>' . $caption . '</i>  &nbsp;<b>Uploaded by:</b> <i>' . $name .  '</i>  &nbsp;<b>Width:</b> <i>' . imagesx($img) . 'px</i>  &nbsp;<b>Height:</b> 
			<i>' . imagesy($img) . 'px</i>'; 
	
	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
