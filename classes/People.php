<?php
/**
 * Handles all functions and properties that are needed to maintain the information for people on the site. 
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

require_once 'lib/geoip.php';
class People {
	private $db;
	private $user;
	private $session;

	function __construct($dbc) {
		$this->db = $dbc;
		$this->user = User::getInstance($dbc);
		$this->session = Session::getInstance($dbc);
	}

	function getSpecificPages($term)
	{
		$term = "%".$term."%";
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `users` WHERE `username` LIKE ?");
		$stmt->bind_param("s", $term);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$k = floor($count/4);
		if($count % 4 == 0) 
			$k--;
		return $k;
	}

	function grabSpecific($page, $term) {
		$t2 = $term;
		$term = "%".$term."%";
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `users` WHERE `username` LIKE ?");
		$stmt->bind_param("s", $term);
		$stmt->bind_result($count);
		$stmt->fetch();

		$posts = $count / 15;
		$stmt->free_result();
		$pages = ceil($posts);
		$limit = 4;
			
		$start = $limit;
		$end = $page * $limit - ($limit);
		
		$check = $this->db->prepare("SELECT * FROM users WHERE `username` LIKE ?");
		$check->bind_param("s", $term);
		$check->execute();
		$check->store_result();

		$amount_count = $check->num_rows;
		$check->free_result();

		$select = $this->db->prepare("SELECT `username`, `uid`, `ip`, `lastlogin`, `lastbeef`, `avatar` FROM `users`  WHERE `username` LIKE ? ORDER BY uid DESC LIMIT ".$end.",".$start."");
		echo $this->db->error;
		$select->bind_param("s", $term);
		$select->execute();
		$select->store_result();
		$select->bind_result($username, $uid, $ip, $lastlogin, $lastbeef, $avatar);
		$return = "";
		$return .= "<info term=\"{$t2}\"></info>";
		if($select->num_rows >= 1)
		{
			while($select->fetch())
			{
				$r = lookup_ip($ip);
				$city = isset($r->city) ? $r->city : 'unlisted';
				$return .= HTMLHelper::markupPeopleResult($this->session->lookupUsername($uid), $this->user->countBeefs($uid), $this->user->countHeats($uid), $city, $lastlogin, $lastbeef, $avatar);
			} 
		} else {
			$return .= "Your search returned no results, please try a different search term.";
		}
		return $return;
	}
}
?>