<?php
require_once('display.php');

//authenticate
$user;
try {
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
} catch (Exception $e) {
	print $e->getMessage();
	exit;
}

if (!$_POST) {
	print '<div style="font-family:Arial">
	<center><form action="createAlbum.php" enctype="multipart/form-data" method="POST">
			<table style="color:#444444;font-size:13px;font-weight:lighter" border="0">';
				if (!$_REQUEST['aid']) {
					print '<tr><td align="right">Name: </td><td><input type="text" name="name" /></td></tr>';
				}
				print '
				<tr><td align="right">File: </td><td><input type="file" name="file1" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file2" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file3" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file4" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file5" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file6" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file7" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file8" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file9" /></td></tr>
				<tr><td align="right">File: </td><td><input type="file" name="file10" /></td></tr>					
				<tr><td align="center" colspan="2"><input type="submit" value="upload"  /></td></tr>
			</table>
			<input type="hidden" name="album" value="' . $_REQUEST['aid'] . '" />
		 </form></center></div>';
	exit;
}


try {
	$uploaddir = 'images/';
	$imgurl_array = validate($_FILES, $uploaddir);

	//$user = User::createFromXML($_COOKIE['uid']);
	$album;
	if ($_POST['album']) {
		$album = Album::createFromXML($_POST['album']);
	}
	else {
		$album_id = generateName();
		while (is_file("albums/{$album_id}.xml")) {
			$album_id = generateName();
		}
		$album = new Album($album_id, htmlspecialchars(stripslashes(trim($_POST['name'])), ENT_QUOTES), 
					htmlspecialchars(stripslashes(trim($user->getEmail())), ENT_QUOTES), date('l jS \of F Y h:i A'));
		$album->flush();
	}

	for ($i = 0; $i < count($imgurl_array); $i++) {
		$photo_id = generateName();
		while (is_file("photos/{$photo_id}.xml")) {
			$photo_id = generateName();
		}
		$photo = new Photo($photo_id, $album->getId(), $imgurl_array[$i], date('l jS \of F Y h:i A'));
		$photo->flush();
		$album->addPhoto($photo);
	}

	$album_photos = $album->getPhotos();
	if (empty($album_photos)) {
		$album->setCover('media/album.png');
	}
	else {
		$cover = $album_photos[array_rand($album_photos)];
		$album->setCover($cover->getSrc());
	}
	$html = '<center><span style="font-family:Tahoma;font-size:15px;color:#1A1A63"><i><b>' . count($imgurl_array) . '</b> photo(s) uploaded succesfully to the album <b>' . $album->getName() . '</b>. Click on the albums button to view your albums</i></span>';
	if (!$_POST['album']) {
		$user->addAlbum($album);
		$html = '<center><span style="font-family:Tahoma;font-size:15px;color:#1A1A63"><i>Your album has been created succesfully. Click on the albums button to view your albums</i></span>';
	}
	
	print $html; 
	unset($album_photos);
} catch (UserException $e) {
	print $e->getMessage();
}
?>
