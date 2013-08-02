<?php
if($allowAccess)
{
	printf(($session->checkAvailable($input) ? 'true' : 'false'));
}
?>