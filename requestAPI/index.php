<?php
/**
 * Handles all requests from the javascript engine that powers our beautiful site.
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

function __autoload($class) {
	require_once '../classes/' . $class . '.php';
}
require_once '../global.php';

$dbc = Database::getConnection();
$session = new Session($dbc);
$beef = new Beef($dbc);
$site = new Site($dbc);
$people = new People($dbc);
$facebook = new FacebookHelper();
$allowAccess = true;

foreach($_GET as $key => $value)
{
	$$key = $_GET[$key];
}
foreach($_POST as $key => $value)
{
	$$key = $_POST[$key];
} 

if(isset($request))
{
		$command = $request;
		$toRemove = array("../", ".php", "/", ".");
		$toReplace = array("", "", "", "");
		$command = str_replace($toRemove, $toReplace, $command);
		
	if(file_exists('requests/' . $command . '.php'))
		include 'requests/' . $command . '.php';
	else
		printf('Error! Command ' . $command . ' not found');
} else {
	Utility::redirect('../index.php');
}