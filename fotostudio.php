<?php

/**
 * @author  Martin Okorodudu <webmaster@fotocrib.com>
 * @license http://opensource.org/licenses/lgpl-license.php
 *          GNU Lesser General Public License, Version 2.1
 */

/**
 * fotostudio.php contains several image manipulation functions implemented
 * using the GD library. Requires php >= 5.0 compiled with the GD library.
 */


/**
 * Creates an image resource from a url or path to a file of type jpg, gif or png.
 * 
 * @param string $img_url URL or pathname to the image file
 * @return image resource
 */
function myImageCreate($img_url) {
	$img = 0;
	if (eregi(".jpg$", $img_url) > 0) {
		$img = imagecreatefromjpeg($img_url);
	}
	elseif (eregi(".png$", $img_url) > 0) {
		$img = imagecreatefrompng($img_url);
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		$img = imagecreatefromgif($img_url);
	}
	return $img;
}

/**
 * Saves an image resource to a file.
 * 
 * @param resource $img The image to be saved.
 * @param string $img_url pathname to the destination file.
 * @return true iff save was successful
 */
function myImageSave($img, $img_url) {
	if (eregi(".jpg$", $img_url) > 0) {
		imagejpeg($img, $img_url);
		return true;
	}
	elseif (eregi(".png$", $img_url) > 0) {
		imagepng($img, $img_url);
		return true;
	}
	elseif (eregi(".gif$", $img_url) > 0) {
		imagegif($img, $img_url);
		return true;
	}
	return false;
}

/**
 * Crops an image.
 *
 * @param resource $img The image to be cropped.
 * @param integer $left_margin Percentage of the image from the left to be cropped.
 * @param integer $right_margin Percentage of the image from the left to be cropped.
 * @param integer $top_margin Percentage of the image from the left to be cropped.
 * @param integer $bottom_margin Percentage of the image from the left to be cropped.
 * @return the cropped image resource
 */
function crop($img, $left_margin, $right_margin, $top_margin, $bottom_margin) {

	if (!is_numeric($left_margin) || !is_numeric($right_margin) || !is_numeric($top_margin) || !is_numeric($bottom_margin)) {
		die("Error: non numeric input to crop function");
	}
	
	$width = imagesx($img);
	$height = imagesy($img);

	$x_start = round(($left_margin / 100) * $width);
	$x_stop = round($width - ($right_margin / 100) * $width);  

	$y_start = round(($top_margin / 100) * $height);
	$y_stop = round($height - ($bottom_margin / 100) * $height);

	$cropped_img_w = $x_stop - $x_start;
	if ($cropped_img_w < 0) {
		die("Bad input for horizontal margins: margin dimensions must not intersect");
	}
	$cropped_img_h = $y_stop - $y_start;
	if ($cropped_img_w < 0) {
		die("Bad input for vertical margins: margin dimensions must not intersect");
	}
	$cropped_img = imagecreatetruecolor($cropped_img_w, $cropped_img_h);
	imagecopy($cropped_img, $img, 0, 0, $x_start, $y_start, $cropped_img_w, $cropped_img_h);
	
	return $cropped_img;
}

/**
 * Rotates an image by a given angle.
 *
 * @param resource $img The image to be rotated.
 * @param Integer $degrees The angle of rotation.
 * @param Integer $r The red value of the bgcolor, 0 <= $r <= 255.
 * @param Integer $g The green value of the bgcolor, 0 <= $g <= 255.
 * @param Integer $b The blue value of the bgcolor, 0 <= $b <= 255.
 * @return the rotated image resource
 */
function rotate($img, $degrees, $r, $g, $b) {
	if (!is_numeric($degrees) || !is_numeric($r) || !is_numeric($g) || !is_numeric($b)) {
		die("Error: non numeric input to rotate function");
	}
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}
	$bgcolor = imagecolorallocate($img, $r, $g, $b);
	$rotated_img = imagerotate($img, $degrees, $bgcolor);
	return $rotated_img;
}

