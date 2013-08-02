<?php
require(__DIR__ . "/../facebook/facebook.php");

class FacebookHelper {
	public $facebook;
	public $session;

	public function __construct() {
		mysql_connect('70.185.172.81', 'wyb', '');
		mysql_select_db('wyb');
		$this->facebook = new Facebook(array(
			'appId'  => '173811679339821',
			'secret' => 'f61f7b8608a6a540d9b542d22000cce3',
			'cookie' => true
		));

		$this->session = $this->facebook->getUser();
	}

	public function hasPostingPermissions() {
		try{
			$uid = $this->facebook->getUser();
			
			$api_call = array(
				'method' => 'users.hasAppPermission',
				'uid' => $uid,
				'ext_perm' => 'publish_stream'
			);
			$can_post = $this->facebook->api($api_call);
			if($can_post){
				return true;
			}
		} catch (Exception $e){}
		return false;
	}

	public function postToWall($content) {
		$uid = $this->facebook->getUser();
		$this->facebook->api('/'.$uid.'/feed', 'post', array(
			'message' => $content,
			'name' => 'What\'s your beef?',
			'description' => 'Share that one thing that pisses you off',
			'caption' => 'websites without slogans',
			'picture' => 'http://www.2000greetings.com/funny_cow.jpg',
			'link' => 'http://localhost/wyb/index.php'
		));
	}

	public function requestPermissions($uid) {
		if(!empty($this->session)) {
			# Active session, let's try getting the user id (getUser()) and user info (api->('/me'))
			try{
				$uid = $this->facebook->getUser();
				
				# req_perms is a comma separated list of the permissions needed
				$url = $this->facebook->getLoginUrl(array(
					'req_perms' => 'email,user_birthday,status_update,publish_stream,user_photos,user_videos'
				));
				return $url;
			} catch (Exception $e){}
		}
		return "0";
	}
}
?>