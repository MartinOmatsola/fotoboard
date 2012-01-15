<?php
require_once 'display.php';

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

	$albums = $user->getAlbums();
	$shared = $user->getSharedAlbums();
	
	$shared_albums = array();
	//remove regular albums from shared
	//this prevents them from being displayed twice
	foreach ($shared as $share) {
		if (!in_array($share, $albums)) {
			$shared_albums[] = $share;
		}
	}
	unset($shared);	
	
	$html =<<<END
			New email: <input type="text" id="email" class="updates" />
						<input type="button" class="options" value="update" onclick="javascript:setEmail('{$uid}')" />
			New password: <input type="password" id="passwd" class="updates" />
						<input type="button" class="options" value="update" onclick="javascript:setPassword('{$uid}')" /><br/><br/>
END;

	$html .= getManageAlbumsHTML($albums);
	$html .= (!empty($shared_albums)) ? '<br /><b>SHARED</b><br />' . getManageAlbumsHTML($shared_albums, 0) : "";
	
	print $html;
} catch (Exception $e) {
	print $e->getMessage();
}
?>
