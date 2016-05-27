<?php
$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

// detection du point en fin du nom de host
if (substr($http_host,strlen($http_host)-1) =='.') {
	$http_host=substr($http_host, 0,strlen($http_host)-1);

	if (isset($_SERVER['REQUEST_URI'])) {
		$http_host.=$_SERVER['REQUEST_URI'];
	}

	if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=="on")
				|| (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS'])=='on'))  {
		$http_host="https://".$http_host;
	}
	else {
		$http_host="http://".$http_host;
	}

	header("Location: ".$http_host);
	die();
}

date_default_timezone_set("Europe/Paris");
define ('_DIMS_DEFAULT_TIMEZONE', 1); // GMT +1

switch($http_host) {
	default:
		define ('_DIMS_DB_LOGIN','<DB_LOGIN>');
		define ('_DIMS_DB_PASSWORD','<DB_PASSWORD>');
		define ('_DIMS_DB_DATABASE','<DB_DATABASE>');
		define ('_DIMS_DB_SERVER','<DB_SERVER>');
		break;
}
