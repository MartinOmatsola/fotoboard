<?php
include_once 'display.php';

echo getMainHeaderHTML();


try {
	//authenticate
	if (!$_COOKIE['uid']) {
		echo getLoginHTML();
		print getMainFooterHTML();
		exit;
	}	
	$uid = $_COOKIE['uid'];
	$session_key = $_COOKIE['session_key'];
	$user = User::createFromXML($uid);
	if ($user->getSessionKey() != $session_key) {
		echo getLoginHTML();
		print getMainFooterHTML();
		exit;
	}

	$name = $user->getFname() . ' ' . $user->getLname();
	$html = "";
	$options = "";
	
	if ($user->getEmail() == "admin") {
		$options = getAdminOptionsHTML();
		$html = getAdminHTML();
	}
	else {
		$albums = $user->getAlbums();
		$html = getAlbumsHTML($albums);
		$options = getOptionsHTML();
	}
		
	print '<p> Logged in as ' . $name . ' (<a href="logout.php">logout</a>)</p> ' . $options .
	

	'<br /><br />

	<span id="hint"></span><br />

	<div id="search"></div></br>

	<div id="main">' . $html . '</div>

	<br /><br />

	<span id="info" class="info"></span>

	<br /><br />';
	
	
} catch (Exception $e) {
	print $e->getMessage();
}

print getMainFooterHTML();


?>