/**
 * Performs some image filter effects.
 *
 * @param resource $img The image resource to be filtered.
 * @param string $effect The required effect, one of "sharpen", "blur"
 *		"edge_enhance", "edge_detect", "grayscale", "brighten", "sharpen",
 *		"contrast", "colorize", "emboss", "reverse", "edge_detectS".
 * @param Integer $r The red value of the bgcolor, 0 <= $r <= 255.
 * @param Integer $g The green value of the bgcolor, 0 <= $g <= 255.
 * @param Integer $b The blue value of the bgcolor, 0 <= $b <= 255.
 * @return the filtered image resource
 */
function doEffect($img, $effect, $r = 0, $g = 0, $b = 0) {
	$matrix;
	$divisor = 1;
	$offset = 0;
	if ($effect == "sharpen") {
		$matrix = array(array( 0, -1, 0 ),
                       			array( -1, 5, -1 ),
                       			array( 0, -1, 0 ) );
	}
	elseif ($effect == "blur") {
		imagefilter($img, IMG_FILTER_GAUSSIAN_BLUR);
		return $img;
	}
	elseif ($effect == "edge_enhance") {
		$matrix = array(array( 0, 0, 0 ),
                       			array( -1, 1, 0 ),
                       			array( 0, 0, 0 ) );
	}
	elseif ($effect == "edge_detect") {
		imagefilter($img, IMG_FILTER_EDGEDETECT);
		return $img;
	}
	elseif ($effect == "grayscale") {
		imagefilter($img, IMG_FILTER_GRAYSCALE);
		return $img;
	}
	elseif ($effect == "brighten") {
		imagefilter($img, IMG_FILTER_BRIGHTNESS, 50);
		return $img;
	}
	elseif ($effect == "contrast") {
		imagefilter($img, IMG_FILTER_CONTRAST, 40);
		return $img;
	}
	elseif ($effect == "colorize") {
		imagefilter($img, IMG_FILTER_COLORIZE, $r, $g, $b);
		return $img;
	}
	elseif ($effect == "edge_detectS") {
		$matrix = array(array( 1, 2, 1 ),
                       			array( 0, 0, 0 ),
                       			array( -1, -2, -1 ) );
	}
	elseif ($effect == "emboss") {
		imagefilter($img, IMG_FILTER_EMBOSS);
		return $img;
	}
	elseif ($effect == "reverse") {
		imagefilter($img, IMG_FILTER_NEGATE);
		return $img;
	}
	else {
		die("effect not supported");
	}
	imageconvolution($img, $matrix, $divisor, $offset);
	return $img;
}

/**
 * Resizes an image.
 *
 * @param resource $img The image to be resized.
 * @param integer $new_w The desired width.
 * @param integer $new_h The desired height.
 * @return the resized image resource
 */
