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

	$photo = Photo::createFromXML($_GET['pid']);
	$tags = getTagsHTML($photo->getTags());
	$comments = getCommentsHTML($photo->getComments(), $photo->getId(), 0);

	$html .= '<img class="ad" src="bound.php?q='. $photo->getSrc() . '" /><br /><br />
		<div class="caps" id="caption">' . $photo->getCaption() . '</div><br />
		<input type="button" class="options" value="back" onClick="search(\'' . $_GET['uid'] . '\')" /><br /><br />
		<div class="tags" id="tag"><i><b>Tags: </b>' . $tags .'</i></div><br />

		<textarea class="updates" rows="3" cols="60" id="new_comment"></textarea><br />
		<input type="button" class="options" value="comment" onClick="addComment(\''. $photo->getId() . '\')" />
	 	
		</br /><br />
		
		<div id="comment_box">'. $comments . '</div>';
	
	//some hidden info to implement going back to search results
	$html.=	'<input type="hidden" id="term" value="' . $_GET['term'] . '" />
			<input type="hidden" id="scope" value="' . $_GET['scope'] . '" />';

	print $html;	
} catch (Exception $e) {
	print $e->getMessage();
}
?>
