<?php
require_once 'Manager.php';
require_once 'fotostudio.php';


function getMainHeaderHTML() {
	return '
	<html>
	<head>
	<link rel="stylesheet" type="text/css" id="style1" href="media/style.css">
	<title>F O T O B O A R D</title>
	<script type="text/javascript" src="fotoboard.js">
	</script>
	</head>
	<body style="background-color:#EEEEEE">
	<center><div class="super_container">
	<div class="ctop"><div class="header">FOTOBOARD</div></div>
	<div id="container" class="box">';
}

function getLoginHTML() {
	return <<<END_BLOCK
	<br/><br/><br/><br/><br/><br/>	
	<table>
		<tr>
			<td align="right" class="inf"><b>email:</b> </td><td><input type="text" id="uid" class="updates" /></td>
		<tr>
		<tr>	
			<td align="right" class="inf"><b>password:</b> </td><td><input type="password" id="passwd" class="updates" /></td>
		<tr>
			<td align="center" colspan="2"><input type="button" class="options" value="login" onClick="login()" /></td>
		</tr>
	</table>
	<br /><br /><br /><br />

END_BLOCK;
}

function getMainFooterHTML() {
	return '</div><br /><br /><div class="cbottom"></div>
	</div><div class="footer">powered by <a href="http://fotocrib.com">fotocrib.com</a></div>
	</center>
	</body>
	</html>';
}

function getOptionsHTML() {
	return '<span style="border-bottom:solid;border-width:thin;border-color:#CFCFE7;background-color:#CCCCCC;">&nbsp;<img src="media/icons/blue_chartlink.png" class="ad" onClick="showAlbums()" onMouseOver="showHint(1)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_gallerylink.png" class="ad" onClick="showSharedAlbums()" onMouseOver="showHint(6)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_imagelink.png" class="ad" onClick="createAlbum(\'\')" onMouseOver="showHint(2)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_config.png" class="ad" onClick="manageAlbums()" onMouseOver="showHint(3)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_view.png" class="ad" onClick="inputBox(\'\', \'search\')" onMouseOver="showHint(5)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;</span>';
}

function getPublicOptionsHTML($uid) {
	return '<span>
		<img src="media/icons/blue_chartlink.png" class="ad" onClick="showPublicAlbums(\'' . $uid . '\')" />
	</span>';
}

function sessionErrorMsg() {
	return "<b>Your session is invalid! Please login again.<b>";
}

/*
 * Returns html that containing 50px by 50px thumbnails display format for photos
 * @param $photos array containing Photo objects to be displayed
 */
function getPhotoHeaderHTML($photos) {
	$html = '<div style="width:510px;height:60px;overflow:auto"><center>';
	foreach ($photos as $img) {
		$html.= '<a href="javascript:showPhoto(\'' . $img->getId() . '\')"><img class="ad" width="50" height="50" src="rounder.php?q=' . $img->getSrc() . '" border="0" title="' . $img->getCaption() . '" /></a> &nbsp;&nbsp;';
	}
	$html.= '</center></div><br />';
	return $html;
}

function getPublicPhotoHeaderHTML($photos) {
	$html = '<div style="width:510px;height:60px;overflow:auto"><center>';
	foreach ($photos as $img) {
		$html.= '<a href="javascript:showPublicPhoto(\'' . $img->getId() . '\')"><img class="ad" width="50" height="50" src="rounder.php?q=' . $img->getSrc() . '" border="0" title="' . $img->getCaption() . '" /></a> &nbsp;&nbsp;';
	}
	$html.= '</center></div><br />';
	return $html;
}

/*
 * Returns html containing rounded thumbnails display format for photos
 * @param $photos array containing Photo objects to be displayed
 */
function getPhotosHTML($photos) {
	if (empty($photos)) {
		return '<i><b>This album is empty.</b></i>';
	}
	
	$html = "";
	foreach ($photos as $photo) {
			$img = myImageCreate($photo->getSrc());
			$html.= '<span style="padding:20px;"><a href="javascript:showPhoto(\'' . $photo->getId() . '\')"><img class="ad" src="rounder.php?q=' . $photo->getSrc() . '" border="0" title="' . $photo->getCaption() . ' : ' . imagesx($img) . 'px &times; '. imagesy($img) .'px' . '" onMouseOver="javascript:showPhotoInfo(\'' . $photo->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a></span>';
		}
	
	return $html;
}

/*
 * Returns html containing rounded thumbnails display format for photos
 * @param $photos array containing Photo objects to be displayed
 */
