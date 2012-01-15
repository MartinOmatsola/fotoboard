<?php
require_once 'display.php';
require_once 'Manager.php';

try {
	$uid = htmlspecialchars(stripslashes(trim($_GET['uid'])), ENT_QUOTES);
	$passwd = htmlspecialchars(stripslashes(trim($_GET['passwd'])), ENT_QUOTES);	
		
	$user = User::createFromXML($uid);
	$crypted_passwd = $user->getPassword();
	
	if (crypt($passwd, substr($crypted_passwd, 0, 11)) ==  $crypted_passwd) {
		//set cookies
		$expires = time() + 3600;
		$session_key = randomString(32);
		setcookie('session_key', $session_key, $expires, '/');
		setcookie('uid', $uid, $expires, '/');
		$user->setSessionKey($session_key);
	
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
	
	}
	else {
		print "<br /><br /><b><i>authentication failed, password is incorrect</i></b><br />";
		echo getLoginHTML();
	}
} catch (UserException $e) {
	print "<br /><b><i>authentication failed</i></b><br />";	
	echo getLoginHTML();//print $e->getMessage();
}
