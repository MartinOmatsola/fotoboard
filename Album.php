<?php


//
// +---------------------------------------------------------------------------+
// |  F O T O B O A R D                                 |
// +---------------------------------------------------------------------------+
// | Copyright (c) 2007 fotocrib.com                                         | 
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | 1. Redistributions of source code must retain the above copyright         |
// |    notice, this list of conditions and the following disclaimer.          |
// | 2. Redistributions in binary form must reproduce the above copyright      |
// |    notice, this list of conditions and the following disclaimer in the    |
// |    documentation and/or other materials provided with the distribution.   |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR      |
// | IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES |
// | OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.   |
// | IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT  |
// | NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF  |
// | THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.         |
// +---------------------------------------------------------------------------+
// | For help, contact webmaster@fotocrib.com          |
// +---------------------------------------------------------------------------+
//
/**
  * @author Martin Okorodudu
 */


require_once 'User.php';
require_once 'Photo.php';
require_once 'AlbumException.php';

class Album {
	/*
 	 * int, album id
	 */
	private $_id;
	
	/*
 	 * int, user id of owner
	 */
	private $_uid;
	
	/*
 	 * string, album name
	 */
	private $_name;
	
	/*
 	 * string, stores date when album was created
	 */
	private $_date;
	
	
	function __construct($id, $name, $uid, $date) {
		$this->_id = $id;
		$this->_name = $name;
		$this->_uid = $uid;
		$this->_date = $date;
	}

	/*
	 * Initialize an existing Album from its xml representation
	 * @param int $id the id of this album
	 */
	static function createFromXML($id) {
		if (is_file("albums/{$id}.xml")) {
			$xml = simplexml_load_file("albums/{$id}.xml");
			return new Album($xml->id."", $xml->name."", $xml->owner."", $xml->created."");
		}
		else {
			throw new AlbumException("Album with id {$id} does not exist");
		}
	}
		
	/**
	 * Saves essential attributes to file
	 * Called only when deleting child nodes
	 * @param SimpleXMLObject $xml the xml object to be saved
	 */
	private function updateXML($xml) {
		$date = date('l jS \of F Y h:i A');
		
		$xml->addChild('id', $this->_id);
		$xml->addChild('owner', $this->_uid);
		$xml->addChild('created', $this->_date);
		$xml->addChild('modified', $date);
		$xml->addChild('num', $this->getSize());
		$xml->addChild('name', $this->_name);
		$xml->addChild('cover', $this->getCover());
		$xml->asXML("albums/{$this->_id}.xml");
	}
		
	function getId() {
		return $this->_id;
	}

	function getName() {
		return $this->_name;
	}

	function getOwnerId() {
		return $this->_uid;
	}
		
	function getDate() {
		return $this->_date;
	}
	
