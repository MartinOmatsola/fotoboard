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

	$html = '<b>Caption:</b> <i>' . $caption . '</i>  &nbsp;<b>Uploaded:</b> <i>' . $photo->getDate() . '</i>  &nbsp;<b>Width:</b> <i>' . imagesx($img) . 'px</i>  &nbsp;<b>Height:</b> <i>' . imagesy($img) . 'px</i>'; 
	
	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
