<?php 
if($allowAccess)
{
	if($type == "beefs") 
		$v = $beef->getSpecificPages($term);
	else if($type == "people")
		$v = $people->getSpecificPages($term);
	else if($type == "latest")
		$v = $beef->getPages();
	echo $site->drawNavigation($pageRequest, $v, $type);
}
?>