function resize($img, $new_w, $new_h) {
	if (!is_numeric($new_w) || !is_numeric($new_h)) {
		die("Error: non numeric input to resize function");
	}
	$w = imagesx($img);
	$h = imagesy($img); 
	$resized_img = imagecreatetruecolor($new_w, $new_h);
	imagecopyresampled($resized_img, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
	return $resized_img;
}

/**
 * Scales an image.
 *
 * @param resource $img The image to be scaled.
 * @param integer $pct The percentage to be scaled to.
 * @return the scaled image resource
 */

function scale($img, $pct) {
	if (!is_numeric($pct)) {
		die("Error: non numeric input to scale function");
	}
	$w = imagesx($img);
	$h = imagesy($img);
	$new_w = ($pct / 100) * $w;
	$new_h = ($pct / 100) * $h; 
	$scaled_img = imagecreatetruecolor($new_w, $new_h);
	imagecopyresampled($scaled_img, $img, 0, 0, 0, 0, $new_w, $new_h, $w, $h);
	return $scaled_img;
}

/**
 * Overlays a whole body of text centered horizontally onto an image.
 *
 * @param resource $img The image resource.
 * @param integer $size The font size.
 * @param integer $angle The angle of the text. Set to 0 for straight line.
 * @param Integer $r The red value of the font color, 0 <= $r <= 255.
 * @param Integer $g The green value of the font color, 0 <= $g <= 255.
 * @param Integer $b The blue value of the font color, 0 <= $b <= 255.
 * @param string $fontpath The path to the required font.
 * @param string $text The text to be inserted.
 * @return the modified image resource
 */
function insertMessage($img, $size, $angle, $r, $g, $b, $fontpath, $text) {
	if (!is_string($text) || !is_numeric($r) || !is_numeric($g) || !is_numeric($b)
		|| !is_numeric($size) || !is_numeric($angle) || !is_string($font)) {
		die("Error: Bad input to insertMessage function");
	}
	
	$img_width = imagesx($img);
	$img_height = imagesy($img);

	//get font height
	$bbox = imagettfbbox($size, $angle, $fontpath, $text);
	$font_height = abs($bbox[1] - $bbox[5]);
	
	//using default left/right margin of 5% for now and linespacing of 2%
	$start_x = 5;
	$start_y = ceil((($font_height / $img_height) * 100) + 2);
	$line_width = $img_width - (2 * 0.05 * $img_width);

	$words = explode(" ", $text);
	$line = "";
	
	for ($i = 0; $i < count($words); $i++) {
		$line .= $words[$i] . " ";
		$len = strlen($words[$i]);
			
		$bbox = imagettfbbox($size, $angle, $fontpath, $line);
		$font_width = abs($bbox[4] - $bbox[0]);

		//check if we exceeded line width
		if ($font_width > $line_width) {
			//replace string that was just appended with newline
			$line = substr($line, 0, strlen($line) - $len - 2) . "\n";
			$i--;
		}
	}
	
	$lines = explode("\n", $line);
	//$offset = $start_y + 2;
	$font_height = $start_y;
	for ($i = 0; $i < count($lines); $i++) {
		$img = insertString($img, $size, $angle, $start_x, $start_y, $r, $g ,$b, $font, $lines[$i]);
		$start_y = $start_y + /*$offset*/ (1.2 * $font_height);
	}
	return $img;
}

/**
 * Inserts a string onto an image. Runoff characters will not be appended to a new line.
 *
 * @param resource $img The image resource.
 * @param integer $size The font size.
 * @param integer $angle The angle of the text. Set to 0 for straight line.
 * @param Integer $r The red value of the font color, 0 <= $r <= 255.
 * @param Integer $g The green value of the font color, 0 <= $g <= 255.
 * @param Integer $b The blue value of the font color, 0 <= $b <= 255.
 * @param string $font The path to the required font.
 * @param string $text The text to be inserted.
 * @return the modified image resource
 */
function insertString($img, $size, $angle, $x, $y, $r, $g, $b, $font, $text) {
	if (!is_string($text) || !is_numeric($x) || !is_numeric($y) || !is_numeric($r) || !is_numeric($g) || !is_numeric($b)
		|| !is_numeric($size) || !is_numeric($angle) || !is_string($font)) {
		die("Error: Bad input to insertString function");
	}
	
	$x_pos = ($x/100) * imagesx($img);
	$y_pos = ($y/100) * imagesy($img);
		
	$textcolor = imagecolorallocate($img, $r, $g, $b);
	$text = str_replace("\'", "'", $text);	
	$text = str_replace('\"', '"', $text);	

	imagettftext($img, $size, $angle, $x_pos, $y_pos, $textcolor, $font, $text);
	return $img;
}

/**
 * Creates a frame around an image.
 *
 * @param resource $img The image resource.
 * @param integer $thickness The thickness of the frame in pixels.
 * @param Integer $r The red value of the frame color, 0 <= $r <= 255.
 * @param Integer $g The green value of the frame color, 0 <= $g <= 255.
 * @param Integer $b The blue value of the frame color, 0 <= $b <= 255.
 * @return the framed image resource
 */
function createFrame($img, $thickness, $r, $g, $b) {
	if (!is_numeric($thickness) || !is_numeric($r) || !is_numeric($g) || !is_numeric($b)) {
		die("Error: Non numeric input to createFrame function");
	}
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}

	$w = imagesx($img);
	$h = imagesy($img);

	$new_w = (2 * $thickness) + $w;
	$new_h = (2 * $thickness) + $h;

	$framed_img = imagecreatetruecolor($new_w, $new_h);
	$color = imagecolorallocate($framed_img, $r, $g, $b);
	imagefill($framed_img, 0, 0, $color);
	imagecopy($framed_img, $img, $thickness, $thickness, 0, 0, $w, $h);
	return $framed_img;
}

