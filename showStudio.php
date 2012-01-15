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
	
	$html .= '

		<img class="ad" src="bound.php?q='. $photo->getSrc() . '" /><br /><br />
		
		<input type="hidden" id="src" value="' . $photo->getSrc() . '" />		

		<select class="updates" onChange="showStudioParams(this.options[this.selectedIndex].value, \''. $photo->getId() . '\')"> 
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
