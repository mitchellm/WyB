<?php
if($allowAccess)
{
	if(isset($update))
	$session->setTimestamp(time());
	if($type == "latest")
		printf($beef->grabAll($pageRequest, time()));
	else if($type == "beefs")
		printf($beef->grabSpecific($pageRequest, $term));
	else if($type == "people") 
		printf($people->grabSpecific($pageRequest, $term));
}
?>