/**
 * Rounds corners and creates rounded frames.
 *
 * @param resource $img The image resource.
 * @param integer $thickness The thickness of the frame. Used when creating a rounded frame.
 *		Otherwise set to 0
 * @param integer $radius The radius of the curve.
 * @param Integer $r The red value of the bgcolor, 0 <= $r <= 255.
 * @param Integer $g The green value of the bgcolor, 0 <= $g <= 255.
 * @param Integer $b The blue value of the bgcolor, 0 <= $b <= 255.
 * @param Integer $r2 The red value of the frame color, 0 <= $r2 <= 255. Used when $thickness > 0.
 * @param Integer $g2 The green value of the frame color, 0 <= $g2 <= 255. Used when $thickness > 0.
 * @param Integer $b2 The blue value of the frame color, 0 <= $b2 <= 255. Used when $thickness > 0.
 * @return the altered image resource
 */
function roundEdges($img, $thickness, $radius, $r, $g, $b, $r2, $g2, $b2) {
	if (!is_numeric($radius) || !is_numeric($r) || !is_numeric($g) || !is_numeric($b) || !is_numeric($thickness)
		|| !is_numeric($r2) || !is_numeric($g2) || !is_numeric($b2)) {
		die("Error: Non numeric input to roundEdges function");
	}
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}
	if ($r2 > 255) {
		$r2 = 255;
	}	
	if ($g2 > 255) {
		$g2 = 255;
	}
	if ($b2 > 255) {
		$b2 = 255;
	}
	if ($r2 < 0) {
		$r2 = 0;
	}
	if ($g2 < 0) {
		$g2 = 0;
	}
	if ($b2 < 0) {
		$b2 = 0;
	}
	
	$w = imagesx($img);
	$h = imagesy($img);
	
	$fill_color = imagecolorallocate($img, $r, $g, $b);
	
	//we want to fill background with border color since this region
	//will make up our new rounded frame
	if ($thickness > 0) {
		$fill_color = imagecolorallocate($img, $r2, $g2, $b2);
	}
	//top left corner
	for ($x = 0; $x <= $radius; $x++) {
		//find y coordinate
		$y = $radius - sqrt(pow($radius, 2) - pow($x - $radius, 2));
		
		//anti alias corners
		if ($thickness == 0) {
			$y1 = $y - floor($y);
			imageline($img, $x, 0, $x, floor($y), $fill_color);
		
			$rgb = ImageColorsForIndex($img, ImageColorAt($img, $x, ceil($y)));
			$diff_color = imageColorExactAlpha( $img, $rgb['red'], $rgb['green'], $rgb['blue'], $y1*100 );

			$im = imagecreatetruecolor(10, 10);
			$bg = imagecolorallocate($im, $r,$g,$b);
			imagefill($im,0,0,$bg);
			for ($i=round($y1 * 10); $i < 10; $i++) {
				for ($j=0; $j < 10; $j++) {
					imagesetpixel($im, $j, $i, $diff_color);
				}
			}
			imagecopyresampled($img, $im, $x, ceil($y), 0, 0, 1, 1, 10, 10);
		}
		//don't anti alias since in this case the frame will be anti aliased in its second pass
		else {
			imageline($img, $x, 0, $x, round($y), $fill_color);
		}
	}
	//bottom left corner
	for ($x = 0; $x <= $radius; $x++) {
		//find y coordinate
		$y = sqrt(pow($radius, 2) - pow($x - $radius, 2)) + $h - $radius;
		
		//anti alias corners
		if ($thickness == 0) {
			$y1 = $y - floor($y);
			imageline($img, $x, ceil($y), $x, $h, $fill_color);
		
			$rgb = ImageColorsForIndex($img, ImageColorAt($img, $x, floor($y)));
			$diff_color = imageColorExactAlpha( $img, $rgb['red'], $rgb['green'], $rgb['blue'], 100-$y1*100 );

			$im = imagecreatetruecolor(10, 10);
			$bg = imagecolorallocate($im, $r,$g,$b);
			imagefill($im,0,0,$bg);
			for ($i = 0 ; $i <= round($y1 * 10); $i++) {
				for ($j=0; $j < 10; $j++) {
					imagesetpixel($im, $j, $i, $diff_color);
				}
			}
			imagecopyresampled($img, $im, $x, floor($y), 0, 0, 1, 1, 10, 10);
		}
		//don't anti alias since in this case the frame will be anti aliased in its second pass
		else {
			imageline($img, $x, floor($y), $x, $h, $fill_color);
		}
	}
	//top right corner
	for ($x = $w - $radius-1; $x <= $w; $x++) {
		//find y coordinate
		$y = $radius - sqrt(pow($radius, 2) - pow($x - $w + $radius, 2));
		
		//anti alias corners
		if ($thickness == 0) {
			$y1 = $y - floor($y);
			imageline($img, $x, 0, $x, floor($y), $fill_color);
		
			$rgb = ImageColorsForIndex($img, ImageColorAt($img, $x, ceil($y)));
			$diff_color = imageColorExactAlpha( $img, $rgb['red'], $rgb['green'], $rgb['blue'], $y1*100 );

			$im = imagecreatetruecolor(10, 10);
			$bg = imagecolorallocate($im, $r,$g,$b);
			imagefill($im,0,0,$bg);
			for ($i=round($y1 * 10); $i < 10; $i++) {
				for ($j=0; $j < 10; $j++) {
					imagesetpixel($im, $j, $i, $diff_color);
				}
			}
			imagecopyresampled($img, $im, $x, ceil($y), 0, 0, 1, 1, 10, 10);
		}
		//don't anti alias since in this case the frame will be anti aliased in its second pass
		else {
			imageline($img, $x, 0, $x, round($y), $fill_color);
		}
	}
	//bottom right corner
	for ($x = $w - $radius-1; $x <= $w; $x++) {
		//find y coordinate
		$y = sqrt(pow($radius, 2) - pow($x - $w + $radius, 2)) + $h - $radius;
		
		//anti alias corners
		if ($thickness == 0) {
			$y1 = $y - floor($y);
			imageline($img, $x, ceil($y), $x, $h, $fill_color);
		
			$rgb = ImageColorsForIndex($img, ImageColorAt($img, $x, floor($y)));
			$diff_color = imageColorExactAlpha( $img, $rgb['red'], $rgb['green'], $rgb['blue'], 100-$y1*100 );

			$im = imagecreatetruecolor(10, 10);
			$bg = imagecolorallocate($im, $r,$g,$b);
			imagefill($im,0,0,$bg);
			for ($i = 0 ; $i <= round($y1 * 10); $i++) {
				for ($j=0; $j < 10; $j++) {
					imagesetpixel($im, $j, $i, $diff_color);
				}
			}
			imagecopyresampled($img, $im, $x, floor($y), 0, 0, 1, 1, 10, 10);
		}
		//don't anti alias since in this case the frame will be anti aliased in its second pass
		else {
			imageline($img, $x, floor($y), $x, $h, $fill_color);
		}
	}
	
	//begin create rounded border frame
	$border_color;
	if ($thickness > 0) {
		$border_color = imagecolorallocate($img, $r2, $g2, $b2);
		$img = createFrame($img, $thickness, $r2, $g2, $b2);
		roundEdges($img, 0, $thickness + $radius, $r, $g, $b, -1, -1, -1);
	}
	
	return $img;
}

