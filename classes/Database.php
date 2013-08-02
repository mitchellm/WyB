<?php
/**
 * This is used to be able to utilize the singleton design patten and prevent more than 1 database connection per page, per database.
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

class Database {
	private static $dbc;
	
	public static function getConnection() {
		if(!self::$dbc)
			self::$dbc = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

			return self::$dbc;
	}
}
?>