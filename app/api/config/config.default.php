<?php
date_default_timezone_set('Europe/Paris');
setlocale (LC_ALL, "fr_FR");

if( ! defined('ROOT_PATH') )	define('ROOT_PATH', dirname(__FILE__).'/..');
if( ! defined('APP_PATH') )		define('APP_PATH', ROOT_PATH . '/app');
if( ! defined('CRYPTO_COST') )	define ('CRYPTO_COST','10');
if( ! defined('CRYPTO_SALT') )	define ('CRYPTO_SALT','Pl.FGi/55Cr');

require_once ROOT_PATH . '/config/database.php';// config DB


function include_dir($path){
	if(!empty($path) && file_exists($path) && is_dir($path)){
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if(pathinfo($entry, PATHINFO_EXTENSION) == 'php'){//en chopan le file info on a juste text/plain
					include_once $path.'/'.$entry;
				}
			}
		}
	}
}
