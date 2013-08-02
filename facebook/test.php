<?php
# We require the library
require("facebook.php");

# Creating the facebook object
$facebook = new Facebook(array(
	'appId'  => '173811679339821',
	'secret' => 'f61f7b8608a6a540d9b542d22000cce3',
	'cookie' => true
));

# Let's see if we have an active session
$session = $facebook->getUser();
$user = $facebook->api('/me');
if(!empty($session)) {
	# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
	try{
		print_r($user);
	} catch (Exception $e){}
} else {
	# There's no active session, let's generate one
	$login_url = $facebook->getLoginUrl();
	header("Location: ".$login_url);
}