/**
 * Transforms an image into an isosceles triangle.
 *
 * @param resource $img The image resource.
 * @param integer $orientation 0 indicates upward facing triangle, 1 indicates downward.
 * @param Integer $r The red value of the bgcolor, 0 <= $r <= 255.
 * @param Integer $g The green value of the bgcolor, 0 <= $g <= 255.
 * @param Integer $b The blue value of the bgcolor, 0 <= $b <= 255.
 */
function triangulate($img, $orientation, $r, $g, $b) {
	if (!is_numeric($r) || !is_numeric($g) || !is_numeric($b) || !is_numeric($orientation)) {
		die("Error: non numeric input to triangulate function");
	}
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}
	
	//get points
	$w = imagesx($img);
	$h = imagesy($img);
	$center = round($w / 2);

	if ($orientation == 0) {
		$points1 = array(0, 0, $center, 0, 0, $h);
		$points2 = array($center, 0, $w, 0, $w, $h);
	}
	else {
		$points1 = array(0, 0, $center, $h, 0, $h);
		$points2 = array($center, $h, $w, 0, $w, $h);
	}
	
	$bg = imagecolorallocate($img, $r, $g, $b);
	$num = count($points1) / 2;
	imagefilledpolygon($img, $points1, $num, $bg);
	imagefilledpolygon($img, $points2, $num, $bg);

	return $img;
}

