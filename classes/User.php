<?php

/**
 * A class that holds functions for a user
 * 
 * @author Mitchell Murphy
 * @author Cruz Bishop
 * @version 0.7.0.1
 */
class User {
	
	/**
	 * The database connection
	 * 
	 * @var \mysqli The database connection to use
	 * @since 0.7.0.0
	 */
	private $db;
	

	private static $instance;

	public static function getInstance($dbc) {
		if(!self::$instance)
			self::$instance = new User($dbc);
			return self::$instance;
	}

	/**
	* Constructs this class
	* 
	* @since 0.7.0.0
	*/
	function __construct($dbc)
	{
		//Set up the database connection
		$this->db = $dbc;
	}
	
	/**
	 * Securely hashes a string with a salt using both the md5 and sha1 algorithms
	 * 
	 * @param string $string to hash
	 * @return string hashed string
	 * @since 0.7.0.0
	 */
	function beefHash($string) {
		$salt = md5($string."%*4!#$;\.k~'(_@");
		$string = md5("$salt$string$salt");
		$string = sha1($string);

		return $string;
	}

	public function countHeats($uid) {
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `heats` WHERE `uid` = {$uid}");
		$stmt->bind_result($count);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				return $count;
			}
		} else {
			return 0;
		}
	}

	public function countBeefs($uid) {
		$stmt = $this->db->prepare("SELECT COUNT(*) FROM `beefs` WHERE `author` = ?");
		$stmt->bind_param("s", $uid);
		$stmt->bind_result($count);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				return $count;
			}
		} else {
			return 0;
		}
	}
}
?>
