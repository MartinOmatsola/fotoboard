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
	$album = Album::createFromXML($photo->getAlbumId());
	
	//check if user owns photo
	if ($album->getOwnerId() == $uid) {

		$album->deletePhotoById($photo->getId());
		print '<i>Your photo was deleted successfully</i><br /><br />
	<input type="button" class="options" value="back to album" onClick="showPhotos(\'' . $photo->getAlbumId() . '\')" />';
	} else {
		print '<i><b>You cannot delete a photo from an album you do not own!</b></i><br />
			<input type="button" class="options" value="back to album" onClick="showPhotos(\'' . $photo->getAlbumId() . '\')" />';
	}	
} catch (Exception $e) {
	print $e->getMessage();
}

?>
