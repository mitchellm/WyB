<?php
/**
 * Handles most, if not all beef specific functions.
 *
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.00
 */
class Beef {

	private $db;
	private $session;
	private static $instance;

	public function __construct($dbc) {
		$this->db = $dbc;
		$this->session = Session::getInstance($dbc);
	}

	public static function getInstance($dbc) {
		if(!self::$instance)
			self::$instance = new Beef($dbc);
			return self::$instance;
	}

	/**
	 * Inserts a beef into the database with the content, author, and the current time
	 * @param string $content the content of the beef
	 */
	function post($content)
	{
		$author = ucfirst($this->session->uid);
		$time = time();
		$lastbeef = date("m/d/y \@ g:i A T");
		$stmt = $this->db->prepare("INSERT INTO `beefs` (`content`, `author`, `timestamp`) VALUES (?, ?, ?)");
		$stmt->bind_param("ssi", $content, $author, $time);
		$stmt->execute();

		$upd = $this->db->prepare("UPDATE `users` SET `lastbeef` = ? WHERE `uid` = ?");
		$upd->bind_param("si", $lastbeef, $this->session->uid);
		$upd->execute();
	}

	/**
	 *
	 * Returns the new count of beefs
	 * @return int the number of pages
	 */
	function getSpecificPages($term)
	{
		$term = "%".$term."%";
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM beefs WHERE `content` LIKE ?");
		$stmt->bind_param("s", $term);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$k = floor($count/4);
		if($count % 4 == 0) 
			$k--;
		return $k;
	}

	/**
	 * 
	 * Counts beefs that occured after the timestamp
	 * @param $timestamp in UNIX (time())
	 */
	function countSpecificPosts($term)
	{
		$term = "%".$term."%";
		$stmt = $this->db->prepare("SELECT * FROM `beefs` WHERE `content` LIKE ?");
		$stmt->bind_param("s", $term);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows;
	}

	/**
	 *
	 * Returns the new count of beefs
	 * @return int the number of pages
	 */
	function getPages()
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM beefs");
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$k = floor($count/4);
		if($count % 4 == 0) 
			$k--;
		return $k;
	}

	/**
	 * 
	 * Counts beefs that occured after the timestamp
	 * @param $timestamp in UNIX (time())
	 */
	function countPosts($timestamp = '')
	{
		if($timestamp == '')
		$timestamp = time();

		$stmt = $this->db->prepare("SELECT * FROM `beefs` WHERE `timestamp` > ?");
		$stmt->bind_param("i", $timestamp);
		$stmt->execute();
		$stmt->store_result();
		return $stmt->num_rows;
	}
	
	function grabSpecific($page, $term) {
		$t2 = $term;
		$term = "%".$term."%";
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `beefs` WHERE `content` LIKE ?");
		$stmt->bind_param("s", $term);
		$stmt->bind_result($count);
		$stmt->fetch();

		$posts = $count / 15;
		$stmt->free_result();
		$pages = ceil($posts);
		$limit = 4;
			
		$start = $limit;
		$end = $page * $limit - ($limit);

		$check = $this->db->prepare("SELECT * FROM beefs WHERE `content` LIKE ?");
		$check->bind_param("s", $term);
		$check->execute();
		$check->store_result();

		$amount_count = $check->num_rows;
		$check->free_result();

		$select = $this->db->prepare("SELECT bid,author,content,heats,timestamp FROM `beefs`  WHERE `content` LIKE ? ORDER BY timestamp DESC LIMIT ".$end.",".$start."");
		$select->bind_param("s", $term);
		$select->execute();
		$select->store_result();
		$select->bind_result($id, $author, $content, $heats, $timestamp);
		$return = "";
		$return .= "<info term=\"{$t2}\"></info>";
		if($select->num_rows >= 1)
		{
			while($select->fetch())
			{
				$avatar = $this->session->lookupAvatar($author);
				$author = $this->session->lookupUsername($author);
				$tago = Utility::timeAgo($timestamp, time());
				$return .= HTMLHelper::markupBeefResult($author, $tago, $content, $heats, $avatar);
			} 
		}
		return $return;
	}

	/**
	 *
	 * Returns the new posts that are to be displayed
	 * @param int $page to get
	 */
	function grabAll($page, $timestamp)
	{
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM beefs");
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();

		$posts = $count / 15;
		$stmt->free_result();
		$pages = ceil($posts);
		$limit = 4;
			
		$start = $limit;
		$end = $page * $limit - ($limit);

		$check = $this->db->prepare("SELECT * FROM beefs");
		$check->execute();
		$check->store_result();

		$amount_count = $check->num_rows;
		$check->free_result();

		$select = $this->db->prepare("SELECT `bid`,`author`,`content`,`heats`,`timestamp` FROM `beefs` WHERE timestamp < '{$timestamp}' ORDER BY `timestamp` DESC LIMIT ".$end.",".$start."");
		$select->execute();
		$select->store_result();
		$select->bind_result($id, $author, $content, $heats, $timestamp);
		$return = "";
		if($select->num_rows >= 1)
		{
			while($select->fetch())
			{
				$avatar = $this->session->lookupAvatar($author);
				$author = $this->session->lookupUsername($author);
				$return .= HTMLHelper::markupBeef($id, $author, $content, $heats, $timestamp, $avatar);
			}
		}
		return $return;
	}

	/**
	 * Returns the beef to update the page with
	 * @param username
	 * @param content for the beef
	 */
	function createBeef($username, $content, $uid)
	{
		$avatar = $this->session->lookupAvatar($uid);
		return HTMLHelper::markupBeef(9000, ucfirst($username), $content, 0, time(), $avatar);
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $id
	 * @param unknown_type $session
	 */
	function reheat($bid, $session, $heats)
	{
		$check = $this->db->prepare("SELECT uid FROM `sessions` WHERE `sid` = ?");
		$check->bind_param("s", $session);
		$check->bind_result($uid);
		$check->execute();
		$check->store_result();
		while($check->fetch())
		{
			if($check->num_rows >= 1)
			{
				$prep = $this->db->prepare("SELECT * FROM `heats` WHERE `uid` = ? AND `bid` = ?");
				$prep->bind_param("ii", $uid, $bid);
				$prep->execute();
				$prep->store_result();
				if($prep->num_rows < 1)
				{
					$heats = $heats + 1;
					$update = $this->db->query("UPDATE  `wyb`.`beefs` SET  `heats` =  '".$heats."' WHERE  `beefs`.`bid` = {$bid}");
		
					$insert = $this->db->prepare("INSERT INTO `heats` (`uid`, `bid`) VALUES (?, ?)");
					$insert->bind_param("ii", $uid, $bid);
					$insert->execute();
					echo number_format($heats);
					return true;
				}
			}
		}
		echo number_format($heats);
		return false;
	}
}
?>