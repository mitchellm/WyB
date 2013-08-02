<?php
if($allowAccess)
{
	if($session->isLoggedIn())
		$beef->reheat($id, $_SESSION['sid'], $heats);
	else
		echo $heats;
}
?>