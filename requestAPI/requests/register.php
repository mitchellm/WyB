<?php
	if($allowAccess)
	{
		$session->register($user, $pass, $email);
		$session->login($user, $pass);
	}
?>