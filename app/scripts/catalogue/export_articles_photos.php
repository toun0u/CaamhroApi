<?php

// Exporte la liste des photos originales et watermarkées qui s'affichent sur le site

define('AUTHORIZED_ENTRY_POINT', true);

chdir(dirname($argv[0]));
chdir('../../../www/');

// load config file
require_once '../config.php'; // load config (mysql, path, etc.)
require_once '../app/include/default_config.php'; // load config (mysql, path, etc.)

include_once(DIMS_APP_PATH . "/modules/system/class_dims.php");
include_once(DIMS_APP_PATH . "/modules/system/class_workspace.php");
// INITIALIZE DIMS OBJECT
$dims = new dims();

include_once DIMS_APP_PATH . '/include/errors.php';
include_once DIMS_APP_PATH . '/include/class_debug.php';

// load DIMS global classes
include_once DIMS_APP_PATH . '/include/class_dims_data_object.php';

// initialize DIMS
include_once DIMS_APP_PATH . '/include/global.php';		// load dims global functions & constants

/**
* Database connection
*
* Don't forget to param db connection in ./include/config.php
*/

if (file_exists(DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php')) {
	include DIMS_APP_PATH.'/include/db/class_db_'._DIMS_SQL_LAYER.'.php';
}

$db = new dims_db(_DIMS_DB_SERVER, _DIMS_DB_LOGIN, _DIMS_DB_PASSWORD, _DIMS_DB_DATABASE);
if(!$db->connection_id) trigger_error(dims_const::_DIMS_MSG_DBERROR, E_USER_ERROR);

$dims->setDb($db);

if ($dims->getHttpHost()=="") $dims->setHttpHost($http_host);
// initialisation des workspaces
$dims->initWorkspaces();

$webworkspaces=$dims->getWebWorkspaces();

dims::setInstance($dims);
$dims->init_metabase();


define ('PHOTOS_PATH', realpath('..').'/photos');

// Ouverture des fichiers en écriture
$fp_originals = fopen(PHOTOS_PATH.'/originals.txt', "w");
flock($fp_originals, LOCK_EX);

$fp_watermarked = fopen(PHOTOS_PATH.'/watermarked.txt', "w");
flock($fp_watermarked, LOCK_EX);

$fp_nope = fopen(PHOTOS_PATH.'/nope.txt', "w");
flock($fp_nope, LOCK_EX);


$rs = $db->query('SELECT DISTINCT(a.`reference`)
	FROM 	`dims_mod_cata_article` a
	INNER JOIN 	`dims_mod_cata_stocks` s
	ON 			s.`id_article` = a.`id`
	AND 		s.`held_in_stock` = 1
	WHERE 	a.`status` = "OK" ;');

while ($row = $db->fetchrow($rs)) {
	if (file_exists(PHOTOS_PATH.'/100x100/'.$row['reference'].'.jpg')) {
		fwrite($fp_originals, $row['reference']."\n");
	}
	else {
		fwrite($fp_nope, $row['reference']."\n");
	}
	if (file_exists(PHOTOS_PATH.'/300x300/'.$row['reference'].'.jpg')) {
		fwrite($fp_watermarked, $row['reference']."\n");
	}
}

// Fermeture des fichiers
flock($fp_originals, LOCK_UN);
fclose($fp_originals);