/**
 * Transforms the image into an icicle.
 * @param resource $img The image to be modified
 * @return the altered image
 */
function icicle($img) {
	$w = imagesx($img);
	$h = imagesy($img);
	
	$top = imagecreatetruecolor($w + round(0.5 * $w), $h);
	$bg = imagecolorallocate($top, 255, 255, 255);
	imagefill($top, 0, 0, $bg);
	$side = imagecreatetruecolor(round(0.5 * $w), $h);
	imagefill($side, 0, 0, $bg);

	$img = stitchTogether($img , $side, 0);
	$img = stitchTogether($top , $img, 1);

	$col = imagecolorallocate($img, 10, 10, 10);
	$edge = 	imagecolorallocate($img, 111, 111, 111);

	$points1 = array($w + round(0.5 * $w), 0, 0, $h, $w, $h);	
	$points2 = array($w + round(0.5 * $w), 0, $w, 2 * $h, $w, $h);

	imagefilledpolygon($img, $points1, 3, $col);	
	imagefilledpolygon($img, $points2, 3, $col);
	imagepolygon($img, $points2, 3, $edge);
	
	return $img;
}

/**
 * Tiles an image.
 *
 * @param resource $img The image resource.
 * @param integer $w The width of each tile.
 * @param integer $h The height of each tile.
 * @param Integer $r The red value of the line color, 0 <= $r <= 255.
 * @param Integer $g The green value of the line color, 0 <= $g <= 255.
 * @param Integer $b The blue value of the line color, 0 <= $b <= 255.
 * @return the tiled image
 */
function makeGrid($img, $w, $h, $r, $g, $b) {
	if (!is_numeric($r) || !is_numeric($g) || !is_numeric($b) || !is_numeric($w) || !is_numeric($h)) {
		die("Error: non numeric input to make grid function");
	}	
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}
	if ($w >= imagesx($img) || $h >= imagesy($img)) {
		die("Error: unit dimensions exceed image dimensions");
	}
	$grid_color = imagecolorallocate($img, $r, $g ,$b);
	
	if ($w != 0) {
		//draw vertical lines
		for ($i = 0; $i < imagesx($img); $i = $i + $w) {
			imageline($img, $i, 0, $i, imagesy($img) -1, $grid_color);
		}
	}
	if ($h != 0) {
		//draw horizontal lines
		for ($j = 0; $j < imagesy($img); $j = $j + $h) {
			imageline($img, 0, $j, imagesx($img) - 1, $j, $grid_color);
		}
	}

	//seal image
	imageline($img, imagesx($img) -1, 0, imagesx($img) -1, imagesy($img) - 1, $grid_color);
	imageline($img, 0, imagesy($img) -1, imagesx($img) -1, imagesy($img) - 1, $grid_color);

	return $img;
}

/**
 * Imitates a comic sketch effect.
 *
 * @param $img The image resource.
 * @param integer $dark 1 => dark, 0 => normal
 * @return the altered image
 */
