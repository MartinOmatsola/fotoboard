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



require_once 'Album.php';
require_once 'UserException.php';
require_once 'utils.php';

class User {

	private $_email;

	private $_fname;

	private $_lname;

	private $_password;
	
	function __construct($fname, $lname, $email, $password) {
		$this->_fname = $fname;
		$this->_lname = $lname;
		$this->_email = $email;
		$this->_password = $password;
	}

	/*
	 * Initialize an existing photo from its xml representation
	 */
	static function createFromXML($email) {
		if (is_file("users/{$email}.xml")) {
			$xml = simplexml_load_file("users/{$email}.xml");
			return new User($xml->fname."", $xml->lname."", $xml->email."", $xml->password."");
		}
		else {
			throw new UserException("User with email {$email} does not exist");
		}
	}
	
	/**
	 * Saves essential attributes to file
	 * Called only when deleting child nodes
	 * @param SimpleXMLObject $xml the xml object to be saved
	 */
	private function updateXML($xml) {
		$xml->addChild('email', $this->_email);
		$xml->addChild('fname', $this->_fname);
		$xml->addChild('lname', $this->_lname);
		$xml->addChild('password', $this->_password);
		$xml->addChild('session', $this->getSessionKey());
		$xml->asXML("users/{$this->_email}.xml");
	}	

