<?php
require_once 'global.php';

function __autoload($class) {
	require_once 'classes/' . $class . '.php';
}

$dbc = Database::getConnection();
$session = new Session($dbc);
$crypter = new Crypter("Any password", MCRYPT_RIJNDAEL_256);
$beef = new Beef($dbc);
echo date("m/d/y \@ g:i A T");

//echo $session->login("mitchell", "12781278");
//echo "<br />";
//echo $session->validateSession();
?>