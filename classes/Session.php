<?php
/**
 * Handles almost everything to do with the user's current session on the site and really is the glue for everything else to build on.
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

require_once 'User.php';
require_once 'HTMLHelper.php';
require_once 'Utility.php';
require_once 'Beef.php';

class Session extends User
{
	private	$db;
	public $sid;
	private $crypter;
	public $uid;
	public $private_name;
	public $public_name;
	public static $instance;

	public static function getInstance($dbc) {
		if(!self::$instance)
			self::$instance = new Session($dbc);
			return self::$instance;
	}

	/**
	 *
	 * Checks if the user is logged in, if he is, it will make sure the session hasn't expired.
	 */
	function __construct($db)
	{
		$this->db = $db;
		$this->crypter = Crypter::getInstance("Any password", MCRYPT_RIJNDAEL_256);
		$this->uid = $this->getUid();
		$this->public_name = $this->getPublicUsername();
		$this->private_name = $this->getPrivateUsername();
		$this->sid = $_SESSION['sid'];
		$this->validateSession();
	}

	/**
	 *
	 * Inserts the new user's username, password (hashed), and email into the mysql database.
	 * @param string|int $user the username
	 * @param string|int $pass the password (non-hashed)
	 * @param string $email the email address
	 */
	function register($user, $pass, $email)
	{
		$user = Utility::formatName($user);
		$email = strtolower($email);
		$pass = User::beefHash($pass);
		$stmt = $this->db->prepare("INSERT INTO `users` (`username` ,`password` ,`email`) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $user, $pass, $email);
		$stmt->execute();
	}

	/**
	 *
	 * Validates a username and password, returns based on success or failure
	 * @param string|int $user
	 * @param string|int $pass
	 * @return boolean
	 */
	function checkLogin($user, $pass)
	{
		$user = Utility::formatName($user);
		$pass = User::beefHash($pass);

		$stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = ? AND `password` = ?");
		$stmt->bind_param("ss", $user, $pass);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0)
		{
			return true;
		}
		return false;
	}

	/**
	 * Makes sure the either email or username is valid
	 * 
	 * @param string|int $toFind
	 * @return boolean
	 * @since 0.7.0.0
	 */
	function checkAvailable($toFind)
	{
		//Get the lowercase arguments
		$toFind = strtolower(mysqli_real_escape_string($this->db, $toFind));
		$match = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
		if (preg_match($match, $toFind))
		{
			$query = $this->db->query("SELECT * FROM `users` WHERE `email`='{$toFind}'");
			if ($query->num_rows > 0)
			{
				return true;
			} 
		}
		else
		{
			$query = $this->db->query("SELECT * FROM `users` WHERE `username`='{$toFind}'");

			if ($query->num_rows > 0)
			{
				return true;
			}
		}
		return false;

	}

	/**
	 * Checks if user is logged in
	 * 
	 * @return boolean True if logged in, otherwise false
	 * @since 0.7.0.0
	 */
	function isLoggedIn()
	{
		if($this->validateSession())
		{
			return true;
		}
		return false;
	}

	/**
	 *
	 * Sets a user session using the php $_SESSION array as well as inserting the rows into the session DB table
	 * @param string|int $user
	 * @param string|int $pass
	 */
	function login($user, $pass)
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$stmt = $this->db->prepare("SELECT `uid` FROM `users` WHERE `username` = ?");
		$stmt->bind_param("s", $user);
		$stmt->bind_result($uid);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				$sess = $this->db->prepare("SELECT `sid` FROM `sessions` WHERE `uid` = ?");
				$sess->bind_param("i", $uid);
				$sess->bind_result($sid);
				$sess->execute();
				$sess->store_result();
				if($sess->num_rows > 0) {
					while($sess->fetch()) {
						$s2 = $this->db->prepare("DELETE FROM `sessions` WHERE `uid` = ?");
						$s2->bind_param("i", $uid);
						$s2->execute();
					}
				}
				$lastlogin = date("m/d/y \@ g:i A T");
				//date("D M j G:i:s T Y");   
				$upd = $this->db->prepare("UPDATE `users` SET `ip` = ?, `lastlogin` = ? WHERE `uid` = ?");
				$upd->bind_param("ssi", $ip, $lastlogin, $uid);
				$upd->execute();

				//echo "1";
				$expiry = time() + 60 * SESSION_LENGTH;
				$sid = hash('sha512', Utility::generateRandID(16));
				$time = time();
				//when the session should expire (the current time + the session length in minutes)
				$setter = $this->db->prepare("INSERT INTO `sessions` (`uid`, `sid`, `expiry`, `ip`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
				$setter->bind_param("isisi", $uid, $sid, $expiry, $_SERVER['REMOTE_ADDR'], $time);
				$setter->execute();
				$_SESSION['sid'] = $sid;
			}
		}
	}

	/**
	 *
	 * Sets a user session using the php $_SESSION array as well as inserting the rows into the session DB table
	 * @param string|int $user
	 * @param string|int $pass
	 */
	function loginOAUTH($user, $pass, $oauthid)
	{
		$lookup = $this->db->prepare("SELECT * FROM `users` WHERE `username` = ? AND `password` = ?");
		$lookup->bind_param("ss", $user, $pass);
		$lookup->execute();
		$lookup->store_result();
		if($lookup->num_rows < 1)
			return;
		$ip = $_SERVER['REMOTE_ADDR'];
		$stmt = $this->db->prepare("SELECT `uid` FROM `users` WHERE `username` = ?");
		$stmt->bind_param("s", $user);
		$stmt->bind_result($uid);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				$sess = $this->db->prepare("SELECT `sid` FROM `sessions` WHERE `uid` = ?");
				$sess->bind_param("i", $uid);
				$sess->bind_result($sid);
				$sess->execute();
				$sess->store_result();
				if($sess->num_rows > 0) {
					while($sess->fetch()) {
						$s2 = $this->db->prepare("DELETE FROM `sessions` WHERE `uid` = ?");
						$s2->bind_param("i", $uid);
						$s2->execute();
					}
				}
				$lastlogin = date("m/d/y \@ g:i A T");
				//date("D M j G:i:s T Y");   
				$upd = $this->db->prepare("UPDATE `users` SET `ip` = ?, `lastlogin` = ? WHERE `uid` = ?");
				$upd->bind_param("ssi", $ip, $lastlogin, $uid);
				$upd->execute();

				//echo "1";
				$expiry = time() + 60 * SESSION_LENGTH;
				$sid = hash('sha512', Utility::generateRandID(16));
				$time = time();
				//when the session should expire (the current time + the session length in minutes)
				$setter = $this->db->prepare("INSERT INTO `sessions` (`uid`, `sid`, `expiry`, `ip`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
				$setter->bind_param("isisi", $uid, $sid, $expiry, $_SERVER['REMOTE_ADDR'], $time);
				$setter->execute();
				echo "hi";
				$_SESSION['sid'] = $sid;
			}
		}
	}

	/**
	 * Deletes the current session from the database and destroys the session.
	 * @param string $sid the session id to remove
	 */
	function logout($sid)
	{
		$sid = htmlentities(mysqli_real_escape_string($this->db, $sid));
		$query = $this->db->query("DELETE FROM sessions WHERE sid='{$sid}'");
		Utility::redirect('index.php');
		session_destroy();
	}

	public function getPrivateUsername() {
		$uid = $this->uid;
		$stmt = $this->db->prepare("SELECT `username`, `fb_username`, `using_fb` FROM `users` WHERE `uid` = ?");
		$stmt->bind_param("i", $uid);
		$stmt->bind_result($username, $fb, $bool);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				if($bool == 0)
				return Utility::formatName($username);
				else if($bool == 1)
				return Utility::formatName($fb);
			}
		} else {
			//le not logged in
		}
	}

	public function getPublicUsername() {
		$uid = $this->uid;
		$stmt = $this->db->prepare("SELECT `username` FROM `users` WHERE `uid` = ?");
		$stmt->bind_param("i", $uid);
		$stmt->bind_result($username);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				return Utility::formatName($username);
			}
		} else {
			//le not logged in
		}
	}
	
	public function lookupUsername($uid) {
		$stmt = $this->db->prepare("SELECT `username` FROM `users` WHERE `uid` = ?");
		$stmt->bind_param("i", $uid);
		$stmt->bind_result($username);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				if(substr($username, 0, 3) == "fb_") {
					$qry = $this->db->prepare("SELECT `fb_username` FROM `users` WHERE `uid` = ?");
					$qry->bind_param("i", $uid);
					$qry->bind_result($fb);
					$qry->execute();
					$qry->store_result();
					if($qry->num_rows > 0) {
						while($qry->fetch()) {
							return $fb;
						}
					}
				}
				return $username;
			}
		} else {
			//le not logged in
		}
	}

	public function lookupAvatar($uid) {
		$stmt = $this->db->prepare("SELECT `avatar` FROM `users` WHERE `uid` = ?");
		$stmt->bind_param("i", $uid);
		$stmt->bind_result($avatar);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				return $avatar;
			}
		} else {
			//le not logged in
		}
	}

	public function getUid() {
		$stmt = $this->db->prepare("SELECT `uid` FROM `sessions` WHERE `sid` = ?");
		$stmt->bind_param("s", $_SESSION['sid']);
		$stmt->bind_result($uid);
		$stmt->execute();
		$stmt->store_result();
		if($stmt->num_rows > 0) {
			while($stmt->fetch()) {
				return $uid;
			}
		}
	}

	public function validateSession() {
		if(isset($_SESSION['sid'])) {
			$sid = $_SESSION['sid'];
			$stmt = $this->db->prepare("SELECT `sid`, `ip`, `expiry`, `timestamp`, `uid` FROM `sessions` WHERE `sid` = ?");
			$stmt->bind_result($sid, $ip, $expiry, $timestamp, $uid);
			$stmt->bind_param("s", $sid);
			$stmt->execute();
			$stmt->store_result();
			if($stmt->num_rows > 0) {
				while($stmt->fetch()) {
					if($expiry > time()) {
						if($ip == $_SERVER['REMOTE_ADDR']) {
							return true;
						}
					}
				}
			}
			$this->logout($_SESSION['sid']);
		} else if(isset($_SESSION['oauth_id'])) {
			
		}
		return false;
	}
}
?>