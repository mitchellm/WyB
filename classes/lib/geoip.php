<?php
/**
 * Accesses the GeoIP functions to lookup an ip using their location database and return a record. 
 * 
 * @author Mitchell Murphy
 * @package What's Your Beef
 * @version 1.0.9
 */

include(__DIR__ . "/../GeoIP/geoipcity.inc");
include(__DIR__ . "/../GeoIP/geoipregionvars.php");

function lookup_ip($ip) {
	$gi = geoip_open(__DIR__ . "/../../data/GeoLiteCity.dat",GEOIP_STANDARD);

	$record = geoip_record_by_addr($gi, $ip);

	geoip_close($gi);
	return $record;
}
?>