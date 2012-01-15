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

	$photo = Photo::createFromXML($_GET['id']);

	//handle comma seperated case
	$tags = htmlspecialchars(stripslashes(trim($_GET['tag'])), ENT_QUOTES);
	$tag_array = explode(", ", $tags);
	foreach ($tag_array as $tag) {
		$photo->addTag($tag);
	}
	
	$tags = getTagsHTML($photo->getTags());
	print '<i><b>Tags: </b>' . $tags .'</i>';
} catch (Exception $e) {
	print $e->getMessage();
}

?>