function comicSketch($img, $dark) {
	$width = imagesx($img);
	$height = imagesy($img);
	
	$img2;
	if ($dark == 0) {
		$img2 = imagecreatetruecolor($width, $height);
		imagecopy($img2, $img, 0, 0, 0, 0, $width, $height);
	}
	for ($j = 0; $j < $height; $j++) {
		for ($i = 0; $i < $width; $i++) {
			$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
		
			//get shaded pixel values
			$shaded_red = round(($width - $i / $width) * $rgb['red']);
			$shaded_green = round(($width - $i / $width) * $rgb['green']);
			$shaded_blue = round(($width - $i / $width) * $rgb['blue']);
			
			$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
		
			imagesetpixel($img, $i, $j, $rep);	
		}
	}
	$img = doEffect($img, 'blur');
	if ($dark == 0) {
		$img = mergeimages($img, $img2, 0, 0, 0, 0, 100, 100, 50);
	}
	return $img;
}

/**
 * Shades an image.
 *
 * @param integer $orientation 0 => left to right, 1 => right to left, 2 => top to bottom, 3 => bottom to top, 
 * 		4 => center outwards horizontally, 5 => center outwards vertically.
 * @return the shaded image
 */
function shade($img, $orientation) {
	if (!is_numeric($orientation)) {
		die("Error: non numeric input to shade function");
	}

	$width = imagesx($img);
	$height = imagesy($img);
	
	if ($orientation == 0) {
		for ($j = 0; $j < $height; $j++) {
			for ($i = 0; $i < $width; $i++) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($i / $width) * $rgb['red']);
				$shaded_green = round(($i / $width) * $rgb['green']);
				$shaded_blue = round(($i / $width) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	elseif ($orientation == 1) {
		for ($j = 0; $j < $height; $j++) {
			for ($i = $width - 1; $i >= 0; $i--) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round((($width - $i) / $width) * $rgb['red']);
				$shaded_green = round((($width - $i) / $width) * $rgb['green']);
				$shaded_blue = round((($width - $i) / $width) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	elseif ($orientation == 2) {
		for ($i = 0; $i < $width; $i++) {
			for ($j = 0; $j < $height; $j++) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($j / $height) * $rgb['red']);
				$shaded_green = round(($j / $height) * $rgb['green']);
				$shaded_blue = round(($j / $height) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	elseif ($orientation == 3) {
		for ($i = 0; $i < $width; $i++) {
			for ($j = $height - 1; $j >= 0; $j--) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round((($height - $j) / $height) * $rgb['red']);
				$shaded_green = round((($height - $j) / $height) * $rgb['green']);
				$shaded_blue = round((($height - $j) / $height) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	elseif ($orientation == 4) {
		for ($j = 0; $j < $height; $j++) {
			for ($i = 0; $i < floor($width / 2); $i++) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($i / ($width / 2)) * $rgb['red']);
				$shaded_green = round(($i / ($width / 2)) * $rgb['green']);
				$shaded_blue = round(($i / ($width / 2)) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
		for ($j = 0; $j < $height; $j++) {
			for ($i = $width - 1; $i >= floor($width / 2) ; $i--) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($width - $i) / ($width / 2) * $rgb['red']);
				$shaded_green = round(($width  - $i) / ($width / 2) * $rgb['green']);
				$shaded_blue = round(($width - $i) / ($width / 2) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	else {
		for ($i = 0; $i < $width; $i++) {
			for ($j = 0; $j < floor($height / 2); $j++) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($j / ($height / 2)) * $rgb['red']);
				$shaded_green = round(($j / ($height / 2)) * $rgb['green']);
				$shaded_blue = round(($j / ($height / 2)) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
		for ($i = 0; $i < $width; $i++) {
			for ($j = $height - 1; $j >= floor($height / 2) ; $j--) {
				$rgb = ImageColorsForIndex($img, ImageColorAt($img, $i, $j));
			
				//get shaded pixel values
				$shaded_red = round(($height - $j) / ($height / 2) * $rgb['red']);
				$shaded_green = round(($height  - $j) / ($height / 2) * $rgb['green']);
				$shaded_blue = round(($height - $j) / ($height / 2) * $rgb['blue']);
				
				$rep = imagecolorallocate($img, $shaded_red, $shaded_green, $shaded_blue);
			
				imagesetpixel($img, $i, $j, $rep);	
			}
		}
	}
	return $img;
}

/**
 * Stacks an image.
 *
 * @param integer $displacement Displacement from original position in pixels.
 * @param integer $num  Number of copies.
 * @param integer $vertical  0 => bottom-up, 1 => top-down
 * @param integer $horizontal  0 => right-left, 1 => left-right
 * @return the stacked image resource
 */
function stack($img, $displacement, $num, $vertical, $horizontal, $r, $g, $b) {
	if (!is_numeric($r) || !is_numeric($g) || !is_numeric($b) || !is_numeric($displacement) || !is_numeric($vertical)
		|| !is_numeric($num) || !is_numeric($horizontal)) {
		die("Error: non numeric input to the stack function");
	}	
	if ($r > 255) {
		$r = 255;
	}	
	if ($g > 255) {
		$g = 255;
	}
	if ($b > 255) {
		$b = 255;
	}
	if ($r < 0) {
		$r = 0;
	}
	if ($g < 0) {
		$g = 0;
	}
	if ($b < 0) {
		$b = 0;
	}	
	if ($displacement < 0) {
		$displacement = 0;
	}
	if ($num <= 1) {
		return $img;
	}
	$img = createFrame($img, 1, 0, 0, 0);
	$w = imagesx($img);
	$h = imagesy($img);

	$stacked_img = imagecreatetruecolor($w + ($num - 1) * $displacement, $h + ($num - 1) * $displacement);	
	$bg = imagecolorallocate($stacked_img, $r, $g, $b);
	imagefill($stacked_img, 0, 0, $bg);

	//bottom-up	
	if (!$vertical) {
		//right-left
		if (!$horizontal) {
			for ($i = $num - 1; $i >= 0; $i--) {
				$loc = $i * $displacement;
				imagecopy($stacked_img, $img, $loc, $loc, 0, 0, $w, $h);
			}
		}
		//left-right
		else {
			$xloc = 0;
			$yloc = ($num - 1) * $displacement;
			for ($i = 0; $i <= $num - 1; $i++) {
				imagecopy($stacked_img, $img, $xloc, $yloc, 0, 0, $w, $h);
				$xloc = $xloc +$displacement;
				$yloc = $yloc - $displacement;
			}
		}
	}
	//top-down
	if ($vertical) {
		if (!$horizontal) {
			$yloc = 0;
			$xloc = ($num - 1) * $displacement;
			for ($i = 0; $i <= $num - 1; $i++) {
				imagecopy($stacked_img, $img, $xloc, $yloc, 0, 0, $w, $h);
				$xloc = $xloc - $displacement;
				$yloc = $yloc + $displacement;
			}	
		}
		else {
			for ($i = 0; $i <= $num - 1; $i++) {
				$loc = $i * $displacement;
				imagecopy($stacked_img, $img, $loc, $loc, 0, 0, $w, $h);
			}
		}
	}	
	return $stacked_img;
}

//stitch images together, if heights are different, smaller image height is resized to larger image
//height
//$orientation = 0 => horizontal, 1=> vertical
function stitchTogether($img1, $img2, $orientation) {
	$img1_w = imagesx($img1);
	$img1_h = imagesy($img1);

	$img2_w = imagesx($img2);
	$img2_h = imagesy($img2);
	
	$stitched_img;
	
	//horizontal stitch
	if ($orientation == 0) {
		//now resize img2 so both heights match
		$resized_img2 = resize($img2, $img2_w, $img1_h);
	
		//create new image to hold stitched images
		$stitched_img = imagecreatetruecolor($img1_w +$img2_w, $img1_h);
	
		imagecopy($stitched_img, $img1, 0, 0, 0, 0, $img1_w, $img1_h);
		imagecopy($stitched_img, $resized_img2, $img1_w, 0, 0, 0,  $img2_w, $img1_h);
	}
	//vertical
	else {
		$resized_img2 = resize($img2, $img1_w, $img2_h);
	
		//create new image to hold stitched images
		$stitched_img = imagecreatetruecolor($img1_w, $img1_h +$img2_h);
	
		imagecopy($stitched_img, $img1, 0, 0, 0, 0, $img1_w, $img1_h);
		imagecopy($stitched_img, $resized_img2, 0, $img1_h, 0, 0,  $img1_w, $img2_h);
	}
	return $stitched_img;
}

?>