	function getSize() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			return $xml->num . ""  + 0;
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	function getMdate() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			return $xml->modified . "";
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	function isShared() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			if ($xml->members) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	function getPhotos() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$photos = array();
			foreach ($xml->photos->children() as $photo_id) {
				$photos[] = Photo::createFromXML($photo_id);
			}
			return $photos;
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	function getMemberIds() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$members = array();
			foreach ($xml->members->children() as $uid) {
				$members[] = $uid . "";
			}
			return $members;
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}	

	function setName($name) {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$xml->name = $name;
			$xml->asXML("albums/{$this->_id}.xml");
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	function setOwnerId($ownerId) {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$xml->owner = $ownerId;
			$xml->asXML("albums/{$this->_id}.xml");
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	function setCover($cover_src) {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$xml->cover = $cover_src;
			$xml->asXML("albums/{$this->_id}.xml");
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	function getCover() {
		if (is_file("albums/{$this->_id}.xml")) {
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			return $xml->cover . "";
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	/**
 	 * Adds a photo to this album
 	 * @param Photo the photo to be added
	 */
	function addPhoto($photo) {
		if (is_file("albums/{$this->_id}.xml")) {
			$date = date('l jS \of F Y h:i A');
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$xml->photos->addChild('photo', $photo->getId());
			$xml->modified = $date . "";
			$xml->num = $xml->num + 1 . "";
			$xml->asXML("albums/{$this->_id}.xml");
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	/**
	 * Converts this album into a shared album
	 */
	function makeShared() {
		if (is_file("albums/{$this->_id}.xml")) {
			$date = date('l jS \of F Y h:i A');
			$xml = simplexml_load_file("albums/{$this->_id}.xml");
			$xml->modified = $date . "";
			if (!$this->isShared()) {
				$xml->addChild('members');
			}
			$xml->asXML("albums/{$this->_id}.xml");
		} else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	} 
	
	/**
	 * Unshares this album
	 */
	function unshare() {
		if (is_file("albums/{$this->_id}.xml")) {
			
			$xml = simplexml_load_string("<?xml version='1.0'?><album>\n<photos>\n</photos>\n</album>");
			$tmp = simplexml_load_file("albums/{$this->_id}.xml");
			
			if (!$tmp->members) {
				throw new AlbumException("Album with id {$this->_id} is not a shared album");
			}
			
			$owner = User::createFromXML($this->getOwnerId());
			$owner->removeSharedAlbumById($this->_id);

			$members = $this->getMemberIds();
			foreach ($members as $member_id) {
				$member = User::createFromXML($member_id);
				$member->removeSharedAlbumById($this->_id);
			}			
			
			foreach ($tmp->photos->children() as $photo_id) {
				$photo_id .= "";
				$xml->photos->addChild('photo', $photo_id);
			}
					
			$this->updateXML($xml);
		} else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	/**
	 * Adds a user to this album
	 */
	function addMember($uid) {
		if (is_file("albums/{$this->_id}.xml")) {
			
			if (!in_array($uid, $this->getMemberIds())) {	
				$date = date('l jS \of F Y h:i A');
				$xml = simplexml_load_file("albums/{$this->_id}.xml");
				if (!$xml->members) {
					throw new AlbumException("Album with id {$this->_id} is not a shared album");
				}
				$xml->modified = $date . "";
				$xml->members->addChild("member", $uid);
				$xml->asXML("albums/{$this->_id}.xml");
			}
			
		} else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	/**
	 * Deletes a member from this album
	 * @param string $uid the id of the user to be deleted
	 */
	function deleteMemberById($uid) {
		if (is_file("albums/{$this->_id}.xml")) {
			
			$xml = simplexml_load_string("<?xml version='1.0'?><album>\n<photos>\n</photos>\n</album>");
			$xml->addChild('members');
			$tmp = simplexml_load_file("albums/{$this->_id}.xml");
			
			if (!$tmp->members) {
				throw new AlbumException("Album with id {$this->_id} is not a shared album");
			}
			//delete member
			foreach ($tmp->members->children() as $id) {
				$id .= "";
				if ($id != $uid) {
					$xml->members->addChild('member', $id);
				}
			}
			
			foreach ($tmp->photos->children() as $photo_id) {
				$photo_id .= "";
				$xml->photos->addChild('photo', $photo_id);		
			}
					
			$this->updateXML($xml);
			/*
			if (count($this->getMemberIds()) == 0) {
				$this->unshare();
			}
			*/
		} else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}
	
	/**
 	 * Deletes a photo from this album
 	 * @param $id the id of the photo to be deleted
	 */
	function deletePhotoById($id) {
		if (is_file("albums/{$this->_id}.xml")) {
			try {
				$photo = Photo::createFromXML($id);
				
				$xml = simplexml_load_string("<?xml version='1.0'?><album>\n<photos>\n</photos>\n</album>");
				$tmp = simplexml_load_file("albums/{$this->_id}.xml");
				foreach ($tmp->photos->children() as $photo_id) {
					$photo_id .= "";
					if ($photo_id != $photo->getId()) {
						$xml->photos->addChild('photo', $photo_id);
					}
				}	
				
				if ($tmp->members) {
					$xml->addChild('members');				
					foreach ($tmp->members->children() as $uid) {
						$xml->members->addChild('member', $uid . "");
					}
				}		
				
				$tmp->num = $tmp->num - 1 . "";
				$tmp->asXML("albums/{$this->_id}.xml");
				$this->updateXML($xml);

				if ($this->getSize() == 0) {
					$this->setCover('media/album.png');
				}
				//check that we are not deleting cover, if we are, reasssign cover
				elseif ($photo->getSrc() == $tmp->cover . "") {
					$my_photos = $this->getPhotos();
					$cover = $my_photos[array_rand($my_photos)];
					$this->setCover($cover->getSrc());
				}
				//delete all tags associated with this photo
				$photo->deleteAllTags();				

				//delete image record and file
				unlink("photos/{$id}.xml");
				unlink($photo->getSrc());
			} catch (PhotoException $e) {
				throw new AlbumException("Photo with id {$id} could not be deleted");
			}
		}
		else {
			throw new AlbumException("Album with id {$this->_id} does not exist");
		}
	}

	/**
	 * Deletes all photos from this album
	 */
	function deleteAllPhotos() {
		try {
			$photos = $this->getPhotos();
			foreach ($photos as $photo) {
				$this->deletePhotoById($photo->getId());
			}
		} catch (AlbumException $e) {
			throw new AlbumException("Photos from album with id {$this->_id} could not be deleted");
		}
	}

	/**
	 * Flushes info to xml files
	 */
	function flush() {
		$xml = simplexml_load_string("<?xml version='1.0'?><album>\n</album>");
		$xml->addChild('id', $this->_id);
		$xml->addChild('owner', $this->_uid);
		$xml->addChild('created', $this->_date);
		$xml->addChild('modified', $this->_date);
		$xml->addChild('photos', '');
		$xml->addChild('cover', '');
		$xml->addChild('num', 0);
		$xml->addChild('name', $this->_name);
		$xml->asXML("albums/{$this->_id}.xml");
	}	
}
?>
