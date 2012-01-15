<?php
//require_once "DB.php";
include_once 'User.php';
include_once 'utils.php';


//header("Content-Type: text/plain");

$imgurl_array = getFiles("../images/");
$user = User::createFromXML("jamie@boomtown.com");

$album = new Album(generateName(), "nature", $user->getEmail(),  date('l jS \of F Y h:i:s A'));
$album->flush();

for ($i = 0; $i < 50; $i++) {
	$photo_id = generateName();
	while (is_file("photos/{$photo_id}.xml")) {
		$photo_id = generateName();
	}
	
	//$name = "images/" . generateName() . substr(basename($imgurl_array[$i]), strrpos($imgurl_array[$i],"."));
	$name = "images/" . basename($imgurl_array[$i]);
	copy($imgurl_array[$i], $name);
	$photo = new Photo($photo_id, $album->getId(), $name, date('l jS \of F Y h:i:s A'));
	$photo->flush();
	$album->addPhoto($photo);
}
$album_photos = $album->getPhotos();
$cover = $album_photos[array_rand($album_photos)];
$album->setCover($cover->getSrc());
$user->addAlbum($album);

$album = new Album(generateName(), "cars", $user->getEmail(),  date('l jS \of F Y h:i:s A'));
$album->flush();

for ($i = 50; $i < 100; $i++) {
	$photo_id = generateName();
	while (is_file("photos/{$photo_id}.xml")) {
		$photo_id = generateName();
	}
	
	//$name = "images/" . generateName() . substr(basename($imgurl_array[$i]), strrpos($imgurl_array[$i],"."));
	$name = "images/" . basename($imgurl_array[$i]);
	copy($imgurl_array[$i], $name);
	$photo = new Photo($photo_id, $album->getId(), $name, date('l jS \of F Y h:i:s A'));
	$photo->flush();
	$album->addPhoto($photo);
}
$album_photos = $album->getPhotos();
$cover = $album_photos[array_rand($album_photos)];
$album->setCover($cover->getSrc());
$user->addAlbum($album);

$album = new Album(generateName(), "live and learn", $user->getEmail(),  date('l jS \of F Y h:i:s A'));
$album->flush();

for ($i = 100; $i < 150; $i++) {
	$photo_id = generateName();
	while (is_file("photos/{$photo_id}.xml")) {
		$photo_id = generateName();
	}
	
	//$name = "images/" . generateName() . substr(basename($imgurl_array[$i]), strrpos($imgurl_array[$i],"."));
	$name = "images/" . basename($imgurl_array[$i]);
	copy($imgurl_array[$i], $name);
	$photo = new Photo($photo_id, $album->getId(), $name, date('l jS \of F Y h:i:s A'));
	$photo->flush();
	$album->addPhoto($photo);
}
$album_photos = $album->getPhotos();
$cover = $album_photos[array_rand($album_photos)];
$album->setCover($cover->getSrc());
$user->addAlbum($album);

$album = new Album(generateName(), "live and learn", $user->getEmail(),  date('l jS \of F Y h:i:s A'));
$album->flush();

for ($i = 150; $i < 200; $i++) {
	$photo_id = generateName();
	while (is_file("photos/{$photo_id}.xml")) {
		$photo_id = generateName();
	}
	
	//$name = "images/" . generateName() . substr(basename($imgurl_array[$i]), strrpos($imgurl_array[$i],"."));
	$name = "images/" . basename($imgurl_array[$i]);
	copy($imgurl_array[$i], $name);
	$photo = new Photo($photo_id, $album->getId(), $name, date('l jS \of F Y h:i:s A'));
	$photo->flush();
	$album->addPhoto($photo);
}
$album_photos = $album->getPhotos();
$cover = $album_photos[array_rand($album_photos)];
$album->setCover($cover->getSrc());
$user->addAlbum($album);

//$user = new User("Jamie", "lynn", "jamie@boomtown.com");
//$user->flush();
//$user->addAlbum($album);
?>
