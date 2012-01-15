<?php
include_once 'fotostudio.php';

header("Content-type: image/png");

$img = myImageCreate($_REQUEST['q']);

//$img = roundEdges($img, round(0.01 * imagesx($img)), round(0.1 * imagesx($img)), 255, 255, 255, 111, 111, 111);

$factor = round((120 / imagesy($img)) * 100);
if (imagesx($img) >= imagesy($img)) {
		$factor = round((120 / imagesx($img)) * 100);
}
$img = scale($img, $factor);
$img = createFrame($img, 1, 255, 255, 255);
$img = createFrame($img, 2, 180, 180, 180);

imagepng($img);

?>