	/**
	 * Adds a friend
	 * @param $id the id of the user to be added
	 */
	function addFriendById($id) {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$xml->friends->addChild('friend', $id);
			$xml->asXML("users/{$this->_email}.xml");
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		}
	}

	function addAlbum($album) {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$xml->albums->addChild('album', $album->getId());
			$xml->asXML("users/{$this->_email}.xml");
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		}
	}
	
	/**
	 * Adds a shared album
	 * @param Album $album the album to be added
	 */
	function addSharedAlbum($album) {
		if (is_file("users/{$this->_email}.xml")) {
			
			if (!in_array($album, $this->getSharedAlbums())) {
				$xml = simplexml_load_file("users/{$this->_email}.xml");
				$xml->shared_albums->addChild('shared_album', $album->getId());
				$xml->asXML("users/{$this->_email}.xml");
			}
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		} 
	}
	
	/**
	 * Deletes an album from this user's albums
	 * @param $id the album id of the album to be deleted
	 */	
	function deleteAlbumById($id) {
		if (is_file("users/{$this->_email}.xml")) {
			try {
				$album = Album::createFromXML($id);
				
				//if this is a shared album call deleteSharedAlbumById()
				if (in_array($album, $this->getSharedAlbums())) {
					$this->deleteSharedAlbumById($album->getId());
				}
				else {
					$album->deleteAllPhotos();

					//update xml record
					$xml = simplexml_load_string("<?xml version='1.0'?><user>\n<albums>\n</albums>\n</user>");
					$xml->addChild('shared_albums');
					$xml->addChild('friends');
					$tmp = simplexml_load_file("users/{$this->_email}.xml");
				
					foreach ($tmp->albums->children() as $album_id) {
						$album_id .= "";
						if ($album_id != $album->getId()) {
							$xml->albums->addChild('album', $album_id);
						}
					}
				
					foreach ($tmp->shared_albums->children() as $album_id) {
						$xml->shared_albums->addChild('shared_album', $album_id . "");
					}
					
					foreach ($tmp->friends->children() as $friend_id) {
						$xml->friends->addChild('friend', $friend_id . "");
					}
					
					$this->updateXML($xml);
									
					//delete album record
					unlink("albums/{$id}.xml");
				}
			} catch (AlbumException $e) {
				throw new UserException("Album with id {$id} could not be deleted");
			}
		}
		else {
			throw new UserException("User with id {$this->_email} does not exist");
		}
	}
	
	/**
	 * Deletes a friend
	 * @param $id the id of the friend to be deleted
	 */
	function deleteFriendById($id) {
		if (is_file("users/{$this->_email}.xml")) {
			
			//update xml record
			$xml = simplexml_load_string("<?xml version='1.0'?><user>\n<albums>\n</albums>\n</user>");
			$xml->addChild('shared_albums');
			$xml->addChild('friends');
			$tmp = simplexml_load_file("users/{$this->_email}.xml");
				
			foreach ($tmp->friends->children() as $friend_id) {
				$friend_id .= "";
				if ($friend_id != $id) {
					$xml->friends->addChild('friend', $id);
				}
			}
			
			foreach ($tmp->albums->children() as $album_id) {
				$xml->albums->addChild('album', $album_id . "");
			}
				
			foreach ($tmp->shared_albums->children() as $album_id) {
				$xml->shared_albums->addChild('shared_album', $album_id . "");
			}

			$this->updateXML($xml);				
		}
		else {
			throw new UserException("User with id {$this->_email} does not exist");
		}		
	}
	
	/**
	 * Removes an album from xml record without deleting from disk
	 * @param $id the id of the album to be removed
	 */
	function removeSharedAlbumById($id) {
		try {
				
			//update xml record
			$xml = simplexml_load_string("<?xml version='1.0'?><user>\n<albums>\n</albums>\n</user>");
			$xml->addChild('shared_albums');
			$xml->addChild('friends');
			$tmp = simplexml_load_file("users/{$this->_email}.xml");
		
			foreach ($tmp->albums->children() as $album_id) {
				$xml->albums->addChild('album', $album_id . "");
			}
				
			foreach ($tmp->shared_albums->children() as $album_id) {
				if ($id != $album_id . "") {
					$xml->shared_albums->addChild('shared_album', $album_id . "");
				}
			}

			foreach ($tmp->friends->children() as $friend_id) {
				$xml->friends->addChild('friend', $friend_id . "");
			}
			
			$this->updateXML($xml);				
			
			//remove myself from album
			$album = Album::createFromXML($id);
			$album->deleteMemberById($this->_email);		
		} catch (Exception $e) {
				throw new UserException("Shared album with id {$id} could not be deleted");
		}
	}
	
	/**
	 * Deletes a shared album
	 * @param $id the id of the shared album to be deleted
	 */
	function deleteSharedAlbumById($id) {
		if (is_file("users/{$this->_email}.xml")) {
				
			$this->removeSharedAlbumById($id);								
			
			//check if I own this album, if I do then
			//we do a hard delete ie this album will
			//also be deleted from members as well
			$album = Album::createFromXML($id);
			
			if ($album->getOwnerId() == $this->_email) {
				$members = $album->getMemberIds();
				foreach ($members as $member) {
					$user = User::createFromXML($member);
					$user->removeSharedAlbumById($id);
				}
				$this->deleteAlbumById($id);
			}
			
		} else {
			throw new UserException("User with id {$this->_email} does not exist");
		}
	}

	/**
	 * Delete this user's albums
	 */
	function deleteAllAlbums() {
		try {
			$albums = $this->getAlbums();
			foreach ($albums as $album) {
				$this->deleteAlbumById($album->getId());
			}
		} catch (AlbumException $e) {
			throw new UserException("Albums of  user with email {$this->_email} could not be deleted");
		}
	}	

	/**
	 * Delete this user's shared albums
	 */
	function deleteAllSharedAlbums() {
		try {
			$albums = $this->getSharedAlbums();
			foreach ($albums as $album) {
				$this->deleteSharedAlbumById($album->getId());
			}
		} catch (AlbumException $e) {
			throw new UserException("Shared albums of  user with email {$this->_email} could not be deleted");
		}
	}	

	function setSessionKey($key) {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$xml->session =  $key;
			$xml->asXML("users/{$this->_email}.xml");
		}
		else {
			throw new UserException("Session key of user with email {$this->_email} could not be set");
		}
	}

	function setEmail($email) {
		if (is_file("users/{$this->_email}.xml")) {
			if ($email != $this->_email) {
				$xml = simplexml_load_file("users/{$this->_email}.xml");
				$xml->email = $email;
				$xml->asXML("users/{$email}.xml");
				unlink("users/{$this->_email}.xml");
				$this->_email = $email;

				foreach ($this->getAlbums() as $album) {
					$album->setOwnerId($this->_email);
				}
			}
		}
		else {
			throw new UserException("Email address could not be reset");
		}
	}

	function setPassword($password) {
		$password = crypt($password, '$1$' . randomString(8) . '$');
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$xml->password = $password;
			$xml->asXML("users/{$this->_email}.xml");
			$this->_password = $password;
		}
		else {
			throw new UserException("Password of user with email {$this->_email} could not be reset");
		}
	}

	function getSessionKey() {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			return $xml->session . "";
		}
		else {
			throw new UserException("Session key of user with email {$this->_email} could not be retrieved");
		}	
	}

	function getFname() {
		return $this->_fname;
	}

	function getLname() {
		return $this->_lname;
	}

	function getEmail() {
		return $this->_email;
	}	

	function getPassword() {
		return $this->_password;
	}

	function getAlbums() {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$albums = array();
			foreach ($xml->albums->children() as $album_id) {
				try {
					$album = Album::createFromXML($album_id);
					$albums[] = $album;
				} catch (AlbumException $e) {
					throw new UserException("Albums of  user with email {$this->_email} could not be retrieved");
				}	
			}
			return $albums;
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		}
	}

	function getSharedAlbums() {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$albums = array();
			foreach ($xml->shared_albums->children() as $album_id) {
				try {
					$album = Album::createFromXML($album_id);
					$albums[] = $album;
				} catch (AlbumException $e) {
					throw new UserException("Shared albums of user with email {$this->_email} could not be retrieved");
				}	
			}
			return $albums;
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		}
	}
	
	/**
	 * Returns an array containing the ids of the friends of this user
	 */
	function getFriendIds() {
		if (is_file("users/{$this->_email}.xml")) {
			$xml = simplexml_load_file("users/{$this->_email}.xml");
			$friend_ids = array();
			foreach ($xml->friends->children() as $friend_id) {
				$friend_ids[] = $friend_id . "";	
			}
			return $friend_ids;
		}
		else {
			throw new UserException("User with email {$this->_email} does not exist");
		}
	}

	/**
	 * Flushes info to xml files
	 * Must be called immediately after creating a new User
	 */
	function flush() {
		$xml = simplexml_load_string("<?xml version='1.0'?><user>\n</user>");
		$xml->addChild('email', $this->_email);
		$xml->addChild('fname', $this->_fname);
		$xml->addChild('lname', $this->_lname);
		$xml->addChild('password', $this->_password);
		$xml->addChild('session');
		$xml->addChild('friends');
		$xml->addChild('albums');
		$xml->addChild('shared_albums');
		$xml->asXML("users/{$this->_email}.xml");
	}

}

?>
