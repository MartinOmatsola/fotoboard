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

	$photo = Photo::createFromXML($_GET['id']);

	$comment = htmlspecialchars(stripslashes(trim($_GET['text'])), ENT_QUOTES);
	if ($comment) {
		$photo->addComment($comment, $user->getFname() . " " . $user->getLname(), date('l jS \of F Y h:i A'));
	}	
	print getCommentsHTML($photo->getComments(), $photo->getId());
} catch (Exception $e) {
	print $e->getMessage();
}

?>
