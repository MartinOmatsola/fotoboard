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
require_once 'ManagerException.php';
require_once 'utils.php';

class Manager {

	private static function getXmlCount($dir) {
		$count = 0;
		foreach (new DirectoryIterator($dir) as $file) {
			if (!$file->isDot() && substr($file->getFilename(), -3, strlen($file->getFilename())) == "xml") {
				$count++;
			}
		}
		return ($dir == "users") ? $count - 1 : $count;
	}
	
	private static function getXmlObjects($dir) {
		$objs = array();
		foreach (new DirectoryIterator($dir) as $file) {
			if (!$file->isDot() && substr($file->getFilename(), -3, strlen($file->getFilename())) == "xml") {
				$id = substr($file->getFilename(), 0, strrpos($file->getFilename(), '.'));
				if ($dir == "users") {
					$user = User::createFromXML($id);
					if (!($user->getFname() == "System" && $user->getLname() == "User")) {
						$objs[] = $user;
					}
				} else if ($dir == "albums") {
					$objs[] = Album::createFromXML($id);
				} else if ($dir == "photos") {
					$objs[] = Photo::createFromXML($id);
				}
			}
		}
		return $objs;
	}

	//====================
	//  USER MANAGEMENT
	//====================
	
	static function createUser($fname, $lname, $uid, $password) {
		$user = new User($fname, $lname, $uid, crypt($password, '$1$' . randomString(8) . '$'));
		$user->flush();
	}

	static function deleteUser($user) {
		try {
			$user->deleteAllAlbums();
			unlink('users/'. $user->getEmail() . '.xml');
		} catch (Exception $e) {
			print $e->getMessage();
		}
	}
	
	static function getUser($id) {
		if (is_file("users/{$id}.xml")) {
			return User::createFromXML($id);
		} else {
			throw new ManagerException("User with id $id does not exist");
		}
	}
	
	/**
	 * Returns an array of all users
	 */
	static function getAllUsers() {
		return Manager::getXmlObjects("users");
	}
	
	/**
	 * Returns the number of users
	 */
	static function getUserCount() {
		return Manager::getXmlCount("users");
	}
	
	
	//====================
	//  ALBUM MANAGEMENT
	//====================
	
	static function getAllAlbums() {
		return Manager::getXmlObjects("albums");
	}
	
	static function getAlbum($id) {
		if (is_file("albums/{$id}.xml")) {
			return Album::createFromXML($id);
		} else {
			throw new ManagerException("Album with id $id does not exist");
		}
	}
	
	static function deleteAlbum($id) {
		if (is_file("albums/{$id}.xml")) {
			$owner = User::createFromXML(Album::createFromXML($id)->getOwnerId());
			$owner->deleteAlbumById($id);
		} else {
			throw new ManagerException("Album with id $id does not exist");
		}
	}
	
	static function getAlbumCount() {
		return Manager::getXmlCount("albums");
	}
	
	
	//====================
	//  PHOTO MANAGEMENT
	//====================
	
	static function getAllPhotos() {
		return Manager::getXmlObjects("photos");
	}
	
	static function getPhoto($id) {
		if (is_file("photos/{$id}.xml")) {
			return Photo::createFromXML($id);
		} else {
			throw new ManagerException("Photo with id $id does not exist");
		}
	}
	
	static function deletePhoto($id) {
		if (is_file("photos/{$id}.xml")) {
			$album = Album::createFromXML(Photo::createFromXML($id)->getAlbumId());
			$album->deletePhotoById($id);
		} else {
			throw new ManagerException("Photo with id $id does not exist");
		}
	}
	
	static function getPhotoCount() {
		return Manager::getXmlCount("photos");
	}
}

?>
