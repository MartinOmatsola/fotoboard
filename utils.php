<?php
require_once 'mail/Mail.php';
require_once 'mail/Mail/mime.php';

//generate a random name for image file
function generateName($len = 12) {
	$name = "";
	for ($i = 0; $i < $len; $i++) {
		$name = $name . rand(0,9);
	}
	return $name;
}

//generates a session key of length $length
function randomString($length = 16) {

  	// start with a blank password
  	$password = "";

  	// define possible characters
  	$possible = "0123456789babcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
    
  	// set up a counter
  	$i = 0; 
    
  	// add random characters to $password until $length is reached
  	while ($i < $length) { 

  		  // pick a random character from the possible ones
  		  $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
        
  		  $password .= $char;
      		  $i++;
  	}

  	// done!
  	return $password;
}

//validate image types and copy valid ones to directory $dir
function validate($files, $dir) {
	
	$keys = array_keys($files);
	$imgurl_array = array();
	for ($i = 0; $i < count($keys); $i++) {
		$key = $keys[$i];
		$imgname = $files[$key]['name'];
		$name = generateName() . substr(basename($imgname), strrpos($imgname,"."));
		$imgtype = $files[$key]['type'];
		$uploadfile = $dir . $name;

		if ($imgtype == "") {
			continue;
		}
		if ($imgtype == "image/gif" or $imgtype == "image/jpeg" or $imgtype == "image/png") {
			move_uploaded_file($files[$key]['tmp_name'], $uploadfile) 
    			or die ("Could not copy");
			$imgurl_array[] = $uploadfile; 
		} 
		else {
			return array();
		}
	}
	return $imgurl_array;
}

//read directory and return array of filenames
function getFiles($dir) {
	$files = array();
	$dh = opendir($dir);
	while (($file = readdir($dh)) !== false) {
		$path = $dir . $file;
		if (!is_dir($path)) {
			$files[] = $path;
		}
		elseif (is_dir($path) && basename($path) != "." && basename($path) != "..") {
			$files = array_merge($files, getFiles($path . "/"));
		}
	}
	closedir($dh);
	return $files;
}

//send photos from an array contain urls to an email address
//return 0 on success, 1 on failure
//$flag = 1 => files in photos already exist, hence do not save them
function emailPhotos($to, $from, $subject, $body, $photos, $flag=0, $replyto='') {
	$mail = &Mail::factory("mail");
	$crlf = "\r\n";
	$headers = array(
			'Subject' => $subject,
			'From' => "mailman@fotocrib.com"
			);
	if (strlen($replyto) > 0) {
		$headers = array(
			'Subject' => $subject,
			'From' => "mailman@fotocrib.com",
			'Reply-To'	=> $replyto		
			);
	}

	$mime = new Mail_mime($crlf);
	$mime->setTXTBody($body);
	
	//save and attach photos
	for ($i = 0; $i < count($photos); $i++) {
		if (!$flag) {
			$img = myImageCreate($photos[$i]);
			$filename = "/home/fotocrib/public_html/tmp/" . generateName() . ".jpg";
			imagejpeg($img, $filename);
			$mime->addAttachment($filename);
		}
		else {
			$mime->addAttachment($photos[$i]);
		}
	}
	$body = $mime->get();
	$headers = $mime->headers($headers);
	return $mail->send($to, $headers, $body);
}
?>
