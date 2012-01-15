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

	$photo_ids = array();

	//do universal search
	if ($_GET['scope']) {
		$term = str_replace(" ", "_", $_GET['term']);
		if (is_file("tags/{$term}.xml")) {
			$xml = simplexml_load_file("tags/{$term}.xml");
			foreach ($xml->children() as $photo) {
				$photo_ids[] = $photo."";
			}
				
		}
	}
	//search my albums only
	else {
		$results = array();
		$albums = $user->getAlbums();
		foreach ($albums as $album) {
			foreach ($album->getPhotos() as $photo) {
				$results = array_merge($results, array_values(preg_grep("/{$_GET['term']}/", $photo->getTags())));
			}
		}
		
		//grab photo ids from xml files
		$seen = array(); //track files we've seen
		foreach ($results as $xml_file) {
			$xml_file = str_replace(" ", "_", $xml_file);
			if (!in_array($xml_file, $seen)) {
				$xml = simplexml_load_file("tags/{$xml_file}.xml");
				foreach ($xml->children() as $photo) {
					$photo_ids[] = $photo."";
				}
				$seen[] = $xml_file;
			}
		}
		unset($results);
	}

	//create photos and print html
	$html = getSearchPhotosHTML($photo_ids);
	
	if ($html) {
		$image = (count($photo_ids) == 1) ? "image" : "images";
		print '<i><b>found '. count($photo_ids) . ' '. $image . '</b></i><br />' . $html;
	}
	else {
		print "<i>could not find any photos tagged <b>${_GET['term']}</b></i>";
	}
	unset($photo_ids);
	unset($seen);
} catch (Exception $e) {
	print $e->getMessage();
}
?>