function getPublicPhotosHTML($photos) {
	if (empty($photos)) {
		return '<i><b>This album is empty.</b></i>';
	}
	$html = "";
	foreach ($photos as $photo) {
			$img = myImageCreate($photo->getSrc());
			$html.= '<span style="padding:20px;"><a href="javascript:showPublicPhoto(\'' . $photo->getId() . '\')"><img class="ad" src="rounder.php?q=' . $photo->getSrc() . '" border="0" title="' . $photo->getCaption() . ' : ' . imagesx($img) . 'px &times; '. imagesy($img) .'px' . '" onMouseOver="javascript:showPhotoInfo(\'' . $photo->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a></span>';
		}
	return $html;
}

/*
 * Returns html containing rounded thumbnails display format for album covers
 * @param $photos array containing Photo objects to be displayed
 */
function getAlbumsHTML($albums) {
	if (empty($albums)) {
		return '<i><b>Empty</b></i>';
	}
	$html = "";
	foreach ($albums as $album) {
	$html.= '<span style="padding:20px;"><a href="javascript:showPhotos(\'' . $album->getId() . '\')"><img class="ad" src="rounder.php?q=' . $album->getCover() . '" border="0" title="' . $album->getName() . '" onMouseOver="javascript:showAlbumInfo(\'' . $album->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a></span>';
	}
	return $html;
}

/*
 * Returns html containing rounded thumbnails display format for album covers, public view
 * @param $photos array containing Photo objects to be displayed
 */
function getPublicAlbumsHTML($albums, $fname) {
	if (empty($albums)) {
		return '<i><b>' . $fname . ' does not have any albums.</b></i>';
	}
	$html = "";
	foreach ($albums as $album) {
	$html.= '<span style="padding:20px;"><a href="javascript:showPublicPhotos(\'' . $album->getId() . '\')"><img class="ad" src="rounder.php?q=' . $album->getCover() . '" border="0" title="' . $album->getName() . '" onMouseOver="javascript:showAlbumInfo(\'' . $album->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a></span>';
	}
	return $html;
}

/*
 * Returns html containing rounded thumbnails display format for search results
 * @param $photo_ids array containing ids of Photo objects to be displayed
 */
function getSearchPhotosHTML($photo_ids) {
	$html = "";
	foreach ($photo_ids as $photo_id) {
		$photo = Photo::createFromXML($photo_id);
		$img = myImageCreate($photo->getSrc());
		$html.=  '<span style="padding:20px;"><a href="javascript:showSearchPhoto(\'' . $photo->getId() . '\', \'' . $_GET['id'] . '\', \'' . $_GET['term'] . '\', \'' . $_GET['scope'] . '\')"><img class="ad" src="rounder.php?q=' . $photo->getSrc() . '" border="0"title="' . $photo->getCaption() . ' : ' . imagesx($img) . 'px &times; '. imagesy($img) .'px' . '" onMouseOver="javascript:showSearchInfo(\'' . $photo->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a></span">';
	}
	return $html;
}

/*
 * Returns html containing comments
 * @param $comment_array nested array containing comment info
 * @param int $pid the pid of the photo
 * @param int $delete determines if delete button should be shown
 */
function getCommentsHTML($comment_array, $pid, $delete=1) {
	//show latest posts first
	$comment_array = array_reverse($comment_array);
	$comments ="";
	foreach ($comment_array as $comment) {
		$comments.= '<div class="com"><b>'.
						$comment['posted_by'] . ' wrote on ' . $comment['date'] . ':</b> <br />' .
						'<i>' . $comment['comment'] . '</i> 
					</div>';
		if ($delete) {		
			$comments.='<input type="button" class="updates" value="delete" onClick="deleteComment(\'' . $comment['id'] . '\', \''. $pid . '\')" /><br /><br />';
		}	
		$comments .= '<br />';
	}
	return $comments;
}

/**
 * Returns html containing the manage albums view
 * @param array $albums contains album ids
 * @param int $flag != 0 => we are displaying shared albums
 */
