<?php
if($allowAccess)
{
	echo $session->login($user, $pass);
}
