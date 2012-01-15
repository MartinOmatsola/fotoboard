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
	$html = '<br />
	<img class="ad" src="rounder.php?q=' . $photo->getSrc() . '" border="0" title="' . $photo->getCaption() . '" onClick="showPhoto(\'' . $photo->getId() . '\')" />
	<br /><br />Click on a tag to delete it<br /><br />';
	
	$html.= '<div id="mtag" class="com">' . getManageTagsHTML($photo->getTags(), $photo->getId()) . '</div><br /><br />';
	$html.= 'Comments<br /><br /><div id="comment_box">' . getCommentsHTML($photo->getComments(), $photo->getId()) . '</div>
			<div id="new_comment"></div>'; // for compatibility with deleteComment.php 
	print $html;	
} catch (Exception $e) {
	print $e->getMessage();
}
?>
