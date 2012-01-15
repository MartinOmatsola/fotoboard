<?php
include_once 'fotostudio.php';

header("Content-type: image/png");

$img = myImageCreate($_REQUEST['q']);
if (imagesx($img) >= 800) {
		$factor = round((800 / imagesx($img)) * 100);
		$img = scale($img, $factor);
}
imagepng($img);
?>
