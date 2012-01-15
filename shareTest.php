<?php
require_once 'User.php';

$album = Album::createFromXML('083844024574');
//$album->addMember("jamielynn@boomtown.com");
//$user = User::createFromXML("martin.omatsola@gmail.com");
$album->deleteMemberById("jamielynn@boomtown.com");
//$user->deleteSharedAlbumById('083844024574');
//$user->addSharedAlbum($album);
//$album->makeShared();
//$owner = User::createFromXML($album->getOwnerId());
//$owner->addSharedAlbum($album);
//$album->unshare();
//print_r($album); 

?>