function getManageAlbumsHTML($albums, $share=1) {
	/*
	$owner;
	$name;
	if (!empty($albums)) {
		$album = $albums[0];
		$owner = User::createFromXML($album->getOwnerId());
		$name = "{$owner->getFname()} {$owner->getLname()}";
	}
	*/
	$html = "";
	foreach ($albums as $album) {
		$owner = User::createFromXML($album->getOwnerId());
		$name = "{$owner->getFname()} {$owner->getLname()}";

		$html.= '<table border="0" width="600" style="border-color:#CFCFE7;border-style:solid;border-width:thin" cellspacing="5" cellpadding="5">
					<tr>
						<td align="center">
							<table border="0">
								<tr>
									<td>
										<a href="javascript:showPhotos(\'' . $album->getId() . '\')"><img class="ad" src="rounder.php?q=' . $album->getCover() . '" border="0" title="' . $album->getName() . '" onMouseOver="javascript:showAlbumInfo(\'' . $album->getId() . '\')" onMouseOut="javascript:document.getElementById(\'info\').innerHTML=\'\'" /></a>
									</td>
								</tr>
							</table>
						</td>
						<td align="left">
							<table style="font-size:12px;color:#444444" border="0">
								<tr><td><b>Name: </b><i>' . $album->getName() . '</i></td></tr>
								<tr><td><b>Images: </b><i>' . $album->getSize() . '</i></td></tr>
								<tr><td><b>Owner: </b><i>' . $name . '</i></td></tr>';
		
		if ($album->isShared()) {
			$memberIds = $album->getMemberIds();
			$aid = $album->getId();
			$html .= '<tr><td><b>Members: </b><span><i>';
			if ($memberIds) {	
				foreach ($memberIds as $id) {
					$member = User::createFromXML($id);
					$html .=<<<END
		<a href="javascript:deleteUserFromSharedAlbum('{$id}', '{$aid}')" title="click to remove from album">{$member->getFname()} {$member->getLname()}</a>, 
END;
				}
			}
			else {
				$html .= 'None';
			}
			
			$index = strrpos($html, ",");
			$html = ($index) ? substr($html, 0, $index) : $html;
			$html .= '</i></span></td></tr>';
		}
	
						$html .= '	<tr><td><b>Created: </b><i>' . $album->getDate() . '</i></td></tr>
								<tr><td><b>Modified: </b><i>' . $album->getMdate() . '</i></td></tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" colspan="2"><div id="' . $album->getId() . '"></div></td>
					</tr>
				</table>
				
				<span>' .
	'<input class="updates" type="button" onClick="createAlbum(\''. $album->getId() . '\')" value="upload" title="upload new photos" /> ' .
	
	'<input class="updates" type="button" value="delete" onClick="deleteAlbum(\'' . $album->getId() . '\')" title="delete this album, irreversible!" /> ';
		

		if ($share) {	
			
			if ($album->isShared()) {
				$html .= '<input class="updates" type="button" onClick="unshareAlbum(\'' . $album->getId() . '\')" value="unshare" title="unshare this album" /> ';
			}
				
			$html .= '<input class="updates" type="button" onClick="inputBox(\'share\', \'' . $album->getId() . '\')" value="share" title="share this album" /> ';
			
	$html.= '<input class="updates" type="button" value="rename" title="got a cooler name for this album?" onClick="inputBox(\'' . $album->getId() . '\', \'' . $album->getId() . '\')" /> ';
		}		
		$html .= '</span><br /><br />';
	}
	
	return $html;
}

/*
 * Returns html containing the tags
 * @param array $tag_array the array containing tags
 */
function getTagsHTML($tag_array) {
	if (empty($tag_array)) {
		return "none";
	}
	$html ="";
	foreach ($tag_array as $tag) {
		$html.= '<a href="javascript:tagSearch(\'' . $tag . '\')">' . $tag . '</a>, ';
	}
	return substr($html, 0, strrpos($html, ","));
}

function getPublicTagsHTML($tag_array) {
	if (empty($tag_array)) {
		return "none";
	}
	$html ="";
	foreach ($tag_array as $tag) {
		$html.= $tag . ', ';
	}
	return substr($html, 0, strrpos($html, ","));
}

/*
 * Returns html containing the tags with delete links
 * @param array $tag_array the array containing tags
 */
function getManageTagsHTML($tag_array, $photo_id) {
	if (empty($tag_array)) {
		return '<b>Tags</b>: <i>none</i>';
	}
	$html = '<b>Tags: </b><i>';
	foreach ($tag_array as $tag) {
		$html.= '<a href="javascript:deleteTag(\'' . $photo_id . '\', \'' . $tag . '\')">' . $tag . '</a>, ';
	}
	return substr($html, 0, strrpos($html, ",")) . '</i>';
}

function getPhotoOptionsHTML($photo) {
	return '		
		<span>
			<input type="button" class="options" value="caption" onClick="inputBox(\'' . $photo->getId() . '\', \'caption\')" title="change the caption" />
			<input type="button" class="options" value="tag" onClick="inputBox(\'' . $photo->getId() . '\', \'tag\')" title="add a tag" />
			<input type="button" class="options" value="studio" onClick="showStudio(\'' . $photo->getId() . '\')" title="add visual effects" />
			<input type="button" class="options" value="manage" onClick="managePhoto(\'' . $photo->getId() . '\')" title="delete tags and comments" />
			<input type="button" class="options" value="delete" onClick="deletePhoto(\'' . $photo->getId() . '\')" title="delete this photo, irreversible!" />
			<input type="button" class="options" value="email" onClick="inputBox(\'' . $photo->getId() . '\', \'send\')" title="send this photo to a friend" />
			<input type="button" class="options" value="album" onClick="showPhotos(\'' . $photo->getAlbumId() . '\')" title="back to album" />
		</span><br /><br />';
}

