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

	$comments = getCommentsHTML($photo->getComments(), $photo->getId());
	
	$album = Album::createFromXML($photo->getAlbumId());
	$photos = $album->getPhotos();
	
	
	$html = getPhotoHeaderHTML($photos);

	$html .= '<img class="ad" src="bound.php?q='. $photo->getSrc() . '" /><br /><br />

		<div class="caps" id="caption">' . $photo->getCaption() . '</div><br />		

		<span>
			<input type="button" class="options" value="caption" onClick="inputBox(\'' . $photo->getId() . '\', \'caption\')" title="change the caption" />
			<input type="button" class="options" value="tag" onClick="inputBox(\'' . $photo->getId() . '\', \'tag\')" title="add a tag" />
			<input type="button" class="options" value="studio" onClick="showStudio(\'' . $photo->getId() . '\')" title="add visual effects" />
			<input type="button" class="options" value="manage" onClick="managePhoto(\'' . $photo->getId() . '\')" title="delete tags and comments" />
			<input type="button" class="options" value="delete" onClick="deletePhoto(\'' . $photo->getId() . '\')" title="delete this photo, irreversible!" />
			<input type="button" class="options" value="album" onClick="showPhotos(\'' . $photo->getAlbumId() . '\')" title="back to album" />
		</span><br /><br />

		<div class="tags" id="tag"><i><b>Tags: </b>' . $tags .'</i></div>
	
		<br />
		
		<textarea class="updates" rows="3" cols="60" id="new_comment"></textarea><br />
		<input type="button" class="options" value="comment" onClick="addComment(\''. $photo->getId() . '\')" />
	 	
		</br /><br />
		
		<div id="comment_box">'. $comments . '</div>';

	print $html;
	
	unset($photos);
	unset($tag_array);
} catch (Exception $e) {
	print $e->getMessage();
}
?>
