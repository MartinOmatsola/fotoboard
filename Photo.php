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



include_once 'PhotoException.php';
include_once 'utils.php';

class Photo {
	
	/*
 	 * int, photo id
	 */
	private $_id;
	
	/*
 	 * int, album id that photo belongs to
	 */
	private $_aid;
	
	/*
 	 * string, stores date photo was uploaded
	 */
	private $_date;
	
	/*
 	 * string, stores path to photo
	 */
	private $_src;
		
	function __construct($id, $aid, $src, $date) {
		$this->_id = $id;
		$this->_aid = $aid;
		$this->_date = $date;
		$this->_src = $src;
	}
	
	/*
	 * Initialize an existing photo from its xml representation
	 */
	static function createFromXML($id) {
		if (is_file("photos/{$id}.xml")) {
			$xml = simplexml_load_file("photos/{$id}.xml");
			return new Photo($xml->id."", $xml->aid."", $xml->src."", $xml->date."");
		} 
		else {
			throw new PhotoException("Photo with id {$id} does not exist");
		}
	}	
		
	function getId() {
		return $this->_id;
	}
		
	function getDate() {
		return $this->_date;
	}

	function getSrc() {
		return $this->_src;
	}

	function getAlbumId() {
		return $this->_aid;
	}
	
	/**
	 * Sets the caption for this photo
	 */
	function setCaption($caption) {
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			$xml->caption = $caption;
			$xml->asXML("photos/{$this->_id}.xml");
		}
		else {
			throw new PhotoException("Photo with id {$this->_id} does not exist");
		}
	}

	function getCaption() {
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			return $xml->caption . "";
		}
		else {
			throw new PhotoException("Photo with id {$this->_id} does not exist");
		}
	}

	/*
	 * Adds a tag to this photo. The information is stored in
 	 * the file tags/$tag.xml
	 * @param string $tag the tag name
	 */
	function addTag($tag) {

		//add to tags record first
		$tag = str_replace(" ", "_", $tag);
		if (is_file("tags/{$tag}.xml")) {
			$xml = simplexml_load_file("tags/{$tag}.xml");
			$tmp = simplexml_load_file("tags/{$tag}.xml");
			$exists = 0;
			foreach ($tmp->photo as $photo_id) {
				if ($photo_id == $this->_id) {
					$exists = 1;
					break;
				}
			}
			if (!$exists) {
				$xml->addChild('photo', $this->_id);
				$xml->asXML("tags/{$tag}.xml");
			}
		}
		else {
			$xml = simplexml_load_string("<?xml version='1.0'?><tags>\n</tags>");
			$xml->addChild('photo', $this->_id);
			$xml->asXML("tags/{$tag}.xml");
		}
		//now add to photo's record
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			$xml->tags->addChild('tag', $tag);
			$xml->asXML("photos/{$this->_id}.xml");
		}
		else {
			throw new PhotoException("Photo with id {$this->_id} does not exist");
		}		
	}

	/**
	 * Returns an array containing all tags of this photo
	 */
	function getTags() {
		if (is_file("photos/{$this->_id}.xml")) {
			$tags = array();
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			foreach ($xml->tags->children() as $tag) {
				$tags[] = str_replace("_", " ", $tag."");
			}
			return $tags;
		}
		else {
			throw new PhotoException("Photo with id {$this->_id} does not exist");
		}
	}

	/**
	 * Untag this photo
	 * @param $tag the tag to be removed
	 */
	function deleteTag($tag) {
		$tag = str_replace(" ", "_", $tag);
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_string("<?xml version='1.0'?><photo>\n</photo>");
			$xml->addChild('tags');
			$tmp = simplexml_load_file("photos/{$this->_id}.xml");
			foreach ($tmp->tags->children() as $dtag) {
				$dtag.="";
				if ($tag != $dtag) {
					$xml->tags->addChild('tag', $dtag);
				}
			}		
			$xml->addChild('id', $this->_id);
			$xml->addChild('aid', $this->_aid);
			$xml->addChild('src', $this->_src);
			$xml->addChild('date', $this->_date);
			$xml->addChild('comments');
			foreach ($this->getComments() as $comment) {
				$com = $xml->comments->addChild('comment', $comment['comment']);
				$com->addAttribute('id', $comment['id']."");
				$com->addAttribute('posted_by', $comment['posted_by']."");
				$com->addAttribute('date', $comment['date']."");
			}
			$xml->addChild('caption', $this->getCaption());
			$xml->asXML("photos/{$this->_id}.xml");

			//now delete entry from tags xml file
			$tag = str_replace(" ", "_", $tag);
			if (is_file("tags/{$tag}.xml")) {
				$xml = simplexml_load_string("<?xml version='1.0'?><tags>\n</tags>");
				$tmp = simplexml_load_file("tags/{$tag}.xml");
				foreach ($tmp->children() as $photo_id) {
					$photo_id.="";
					if ($photo_id != $this->_id) {
						$xml->addChild('photo', $photo_id);
					}
				}
				//delete this tag xml file if there are no children
				if (!$xml->children()) {
					unlink("tags/{$tag}.xml");
				}
				else {
					$xml->asXML("tags/{$tag}.xml");
				}
			}
		}
		else {
			$tag = str_replace("_", " ". $tag);
			throw new PhotoException("photo with id {$this->_id} has not been tagged with {$tag}");
		}
	}

	/**
	 * Delete all tags of this photo
	 */
	function deleteAllTags() {
		foreach ($this->getTags() as $tag) {
			$this->deleteTag($tag);
		}
	}

	/**
	 * Adds a comment to this photo
	 * @param string $comment the comment
	 */ 
	function addComment($comment, $name, $date) {
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			$dcomment = $xml->comments->addChild('comment', $comment);
			$dcomment->addAttribute('id', generateName());
			$dcomment->addAttribute('posted_by', $name);
			$dcomment->addAttribute('date', $date);
			$xml->asXML("photos/{$this->_id}.xml");
		}
		else {
			throw new PhotoException("Photo with id {$this->_id} does not exist");
		}
	}

	/**
	 * Deletes a comment
	 * @param int $cid the id of the comment to be deleted
	 */
	function deleteComment($cid) {
		if (is_file("photos/{$this->_id}.xml")) {
			$xml = simplexml_load_string("<?xml version='1.0'?><photo>\n</photo>");
			$tmp = simplexml_load_file("photos/{$this->_id}.xml");
			$xml->addChild('comments');
			foreach ($tmp->comments->children() as $dcomment) {
				//$node = 
				if ($cid != $dcomment['id']."") {
					$com = $xml->comments->addChild('comment', $dcomment);
					$com->addAttribute('id', $dcomment['id']."");
					$com->addAttribute('posted_by', $dcomment['posted_by']."");
					$com->addAttribute('date', $dcomment['date']."");
				}
			}
			$xml->addChild('id', $this->_id);
			$xml->addChild('aid', $this->_aid);
			$xml->addChild('src', $this->_src);
			$xml->addChild('date', $this->_date);
			$xml->addChild('caption', $this->getCaption());
			$xml->addChild('tags', '');
			foreach ($this->getTags() as $tag) {
				$xml->tags->addChild('tag', $tag);
			}
			$xml->asXML("photos/{$this->_id}.xml");
		}
		else {
			throw new PhotoException("Could not delete comment for Photo with id {$this->_id}");
		}
	}

	function getComments() {
		if (is_file("photos/{$this->_id}.xml")) {
			$comments = array();
			$xml = simplexml_load_file("photos/{$this->_id}.xml");
			foreach ($xml->comments->children() as $comment) {
				$comments[] = array(
									'comment' => $comment."",
									'id' => $comment['id']."",
									'posted_by' => $comment['posted_by']."",
									'date' => $comment['date'].""
									);
			}
			return $comments;	
		}
		else {
			throw new PhotoException("Could not retrieve comments for Photo with id {$this->_id}");
		}
	}

	/*
	 * Flushes info to xml files
	 * Must be called immediately after creating a new Photo
	 */
	function flush() {
		$xml = simplexml_load_string("<?xml version='1.0'?><photo>\n</photo>");
		$xml->addChild('id', $this->_id);
		$xml->addChild('aid', $this->_aid);
		$xml->addChild('src', $this->_src);
		$xml->addChild('date', $this->_date);
		$xml->addChild('comments');
		$xml->addChild('caption');
		$xml->addChild('tags');
		$xml->asXML("photos/{$this->_id}.xml");
	}
		
}
?>