function getAdminOptionsHTML() {
	return '		
		&nbsp;<span style="border-bottom:solid;border-width:thin;border-color:#CFCFE7;background-color:#CCCCCC;">&nbsp;<img src="media/icons/blue_info.png" class="ad" onClick="window.location=\'.\'" onMouseOver="showHint(7)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_view.png" class="ad" onClick="inputBox(\'search_user\', \'\')" onMouseOver="showHint(8)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;<img src="media/icons/blue_config.png" class="ad" onClick="adminShowPassword()" onMouseOver="showHint(9)" onMouseOut="javascript:document.getElementById(\'hint\').innerHTML=\'\'" />&nbsp;</span>';
}

function getAdminHTML() {
	$users = Manager::getUserCount();
	$albums = Manager::getAlbumCount();
	$photos = Manager::getPhotoCount();
	
	return <<<END
<table cellpadding='5' style='color:#444444;width:100px;'>
	<tr>
		<td>Users:</td>
		<td>{$users}</td>
		<td>
			<img class="ad" title="users" src="media/icons/report_user.png" onclick="javascript:adminShowUsers()" />
		</td> 
		<td>
			<img class="ad" title="add user" src="media/icons/user_add.png" onclick="javascript:adminShowNewUserDetails()"/>
		</td>
	</tr>
	<tr>
		<td>Albums:</td>
		<td>{$albums}</td>
		<td>
			<img class="ad" title="albums" src="media/icons/folder_picture.png" onclick="javascript:adminShowAlbums()"></a>
		</td>
		<td></td>
	</tr>
	<tr>
		<td>Photos:</td>
		<td>{$photos}</td>		
		<td><img class="ad" title="photos" src="media/icons/pictures.png" onclick="javascript:adminShowPhotos()"></a></td>
		</td>
	</tr>
</table>
END;
}

function getUsersHTML($users) {
	$html = "<table cellpadding='5' style='color:#444444;width:600px;'>";
	$count = 0;
	if (is_array($users)) {
		foreach ($users as $user) {
			$name = $user->getFname() . " " . $user->getLname();
			$id = $user->getEmail();
			
			if ($count == 0) {
				$html.='<tr>';
			}
			$count++;			

			$html .=<<<END
		<td align="center">
			<img class="ad" title="browse" src="media/icons/folder_user.png" onclick="javascript:adminShowUserAlbums('{$id}')" />
			<img class="ad" title="edit" src="media/icons/blue_edit.png" onclick="javascript:adminShowUserDetails('{$id}')" />
			<img class="ad" title="delete" src="media/icons/blue_delete.png" onclick="javascript:adminDeleteUser('{$id}')" />
			<i>{$name}</i>
		</td>
END;

			if ($count == 3) {
				$html.= '</tr>';
				$count = 0;
			}
		} 
	}
	
	$html.= "</table>";

	return $html;
}

function getUserDetailsHTML($user) {
	$id = $user->getEmail();
	
	return <<<END
	<table>
		<tr>
			<td align="right">Email:</td>
			<td><input id="email" class="updates" type="text" value="{$id}" /></td>
			<td><input class="options" type="button" value="save" onClick="javascript:saveUserEmail('{$id}')" /></td>
		</tr>
		<tr>
			<td align="right">New Password:</td>
			<td><input id="password" class="updates" type="password" /></td>
			<td><input class="options" type="button" value="save" onClick="javascript:saveUserPassword('{$id}')" /></td>
		</tr>
	</table>	
END;
}

function getNewUserDetailsHTML() {
	
	return <<<END
	<table>
		<tr>
			<td align="right">First Name:</td>
			<td><input id="fname" class="updates" type="text" /></td>
		</tr>
		<tr>
			<td align="right">Last Name:</td>
			<td><input id="lname" class="updates" type="text" /></td>
		</tr>
		<tr>
			<td align="right">Email:</td>
			<td><input id="email" class="updates" type="text" /></td>
		</tr>
		<tr>
			<td align="right">Password:</td>
			<td><input id="password" class="updates" type="password" /></td>
		</tr>
		<tr>	
			<td></td><td><input class="options" type="button" value="save" onClick="javascript:adminSaveNewUserDetails()" /></td>
		</tr>
	</table>	
END;
}
?>
