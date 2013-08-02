<?php
if($allowAccess)
{
	$beef->post($content);
	printf($beef->createBeef($session->private_name, $content, $session->uid));
}
?>