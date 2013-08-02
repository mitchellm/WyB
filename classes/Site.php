<?php
/**
 * This class handles the poritons of the code that require displays and drawing on the main index
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */
 
require_once 'HTMLHelper.php';
require_once 'Utility.php';

class Site {
	private $dbc;
	private $beef;
	private $session;
	private static $instance;

	public static function getInstance($dbc) {
		if(!self::$instance)
			self::$instance = new Site($dbc);
			return self::$instance;
	}	

	public function __construct($dbc) {
		$this->db = $dbc;
		$this->beef = Beef::getInstance($dbc);
		$this->user = User::getInstance($dbc);
	}

	public function drawNavigation($page, $pages, $type = null)
	{
		$return = "";
		$return .= "<info pages=\"{$pages}\">";
		if($page <= 9) {
			for($x = 1; $x < 10; $x++) {
				if($x - 1 > $pages && $x != 1)
					break;

				if($page == $x)
					$return .= HTMLHelper::naviLink($x, "current", $x, $type);
				else
					$return .= HTMLHelper::naviLink($x, null, $x, $type);
			}
			if($pages > 9) {
				$return .= HTMLHelper::naviLink("10", null, "...", $type);
				$return .= HTMLHelper::naviLink("10", null, 10, $type);
			}
		}

		if($page > 9 && $pages >= $page) {
			$index = floor($page / 9) * 9;
			$final = $index + 9;
			$nextI = $final;
			$before = $index - 1;
			
			$return .= HTMLHelper::naviLink($before, null, $before, $type);
			$return .= HTMLHelper::naviLink($before, null, "...", $type);
			
			for($index; $index < $final; $index++) {
				if($index > $pages)
					break;

				if($page == $index)
						$return .= HTMLHelper::naviLink($index, "current", $index, $type);
					else
						$return .= HTMLHelper::naviLink($index, null, $index, $type);
			}
			
			if($pages > $final-1) {
				$return .= HTMLHelper::naviLink($nextI, null, "...", $type);
				$return .= HTMLHelper::naviLink($nextI, null, $nextI, $type);
			}
		}
		return $return;
	}

	public function searchDatabase($type, $term) {
		$r = "";
		if($type == "beefs") {
			$stmt = $this->db->prepare("SELECT * FROM beefs WHERE content LIKE ? ORDER BY bid DESC");
			$term = "%" . $term . "%";
			$stmt->bind_param("s", $term);
			$stmt->bind_result($id, $heats, $timestamp, $author, $content);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				while($stmt->fetch()) {					
					$tago = Utility::timeAgo($timestamp, time());
					$r .= HTMLHelper::markupBeefResult($author, $tago, $content, $heats);
				}
			}
			else {
				$tago = Utility::timeAgo(0, time());
				$r .= HTMLHelper::markupBeefResult("Null", 0, "No beef for the criteria was found!", 'null');
			}
		} else if($type == "people") {
			$stmt = $this->db->prepare("SELECT `username`, `email`, `uid` FROM users WHERE username LIKE ? ORDER BY uid DESC");
			$stmt->bind_result($username, $email, $uid);
			$term = "%" . $term . "%";
			$stmt->bind_param("s", $term);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				while($stmt->fetch()) {					
					$r .= HTMLHelper::markupPeopleResult($username, $this->user->countBeefs($username), $this->user->countHeats($uid), 'Ninja land');
				}
			}
			else {
				$r .= HTMLHelper::markupPeopleResult("Not found", 0, 0, 'null');
			}
		}
		return $r;
	}

}
?>