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
	
	$img = myImageCreate($_GET['src']);
	$img_url = "tmp/" . generateName() . ".png";

	$r;
	$g;
	$b;
	$r2;
	$g2;
	$b2;
	$rgb = $_GET['rgb'];
	$rgb2 = $_GET['rgb2'];
	if ($rgb) { 
		$r = hexdec(substr($rgb, 0, 2));
		$g = hexdec(substr($rgb, 2, 2));
		$b = hexdec(substr($rgb, 4));
	}

	if ($rgb2) { 
		$r2 = hexdec(substr($rgb2, 0, 2));
		$g2 = hexdec(substr($rgb2, 2, 2));
		$b2 = hexdec(substr($rgb2, 4));
	}
	
	//process request
	if ($_GET['fn'] == "round") {
		if (is_numeric($_GET['radius'])) {
			$img = roundEdges($img, 0, $_GET['radius'], $r, $g, $b, -1, -1, -1);
		}
	}
	elseif ($_GET['fn'] == 'rotate') {
		if (is_numeric($_GET['angle'])) {
			$img = rotate($img, $_GET['angle'], $r, $g, $b);
		}
	}
	elseif ($_GET['fn'] == frame) {
		if (is_numeric($_GET['thickness'])) {
			$img = createFrame($img, $_GET['thickness'], $r, $g, $b);
		}
	}
	elseif ($_GET['fn'] == 'rframe') {
		if (is_numeric($_GET['rradius'])) {
			$img = roundEdges($img, $_GET['rthickness'], $_GET['rradius'], $r2, $g2, $b2, $r, $g, $b);
		}
	}
	elseif ($_GET['fn'] == 'resize') {
		if (is_numeric($_GET['w']) && is_numeric($_GET['h'])) {
			$img = resize($img, $_GET['w'], $_GET['h'] );
		}
	}
	elseif ($_GET['fn'] == 'scale') {
		if (is_numeric($_GET['pct'])) {
			$img = scale($img, $_GET['pct'] );
		}
	}

	myImageSave($img, $img_url);

	$html .= '<span>
				<input class="options" type="button" value="save" onClick="save(\'' . $photo->getId() . '\')" />
				<input type="button" class="options" value="album" onClick="showPhotos(\'' . $photo->getAlbumId() . '\')" />			
		</span><br/><br />

		<img class="ad" src="bound.php?q='. $img_url . '" /><br /><br />

		<input type="hidden" id="src" value="'. $img_url . '" />
		
		<select class="updates" onChange="showStudioParams(this.options[this.selectedIndex].value, \''. $photo->getId() . '\',\'' . $img_url . '\')"> 
				<option>-- Select a function --</option>
				<option value="frame">frame</option>
				<option value="round">round corners</option>
				<option value="rframe">rounded frame</option>
				<option value="resize">resize</option>
				<option value="scale">scale</option>
				<option value="rotate">rotate</option>
		</select>
		<div id="params"></div>';

	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
