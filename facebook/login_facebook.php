<?php
require_once __DIR__ . '/../global.php';
mysql_connect('70.185.172.81', 'wyb', '');
mysql_select_db('wyb');

# We require the library
require("facebook.php");
function __autoload($class_name) {
	require_once __DIR__ . '/../classes/' . $class_name . '.php';
}
$dbc = Database::getConnection();
$sessObj = new Session($dbc);

# Creating the facebook object
$facebook = new Facebook(array(
	'appId'  => '173811679339821',
	'secret' => 'f61f7b8608a6a540d9b542d22000cce3',
	'cookie' => true
));

# Let's see if we have an active session
$session = $facebook->getUser();

if(!empty($session)) {
	# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
	try{
		$uid = $facebook->getUser();
		$user = $facebook->api('/me');
	} catch (Exception $e){}
	
	if(!empty($user)){
		# We have an active session, let's check if we have already registered the user
		$query = mysql_query("SELECT * FROM users WHERE oauth_provider = 'facebook' AND oauth_uid = ". $user['id']);
		$result = mysql_fetch_array($query);
		
		# If not, let's add it to the database
		if(empty($result)){
			$string = uniqid();
			$salt = md5($string."%*4!#$;\.k~'(_@");
			$string = md5("$salt$string$salt");
			$pass = sha1($string);
			$usr = uniqid('fb_');
			$avatar = "http://graph.facebook.com/{$user['id']}/picture";
			$query = mysql_query("INSERT INTO users (oauth_provider, oauth_uid, fb_username, username, password, using_fb, avatar) VALUES ('facebook', {$user['id']}, '{$user['name']}', '{$usr}', '{$pass}', '1', '{$avatar}')");
			$query = mysql_query("SELECT * FROM users WHERE uid = " . mysql_insert_id());
			$result = mysql_fetch_array($query);
		}
		$sessObj->loginOAUTH($result['username'], $result['password'], $result['oauth_uid']);
		Utility::redirect("../index.php");
	} else {
		# For testing purposes, if there was an error, let's kill the script
		die("There was an error.");
	}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}