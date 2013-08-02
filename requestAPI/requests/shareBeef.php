<?php
	//$content of beef
	//$id of the beef
	$bool = $facebook->hasPostingPermissions();
	if($bool) {
		$facebook->postToWall($content);
	} else {
		echo "0";
	}
?>